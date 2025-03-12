<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsController extends Controller
{
    public function rolesPermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return Inertia::render('RolesAndPermissions', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
}
