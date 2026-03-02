<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;

        $stats = [
            'total_clients' => Client::where('company_id', $company->id)->count(),
            'total_invoices' => Invoice::where('company_id', $company->id)->count(),
            'paid_invoices' => Invoice::where('company_id', $company->id)->where('status', 'paid')->count(),
            'overdue_invoices' => Invoice::where('company_id', $company->id)->where('status', 'overdue')->count(),
            'unpaid_invoices' => Invoice::where('company_id', $company->id)->whereIn('status', ['sent', 'stalled'])->count(),
            'revenue_today' => Invoice::where('company_id', $company->id)->where('status', 'paid')->whereDate('paid_at', today())->sum('grand_total'),
            'revenue_this_week' => Invoice::where('company_id', $company->id)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('grand_total'),
            'revenue_this_month' => Invoice::where('company_id', $company->id)->where('status', 'paid')->whereMonth('paid_at', now()->month)->sum('grand_total'),
            'total_expenses_this_month' => Expense::where('company_id', $company->id)->whereMonth('expense_date', now()->month)->sum('amount'),
        ];

        $recent_invoices = Invoice::where('company_id', $company->id)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        $overdue_invoices = Invoice::where('company_id', $company->id)
            ->where('status', 'overdue')
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recent_invoices', 'overdue_invoices', 'company'));
    }
}