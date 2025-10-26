@extends('layouts.app')

@section('title', 'Dashboard - CYventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">System Dashboard</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Real-time inventory monitoring and transaction processing</p>
</div>

<!-- Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($stats['total_items'] ?? 0) }}</div>
        <div class="stat-label">Total Items</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value">{{ number_format($stats['low_stock_items'] ?? 0) }}</div>
        <div class="stat-label">Low Stock Alerts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value stat-value-currency">₱{{ number_format($stats['total_value'] ?? 0, 2) }}</div>
        <div class="stat-label">Total Inventory Value</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value">{{ number_format($stats['today_transactions'] ?? 0) }}</div>
        <div class="stat-label">Today's Transactions</div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Recent Transactions</h2>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ New Transaction</a>
    </div>
    
    @if(isset($recent_transactions) && $recent_transactions->count() > 0)
    <table class="cyber-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recent_transactions as $transaction)
            <tr>
                <td>#{{ $transaction->id }}</td>
                <td>{{ $transaction->inventory->item_name ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-{{ $transaction->transaction_type }}">
                        {{ ucfirst($transaction->transaction_type) }}
                    </span>
                </td>
                <td>{{ number_format($transaction->quantity) }}</td>
                <td class="currency-cell">₱{{ number_format($transaction->total_amount, 2) }}</td>
                <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                <td>
                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
        <div style="font-size: 2rem; margin-bottom: 1rem;">📋</div>
        No recent transactions found
    </div>
    @endif
</div>

<!-- Low Stock Items -->
@if(isset($low_stock_items) && $low_stock_items->count() > 0)
<div class="table-container" style="margin-top: 2rem;">
    <div class="table-header">
        <h2 class="table-title" style="color: var(--warning);">⚠️ Low Stock Items</h2>
        <a href="{{ route('inventory.index') }}" class="btn btn-warning">Manage Inventory</a>
    </div>
    
    <table class="cyber-table">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($low_stock_items as $item)
            <tr>
                <td style="font-family: monospace; font-weight: bold;">{{ $item->item_code }}</td>
                <td>{{ $item->item_name }}</td>
                <td>
                    <span style="color: var(--danger); font-weight: bold;">{{ $item->quantity }}</span>
                </td>
                <td>{{ $item->reorder_level }}</td>
                <td>
                    <a href="{{ route('inventory.show', $item) }}" class="btn btn-secondary">View</a>
                    <a href="{{ route('transactions.create', ['item_id' => $item->id]) }}" class="btn btn-success">Restock</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<style>
/* Enhanced stat card styling for large values */
.stat-card {
    background: var(--dark-card);
    border: 1px solid var(--border-glow);
    border-radius: 8px;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 900;
    color: var(--neon-cyan);
    text-shadow: 0 0 10px var(--neon-cyan);
    margin-bottom: 0.5rem;
    line-height: 1;
    word-break: break-all;
    overflow-wrap: break-word;
    /* Ensure text fits */
    max-width: 100%;
}

/* Special styling for currency values */
.stat-value-currency {
    font-size: 1.5rem; /* Smaller font for currency */
    letter-spacing: -0.5px; /* Tighter letter spacing */
}

/* Responsive font sizes for stat values */
@media (max-width: 1200px) {
    .stat-value {
        font-size: 1.8rem;
    }
    
    .stat-value-currency {
        font-size: 1.3rem;
    }
}

@media (max-width: 768px) {
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-value-currency {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .stat-value {
        font-size: 1.2rem;
    }
    
    .stat-value-currency {
        font-size: 0.9rem;
    }
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.2;
}

/* Currency cell in tables */
.currency-cell {
    font-family: monospace;
    text-align: right;
    font-weight: bold;
}

/* Stats grid responsive improvements */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

/* Ensure minimum card width for proper display */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

/* Badge styling */
.badge-inbound {
    background: var(--neon-cyan);
    color: var(--dark-bg);
}

.badge-outbound {
    background: var(--neon-pink);
    color: var(--dark-bg);
}
</style>
@endsection