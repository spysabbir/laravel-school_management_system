<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Teacher')->get();

        return Inertia::render('dashboard/user/teacher/Index', [
            'users' => $users,
        ]);
    }
}
