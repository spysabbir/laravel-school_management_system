<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'Admin';

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
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $user->update($validated);
        $user->roles()->sync($request->role_ids);

        return back()->with('success', 'User updated successfully.');
    }
}
