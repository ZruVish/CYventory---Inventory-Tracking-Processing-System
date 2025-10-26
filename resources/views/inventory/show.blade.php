@extends('layouts.app')

@section('title', 'Item Details - Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $inventory->item_name }}</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Detailed cyberpunk item information</p>
</div>

<!-- Item Information Card -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Item Information</h2>
        <div>
            <span class="badge badge-{{ $inventory->status }}">
                {{ ucfirst($inventory->status) }}
            </span>
            @if($inventory->isLowStock())
                <span class="badge badge-low-stock">Low Stock</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div class="form-group">
                    <label class="form-label">Item Code</label>
                    <div style="color: var(--neon-cyan); font-weight: bold; font-size: 1.1rem;">{{ $inventory->item_code }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <div style="font-weight: bold;">{{ $inventory->item_name }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <div>{{ $inventory->category }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <div style="background: var(--dark-surface); padding: 1rem; border-radius: 4px; min-height: 60px;">
                        {{ $inventory->description ?: 'No description available' }}
                    </div>
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Current Quantity</label>
                    <div style="font-weight: bold; color: var(--neon-cyan); font-size: 1.5rem;">
                        {{ number_format($inventory->quantity) }}
                        @if($inventory->isLowStock())
                            <span style="color: var(--danger); font-size: 0.8rem; display: block;">
                                ⚠ Below reorder level ({{ $inventory->reorder_level }})
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Unit Price</label>
                    <div style="font-weight: bold; font-size: 1.2rem;">₱{{ number_format($inventory->unit_price, 2) }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Total Value</label>
                    <div style="font-weight: bold; color: var(--neon-cyan); font-size: 1.3rem;">₱{{ number_format($inventory->total_value, 2) }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Reorder Level</label>
                    <div>{{ number_format($inventory->reorder_level) }} units</div>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--border-glow); margin-top: 2rem; padding-top: 1rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                <div>
                    <strong>Created:</strong> {{ $inventory->created_at->format('M d, Y H:i:s') }}
                </div>
                <div>
                    <strong>Last Updated:</strong> {{ $inventory->updated_at->format('M d, Y H:i:s') }}
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-primary">Edit Item</a>
            <a href="{{ route('transactions.create', ['item_id' => $inventory->id]) }}" class="btn btn-success">New Transaction</a>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
@if($recentTransactions && $recentTransactions->count() > 0)
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h2 class="card-title">Recent Transactions</h2>
        <a href="{{ route('transactions.index', ['item_id' => $inventory->id]) }}" class="btn btn-secondary">View All</a>
    </div>
    <div class="card-body">
        <table class="cyber-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Processed By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $transaction)
                <tr>
                    <td>#{{ $transaction->id }}</td>
                    <td>
                        <span class="badge badge-{{ $transaction->transaction_type }}">
                            {{ ucfirst($transaction->transaction_type) }}
                        </span>
                    </td>
                    <td>{{ number_format($transaction->quantity) }}</td>
                    <td>₱{{ number_format($transaction->unit_price, 2) }}</td>
                    <td>₱{{ number_format($transaction->total_amount, 2) }}</td>
                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $transaction->processed_by }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Stock Level Chart (Optional Enhancement) -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h2 class="card-title">Stock Status</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="stat-card" style="background: rgba(0, 255, 255, 0.1); border: 1px solid var(--neon-cyan); padding: 1rem; border-radius: 4px;">
                <div style="color: var(--neon-cyan); font-size: 0.9rem;">Current Stock</div>
                <div style="color: var(--neon-cyan); font-size: 2rem; font-weight: bold;">{{ number_format($inventory->quantity) }}</div>
            </div>
            
            <div class="stat-card" style="background: rgba(255, 165, 0, 0.1); border: 1px solid var(--warning); padding: 1rem; border-radius: 4px;">
                <div style="color: var(--warning); font-size: 0.9rem;">Reorder Level</div>
                <div style="color: var(--warning); font-size: 2rem; font-weight: bold;">{{ number_format($inventory->reorder_level) }}</div>
            </div>
            
            <div class="stat-card" style="background: rgba(0, 255, 0, 0.1); border: 1px solid var(--success); padding: 1rem; border-radius: 4px;">
                <div style="color: var(--success); font-size: 0.9rem;">Stock Status</div>
                <div style="color: var(--success); font-size: 1.5rem; font-weight: bold;">
                    @if($inventory->quantity <= 0)
                        <span style="color: var(--danger);">OUT OF STOCK</span>
                    @elseif($inventory->isLowStock())
                        <span style="color: var(--warning);">LOW STOCK</span>
                    @else
                        HEALTHY
                    @endif
                </div>
            </div>
            
            <div class="stat-card" style="background: rgba(138, 43, 226, 0.1); border: 1px solid #8a2be2; padding: 1rem; border-radius: 4px;">
                <div style="color: #8a2be2; font-size: 0.9rem;">Total Value</div>
                <div style="color: #8a2be2; font-size: 1.5rem; font-weight: bold;">₱{{ number_format($inventory->total_value, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card" style="margin-top: 2rem; background: rgba(255, 0, 255, 0.05); border-color: var(--neon-purple);">
    <div class="card-header">
        <h2 class="card-title" style="color: var(--neon-purple);">Quick Actions</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            @if(auth()->user()->canCreateTransactions())
                <a href="{{ route('transactions.create', ['item_id' => $inventory->id, 'type' => 'inbound']) }}" 
                   class="btn btn-success" style="display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <span style="font-size: 1.5rem; margin-right: 0.5rem;">+</span>
                    Add Stock (Inbound)
                </a>
                
                @if($inventory->quantity > 0)
                <a href="{{ route('transactions.create', ['item_id' => $inventory->id, 'type' => 'outbound']) }}" 
                   class="btn btn-warning" style="display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <span style="font-size: 1.5rem; margin-right: 0.5rem;">-</span>
                    Remove Stock (Outbound)
                </a>
                @endif
            @endif
            
            @if(auth()->user()->canManageInventory())
                <a href="{{ route('inventory.edit', $inventory) }}" 
                   class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; padding: 1rem;">
                    <span style="margin-right: 0.5rem;">⚙</span>
                    Edit Item Details
                </a>
            @endif
        </div>
    </div>
</div>
@endsection