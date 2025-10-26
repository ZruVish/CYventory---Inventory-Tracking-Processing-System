@extends('layouts.app')

@section('title', 'Edit Transaction Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Transaction #{{ $transaction->id }}</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Modify transaction details</p>
</div>

<div class="card" style="background: rgba(255, 165, 0, 0.05); border-color: var(--warning);">
    <div class="card-header">
        <h2 class="card-title" style="color: var(--warning);">⚠ Transaction Edit Warning</h2>
    </div>
    <div class="card-body">
        <p style="color: var(--warning); margin: 0;">
            Editing transactions will affect inventory calculations. Only modify if you understand the impact on stock levels.
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Transaction Details</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}" id="editTransactionForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" name="reference_number" class="form-control" 
                       placeholder="PO-2024-001, SO-2024-001, etc." 
                       value="{{ old('reference_number', $transaction->reference_number) }}">
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                    Previous: {{ $transaction->reference_number ?: 'Not set' }}
                </div>
                @error('reference_number')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Processed By *</label>
                <input type="text" name="processed_by" class="form-control" 
                       placeholder="Enter processor name" 
                       value="{{ old('processed_by', $transaction->processed_by) }}" required>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                    Previous: {{ $transaction->processed_by }}
                </div>
                @error('processed_by')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" 
                          placeholder="Additional transaction notes...">{{ old('notes', $transaction->notes) }}</textarea>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                    Previous: {{ $transaction->notes ?: 'No notes' }}
                </div>
                @error('notes')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Original Transaction Info -->
            <div class="card" style="background: rgba(0, 255, 255, 0.05); border-color: var(--neon-cyan);">
                <div class="card-header">
                    <h3 style="color: var(--neon-cyan); margin: 0; font-size: 1rem;">Original Transaction</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                        <div>
                            <strong>Original Item:</strong> {{ $transaction->inventory->item_code }} - {{ $transaction->inventory->item_name }}<br>
                            <strong>Original Type:</strong> {{ ucfirst($transaction->transaction_type) }}<br>
                            <strong>Original Quantity:</strong> {{ number_format($transaction->quantity) }}<br>
                            <strong>Processing Date:</strong> {{ $transaction->processed_at->format('M d, Y H:i:s') }}
                        </div>
                        <div>
                            <strong>Original Price:</strong> ₱{{ number_format($transaction->unit_price, 2) }}<br>
                            <strong>Original Total:</strong> ₱{{ number_format($transaction->total_amount, 2) }}<br>
                            <strong>Original Processor:</strong> {{ $transaction->processed_by }}<br>
                            <strong>Created:</strong> {{ $transaction->created_at->format('M d, Y H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-secondary">Cancel</a>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Back to List</a>
                <button type="button" class="btn btn-primary" onclick="confirmTransactionUpdate()">Update Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmTransactionUpdate() {
    const form = document.getElementById('editTransactionForm');
    const referenceNumber = form.querySelector('input[name="reference_number"]').value;
    const processedBy = form.querySelector('input[name="processed_by"]').value;
    const notes = form.querySelector('textarea[name="notes"]').value;
    
    if (!processedBy.trim()) {
        showError('Incomplete Form', 'Processed By field is required');
        return;
    }
    
    const changes = [];
    if (referenceNumber !== '{{ $transaction->reference_number ?? '' }}') {
        changes.push(`Reference Number: "{{ $transaction->reference_number ?? 'Not set' }}" → "${referenceNumber || 'Not set'}"`);
    }
    if (processedBy !== '{{ $transaction->processed_by }}') {
        changes.push(`Processed By: "{{ $transaction->processed_by }}" → "${processedBy}"`);
    }
    if (notes !== '{{ $transaction->notes ?? '' }}') {
        changes.push(`Notes: Changed`);
    }
    
    if (changes.length === 0) {
        showWarning('No Changes', 'No changes were detected in the form.');
        return;
    }
    
    const message = `
        <div style="text-align: left;">
            <p><strong>Transaction #{{ $transaction->id }}</strong></p>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">{{ $transaction->inventory->item_code }} - {{ $transaction->inventory->item_name }}</p>
            <p><strong>Changes to be made:</strong></p>
            <ul style="margin: 1rem 0; padding-left: 1.5rem; color: var(--neon-cyan);">
                ${changes.map(change => `<li>${change}</li>`).join('')}
            </ul>
            <p style="color: var(--warning); margin-top: 1rem; font-size: 0.9rem;">
                ⚠️ This will update the transaction record but will not affect inventory quantities.
            </p>
        </div>
    `;
    
    confirmUpdate('Update Transaction', message, function() {
        form.submit();
    });
}
</script>
@endsection