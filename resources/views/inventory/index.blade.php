@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="page-header">
    <h1 class="page-title">Inventory Management</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Manage your inventory items</p>
</div>

<!-- Search and Filter -->
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.index') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Search Items</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, code, or category..." 
                           value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Inventory Items ({{ $inventories->total() }})</h2>
        @if(auth()->user()->canManageInventory())
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">+ Add New Item</a>
        @endif
    </div>
    <table class="cyber-table">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Value</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $item)
            <tr>
                <td>{{ $item->item_code }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category }}</td>
                <td>
                    {{ $item->quantity }}
                    @if($item->isLowStock())
                        <span class="badge badge-low-stock">Low</span>
                    @endif
                </td>
                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                <td>₱{{ number_format($item->total_value, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $item->status }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    <div class="action-buttons-fixed">
                        <a href="{{ route('inventory.show', $item) }}" class="btn-fixed btn-view">View</a>
                        @if(auth()->user()->canManageInventory())
                            <a href="{{ route('inventory.edit', $item) }}" class="btn-fixed btn-edit">Edit</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('inventory.destroy', $item) }}" id="delete-form-{{ $item->id }}" style="display: inline; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-fixed btn-delete" 
                                        onclick='confirmInventoryDelete(@json($item->item_name), "delete-form-{{ $item->id }}")'>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: var(--text-muted);">
                    No inventory items found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($inventories->hasPages())
<div style="text-align: center; margin-top: 2rem;">
    {{ $inventories->links() }}
</div>
@endif

<style>
/* Fixed size action buttons - overrides existing styles */
.action-buttons-fixed {
    display: flex !important;
    gap: 0.5rem !important;
    align-items: center !important;
    justify-content: flex-start !important;
    flex-wrap: nowrap !important;
}

.btn-fixed {
    /* Force consistent dimensions */
    width: 70px !important;
    height: 35px !important;
    min-width: 70px !important;
    min-height: 35px !important;
    
    /* Center content */
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    
    /* Reset margins and padding */
    margin: 0 !important;
    padding: 0 !important;
    
    /* Typography */
    font-family: 'Orbitron', monospace !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    text-decoration: none !important;
    
    /* Base styling */
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    
    /* Text centering */
    line-height: 1 !important;
    white-space: nowrap !important;
}

/* View Button Styling */
.btn-view {
    background: transparent !important;
    color: var(--text-secondary) !important;
    border: 1px solid var(--text-secondary) !important;
}

.btn-view:hover {
    color: var(--neon-cyan) !important;
    border-color: var(--neon-cyan) !important;
    box-shadow: 0 0 20px rgba(0, 255, 255, 0.3) !important;
    transform: translateY(-2px) !important;
}

/* Edit Button Styling */
.btn-edit {
    background: var(--success) !important;
    color: var(--dark-bg) !important;
    border: 1px solid var(--success) !important;
}

.btn-edit:hover {
    background: #38a169 !important;
    box-shadow: 0 0 20px rgba(0, 255, 136, 0.4) !important;
    transform: translateY(-2px) !important;
}

/* Delete Button Styling */
.btn-delete {
    background: var(--danger) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--danger) !important;
}

.btn-delete:hover {
    background: #c53030 !important;
    box-shadow: 0 0 20px rgba(255, 68, 68, 0.4) !important;
    transform: translateY(-2px) !important;
}

/* Active state */
.btn-fixed:active {
    transform: translateY(0) !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .action-buttons-fixed {
        flex-direction: column !important;
        gap: 0.25rem !important;
    }
    
    .btn-fixed {
        width: 60px !important;
        height: 30px !important;
        min-width: 60px !important;
        min-height: 30px !important;
        font-size: 0.6rem !important;
    }
}

/* Ensure table cell doesn't interfere */
.cyber-table td:last-child {
    padding: 0.5rem !important;
}
</style>
@endsection

@push('scripts')
<script>
function confirmInventoryDelete(itemName, formId) {
    // Fallback if SweetAlert2 is not available
    if (typeof Swal === 'undefined' || typeof confirmDelete === 'undefined') {
        const proceed = window.confirm(`Delete the inventory item: "${itemName}"? This action cannot be undone.`);
        if (proceed) {
            const form = document.getElementById(formId);
            if (form) form.submit();
        }
        return;
    }

    const message = `
        <div style="text-align: center;">
            <p>You are about to delete the inventory item:</p>
            <p style="color: var(--neon-cyan); font-weight: bold; margin: 1rem 0; font-size: 1.1rem;">${itemName}</p>
            <p style="color: var(--warning);">⚠️ This action cannot be undone!</p>
            <p style="font-size: 0.9rem; color: var(--text-muted);">All related transaction history will be preserved.</p>
        </div>
    `;
    
    confirmDelete('Delete Inventory Item', message, function() {
        const form = document.getElementById(formId);
        if (form) form.submit();
    });
}
</script>
@endpush