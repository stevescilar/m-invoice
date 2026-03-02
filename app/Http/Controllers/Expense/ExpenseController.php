<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('company_id', Auth::user()->company_id)
            ->latest('expense_date')
            ->paginate(15);

        $stats = [
            'total_this_month' => Expense::where('company_id', Auth::user()->company_id)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'total_this_year' => Expense::where('company_id', Auth::user()->company_id)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'total_all' => Expense::where('company_id', Auth::user()->company_id)
                ->sum('amount'),
        ];

        // Monthly breakdown for chart
        $monthlyExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyExpenses[] = [
                'month'  => $month->format('M Y'),
                'amount' => Expense::where('company_id', Auth::user()->company_id)
                    ->whereMonth('expense_date', $month->month)
                    ->whereYear('expense_date', $month->year)
                    ->sum('amount'),
            ];
        }

        // By category
        $byCategory = Expense::where('company_id', Auth::user()->company_id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('expenses.index', compact('expenses', 'stats', 'monthlyExpenses', 'byCategory'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'     => 'required|string|max:100',
            'description'  => 'nullable|string',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create([
            'company_id'   => Auth::user()->company_id,
            'category'     => $request->category,
            'description'  => $request->description,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt_path' => $receiptPath,
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorizeExpense($expense);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);

        $request->validate([
            'category'     => 'required|string|max:100',
            'description'  => 'nullable|string',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only(['category', 'description', 'amount', 'expense_date']);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    private function authorizeExpense(Expense $expense): void
    {
        if ($expense->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}