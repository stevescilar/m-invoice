<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with('owner', 'subscription')
            ->withCount('users', 'invoices', 'clients')
            ->latest()
            ->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load('owner', 'subscription', 'users');
        $invoiceStats = [
            'total'   => $company->invoices()->count(),
            'paid'    => $company->invoices()->where('status', 'paid')->count(),
            'overdue' => $company->invoices()->where('status', 'overdue')->count(),
            'revenue' => $company->invoices()->where('status', 'paid')->sum('grand_total'),
        ];
        $transactions = $company->subscriptionTransactions()->latest()->take(10)->get();

        return view('admin.companies.show', compact('company', 'invoiceStats', 'transactions'));
    }

    public function toggleBypass(Company $company)
    {
        $company->update(['is_bypass' => !$company->is_bypass]);

        if ($company->is_bypass) {
            $company->subscription->update([
                'plan'     => 'yearly',
                'status'   => 'active',
                'on_trial' => false,
                'ends_at'  => now()->addYears(10),
            ]);
        }

        return back()->with('success', 'Bypass ' . ($company->is_bypass ? 'activated' : 'deactivated') . ' for ' . $company->name);
    }

    public function extendTrial(Company $company)
    {
        $sub = $company->subscription;
        $sub->update([
            'on_trial'      => true,
            'trial_ends_at' => now()->addDays(3),
            'status'        => 'trial',
        ]);

        return back()->with('success', 'Trial extended by 3 days for ' . $company->name);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted.');
    }
}