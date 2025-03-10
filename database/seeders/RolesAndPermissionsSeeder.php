<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
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

        // Create Permissions
        $permissions = [
            'Role Management' => [
                'Create Role', 'Read Role', 'Update Role', 'Delete Role',
            ],
            'Permission Management' => [
                'Create Permission', 'Read Permission', 'Update Permission', 'Delete Permission',
            ],
            'Role Permission Management' => [
                'Assign Role Permission', 'Read Role Permission', 'Revoke Role Permission',
            ],
            'User Management' => [
                'Create User', 'Read User', 'Update User', 'Delete User',
            ],
        ];

        foreach ($permissions as $group => $permissionNames) {
            foreach ($permissionNames as $name) {
                Permission::firstOrCreate(['name' => $name, 'group' => $group]);
            }
        }

        // Assign Permissions to Roles
        $roleHasPermissions = [
            'Super Admin' => [
                'Role Management' => ['Create Role', 'Read Role', 'Update Role', 'Delete Role'],
                'Permission Management' => ['Create Permission', 'Read Permission', 'Update Permission', 'Delete Permission'],
                'Role Permission Management' => ['Assign Role Permission', 'Read Role Permission', 'Revoke Role Permission'],
                'User Management' => ['Create User', 'Read User', 'Update User', 'Delete User'],
            ],
            'Admin' => [
                'User Management' => ['Create User', 'Read User', 'Update User', 'Delete User'],
            ],
        ];

        foreach ($roleHasPermissions as $roleName => $permissionGroups) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                foreach ($permissionGroups as $group => $permissionNames) {
                    foreach ($permissionNames as $name) {
                        $permission = Permission::where('name', $name)->first();
                        if ($permission && !$role->hasPermissionTo($permission)) {
                            $role->givePermissionTo($permission);
                        }
                    }
                }
            }
        }
    }
}
