<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionTransaction;

class AdminTransactionController extends Controller
{
    public function index()
    {
        $transactions = SubscriptionTransaction::with('company')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_revenue'      => SubscriptionTransaction::where('status', 'completed')->sum('amount'),
            'revenue_this_month' => SubscriptionTransaction::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            'total_completed'    => SubscriptionTransaction::where('status', 'completed')->count(),
            'total_failed'       => SubscriptionTransaction::where('status', 'failed')->count(),
            'total_pending'      => SubscriptionTransaction::where('status', 'pending')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }
}