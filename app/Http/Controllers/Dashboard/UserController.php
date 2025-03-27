<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
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
        $users = User::all();
        return inertia('users/Index', ['users' => $users]);
    }

    public function create()
    {
        $roles = Role::all();
        return inertia('users/Create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'roles' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return inertia('users/Edit', ['currentUser' => $user, 'roles' => $roles]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
