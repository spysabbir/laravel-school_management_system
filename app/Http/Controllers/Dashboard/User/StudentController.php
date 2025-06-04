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
            'birth_reg_no' => 'required|string|max:50|unique:students,birth_reg_no',
            'registration_no' => 'required|string|max:50|unique:students,registration_no',
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_relation' => 'required|string|max:50',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'required|string|email|max:255',
            'guardian_address' => 'required|string|max:255',
            'present_address' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'status' => 'required|in:Running,Graduated,Transferred,Dropped Out,Suspended, Expelled',
            'graduation_date' => 'required_if:status,Graduated|date|nullable',
            'transfer_date' => 'required_if:status,Transferred|date|nullable',
            'dropout_date' => 'required_if:status,Dropped Out|date|nullable',
            'suspension_date' => 'required_if:status,Suspended|date|nullable',
            'expulsion_date' => 'required_if:status,Expelled|date|nullable',
            'transfer_reason' => 'required_if:status,Transferred|string|nullable',
            'suspension_reason' => 'required_if:status,Suspended|string|nullable',
            'expulsion_reason' => 'required_if:status,Expelled|string|nullable',
            'student_type' => 'required|in:Regular,Irregular',
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'required|exists:groups,id',
            'shift_id' => 'required|exists:shifts,id',
            'roll_number' => 'required|string|max:50',
            'academic_year' => 'required|digits:4',
            'type' => 'required|in:New,Transfer,Re Admission',
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

        $student = Student::create([
            'user_id' => $user->id,
            'birth_reg_no' => $request->birth_reg_no,
            'registration_no' => $request->registration_no,
            'status' => $request->status,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'guardian_name' => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'guardian_address' => $request->guardian_address,
            'graduation_date' => $request->graduation_date,
            'transfer_date' => $request->transfer_date,
            'dropout_date' => $request->dropout_date,
            'suspension_date' => $request->suspension_date,
            'expulsion_date' => $request->expulsion_date,
            'transfer_reason' => $request->transfer_reason,
            'suspension_reason' => $request->suspension_reason,
            'expulsion_reason' => $request->expulsion_reason,
            'created_by' => auth()->id(),
        ]);

        $roll_number = $student->id . '-' . $request->academic_year;
        Admission::create([
            'student_id' => $student->id,
            'student_type' => $request->student_type,
            'class_id' => $request->class_id,
            'group_id' => $request->group_id,
            'shift_id' => $request->shift_id,
            'roll_number' => $roll_number,
            'academic_year' => $request->academic_year,
            'type' => $request->admission_type,
            'created_by' => auth()->id(),
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
            'birth_reg_no' => 'required|string|max:50|unique:students,birth_reg_no,' . $user->student->id,
            'registration_no' => 'required|string|max:50|unique:students,registration_no,' . $user->student->id,
            'religion' => 'required|in:Islam,Christianity,Hinduism,Buddhism,Other',
            'phone' => 'required|string|max:15',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_relation' => 'required|string|max:50',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'required|string|email|max:255',
            'guardian_address' => 'required|string|max:255',
            'present_address' => 'required|string|max:255',
            'status' => 'required|in:Running,Graduated,Transferred,Dropped Out,Suspended, Expelled',
            'graduation_date' => 'required_if:status,Graduated|date|nullable',
            'transfer_date' => 'required_if:status,Transferred|date|nullable',
            'dropout_date' => 'required_if:status,Dropped Out|date|nullable',
            'suspension_date' => 'required_if:status,Suspended|date|nullable',
            'expulsion_date' => 'required_if:status,Expelled|date|nullable',
            'transfer_reason' => 'required_if:status,Transferred|string|nullable',
            'suspension_reason' => 'required_if:status,Suspended|string|nullable',
            'expulsion_reason' => 'required_if:status,Expelled|string|nullable',
            'student_type' => 'required|in:Regular,Irregular',
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'required|exists:groups,id',
            'shift_id' => 'required|exists:shifts,id',
            'roll_number' => 'required|string|max:50',
            'academic_year' => 'required|digits:4',
            'type' => 'required|in:New,Transfer,Re Admission',
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

        $student = Student::where('user_id', $user->id)->firstOrFail();
        $student->update([
            'user_id' => $user->id,
            'birth_reg_no' => $request->birth_reg_no,
            'registration_no' => $request->registration_no,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'guardian_name' => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'guardian_address' => $request->guardian_address,
            'status' => $request->status,
            'graduation_date' => $request->graduation_date,
            'transfer_date' => $request->transfer_date,
            'dropout_date' => $request->dropout_date,
            'suspension_date' => $request->suspension_date,
            'expulsion_date' => $request->expulsion_date,
            'transfer_reason' => $request->transfer_reason,
            'suspension_reason' => $request->suspension_reason,
            'expulsion_reason' => $request->expulsion_reason,
            'updated_by' => auth()->id(),
        ]);

        $admission = Admission::where('student_id', $user->id)->first();
        $admission->update([
            'student_type' => $request->student_type,
            'class_id' => $request->class_id,
            'group_id' => $request->group_id,
            'shift_id' => $request->shift_id,
            'academic_year' => $request->academic_year,
            'type' => $request->type,
            'updated_by' => auth()->id(),
        ]);

        $user->roles()->sync(Role::whereIn('name', ['Student'])->pluck('id')->toArray());

        return back()->with('success', 'User updated successfully.');
    }
}
