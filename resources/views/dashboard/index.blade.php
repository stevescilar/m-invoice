@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 text-sm">{{ $company->name }} &mdash; {{ now()->format('l, F j Y') }}</p>
    </div>
</div>

<!-- Period Tabs -->
<div x-data="{ period: 'month' }" class="space-y-6">
    <div class="flex gap-2">
        @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'quarter' => 'This Quarter', 'year' => 'This Year'] as $key => $label)
        <button @click="period = '{{ $key }}'"
            :class="period === '{{ $key }}' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400'"
            class="px-4 py-1.5 rounded-full text-sm border">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <!-- Revenue & Profit Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <!-- Revenue -->
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 uppercase font-medium">Revenue</p>
                <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div x-show="period === 'today'" class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_today'], 2) }}</div>
            <div x-show="period === 'week'" class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_week'], 2) }}</div>
            <div x-show="period === 'month'" class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_month'], 2) }}</div>
            <div x-show="period === 'quarter'" class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_quarter'], 2) }}</div>
            <div x-show="period === 'year'" class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_year'], 2) }}</div>
        </div>

        <!-- Profit -->
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 uppercase font-medium">Profit</p>
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="circle-dollar-sign" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div x-show="period === 'today'" class="text-2xl font-bold text-blue-600">Ksh {{ number_format($stats['profit_today'], 2) }}</div>
            <div x-show="period === 'week'" class="text-2xl font-bold text-blue-600">Ksh {{ number_format($stats['profit_this_week'], 2) }}</div>
            <div x-show="period === 'month'" class="text-2xl font-bold text-blue-600">Ksh {{ number_format($stats['profit_this_month'], 2) }}</div>
            <div x-show="period === 'quarter'" class="text-2xl font-bold text-blue-600">Ksh {{ number_format($stats['profit_this_quarter'], 2) }}</div>
            <div x-show="period === 'year'" class="text-2xl font-bold text-blue-600">Ksh {{ number_format($stats['profit_this_year'], 2) }}</div>
        </div>

        <!-- Expenses -->
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 uppercase font-medium">Expenses</p>
                <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="receipt" class="w-4 h-4 text-red-500"></i>
                </div>
            </div>
            <div x-show="period === 'today'" class="text-2xl font-bold text-red-500">—</div>
            <div x-show="period === 'week'" class="text-2xl font-bold text-red-500">—</div>
            <div x-show="period === 'month'" class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['expenses_this_month'], 2) }}</div>
            <div x-show="period === 'quarter'" class="text-2xl font-bold text-red-500">—</div>
            <div x-show="period === 'year'" class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['expenses_this_year'], 2) }}</div>
        </div>

        <!-- Net -->
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 uppercase font-medium">Net</p>
                <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="scale" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            @php
                $netMonth = $stats['profit_this_month'] - $stats['expenses_this_month'];
                $netYear  = $stats['profit_this_year'] - $stats['expenses_this_year'];
            @endphp
            <div x-show="period === 'today'" class="text-2xl font-bold {{ $stats['profit_today'] >= 0 ? 'text-green-600' : 'text-red-500' }}">Ksh {{ number_format($stats['profit_today'], 2) }}</div>
            <div x-show="period === 'week'" class="text-2xl font-bold {{ $stats['profit_this_week'] >= 0 ? 'text-green-600' : 'text-red-500' }}">Ksh {{ number_format($stats['profit_this_week'], 2) }}</div>
            <div x-show="period === 'month'" class="text-2xl font-bold {{ $netMonth >= 0 ? 'text-green-600' : 'text-red-500' }}">Ksh {{ number_format($netMonth, 2) }}</div>
            <div x-show="period === 'quarter'" class="text-2xl font-bold {{ $stats['profit_this_quarter'] >= 0 ? 'text-green-600' : 'text-red-500' }}">Ksh {{ number_format($stats['profit_this_quarter'], 2) }}</div>
            <div x-show="period === 'year'" class="text-2xl font-bold {{ $netYear >= 0 ? 'text-green-600' : 'text-red-500' }}">Ksh {{ number_format($netYear, 2) }}</div>
        </div>
    </div>

    <!-- Invoice Status -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="file-text" class="w-5 h-5 text-gray-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-700">{{ $stats['total_invoices'] }}</p>
                <p class="text-xs text-gray-500">Total Invoices</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600">{{ $stats['paid_invoices'] }}</p>
                <p class="text-xs text-gray-500">Paid</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-5 h-5 text-yellow-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500">{{ $stats['unpaid_invoices'] }}</p>
                <p class="text-xs text-gray-500">Unpaid</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-red-500">{{ $stats['overdue_invoices'] }}</p>
                <p class="text-xs text-gray-500">Overdue</p>
            </div>
        </div>
    </div>

</div>

<!-- Chart -->
<div class="bg-white rounded-xl shadow p-5 mt-6">
    <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
        <i data-lucide="bar-chart-2" class="w-4 h-4 text-green-600"></i>
        Revenue vs Profit (Last 6 Months)
    </h2>
    <canvas id="revenueChart" height="100"></canvas>
</div>

<!-- Bottom Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

    <!-- Recent Invoices -->
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-green-600"></i>
                Recent Invoices
            </h2>
            <a href="{{ route('invoices.index') }}" class="text-sm text-green-600 hover:underline">View all</a>
        </div>
        @forelse($recent_invoices as $invoice)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-700">{{ $invoice->client->name }}</p>
                <p class="text-xs text-gray-400">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold">Ksh {{ number_format($invoice->grand_total, 2) }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-600' : '' }}
                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-600' : '' }}
                    {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $invoice->status === 'draft' ? 'bg-yellow-100 text-yellow-600' : '' }}
                    {{ $invoice->status === 'stalled' ? 'bg-orange-100 text-orange-600' : '' }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400">No invoices yet.</p>
        @endforelse
    </div>

    <!-- Overdue Invoices -->
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                Overdue Invoices
            </h2>
            <a href="{{ route('invoices.index') }}" class="text-sm text-red-500 hover:underline">View all</a>
        </div>
        @forelse($overdue_invoices as $invoice)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <div>
                <p class="text-sm font-medium text-gray-700">{{ $invoice->client->name }}</p>
                <p class="text-xs text-gray-400">Due: {{ $invoice->due_date->format('M j, Y') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-red-500">Ksh {{ number_format($invoice->grand_total, 2) }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 flex items-center gap-1">
            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            No overdue invoices!
        </p>
        @endforelse
    </div>

</div>

<!-- Referral QR Code -->
<div class="bg-white rounded-xl shadow p-5 mt-6">
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="flex-shrink-0">
            {!! QrCode::size(140)->color(22, 163, 74)->generate(auth()->user()->company->getReferralUrl()) !!}
        </div>
        <div>
            <h2 class="font-bold text-gray-800 text-lg mb-1 flex items-center gap-2">
                <i data-lucide="gift" class="w-5 h-5 text-green-600"></i>
                Share M-Invoice & Earn Free Days
            </h2>
            <p class="text-gray-500 text-sm mb-3">
                Share your unique QR code or link. Every business that registers using your link
                gives you <strong class="text-green-600">+1 free day</strong> on your subscription.
            </p>
            <div class="flex items-center gap-2 bg-gray-50 border rounded-lg px-4 py-2 mb-3">
                <i data-lucide="link" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                <span class="text-sm text-gray-600 truncate flex-1" id="referral-link">{{ auth()->user()->company->getReferralUrl() }}</span>
                <button onclick="copyReferral()" class="text-green-600 text-sm font-medium hover:text-green-700 flex-shrink-0 flex items-center gap-1">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span id="copy-btn-text">Copy</span>
                </button>
            </div>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                You've referred <strong class="mx-1">{{ auth()->user()->company->referral_count }}</strong> business(es) so far.
            </p>
        </div>
    </div>
</div>

<script>
function copyReferral() {
    const link = document.getElementById('referral-link').textContent.trim();
    navigator.clipboard.writeText(link);
    const btn = document.getElementById('copy-btn-text');
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
}
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(array_column($monthlyData, 'month')),
        datasets: [
            {
                label: 'Revenue',
                data: @json(array_column($monthlyData, 'revenue')),
                backgroundColor: 'rgba(22, 163, 74, 0.7)',
                borderRadius: 6,
            },
            {
                label: 'Profit',
                data: @json(array_column($monthlyData, 'profit')),
                backgroundColor: 'rgba(37, 99, 235, 0.7)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Ksh ' + value.toLocaleString()
                }
            }
        }
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection