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
        $company_id = $company->id;

        $stats = [
            'total_clients'             => Client::where('company_id', $company_id)->count(),
            'total_invoices'            => Invoice::where('company_id', $company_id)->count(),
            'paid_invoices'             => Invoice::where('company_id', $company_id)->where('status', 'paid')->count(),
            'overdue_invoices'          => Invoice::where('company_id', $company_id)->where('status', 'overdue')->count(),
            'unpaid_invoices'           => Invoice::where('company_id', $company_id)->whereIn('status', ['sent', 'stalled'])->count(),

            // Revenue
            'revenue_today'             => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereDate('paid_at', today())->sum('grand_total'),
            'revenue_this_week'         => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('grand_total'),
            'revenue_this_month'        => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('grand_total'),
            'revenue_this_quarter'      => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfQuarter(), now()->endOfQuarter()])->sum('grand_total'),
            'revenue_this_year'         => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereYear('paid_at', now()->year)->sum('grand_total'),

            // Profit (from invoices that have profit data)
            'profit_today'              => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereDate('paid_at', today())->sum('total_profit'),
            'profit_this_week'          => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_profit'),
            'profit_this_month'         => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('total_profit'),
            'profit_this_quarter'       => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereBetween('paid_at', [now()->startOfQuarter(), now()->endOfQuarter()])->sum('total_profit'),
            'profit_this_year'          => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereYear('paid_at', now()->year)->sum('total_profit'),

            // Expenses
            'expenses_this_month'       => Expense::where('company_id', $company_id)->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'expenses_this_year'        => Expense::where('company_id', $company_id)->whereYear('expense_date', now()->year)->sum('amount'),
        ];

        $recent_invoices = Invoice::where('company_id', $company_id)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        $overdue_invoices = Invoice::where('company_id', $company_id)
            ->where('status', 'overdue')
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        // Monthly revenue + profit for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyData[] = [
                'month'   => $month->format('M Y'),
                'revenue' => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereMonth('paid_at', $month->month)->whereYear('paid_at', $month->year)->sum('grand_total'),
                'profit'  => Invoice::where('company_id', $company_id)->where('status', 'paid')->whereMonth('paid_at', $month->month)->whereYear('paid_at', $month->year)->sum('total_profit'),
            ];
        }

        return view('dashboard.index', compact('stats', 'recent_invoices', 'overdue_invoices', 'company', 'monthlyData'));
    }
}