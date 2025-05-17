<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ExpenseCategory;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/expense/Index', [
            'expenses' => Expense::all(),
        ]);
    }

    public function create()
    {
        return view('dashboard.expenses.create');
    }

    public function store(Request $request)
    {
        // Store logic here
        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        return view('dashboard.expenses.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update logic here
        return redirect()->route('expenses.index');
    }

    public function destroy($id)
    {
        // Delete logic here
        return redirect()->route('expenses.index');
    }
}
