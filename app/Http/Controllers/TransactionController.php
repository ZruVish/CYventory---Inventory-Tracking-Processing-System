<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('role:admin,manager')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Transaction::with('inventory');

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('item_id')) {
            $query->where('inventory_id', $request->item_id);
        }

        $transactions = $query->latest()->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        if (!auth()->user()->canCreateTransactions()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        $inventories = Inventory::active()->orderBy('item_name')->get();
        return view('transactions.create', compact('inventories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canCreateTransactions()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'transaction_type' => 'required|in:inbound,outbound',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'reference_number' => 'nullable|max:255',
            'notes' => 'nullable',
            'processed_by' => 'nullable|max:255'
        ]);

        // Set processed_by to current user if not provided
        if (empty($validated['processed_by'])) {
            $validated['processed_by'] = auth()->user()->name;
        }

        try {
            DB::transaction(function () use ($validated) {
                $inventory = Inventory::lockForUpdate()->findOrFail($validated['inventory_id']);
                
                // Check stock for outbound transactions
                if ($validated['transaction_type'] === 'outbound' && $inventory->quantity < $validated['quantity']) {
                    throw new \Exception('Insufficient stock. Available: ' . $inventory->quantity . ', Requested: ' . $validated['quantity']);
                }
                
                // Calculate total amount
                $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
                $validated['processed_at'] = now();

                // Create transaction
                Transaction::create($validated);

                // Update inventory quantity - FIXED LOGIC
                if ($validated['transaction_type'] === 'inbound') {
                    // Inbound = Adding stock (purchase, return from customer, etc.)
                    $inventory->quantity += $validated['quantity'];
                } else {
                    // Outbound = Reducing stock (sale, issue, return to supplier, etc.)
                    $inventory->quantity -= $validated['quantity'];
                }
                
                $inventory->save();
            });

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction processed successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('inventory');
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if (!auth()->user()->canCreateTransactions()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        $inventories = Inventory::active()->orderBy('item_name')->get();
        return view('transactions.edit', compact('transaction', 'inventories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->canCreateTransactions()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        
        // Only allow editing certain fields to maintain data integrity
        $validated = $request->validate([
            'reference_number' => 'nullable|max:255',
            'notes' => 'nullable',
            'processed_by' => 'required|max:255'
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        // Only admins can delete transactions
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Only administrators can delete transactions.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                $inventory = Inventory::lockForUpdate()->findOrFail($transaction->inventory_id);
                
                // Reverse the inventory change - FIXED LOGIC
                if ($transaction->transaction_type === 'inbound') {
                    // Reverse inbound: subtract the quantity that was added
                    $inventory->quantity -= $transaction->quantity;
                } else {
                    // Reverse outbound: add back the quantity that was removed
                    $inventory->quantity += $transaction->quantity;
                }
                
                // Ensure quantity doesn't go negative
                if ($inventory->quantity < 0) {
                    throw new \Exception('Cannot delete transaction: Would result in negative stock.');
                }
                
                $inventory->save();
                $transaction->delete();
            });

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction deleted and inventory adjusted.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function stats()
    {
        $stats = [
            'today_inbound' => Transaction::inbound()->today()->sum('quantity'),
            'today_outbound' => Transaction::outbound()->today()->sum('quantity'),
            'today_value' => Transaction::today()->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}