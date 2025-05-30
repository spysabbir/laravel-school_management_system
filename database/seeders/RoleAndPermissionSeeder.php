<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles and permissions

        // Roles
        $roles = [
            'Super Admin',
            'Admin',
            'Student',
            'Parent',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Permissions
        $permissions = [
            'Roles And Permissions Management' => [
                'Read Roles And Permissions', 'Create Roles', 'Delete Roles', 'Assign Roles And Permissions', 'Revoke Roles And Permissions'
            ],
        ];

        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::create(['name' => $perm, 'group_name' => $group]);
            }
        }

        // Assign permissions to roles
        $assign = [
            'Admin' => [
                'Roles And Permissions Management' => ['Read Roles And Permissions'],
            ],
        ];

        foreach ($assign as $roleName => $groups) {
            $role = Role::findByName($roleName);
            foreach ($groups as $group => $perms) {
                foreach ($perms as $perm) {
                    $permission = Permission::where('name', $perm)->first();
                    if ($permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
