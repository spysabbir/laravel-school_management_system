<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Classe;

class ClasseController extends Controller
{
    public function index()
    {
        $classes = Classe::all();
        return Inertia::render('dashboard/classe/Index', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
        ]);

        Classe::create([
            'name' => $request->name,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('classes.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $id,
        ]);

        $classe = Classe::findOrFail($id);
        $classe->update([
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('classes.index');
    }

    public function destroy($id)
    {
        $classe = Classe::findOrFail($id);

        // if ($classe->students()->count() > 0) {
        //     return redirect()->back()->withErrors([
        //         'error' => 'Cannot delete this class because it has associated students.',
        //     ]);
        // }

        $classe->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $classe->delete();

        return redirect()->route('classes.index');
    }
}
