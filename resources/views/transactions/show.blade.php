@extends('layouts.app')

@section('title', 'Transaction Details - Cyberpunk Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Transaction #{{ $transaction->id }}</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Transaction details and information</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Transaction Information</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div class="form-group">
                    <label class="form-label">Transaction ID</label>
                    <div style="color: var(--neon-cyan); font-weight: bold;">#{{ $transaction->id }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Item</label>
                    <div>{{ $transaction->inventory->item_code }} - {{ $transaction->inventory->item_name }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Transaction Type</label>
                    <div>
                        <span class="badge badge-{{ $transaction->transaction_type }}">
                            {{ ucfirst($transaction->transaction_type) }}
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <div style="font-weight: bold; color: var(--neon-cyan);">{{ number_format($transaction->quantity) }}</div>
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Unit Price</label>
                    <div style="font-weight: bold;">₱{{ number_format($transaction->unit_price, 2) }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Total Amount</label>
                    <div style="font-weight: bold; color: var(--neon-cyan); font-size: 1.2rem;">₱{{ number_format($transaction->total_amount, 2) }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Reference Number</label>
                    <div>{{ $transaction->reference_number ?: 'N/A' }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Processed By</label>
                    <div>{{ $transaction->processed_by }}</div>
                </div>
            </div>
        </div>
        
        @if($transaction->notes)
        <div class="form-group">
            <label class="form-label">Notes</label>
            <div style="background: var(--dark-surface); padding: 1rem; border-radius: 4px;">
                {{ $transaction->notes }}
            </div>
        </div>
        @endif
        
        <div class="form-group">
            <label class="form-label">Processing Details</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                <div>
                    <strong>Processed At:</strong> {{ $transaction->processed_at->format('M d, Y H:i:s') }}
                </div>
                <div>
                    <strong>Created At:</strong> {{ $transaction->created_at->format('M d, Y H:i:s') }}
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back to Transactions</a>
            @if(auth()->user()->canCreateTransactions())
                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">Edit Transaction</a>
            @endif
        </div>
    </div>
</div>
@endsection