<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::with(['createdBy', 'updatedBy', 'deletedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('dashboard/designation/Index', [
            'designations' => $designations,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
        ]);

        Designation::create([
            'name' => $request->name,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('designations.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $id,
        ]);


        $designation = Designation::findOrFail($id);
        $designation->update([
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('designations.index');
    }

    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);

        $designation->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $designation->delete();

        return redirect()->route('designations.index');
    }
}
