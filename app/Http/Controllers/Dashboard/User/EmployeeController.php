<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Designation;
use App\Models\Employee;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'employee'])->where('type', 'Employee')->get();
        return Inertia::render('dashboard/user/employee/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('dashboard/user/employee/Create', [
            'roles' => Role::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'national_id_no' => 'required|string|max:20|unique:employees,national_id_no',
            'joining_date' => 'required|date',
            'type' => 'required|in:Full Time,Part Time,Contract',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Running,Resigned,Retired,Suspended',
            'resignation_date' => 'required_if:status,Resigned|date|nullable',
            'retirement_date' => 'required_if:status,Retired|date|nullable',
            'suspension_date' => 'required_if:status,Suspended|date|nullable',
            'resignation_reason' => 'required_if:status,Resigned|string|nullable',
            'suspension_reason' => 'required_if:status,Suspended|string|nullable',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'phone' => $request->phone,
            'present_address' => $request->present_address,
            'password' => Hash::make($request->password),
            'type' => 'Employee',
        ]);

        Employee::create([
            'user_id' => $user->id,
            'designation_id' => $request->designation_id,
            'national_id_no' => $request->national_id_no,
            'joining_date' => $request->joining_date,
            'type' => $request->type,
            'status' => $request->status,
            'resignation_date' => $request->resignation_date,
            'retirement_date' => $request->retirement_date,
            'suspension_date' => $request->suspension_date,
            'resignation_reason' => $request->resignation_reason,
            'suspension_reason' => $request->suspension_reason,
            'created_by' => auth()->id(),
        ]);

        $user->roles()->sync($request->role_ids);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit($id)
    {
        $user = User::with(['roles', 'employee'])->findOrFail($id);
        return Inertia::render('dashboard/user/employee/Edit', [
            'user' => $user,
            'roles' => Role::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'national_id_no' => 'required|string|max:20|unique:employees,national_id_no,' . $user->employee->id,
            'joining_date' => 'required|date',
            'type' => 'required|in:Full Time,Part Time,Contract',
            'designation_id' => 'required|exists:designations,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'present_address' => 'required|string|max:255',
            'status' => 'required|in:Running,Resigned,Retired,Suspended',
            'resignation_date' => 'required_if:status,Resigned|date|nullable',
            'retirement_date' => 'required_if:status,Retired|date|nullable',
            'suspension_date' => 'required_if:status,Suspended|date|nullable',
            'resignation_reason' => 'required_if:status,Resigned|string|nullable',
            'suspension_reason' => 'required_if:status,Suspended|string|nullable',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'phone' => $request->phone,
            'present_address' => $request->present_address,
        ]);

        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        $employee->update([
            'designation_id' => $request->designation_id,
            'national_id_no' => $request->national_id_no,
            'joining_date' => $request->joining_date,
            'type' => $request->type,
            'status' => $request->status,
            'resignation_date' => $request->resignation_date,
            'retirement_date' => $request->retirement_date,
            'suspension_date' => $request->suspension_date,
            'resignation_reason' => $request->resignation_reason,
            'suspension_reason' => $request->suspension_reason,
            'updated_by' => auth()->id(),
        ]);

        $user->roles()->sync($request->role_ids);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }
}
