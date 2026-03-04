<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $company  = auth()->user()->company;
        $search   = $request->get('search');
        $category = $request->get('category');
        $period   = $request->get('period', 'all');
        $sort     = $request->get('sort', 'latest');

        $query = Expense::where('company_id', $company->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        match($period) {
            'this_month'  => $query->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year),
            'last_month'  => $query->whereMonth('expense_date', now()->subMonth()->month)->whereYear('expense_date', now()->subMonth()->year),
            'this_year'   => $query->whereYear('expense_date', now()->year),
            default       => null,
        };

        match($sort) {
            'oldest'      => $query->oldest('expense_date'),
            'amount_high' => $query->orderByDesc('amount'),
            'amount_low'  => $query->orderBy('amount'),
            default       => $query->latest('expense_date'),
        };

        $expenses = $query->paginate(15);

        $stats = [
            'total_this_month' => Expense::where('company_id', $company->id)->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'total_this_year'  => Expense::where('company_id', $company->id)->whereYear('expense_date', now()->year)->sum('amount'),
            'total_all'        => Expense::where('company_id', $company->id)->sum('amount'),
        ];

        $byCategory = Expense::where('company_id', $company->id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $monthlyExpenses = collect(range(5, 0))->map(function($i) use ($company) {
            $date = now()->subMonths($i);
            return [
                'month'  => $date->format('M Y'),
                'amount' => Expense::where('company_id', $company->id)
                    ->whereMonth('expense_date', $date->month)
                    ->whereYear('expense_date', $date->year)
                    ->sum('amount'),
            ];
        })->toArray();

        return view('expenses.index', compact('expenses', 'stats', 'byCategory', 'monthlyExpenses'));
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