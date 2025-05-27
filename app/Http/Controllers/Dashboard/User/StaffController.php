<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Staff')->get();
        return Inertia::render('dashboard/user/staff/Index', [
            'users' => $users,
        ]);
    }
}
