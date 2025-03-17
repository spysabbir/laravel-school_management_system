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
            new Middleware('index', only: ['index']),
            new Middleware('index', only: ['index']),
        ];
    }

    public function rolesAndPermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return Inertia::render('role-and-permission/Index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
}
