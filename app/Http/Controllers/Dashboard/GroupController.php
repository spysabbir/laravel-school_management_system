<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/group/Index', [
            'groups' => Group::all(),
        ]);
    }

    public function create()
    {
        return inertia('dashboard/group/Create');
    }

    public function store(Request $request)
    {
        // Store the group data
        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    public function edit($id)
    {
        return inertia('dashboard/group/Edit', [
            'group' => Group::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        // Update the group data
        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the group data
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
