<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index()
    {
        $users = User::where('type', 'Parent')->get();
        return Inertia::render('dashboard/user/parent/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'Parent';

        $user = User::create($validated);
        $user->roles()->sync(Role::whereIn('name', ['Parent'])->pluck('id')->toArray());

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }
}
