<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function canManageInventory()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canManageUsers()
    {
        return $this->role === 'admin';
    }

    public function canCreateTransactions()
    {
        return $this->is_active && in_array($this->role, ['admin', 'manager']);
    }

    public function canViewTransactions()
    {
        return $this->is_active;
    }
}