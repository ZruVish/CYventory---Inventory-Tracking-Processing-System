@extends('layouts.app')

@section('title', 'New Transaction - Cyventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Process New Transaction</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Execute inventory movement operations</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Transaction Details</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Select Inventory Item *</label>
                <select name="inventory_id" class="form-control" required id="inventorySelect">
                    <option value="">Choose an item...</option>
                    @foreach($inventories as $item)
                    <option value="{{ $item->id }}" 
                            data-price="{{ $item->unit_price }}"
                            data-stock="{{ $item->quantity }}"
                            data-code="{{ $item->item_code }}"
                            data-name="{{ $item->item_name }}"
                            {{ old('inventory_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->item_code }} - {{ $item->item_name }} (Stock: {{ $item->quantity }})
                    </option>
                    @endforeach
                </select>
                @error('inventory_id')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Transaction Type *</label>
                <select name="transaction_type" class="form-control" required id="transactionType">
                    <option value="">Select transaction type...</option>
                    <option value="inbound" {{ old('transaction_type') == 'inbound' ? 'selected' : '' }}>
                        Inbound (Stock In / Purchase / Return)
                    </option>
                    <option value="outbound" {{ old('transaction_type') == 'outbound' ? 'selected' : '' }}>
                        Outbound (Stock Out / Sale / Issue)
                    </option>
                </select>
                @error('transaction_type')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="1" 
                           value="{{ old('quantity') }}" required id="quantityInput">
                    <div id="stockWarning" style="color: var(--warning); font-size: 0.8rem; margin-top: 0.5rem; display: none;">
                        Warning: Insufficient stock available
                    </div>
                    @error('quantity')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Unit Price (₱) *</label>
                    <input type="number" name="unit_price" class="form-control" step="0.01" min="0" 
                           value="{{ old('unit_price') }}" required id="unitPriceInput">
                    @error('unit_price')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Total Amount (₱)</label>
                <input type="text" class="form-control" id="totalAmount" readonly 
                       style="background: var(--dark-surface); color: var(--neon-cyan); font-weight: bold;">
            </div>

            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" name="reference_number" class="form-control" 
                       placeholder="PO-2024-001, SO-2024-001, etc." 
                       value="{{ old('reference_number') }}">
                @error('reference_number')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Processed By *</label>
                <input type="text" name="processed_by" class="form-control" 
                       placeholder="Enter processor name" 
                       value="{{ old('processed_by', auth()->user()->name) }}" required>
                @error('processed_by')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" 
                          placeholder="Additional transaction notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Transaction Summary -->
            <div class="card" style="background: rgba(0, 255, 255, 0.05); border-color: var(--neon-cyan);" id="transactionSummary">
                <div class="card-header">
                    <h3 style="color: var(--neon-cyan); margin: 0; font-size: 1rem;">Transaction Summary</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <strong>Item:</strong> <span id="summaryItem">-</span><br>
                            <strong>Current Stock:</strong> <span id="summaryStock">-</span><br>
                            <strong>Transaction Type:</strong> <span id="summaryType">-</span>
                        </div>
                        <div>
                            <strong>Quantity:</strong> <span id="summaryQuantity">-</span><br>
                            <strong>Unit Price:</strong> <span id="summaryPrice">-</span><br>
                            <strong>New Stock Level:</strong> <span id="summaryNewStock">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="button" class="btn btn-primary" id="submitBtn" onclick="confirmTransactionSubmit()">Process Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventorySelect = document.getElementById('inventorySelect');
    const transactionType = document.getElementById('transactionType');
    const quantityInput = document.getElementById('quantityInput');
    const unitPriceInput = document.getElementById('unitPriceInput');
    const totalAmount = document.getElementById('totalAmount');
    const stockWarning = document.getElementById('stockWarning');
    const submitBtn = document.getElementById('submitBtn');

    // Summary elements
    const summaryItem = document.getElementById('summaryItem');
    const summaryStock = document.getElementById('summaryStock');
    const summaryType = document.getElementById('summaryType');
    const summaryQuantity = document.getElementById('summaryQuantity');
    const summaryPrice = document.getElementById('summaryPrice');
    const summaryNewStock = document.getElementById('summaryNewStock');

    let selectedItem = null;

    // Update item details when inventory is selected
    inventorySelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            selectedItem = {
                id: option.value,
                code: option.dataset.code,
                name: option.dataset.name,
                price: parseFloat(option.dataset.price),
                stock: parseInt(option.dataset.stock)
            };
            
            unitPriceInput.value = selectedItem.price;
            summaryItem.textContent = selectedItem.code + ' - ' + selectedItem.name;
            summaryStock.textContent = selectedItem.stock;
        } else {
            selectedItem = null;
            unitPriceInput.value = '';
            summaryItem.textContent = '-';
            summaryStock.textContent = '-';
        }
        updateSummary();
    });

    // Update summary when form values change
    function updateSummary() {
        const type = transactionType.value;
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(unitPriceInput.value) || 0;
        
        summaryType.textContent = type ? type.charAt(0).toUpperCase() + type.slice(1) : '-';
        summaryQuantity.textContent = quantity || '-';
        summaryPrice.textContent = price ? '₱' + price.toLocaleString('en-US', {minimumFractionDigits: 2}) : '-';
        
        // Calculate total
        const total = quantity * price;
        totalAmount.value = total ? '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2}) : '';
        
        // Calculate new stock level
        if (selectedItem && quantity && type) {
            let newStock;
            if (type === 'inbound') {
                newStock = selectedItem.stock + quantity;
            } else {
                newStock = selectedItem.stock - quantity;
            }
            summaryNewStock.textContent = newStock;
            summaryNewStock.style.color = newStock < 0 ? 'var(--danger)' : 
                                        newStock <= (selectedItem.stock * 0.2) ? 'var(--warning)' : 
                                        'var(--success)';
        } else {
            summaryNewStock.textContent = '-';
        }
        
        // Validate outbound transactions
        validateTransaction();
    }

    function validateTransaction() {
        const type = transactionType.value;
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (selectedItem && type === 'outbound' && quantity > selectedItem.stock) {
            stockWarning.style.display = 'block';
            stockWarning.textContent = `Only ${selectedItem.stock} units available in stock`;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Insufficient Stock';
        } else {
            stockWarning.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Process Transaction';
        }
    }

    // Event listeners
    transactionType.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    unitPriceInput.addEventListener('input', updateSummary);

    // Global function for SweetAlert2 confirmation
    window.confirmTransactionSubmit = function() {
        if (submitBtn.disabled) {
            showError('Invalid Transaction', 'Cannot process transaction: Insufficient stock');
            return false;
        }

        if (!selectedItem || !transactionType.value || !quantityInput.value || !unitPriceInput.value) {
            showError('Incomplete Form', 'Please fill in all required fields');
            return false;
        }

        const type = transactionType.value;
        const quantity = parseInt(quantityInput.value);
        const price = parseFloat(unitPriceInput.value);
        const total = quantity * price;
        let newStock;
        
        if (type === 'inbound') {
            newStock = selectedItem.stock + quantity;
        } else {
            newStock = selectedItem.stock - quantity;
        }

        const transactionData = {
            itemName: selectedItem.code + ' - ' + selectedItem.name,
            transactionType: type.charAt(0).toUpperCase() + type.slice(1),
            quantity: quantity.toLocaleString(),
            unitPrice: price.toLocaleString('en-US', {minimumFractionDigits: 2}),
            totalAmount: total.toLocaleString('en-US', {minimumFractionDigits: 2}),
            currentStock: selectedItem.stock,
            newStock: newStock
        };

        confirmTransaction(transactionData, function() {
            submitBtn.innerHTML = '<div class="loading" style="width: 16px; height: 16px;"></div> Processing...';
            submitBtn.disabled = true;
            document.getElementById('transactionForm').submit();
        });
    };
});
</script>

<style>
.loading {
    border: 2px solid var(--border-glow);
    border-radius: 50%;
    border-top: 2px solid var(--neon-cyan);
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection