<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Classe;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with(['class', 'createdBy', 'updatedBy', 'deletedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        $classes = Classe::all();
        return Inertia::render('dashboard/shift/Index', [
            'shifts' => $shifts,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shifts')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                }),
            ],
        ]);

        Shift::create([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('shifts.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shifts')
                    ->ignore($id)
                    ->where(function ($query) use ($request) {
                        return $query->where('class_id', $request->class_id);
                    }),
            ],
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('shifts.index');
    }

    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);

        // if ($shift->students()->count() > 0) {
        //     return redirect()->back()->withErrors([
        //         'error' => 'Cannot delete this shift because it has associated students.',
        //     ]);
        // }

        $shift->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $shift->delete();

        return redirect()->route('shifts.index');
    }
}
