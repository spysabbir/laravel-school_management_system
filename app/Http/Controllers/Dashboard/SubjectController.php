<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\Classe;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Group;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['class', 'group', 'createdBy', 'updatedBy', 'deletedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        $classes = Classe::all();
        $groups = Group::all();
        return Inertia::render('dashboard/subject/Index', [
            'subjects' => $subjects,
            'classes' => $classes,
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'nullable|exists:groups,id',
            'type' => 'required|in:Compulsory,Optional,Practical',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->where(function ($query) use ($request) {
                    $query->where('class_id', $request->class_id);
                    if ($request->filled('group_id')) {
                        $query->where('group_id', $request->group_id);
                    } else {
                        $query->whereNull('group_id');
                    }
                }),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->where(function ($query) use ($request) {
                    $query->where('class_id', $request->class_id);
                    if ($request->filled('group_id')) {
                        $query->where('group_id', $request->group_id);
                    } else {
                        $query->whereNull('group_id');
                    }
                }),
            ],
        ]);

        Subject::create([
            'class_id' => $request->class_id,
            'group_id' => $request->group_id,
            'type' => $request->type,
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('subjects.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'group_id' => 'nullable|exists:groups,id',
            'type' => 'required|in:Compulsory,Optional,Practical',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($id)->where(function ($query) use ($request) {
                    $query->where('class_id', $request->class_id);
                    if ($request->filled('group_id')) {
                        $query->where('group_id', $request->group_id);
                    } else {
                        $query->whereNull('group_id');
                    }
                }),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($id)->where(function ($query) use ($request) {
                    $query->where('class_id', $request->class_id);
                    if ($request->filled('group_id')) {
                        $query->where('group_id', $request->group_id);
                    } else {
                        $query->whereNull('group_id');
                    }
                }),
            ],
        ]);


        $subject = Subject::findOrFail($id);
        $subject->update([
            'class_id' => $request->class_id,
            'group_id' => $request->group_id,
            'type' => $request->type,
            'name' => $request->name,
            'code' => $request->code,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('subjects.index');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        // if ($subject->students()->count() > 0) {
        //     return redirect()->back()->withErrors([
        //         'error' => 'Cannot delete this subject because it has associated students.',
        //     ]);
        // }

        $subject->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $subject->delete();

        return redirect()->route('subjects.index');
    }
}
