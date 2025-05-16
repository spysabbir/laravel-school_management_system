<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use Inertia\Inertia;

class YearController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/year/Index', [
            'years' => Year::all(),
        ]);
    }

    public function create()
    {
        return inertia('dashboard/year/Create');
    }

    public function store(Request $request)
    {
        // Store the year data
        return redirect()->route('years.index')->with('success', 'Year created successfully.');
    }

    public function edit($id)
    {
        return inertia('dashboard/year/Edit', [
            'year' => Year::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        // Update the year data
        return redirect()->route('years.index')->with('success', 'Year updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the year data
        return redirect()->route('years.index')->with('success', 'Year deleted successfully.');
    }
}
