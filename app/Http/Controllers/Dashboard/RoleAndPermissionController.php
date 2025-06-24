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
        $roles = Role::with('permissions')->select('*')->get();

        return inertia('dashboard/role-permission/Index', compact('roles'));
    }

    public function createRoles()
    {
        return inertia('dashboard/role-permission/Create');
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
        $rolePermissions = $role->permissions()->get();

        return inertia('dashboard/role-permission/Assign', [
            'role' => $role,
            'permissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
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
