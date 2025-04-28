<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // 'auth',
            // new Middleware('log', only: ['index']),
            // new Middleware('subscribed', except: ['store']),
        ];

    }

    public function index()
    {
        $roles = Role::all();
        return inertia('dashboard/roles-permissions/Index', compact('roles'));
    }

    public function createRoles()
    {
        return inertia('dashboard/roles-permissions/Create');
    }

    public function storeRoles(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles.permissions')->with('success', 'Role created successfully.');
    }

    public function destroyRoles($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.permissions')->with('success', 'Role deleted successfully.');
    }

    public function assign($id)
    {
        $role = Role::findOrFail($id);
        $allPermissions = Permission::all();

        $assignedPermissionIds = $role->getAllPermissions()->pluck('id');

        return inertia('dashboard/roles-permissions/Assign', [
            'role' => $role,
            'permissions' => $allPermissions->toArray(),
            // 'assignedPermissions' => $assignedPermissionIds,
            'assignedPermissions' => $role->permissions->map(fn($permission) => ['id' => $permission->id]),
        ]);
    }

    public function assignStore(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->detach();
        $role->permissions()->attach($request->permission_ids);

        return redirect()->route('roles.permissions')->with('success', 'Permissions assigned successfully.');
    }

    public function revoke($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();

        return redirect()->route('roles.permissions')->with('success', 'Permissions revoked successfully.');
    }

}
