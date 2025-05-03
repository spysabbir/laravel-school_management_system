<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('dashboard/Dashboard');
    }

    public function appearance()
    {
        return Inertia::render('account-settings/Appearance');
    }
}
