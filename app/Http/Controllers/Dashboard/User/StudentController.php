<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Student')->get();
        return Inertia::render('dashboard/user/student/Index', [
            'users' => $users,
        ]);
    }
}
