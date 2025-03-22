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

    public function roleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Role created successfully.');
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
}
