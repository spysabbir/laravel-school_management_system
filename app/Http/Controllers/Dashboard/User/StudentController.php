<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Classe;
use App\Models\Group;
use App\Models\Shift;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $users = User::where('type', 'Student')->get();
        return Inertia::render('dashboard/user/student/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $classes = Classe::all();
        $groups = Group::all();
        $shifts = Shift::all();
        return Inertia::render('dashboard/user/student/Create', [
            'classes' => $classes,
            'groups' => $groups,
            'shifts' => $shifts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'required|exists:groups,id',
            'shift_id' => 'required|exists:shifts,id',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_relation' => 'required|string|max:50',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'required|string|email|max:255',
            'guardian_address' => 'required|string|max:255',
            'present_address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Active,Inactive,Suspended',
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
            'type' => 'Student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'guardian_name' => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'guardian_address' => $request->guardian_address,
        ]);

        Admission::create([
            'student_id' => $user->id,
            'class_id' => $request->class_id,
            'group_id' => $request->group_id,
            'shift_id' => $request->shift_id,
        ]);

        $user->roles()->sync(Role::whereIn('name', ['Student'])->pluck('id')->toArray());

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'required|exists:groups,id',
            'shift_id' => 'required|exists:shifts,id',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_relation' => 'required|string|max:50',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'required|string|email|max:255',
            'guardian_address' => 'required|string|max:255',
            'present_address' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
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

        $user->student->update([
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'guardian_name' => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'guardian_address' => $request->guardian_address,
        ]);

        $admission = Admission::where('student_id', $user->id)->first();
        if ($admission) {
            $admission->update([
                'class_id' => $request->class_id,
                'group_id' => $request->group_id,
                'shift_id' => $request->shift_id,
            ]);
        } else {
            Admission::create([
                'student_id' => $user->id,
                'class_id' => $request->class_id,
                'group_id' => $request->group_id,
                'shift_id' => $request->shift_id,
            ]);
        }

        $user->roles()->sync(Role::whereIn('name', ['Student'])->pluck('id')->toArray());

        return back()->with('success', 'User updated successfully.');
    }
}
