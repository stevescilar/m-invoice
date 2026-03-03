<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies'      => Company::count(),
            'total_users'          => User::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'trial_subscriptions'  => Subscription::where('on_trial', true)->whereDate('trial_ends_at', '>=', now())->count(),
            'expired_trials'       => Subscription::where('on_trial', true)->whereDate('trial_ends_at', '<', now())->count(),
            'monthly_subs'         => Subscription::where('plan', 'monthly')->where('status', 'active')->count(),
            'yearly_subs'          => Subscription::where('plan', 'yearly')->where('status', 'active')->count(),

            // Revenue from transactions
            'revenue_today'        => SubscriptionTransaction::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'revenue_this_month'   => SubscriptionTransaction::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'),
            'revenue_this_year'    => SubscriptionTransaction::where('status', 'completed')->whereYear('created_at', now()->year)->sum('amount'),
            'revenue_all_time'     => SubscriptionTransaction::where('status', 'completed')->sum('amount'),

            'total_invoices'       => Invoice::count(),
            'total_transactions'   => SubscriptionTransaction::where('status', 'completed')->count(),
        ];

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRevenue[] = [
                'month'  => $month->format('M Y'),
                'amount' => SubscriptionTransaction::where('status', 'completed')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('amount'),
                'companies' => Company::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }

        $recent_companies    = Company::with('owner', 'subscription')->latest()->take(8)->get();
        $recent_transactions = SubscriptionTransaction::with('company')->where('status', 'completed')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'monthlyRevenue', 'recent_companies', 'recent_transactions'));
    }
}