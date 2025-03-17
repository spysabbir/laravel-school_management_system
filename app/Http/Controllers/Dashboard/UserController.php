<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('index', only: ['index']),
            new Middleware('index', only: ['index']),
        ];
    }

    public function users()
    {
        $users = User::all();
        return inertia('users/Index', ['users' => $users]);
    }
}
