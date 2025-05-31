<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@email.com',
            'gender' => 'Male',
            'date_of_birth' => '2025-05-11',
            'religion' => 'Islam',
            'phone' => '1234567890',
            'present_address' => '123 Main St, City, Country',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'type' => 'Admin',
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create an admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'gender' => 'Male',
            'date_of_birth' => '2025-05-11',
            'religion' => 'Islam',
            'phone' => '1234567890',
            'present_address' => '123 Main St, City, Country',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'type' => 'Admin',
        ]);
        $admin->assignRole('Admin');
    }
}
