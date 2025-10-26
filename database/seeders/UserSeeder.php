<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create default admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cyventory.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample manager
        User::create([
            'name' => 'Inventory Manager',
            'email' => 'manager@cyventory.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample staff
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@cyventory.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}