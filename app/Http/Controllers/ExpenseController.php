<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['createdBy', 'updatedBy']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->paginate(20);
        $expenseCategories = ExpenseCategory::active()->orderBy('name')->get();
        $usedCategories = Expense::distinct()->pluck('category');

        // Summary statistics
        $todayExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $totalExpenses = Expense::sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'expenseCategories',
            'usedCategories',
            'todayExpenses',
            'thisMonthExpenses',
            'totalExpenses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'note' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();

        $expense = Expense::create($validated);

        // Create corresponding accounting entry
        AccountingEntry::create([
            'date' => $expense->expense_date,
            'type' => 'depense',
            'amount' => $expense->amount,
            'reference_type' => 'Expense',
            'reference_id' => $expense->id,
            'description' => '[' . $expense->category . '] ' . $expense->label . ($expense->note ? ' - ' . $expense->note : ''),
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Dépense enregistrée avec succès.');
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'note' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = Auth::id();

        $expense->update($validated);

        // Sync the corresponding accounting entry
        $accountingEntry = AccountingEntry::where('reference_type', 'Expense')
            ->where('reference_id', $expense->id)
            ->first();

        if ($accountingEntry) {
            $accountingEntry->update([
                'date' => $expense->expense_date,
                'amount' => $expense->amount,
                'description' => '[' . $expense->category . '] ' . $expense->label . ($expense->note ? ' - ' . $expense->note : ''),
                'status' => 'active',
            ]);
        }

        return back()->with('success', 'Dépense modifiée avec succès.');
    }

    public function destroy(Expense $expense)
    {
        // Delete the corresponding accounting entry
        AccountingEntry::where('reference_type', 'Expense')
            ->where('reference_id', $expense->id)
            ->delete();

        $expense->delete();

        return back()->with('success', 'Dépense supprimée avec succès.');
    }
}
