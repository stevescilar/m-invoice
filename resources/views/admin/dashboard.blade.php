@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
    <p class="text-gray-500 text-sm">{{ now()->format('l, F j Y') }}</p>
</div>

<!-- Revenue Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">Today's Revenue</p>
        <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_today'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">This Month</p>
        <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_month'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">This Year</p>
        <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_year'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">All Time</p>
        <p class="text-2xl font-bold text-gray-700">Ksh {{ number_format($stats['revenue_all_time'], 2) }}</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-3xl font-bold text-gray-800">{{ $stats['total_companies'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Companies</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $stats['trial_subscriptions'] }}</p>
        <p class="text-xs text-gray-500 mt-1">On Trial</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $stats['active_subscriptions'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Active Subscriptions</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-3xl font-bold text-red-500">{{ $stats['expired_trials'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Expired Trials</p>
    </div>
</div>

<!-- Subscription Breakdown -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['monthly_subs'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Monthly Plans</p>
        <p class="text-xs text-gray-400">Ksh {{ number_format($stats['monthly_subs'] * 700, 0) }}/month</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-2xl font-bold text-orange-500">{{ $stats['yearly_subs'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Yearly Plans</p>
        <p class="text-xs text-gray-400">Ksh {{ number_format($stats['yearly_subs'] * 5000, 0) }}/year</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center">
        <p class="text-2xl font-bold text-gray-700">{{ $stats['total_invoices'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Invoices Created</p>
    </div>
</div>

<!-- Chart -->
<div class="bg-white rounded-xl shadow p-5 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">Revenue & New Companies (Last 6 Months)</h2>
    <canvas id="adminChart" height="80"></canvas>
</div>

<!-- Bottom Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Recent Companies -->
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700">Recent Companies</h2>
            <a href="{{ route('admin.companies.index') }}" class="text-sm text-green-600 hover:underline">View all</a>
        </div>
        @foreach($recent_companies as $company)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-700">{{ $company->name }}</p>
                <p class="text-xs text-gray-400">{{ $company->owner?->email }}</p>
            </div>
            <div class="text-right">
                @if($company->subscription)
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $company->subscription->isOnTrial() ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $company->subscription->isActive() && !$company->subscription->isOnTrial() ? 'bg-green-100 text-green-600' : '' }}
                    {{ !$company->subscription->isActive() ? 'bg-red-100 text-red-600' : '' }}">
                    {{ $company->subscription->isOnTrial() ? 'Trial' : ucfirst($company->subscription->plan) }}
                </span>
                @endif
                <p class="text-xs text-gray-400 mt-1">{{ $company->created_at->format('M j, Y') }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700">Recent Payments</h2>
            <a href="{{ route('admin.transactions.index') }}" class="text-sm text-green-600 hover:underline">View all</a>
        </div>
        @forelse($recent_transactions as $tx)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-700">{{ $tx->company->name }}</p>
                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $tx->plan)) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-green-600">Ksh {{ number_format($tx->amount, 2) }}</p>
                <p class="text-xs text-gray-400">{{ $tx->created_at->format('M j, Y') }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400">No transactions yet.</p>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('adminChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(array_column($monthlyRevenue, 'month')),
        datasets: [
            {
                label: 'Revenue (Ksh)',
                data: @json(array_column($monthlyRevenue, 'amount')),
                backgroundColor: 'rgba(22, 163, 74, 0.7)',
                borderRadius: 6,
                yAxisID: 'y',
            },
            {
                label: 'New Companies',
                data: @json(array_column($monthlyRevenue, 'companies')),
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderRadius: 6,
                yAxisID: 'y1',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                beginAtZero: true,
                position: 'left',
                ticks: { callback: v => 'Ksh ' + v.toLocaleString() }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                grid: { drawOnChartArea: false },
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>

@endsection