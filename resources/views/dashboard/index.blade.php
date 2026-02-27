@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-gray-500 text-sm">{{ $company->name }} &mdash; {{ now()->format('l, F j Y') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 uppercase">Revenue Today</p>
            <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_today'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 uppercase">This Week</p>
            <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_week'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 uppercase">This Month</p>
            <p class="text-2xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_month'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 uppercase">Expenses (Month)</p>
            <p class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['total_expenses_this_month'], 2) }}</p>
        </div>
    </div>

    <!-- Invoice Status -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-5 text-center">
            <p class="text-3xl font-bold text-gray-700">{{ $stats['total_invoices'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Invoices</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['paid_invoices'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Paid</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 text-center">
            <p class="text-3xl font-bold text-yellow-500">{{ $stats['unpaid_invoices'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Unpaid</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 text-center">
            <p class="text-3xl font-bold text-red-500">{{ $stats['overdue_invoices'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Overdue</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Recent Invoices -->
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-700">Recent Invoices</h2>
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
                        <span
                            class="text-xs px-2 py-0.5 rounded-full
                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-600' : '' }}
                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-600' : '' }}
                    {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
                    {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                    {{ $invoice->status === 'stalled' ? 'bg-yellow-100 text-yellow-600' : '' }}">
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
                <h2 class="font-semibold text-gray-700">Overdue Invoices</h2>
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
                <p class="text-sm text-gray-400 flex items-center gap-1">✅ No overdue invoices!</p>
            @endforelse
        </div>

    </div>

@endsection
