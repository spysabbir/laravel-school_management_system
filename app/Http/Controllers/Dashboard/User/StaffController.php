<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Staff')->get();
        return Inertia::render('dashboard/user/staff/Index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'Staff';

        $user = User::create($validated);
        $user->roles()->sync($request->role_ids);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $user->update($validated);
        $user->roles()->sync($request->role_ids);

        return back()->with('success', 'User updated successfully.');
    }
}
