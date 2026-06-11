<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@loms.com'],
            [
                'name'      => 'Admin User',
                'email'     => 'admin@loms.com',
                'password'  => Hash::make('Admin@LOMS2024!'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@loms.com'],
            [
                'name'      => 'Staff User',
                'email'     => 'staff@loms.com',
                'password'  => Hash::make('Staff@LOMS2024!'),
                'role'      => 'staff',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'delivery@loms.com'],
            [
                'name'      => 'Delivery Agent',
                'email'     => 'delivery@loms.com',
                'password'  => Hash::make('Delivery@LOMS2024!'),
                'role'      => 'delivery',
                'is_active' => true,
            ]
        );
    }
}
