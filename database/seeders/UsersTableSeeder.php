<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = new User();
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'superadmin@spysabbir.com';
        $superAdmin->password = bcrypt('password');
        $superAdmin->save();

        $superAdminRole = Role::findByName('Super Admin');
        $superAdmin->assignRole($superAdminRole);

        // Admin
        $admin = new User();
        $admin->name = 'Admin';
        $admin->email = 'admin@spysabbir.com';
        $admin->password = bcrypt('password');
        $admin->save();

        $adminRole = Role::findByName('Admin');
        $admin->assignRole($adminRole);
    }
}
