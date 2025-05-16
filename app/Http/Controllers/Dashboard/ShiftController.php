<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/shift/Index', [
            'shifts' => Shift::all(),
        ]);
    }

    public function create()
    {
        return view('dashboard.shifts.create');
    }

    public function store(Request $request)
    {
        // Store the shift data
        return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
    }

    public function edit($id)
    {
        return view('dashboard.shifts.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update the shift data
        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the shift data
        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
    }
}
