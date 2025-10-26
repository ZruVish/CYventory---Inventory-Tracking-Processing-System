@extends('layouts.app')

@section('title', 'User Management - Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Management</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Manage system users and permissions</p>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">System Users ({{ $users->total() }})</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add New User</a>
    </div>
    <table class="cyber-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="badge badge-{{ $user->role }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $user->is_active ? 'active' : 'inactive' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-success">Edit</a>
                        @if($user->id !== auth()->id())
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmUserDelete('{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->id }}')">
                            Delete
                        </button>
                        @else
                        <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete your own account">
                            Delete
                        </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted);">
                    No users found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div style="text-align: center; margin-top: 2rem;">
    {{ $users->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
// On page load, show success and optionally highlight newly created user from sessionStorage
document.addEventListener('DOMContentLoaded', function() {
    try {
        const raw = sessionStorage.getItem('newUserPreview');
        if (raw) {
            const data = JSON.parse(raw);
            sessionStorage.removeItem('newUserPreview');
            if (typeof Swal !== 'undefined') {
                const html = `
                    <div style="text-align: left;">
                        <p><strong>Account created successfully.</strong></p>
                        <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                            <li><strong>Name:</strong> ${data.name || ''}</li>
                            <li><strong>Email:</strong> ${data.email || ''}</li>
                            <li><strong>Role:</strong> ${(data.role || '').toUpperCase()}</li>
                            <li><strong>Status:</strong> ${data.is_active || 'Inactive'}</li>
                        </ul>
                    </div>
                `;
                Swal.fire({
                    icon: 'success',
                    title: 'User Created',
                    html,
                    ...cyberpunkTheme,
                    confirmButtonText: 'OK'
                });
            }

            // Attempt to scroll to the new user row if present
            const rows = document.querySelectorAll('.cyber-table tbody tr');
            rows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(1)');
                const emailCell = row.querySelector('td:nth-child(2)');
                if (!nameCell || !emailCell) return;
                if (nameCell.textContent.trim() === (data.name || '').trim() &&
                    emailCell.textContent.trim() === (data.email || '').trim()) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.style.outline = '2px solid var(--neon-cyan)';
                    row.style.boxShadow = '0 0 20px rgba(0, 255, 255, 0.4)';
                    setTimeout(() => {
                        row.style.outline = '';
                        row.style.boxShadow = '';
                    }, 3000);
                }
            });
        }
    } catch (e) { /* ignore */ }
});
// Hidden forms for deletion
@foreach($users as $user)
@if($user->id !== auth()->id())
document.body.insertAdjacentHTML('beforeend', `
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="delete-user-form-{{ $user->id }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
`);
@endif
@endforeach

function confirmUserDelete(userName, userEmail, userRole, userId) {
    const roleColor = userRole === 'admin' ? 'var(--neon-purple)' : 
                     userRole === 'manager' ? 'var(--neon-cyan)' : 'var(--text-muted)';
    
    const message = `
        <div style="text-align: center;">
            <p>You are about to permanently delete this user account:</p>
            <div style="background: rgba(255, 68, 68, 0.1); border: 1px solid var(--danger); border-radius: 4px; padding: 1rem; margin: 1rem 0;">
                <p style="color: var(--neon-cyan); font-weight: bold; margin: 0.5rem 0;">${userName}</p>
                <p style="color: var(--text-secondary); margin: 0.5rem 0;">${userEmail}</p>
                <p style="color: ${roleColor}; font-weight: bold; text-transform: uppercase; margin: 0.5rem 0;">${userRole}</p>
            </div>
            <div style="color: var(--danger); margin: 1rem 0;">
                <p style="font-weight: bold;">⚠️ WARNING ⚠️</p>
                <p>This action is PERMANENT and cannot be undone!</p>
                <p style="font-size: 0.9rem;">The user will lose all access immediately.</p>
            </div>
        </div>
    `;
    
    confirmDelete('Delete User Account', message, function() {
        document.getElementById('delete-user-form-' + userId).submit();
    });
}
</script>
@endpush

<style>
.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    justify-content: flex-start;
}

.action-buttons .btn {
    margin: 0;
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

.badge-admin {
    background: var(--neon-purple);
    color: var(--text-primary);
}
.badge-manager {
    background: var(--neon-cyan);
    color: var(--dark-bg);
}
.badge-staff {
    background: var(--text-muted);
    color: var(--text-primary);
}
</style>