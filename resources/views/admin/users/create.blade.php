@extends('layouts.app')

@section('title', 'Add User - Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add New User</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Create a new system user account</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">User Information</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" 
                       placeholder="Enter full name" 
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" 
                       placeholder="Enter email address" 
                       value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Enter password (min 8 chars)" required>
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm password" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                <div class="form-group">
                    <label class="form-label">User Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="">Select user role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            Administrator (Full Access)
                        </option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>
                            Manager (Inventory Management)
                        </option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>
                            Staff (Read Only)
                        </option>
                    </select>
                    @error('role')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin: 0; display: flex; align-items: center;">
                    <label class="checkbox-container" style="margin: 0;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span class="checkmark"></span>
                        <span class="checkbox-label">Active Account</span>
                    </label>
                </div>
            </div>

            <div class="card" style="background: rgba(138, 43, 226, 0.05); border-color: var(--neon-purple); margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="color: var(--neon-purple); margin: 0; font-size: 1rem;">Role Permissions</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                        <div>
                            <strong style="color: var(--neon-purple);">Administrator:</strong><br>
                            • Manage all users<br>
                            • Full inventory control<br>
                            • All transaction access<br>
                            • System configuration
                        </div>
                        <div>
                            <strong style="color: var(--neon-cyan);">Manager:</strong><br>
                            • Add/edit inventory<br>
                            • Process transactions<br>
                            • View all reports<br>
                            • No user management
                        </div>
                        <div>
                            <strong style="color: var(--text-muted);">Staff:</strong><br>
                            • View inventory only<br>
                            • View transactions<br>
                            • Basic dashboard access<br>
                            • No modifications
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="button" class="btn btn-primary" onclick="confirmUserCreate()">Create User</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmUserCreate() {
    const form = document.querySelector('form[action="{{ route('admin.users.store') }}"]');
    const userName = form.querySelector('input[name="name"]').value;
    const userEmail = form.querySelector('input[name="email"]').value;
    const userRole = form.querySelector('select[name="role"]').value;
    const isActive = form.querySelector('input[name="is_active"]').checked ? 'Active' : 'Inactive';

    if (!userName || !userEmail || !userRole) {
        showError('Incomplete Form', 'Please fill in all required fields');
        return;
    }

    const message = `
        <div style="text-align: left;">
            <p><strong>User Details:</strong></p>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li><strong>Name:</strong> ${userName}</li>
                <li><strong>Email:</strong> ${userEmail}</li>
                <li><strong>Role:</strong> ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}</li>
                <li><strong>Status:</strong> ${isActive}</li>
            </ul>
            <p style="color: var(--warning); margin-top: 1rem;">
                ${userRole === 'admin' ? '⚠️ This user will have full system access.' : ''}
            </p>
        </div>
    `;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'question',
            title: 'Create New User',
            html: message,
            ...cyberpunkTheme,
            showCancelButton: true,
            confirmButtonText: 'Yes, Create It!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    const preview = { name: userName, email: userEmail, role: userRole, is_active: isActive };
                    sessionStorage.setItem('newUserPreview', JSON.stringify(preview));
                } catch (e) { /* ignore */ }
                form.submit();
            }
        });
    } else {
        if (window.confirm('Create this new user?')) form.submit();
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('admin.users.store') }}"]');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        // Intercept default submit to show confirmation first
        e.preventDefault();
        if (typeof confirmUserCreate === 'function') {
            confirmUserCreate();
        } else {
            form.submit();
        }
    });
});
</script>

<style>
.error-text {
    color: var(--danger);
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    color: var(--text-secondary);
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 1px solid var(--border-glow);
    border-radius: 3px;
    background: var(--dark-surface);
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark {
    background: var(--neon-cyan);
    border-color: var(--neon-cyan);
}

.checkbox-container input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--dark-bg);
    font-size: 12px;
    font-weight: bold;
}
</style>
@endsection