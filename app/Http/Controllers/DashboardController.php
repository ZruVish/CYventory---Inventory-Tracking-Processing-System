<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items' => Inventory::count(),
            'low_stock_items' => Inventory::lowStock()->count(),
            'total_value' => Inventory::sum(\DB::raw('quantity * unit_price')),
            'today_transactions' => Transaction::today()->count(),
            'inbound_today' => Transaction::inbound()->today()->sum('quantity'),
            'outbound_today' => Transaction::outbound()->today()->sum('quantity'),
        ];

        $recent_transactions = Transaction::with('inventory')
            ->latest()
            ->take(10)
            ->get();

        $low_stock_items = Inventory::lowStock()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_transactions', 'low_stock_items'));
    }
}