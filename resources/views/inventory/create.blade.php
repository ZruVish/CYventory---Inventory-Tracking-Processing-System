@extends('layouts.app')

@section('title', 'Add New Item - Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add New Inventory Item</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Enter the details for the new cyberpunk item</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Item Information</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('inventory.store') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Item Code *</label>
                <input type="text" name="item_code" class="form-control" 
                       placeholder="Enter unique item code (e.g., QP-2024-001)" 
                       value="{{ old('item_code') }}" required>
                @error('item_code')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Item Name *</label>
                <input type="text" name="item_name" class="form-control" 
                       placeholder="Enter item name" 
                       value="{{ old('item_name') }}" required>
                @error('item_name')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="">Select category</option>
                    <option value="Processors" {{ old('category') == 'Processors' ? 'selected' : '' }}>Processors</option>
                    <option value="Interfaces" {{ old('category') == 'Interfaces' ? 'selected' : '' }}>Interfaces</option>
                    <option value="Displays" {{ old('category') == 'Displays' ? 'selected' : '' }}>Displays</option>
                    <option value="Memory" {{ old('category') == 'Memory' ? 'selected' : '' }}>Memory</option>
                    <option value="Storage" {{ old('category') == 'Storage' ? 'selected' : '' }}>Storage</option>
                    <option value="Weapons" {{ old('category') == 'Weapons' ? 'selected' : '' }}>Weapons</option>
                    <option value="Cybernetics" {{ old('category') == 'Cybernetics' ? 'selected' : '' }}>Cybernetics</option>
                </select>
                @error('category')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" 
                          placeholder="Enter item description">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Initial Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="0" 
                           value="{{ old('quantity', 0) }}" required>
                    @error('quantity')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price (₱) *</label>
                    <input type="number" name="unit_price" class="form-control" step="0.01" min="0" 
                           value="{{ old('unit_price') }}" required>
                    @error('unit_price')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Level *</label>
                    <input type="number" name="reorder_level" class="form-control" min="0" 
                           value="{{ old('reorder_level', 5) }}" required>
                    @error('reorder_level')
                        <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
