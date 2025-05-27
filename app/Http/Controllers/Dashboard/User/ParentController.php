<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class ParentController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('type', 'Parent')->get();
        return Inertia::render('dashboard/user/parent/Index', [
            'users' => $users,
        ]);
    }
}
