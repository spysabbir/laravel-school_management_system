<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = [
            'Super Admin',
            'Admin',
            'Teacher',
            'Staff',
            'Student',
            'Parent',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            'Roles And Permissions Management' => [
                'Read Roles And Permissions', 'Create Role', 'Delete Role', 'Assign Role Permission', 'Revoke Role Permission',
            ],
            'Users Management' => [
                'Read Users', 'Create User', 'Update User', 'Delete User',
            ],
        ];

        foreach ($permissions as $group_name => $permissionNames) {
            foreach ($permissionNames as $name) {
                Permission::firstOrCreate(['name' => $name, 'group_name' => $group_name]);
            }
        }

        // Assign permissions to roles
        $roleHasPermissions = [
            'Admin' => [
                'Roles And Permissions Management' => ['Read Roles And Permissions', 'Create Role', 'Delete Role', 'Assign Role Permission', 'Revoke Role Permission'],
                'Users Management' => ['Read Users', 'Create User', 'Update User', 'Delete User'],
            ],
        ];

        foreach ($roleHasPermissions as $roleName => $permissionGroups) {
            $role = Role::where('name', $roleName)->first();
            foreach ($permissionGroups as $group_name => $permissionNames) {
                foreach ($permissionNames as $name) {
                    $permission = Permission::where('name', $name)->first();
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
