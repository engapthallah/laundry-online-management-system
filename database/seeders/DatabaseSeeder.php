<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@loms.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '1234567890',
                'address' => 'Admin HQ Office, City Center',
                'is_active' => true,
            ]
        );

        // Staff User
        User::updateOrCreate(
            ['email' => 'staff@loms.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '2345678901',
                'address' => 'LOMS Main Laundry Shop, Sector 4',
                'is_active' => true,
            ]
        );

        // Delivery User
        User::updateOrCreate(
            ['email' => 'delivery@loms.com'],
            [
                'name' => 'Delivery User',
                'password' => Hash::make('password'),
                'role' => 'delivery',
                'phone' => '3456789012',
                'address' => 'LOMS Dispatch Hub, Sector 9',
                'is_active' => true,
            ]
        );

        // Customer User
        User::updateOrCreate(
            ['email' => 'customer@loms.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '4567890123',
                'address' => '123 Customer Residence St, Suburbia',
                'is_active' => true,
            ]
        );
    }
}
