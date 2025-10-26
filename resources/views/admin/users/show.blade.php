@extends('layouts.app')

@section('title', 'User Details - Admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">User Details</h1>
    <p style="color: var(--text-secondary); font-size: 1.1rem;">View user account information</p>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ $user->name }}</h2>
        <div>
            <span class="badge badge-{{ $user->role }}">
                {{ ucfirst($user->role) }}
            </span>
            <span class="badge badge-{{ $user->is_active ? 'active' : 'inactive' }}">
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div class="form-group">
                    <label class="form-label">User ID</label>
                    <div style="color: var(--neon-cyan); font-weight: bold;">#{{ $user->id }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div style="font-weight: bold;">{{ $user->name }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div>{{ $user->email }}</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">User Role</label>
                    <div>
                        <span class="badge badge-{{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Account Status</label>
                    <div>
                        <span class="badge badge-{{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Verified</label>
                    <div style="color: {{ $user->email_verified_at ? 'var(--success)' : 'var(--warning)' }};">
                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        @if($user->email_verified_at)
                            <br><small style="color: var(--text-muted);">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Member Since</label>
                    <div>{{ $user->created_at->format('M d, Y') }}</div>
                    <small style="color: var(--text-muted);">{{ $user->created_at->diffForHumans() }}</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Last Updated</label>
                    <div>{{ $user->updated_at->format('M d, Y H:i') }}</div>
                    <small style="color: var(--text-muted);">{{ $user->updated_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
        
        <!-- Role Permissions Display -->
        <div class="card" style="background: rgba(138, 43, 226, 0.05); border-color: var(--neon-purple); margin-top: 2rem;">
            <div class="card-header">
                <h3 style="color: var(--neon-purple); margin: 0; font-size: 1rem;">Role Permissions</h3>
            </div>
            <div class="card-body">
                @if($user->role === 'admin')
                    <div style="color: var(--neon-purple);">
                        <strong>Administrator Privileges:</strong><br>
                        • Manage all users (create, edit, delete)<br>
                        • Full inventory control (all operations)<br>
                        • Complete transaction access<br>
                        • System configuration access<br>
                        • Access to all reports and analytics
                    </div>
                @elseif($user->role === 'manager')
                    <div style="color: var(--neon-cyan);">
                        <strong>Manager Privileges:</strong><br>
                        • Add and edit inventory items<br>
                        • Process all transactions<br>
                        • View all reports and analytics<br>
                        • Cannot manage users<br>
                        • Cannot delete inventory items
                    </div>
                @else
                    <div style="color: var(--text-muted);">
                        <strong>Staff Privileges:</strong><br>
                        • View inventory (read-only)<br>
                        • View transactions (read-only)<br>
                        • Basic dashboard access<br>
                        • Cannot create, edit, or delete<br>
                        • Limited to viewing operations only
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; flex-wrap: wrap;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
            
            @if($user->id !== auth()->id())
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
                
                @if($user->is_active)
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <input type="hidden" name="is_active" value="0">
                        <button type="submit" class="btn btn-warning" 
                                onclick="return confirm('Are you sure you want to deactivate this user?')">
                            Deactivate Account
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('Are you sure you want to activate this user?')">
                            Activate Account
                        </button>
                    </form>
                @endif
                
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">
                        Delete User
                    </button>
                </form>
            @else
                <div class="alert" style="background: rgba(255, 165, 0, 0.1); border-color: var(--warning); color: var(--warning); padding: 1rem; border-radius: 4px; margin-left: 1rem;">
                    <strong>Note:</strong> You are viewing your own account. Some actions are restricted.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Account Activity Summary -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h2 class="card-title">Account Summary</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="stat-card" style="background: rgba(0, 255, 255, 0.1); border: 1px solid var(--neon-cyan); padding: 1rem; border-radius: 4px; text-align: center;">
                <div style="color: var(--neon-cyan); font-size: 0.9rem;">Account Age</div>
                <div style="color: var(--neon-cyan); font-size: 1.5rem; font-weight: bold;">{{ $user->created_at->diffInDays(now()) }} days</div>
            </div>
            
            <div class="stat-card" style="background: rgba(138, 43, 226, 0.1); border: 1px solid var(--neon-purple); padding: 1rem; border-radius: 4px; text-align: center;">
                <div style="color: var(--neon-purple); font-size: 0.9rem;">User Role</div>
                <div style="color: var(--neon-purple); font-size: 1.5rem; font-weight: bold;">{{ ucfirst($user->role) }}</div>
            </div>
            
            <div class="stat-card" style="background: rgba({{ $user->is_active ? '0, 255, 136' : '255, 68, 68' }}, 0.1); border: 1px solid var(--{{ $user->is_active ? 'success' : 'danger' }}); padding: 1rem; border-radius: 4px; text-align: center;">
                <div style="color: var(--{{ $user->is_active ? 'success' : 'danger' }}); font-size: 0.9rem;">Status</div>
                <div style="color: var(--{{ $user->is_active ? 'success' : 'danger' }}); font-size: 1.5rem; font-weight: bold;">{{ $user->is_active ? 'Active' : 'Inactive' }}</div>
            </div>
        </div>
    </div>
</div>

<style>
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

.badge-active {
    background: var(--success);
    color: var(--dark-bg);
}

.badge-inactive {
    background: var(--danger);
    color: var(--text-primary);
}
</style>
@endsection