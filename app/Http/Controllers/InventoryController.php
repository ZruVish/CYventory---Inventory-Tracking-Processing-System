<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only admin and manager can create, edit, delete inventory
        //$this->middleware('role:admin,manager')->except(['index', 'show', 'search']);
    }

    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inventories = $query->paginate(15);
        $categories = Inventory::distinct()->pluck('category');

        return view('inventory.index', compact('inventories', 'categories'));
    }

    public function create()
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        
        $validated = $request->validate([
            'item_code' => 'required|unique:inventories',
            'item_name' => 'required|max:255',
            'description' => 'nullable',
            'category' => 'required|max:100',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('transactions');
        
        // Get recent transactions for this item
        $recentTransactions = $inventory->transactions()
            ->latest()
            ->take(10)
            ->get();

        return view('inventory.show', compact('inventory', 'recentTransactions'));
    }

    public function edit(Inventory $inventory)
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403, 'Access denied. Admin or Manager privileges required.');
        }
        
        $validated = $request->validate([
            'item_code' => 'required|unique:inventories,item_code,' . $inventory->id,
            'item_name' => 'required|max:255',
            'description' => 'nullable',
            'category' => 'required|max:100',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Inventory $inventory)
    {
        // Only admin can delete inventory items
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Only administrators can delete inventory items.');
        }

        // Check if inventory has transactions
        if ($inventory->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete inventory item with existing transactions.');
        }

        $inventory->delete();
        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function search(Request $request)
    {
        $items = Inventory::where('item_name', 'like', '%' . $request->q . '%')
            ->orWhere('item_code', 'like', '%' . $request->q . '%')
            ->take(10)
            ->get();

        return response()->json($items);
    }
}