<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@spysabbir.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole(Role::findByName('Super Admin'));

        // Create Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@spysabbir.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole(Role::findByName('Admin'));

        // Create Teacher
        $teacher = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@spysabbir.com',
            'password' => bcrypt('password'),
        ]);
        $teacher->assignRole(Role::findByName('Teacher'));

        // Create Student
        $student = User::create([
            'name' => 'Student',
            'email' => 'student@spysabbir.com',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole(Role::findByName('Student'));

        // Create Parent
        $parent = User::create([
            'name' => 'Parent',
            'email' => 'parent@spysabbir.com',
            'password' => bcrypt('password'),
        ]);
        $parent->assignRole(Role::findByName('Parent'));
    }
}
