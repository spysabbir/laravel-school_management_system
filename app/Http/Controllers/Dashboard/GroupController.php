<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\Group;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with(['class', 'createdBy', 'updatedBy', 'deletedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        $classes = Classe::all();
        return Inertia::render('dashboard/group/Index', [
            'groups' => $groups,
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
            Rule::unique('groups')->where(function ($query) use ($request) {
                return $query->where('class_id', $request->class_id);
            }),
        ],
    ]);

        Group::create([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('groups.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groups')
                    ->ignore($id)
                    ->where(function ($query) use ($request) {
                        return $query->where('class_id', $request->class_id);
                    }),
            ],
        ]);


        $group = Group::findOrFail($id);
        $group->update([
            'class_id' => $request->class_id,
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('groups.index');
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        // if ($group->students()->count() > 0) {
        //     return redirect()->back()->withErrors([
        //         'error' => 'Cannot delete this group because it has associated students.',
        //     ]);
        // }

        $group->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $group->delete();

        return redirect()->route('groups.index');
    }
}
