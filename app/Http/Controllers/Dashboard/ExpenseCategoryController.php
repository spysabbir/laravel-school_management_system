<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard/expense-category/Index', [
            'expenseCategories' => ExpenseCategory::all(),
        ]);
    }

    public function create()
    {
        return view('dashboard.expense-categories.create');
    }

    public function store(Request $request)
    {
        // Store logic here
        return redirect()->route('expense-categories.index');
    }

    public function edit($id)
    {
        return view('dashboard.expense-categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update logic here
        return redirect()->route('expense-categories.index');
    }

    public function destroy($id)
    {
        // Delete logic here
        return redirect()->route('expense-categories.index');
    }
}
