<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // new Middleware('index', only: ['index']),
            // new Middleware('index', only: ['index']),
        ];
    }


    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return Inertia::render('roles-and-permissions/Index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function roleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles'
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles-and-permissions')->with('success', 'Role created successfully.');
    }

    public function roleDestroy($id)
    {
        $role = Role::findById($id);
        $role->delete();
        return redirect()->route('roles-and-permissions')->with('success', 'Role deleted successfully.');
    }

    public function assign($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::all();
        return Inertia::render('roles-and-permissions/Assign', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function assignPermission(Request $request, $id)
    {
        $role = Role::findById($id);
        $role->givePermissionTo($request->permissions);
        return redirect()->route('roles-and-permissions')->with('success', 'Permissions assigned successfully.');
    }

    public function revoke($id)
    {
        $role = Role::findById($id);
        $role->revokePermissionTo(Permission::all());
        return redirect()->route('roles-and-permissions')->with('success', 'Permissions revoked successfully.');
    }
}
