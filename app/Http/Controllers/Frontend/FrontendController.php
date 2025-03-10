<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function index()
    {
        return Inertia::render('frontend/Index');
    }

    public function about()
    {
        return Inertia::render('frontend/About');
    }

    public function contact()
    {
        return Inertia::render('frontend/Contact');
    }
}
