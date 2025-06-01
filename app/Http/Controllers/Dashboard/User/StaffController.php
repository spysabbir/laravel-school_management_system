<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Designation;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'staff'])->where('type', 'Staff')->get();
        return Inertia::render('dashboard/user/staff/Index', [
            'users' => $users,
            'roles' => Role::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $userData = $validated;
        $userData['password'] = Hash::make($validated['password']);
        $userData['type'] = 'Staff';

        unset($userData['designation_id']);

        $user = User::create($userData);

        $user->roles()->sync($request->role_ids);

        $user->staff()->create([
            'designation_id' => $validated['designation_id'],
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $userData = $validated;
        unset($userData['designation_id']);

        $user->update($userData);

        $user->roles()->sync($request->role_ids);

        $user->staff()->updateOrCreate(
            ['user_id' => $user->id],
            ['designation_id' => $validated['designation_id']]
        );

        return back()->with('success', 'User updated successfully.');
    }
}
