<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome');
    }
}
