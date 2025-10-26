@extends('layouts.app')

@section('title', 'Edit Item - Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Inventory Item</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Update cyberpunk item details</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Item Information</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('inventory.update', $inventory) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Item Code *</label>
                <input type="text" name="item_code" class="form-control" 
                       placeholder="Enter unique item code (e.g., QP-2024-001)" 
                       value="{{ old('item_code', $inventory->item_code) }}" required>
                @error('item_code')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Item Name *</label>
                <input type="text" name="item_name" class="form-control" 
                       placeholder="Enter item name" 
                       value="{{ old('item_name', $inventory->item_name) }}" required>
                @error('item_name')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="">Select category</option>
                    <option value="Processors" {{ old('category', $inventory->category) == 'Processors' ? 'selected' : '' }}>Processors</option>
                    <option value="Interfaces" {{ old('category', $inventory->category) == 'Interfaces' ? 'selected' : '' }}>Interfaces</option>
                    <option value="Displays" {{ old('category', $inventory->category) == 'Displays' ? 'selected' : '' }}>Displays</option>
                    <option value="Memory" {{ old('category', $inventory->category) == 'Memory' ? 'selected' : '' }}>Memory</option>
                    <option value="Storage" {{ old('category', $inventory->category) == 'Storage' ? 'selected' : '' }}>Storage</option>
                    <option value="Weapons" {{ old('category', $inventory->category) == 'Weapons' ? 'selected' : '' }}>Weapons</option>
                    <option value="Cybernetics" {{ old('category', $inventory->category) == 'Cybernetics' ? 'selected' : '' }}>Cybernetics</option>
                </select>
                @error('category')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" 
                          placeholder="Enter item description">{{ old('description', $inventory->description) }}</textarea>
                @error('description')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Current Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="0" 
                           value="{{ old('quantity', $inventory->quantity) }}" required>
                    <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                        Previous: {{ $inventory->quantity }}
                    </div>
                    @error('quantity')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price (₱) *</label>
                    <input type="number" name="unit_price" class="form-control" step="0.01" min="0" 
                           value="{{ old('unit_price', $inventory->unit_price) }}" required>
                    <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                        Previous: ₱{{ number_format($inventory->unit_price, 2) }}
                    </div>
                    @error('unit_price')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Level *</label>
                    <input type="number" name="reorder_level" class="form-control" min="0" 
                           value="{{ old('reorder_level', $inventory->reorder_level) }}" required>
                    <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                        Previous: {{ $inventory->reorder_level }}
                    </div>
                    @error('reorder_level')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $inventory->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $inventory->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Change Summary Card -->
            <div class="card" style="background: rgba(255, 165, 0, 0.05); border-color: var(--warning); margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="color: var(--warning); margin: 0; font-size: 1rem;">Change Summary</h3>
                </div>
                <div class="card-body">
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">
                        <strong>Item:</strong> {{ $inventory->item_code }} - {{ $inventory->item_name }}<br>
                        <strong>Last Updated:</strong> {{ $inventory->updated_at->format('M d, Y H:i:s') }}<br>
                        <strong>Current Total Value:</strong> ₱{{ number_format($inventory->total_value, 2) }}
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-secondary">Cancel</a>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to List</a>
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </form>
    </div>
</div>
@endsection