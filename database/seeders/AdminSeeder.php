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
                'name'     => 'Admin User',
                'email'    => 'admin@loms.com',
                'password' => Hash::make('Admin@LOMS2024!'),
                'role'     => 'admin',
                'is_active'=> true,
            ]
        );
    }
}
