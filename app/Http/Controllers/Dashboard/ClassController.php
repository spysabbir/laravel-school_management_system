<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Classes;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::all();
        return Inertia::render('dashboard/class/Index', [
            'classes' => $classes,
        ]);
    }

    public function create()
    {
        return view('dashboard.class.create');
    }

    public function store(Request $request)
    {
        // Store class logic
        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function edit($id)
    {
        return view('dashboard.class.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update class logic
        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy($id)
    {
        // Delete class logic
        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
