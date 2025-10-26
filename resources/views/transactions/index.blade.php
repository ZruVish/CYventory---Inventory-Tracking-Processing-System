@extends('layouts.app')

@section('title', 'Transactions - Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Transaction History</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Monitor all inventory transactions</p>
</div>

<!-- Filter Form -->
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('transactions.index') }}">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Transaction Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>Inbound</option>
                        <option value="outbound" {{ request('type') == 'outbound' ? 'selected' : '' }}>Outbound</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">All Transactions ({{ $transactions->total() }})</h2>
        @if(auth()->user()->canCreateTransactions())
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ New Transaction</a>
        @endif
    </div>
    <table class="cyber-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Processed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>#{{ $transaction->id }}</td>
                <td>{{ $transaction->inventory->item_name }}</td>
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
                <td>
                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: var(--text-muted);">
                    No transactions found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($transactions->hasPages())
<div style="text-align: center; margin-top: 2rem;">
    {{ $transactions->links() }}
</div>
@endif
@endsection