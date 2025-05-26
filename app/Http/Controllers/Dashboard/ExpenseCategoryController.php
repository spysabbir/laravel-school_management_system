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

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('expense-categories.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
        ]);

        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->update([
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ]);

        return redirect()->route('expense-categories.index');
    }

    public function destroy($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);

        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot delete this category because it has associated expenses.',
            ]);
        }

        $expenseCategory->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index');
    }
}
