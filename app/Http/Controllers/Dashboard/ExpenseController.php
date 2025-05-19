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
            'expenses' => Expense::with(['expenseCategory', 'createdBy', 'updatedBy', 'deletedBy'])
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('dashboard/expense/Create', [
            'expenseCategories' => ExpenseCategory::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Expense::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        $expense = Expense::with(['expenseCategory', 'createdBy', 'updatedBy', 'deletedBy'])
            ->findOrFail($id);

        return Inertia::render('dashboard/expense/Edit', [
            'expense' => $expense,
            'expenseCategories' => ExpenseCategory::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            ...$validated,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('expenses.index');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'deleted_by' => auth()->id(),
        ]);
        $expense->delete();

        return redirect()->route('expenses.index');
    }
}
