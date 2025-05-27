<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Admin')->get();
        return Inertia::render('dashboard/user/admin/Index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }
}
