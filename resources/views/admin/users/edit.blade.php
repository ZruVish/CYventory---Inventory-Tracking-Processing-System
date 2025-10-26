@extends('layouts.app')

@section('title', 'Edit User - Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit User</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">Modify user account details</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">User Information</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" id="userUpdateForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" 
                       placeholder="Enter full name" 
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" 
                       placeholder="Enter email address" 
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Enter new password">
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm new password">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                <div class="form-group">
                    <label class="form-label">User Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="">Select user role</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                            Administrator (Full Access)
                        </option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>
                            Manager (Inventory Management)
                        </option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>
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
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        <span class="checkbox-label">Active Account</span>
                    </label>
                </div>
            </div>

            <div class="card" style="background: rgba(255, 165, 0, 0.05); border-color: var(--warning); margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="color: var(--warning); margin: 0; font-size: 1rem;">Current User Info</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                        <div>
                            <strong>Current Role:</strong> {{ ucfirst($user->role) }}<br>
                            <strong>Account Status:</strong> {{ $user->is_active ? 'Active' : 'Inactive' }}<br>
                            <strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}
                        </div>
                        <div>
                            <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}<br>
                            <strong>Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}<br>
                            @if($user->id === auth()->id())
                                <span style="color: var(--warning);"><strong>⚠️ You are editing your own account</strong></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="button" class="btn btn-primary" onclick="confirmUserUpdate()">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmUserUpdate() {
    const form = document.getElementById('userUpdateForm');
    const userName = form.querySelector('input[name="name"]').value;
    const userEmail = form.querySelector('input[name="email"]').value;
    const userRole = form.querySelector('select[name="role"]').value;
    const isActive = form.querySelector('input[name="is_active"]:checked') ? 'Active' : 'Inactive';
    const hasNewPassword = form.querySelector('input[name="password"]').value.length > 0;
    
    if (!userName || !userEmail || !userRole) {
        showError('Incomplete Form', 'Please fill in all required fields');
        return;
    }
    
    let message = `
        <div style="text-align: left;">
            <p><strong>User Details:</strong></p>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li><strong>Name:</strong> ${userName}</li>
                <li><strong>Email:</strong> ${userEmail}</li>
                <li><strong>Role:</strong> ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}</li>
                <li><strong>Status:</strong> ${isActive}</li>
                ${hasNewPassword ? '<li><strong>Password:</strong> Will be updated</li>' : '<li><strong>Password:</strong> Will remain unchanged</li>'}
            </ul>
            <p style="color: var(--warning); margin-top: 1rem;">
                ${userRole === 'admin' ? '⚠️ This user will have full system access.' : ''}
                ${isActive === 'Inactive' ? '⚠️ This user will not be able to login.' : ''}
            </p>
        </div>
    `;
    
    confirmUpdate('Update User Account', message, function() {
        form.submit();
    });
}
</script>

<style>
.error-text {
    color: var(--danger);
    font-size: 0.8rem;
    margin-top: 0.5rem;
}
</style>
@endsection