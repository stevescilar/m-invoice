@extends('layouts.app')
@section('title', 'Financial, Profit & KRA VAT Reports')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Financial, Profit &amp; KRA Tax Reports</h1>
        <p class="text-xs text-gray-400 mt-0.5">
            Period: <strong>{{ $startDate->format('M j, Y') }}</strong> — <strong>{{ $endDate->format('M j, Y') }}</strong>
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('reports.export.csv', request()->query()) }}"
            class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600"></i> Export CSV / Excel
        </a>
        <a href="{{ route('reports.export.pdf', request()->query()) }}"
            class="inline-flex items-center gap-1.5 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
            <i data-lucide="download" class="w-4 h-4"></i> Download PDF Report
        </a>
    </div>
</div>

{{-- Period Filter Bar --}}
<div class="bg-white rounded-xl shadow p-4 mb-6" x-data="{ period: '{{ $period }}' }">
    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Report Period</label>
            <select name="period" x-model="period"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_quarter">This Quarter</option>
                <option value="this_year">This Year</option>
                <option value="all_time">All Time</option>
                <option value="custom">Custom Date Range</option>
            </select>
        </div>

        <div x-show="period === 'custom'" class="flex items-end gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from', $startDate->format('Y-m-d')) }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to', $endDate->format('Y-m-d')) }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-1.5">
            <i data-lucide="filter" class="w-4 h-4"></i> Apply Filter
        </button>
    </form>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-xs font-semibold uppercase text-gray-400">Total Invoiced</p>
        <p class="text-xl font-extrabold text-gray-800 mt-1">Ksh {{ number_format($totalBilled, 2) }}</p>
        <p class="text-xs text-green-600 mt-1">Collected: Ksh {{ number_format($totalCollected, 2) }}</p>
        <p class="text-xs text-red-500">Unpaid Balance: Ksh {{ number_format($totalOutstanding, 2) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
        <p class="text-xs font-semibold uppercase text-gray-400">Costs &amp; Expenses</p>
        <p class="text-xl font-extrabold text-gray-800 mt-1">Ksh {{ number_format($totalBuyingCost + $totalExpenses, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Material Buying Cost: Ksh {{ number_format($totalBuyingCost, 2) }}</p>
        <p class="text-xs text-gray-500">Operating Expenses: Ksh {{ number_format($totalExpenses, 2) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-600">
        <p class="text-xs font-semibold uppercase text-gray-400">Net Profit (After Expenses)</p>
        <p class="text-xl font-extrabold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
            Ksh {{ number_format($netProfit, 2) }}
        </p>
        <p class="text-xs text-gray-500 mt-1">Gross Invoice Profit: Ksh {{ number_format($grossInvoiceProfit, 2) }}</p>
        <p class="text-xs text-gray-500">Net Margin: {{ $netMargin }}%</p>
    </div>

    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-600">
        <p class="text-xs font-semibold uppercase text-purple-600">KRA ETR VAT (16%)</p>
        <p class="text-xl font-extrabold text-purple-700 mt-1">Ksh {{ number_format($vatBilled, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">VAT on Paid Invoices: Ksh {{ number_format($vatCollected, 2) }}</p>
        <p class="text-xs text-gray-500">ETR Invoices: {{ $etrInvoices->count() }} ({{ $company->kra_pin ?: 'No PIN set' }})</p>
    </div>
</div>

{{-- Invoices Profit & VAT Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Invoices Profit &amp; VAT Breakdown ({{ $invoices->count() }})</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Invoice #</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Client</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Grand Total</th>
                    <th class="px-4 py-3 text-right">Paid</th>
                    <th class="px-4 py-3 text-right">Buying Cost</th>
                    <th class="px-4 py-3 text-right">VAT (16%)</th>
                    <th class="px-4 py-3 text-right">Net Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-green-600 hover:underline">{{ $inv->invoice_number }}</a>
                        @if($inv->etr_enabled)
                            <span class="text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded ml-1">ETR</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->issue_date->format('M j, Y') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $inv->client->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : ($inv->status === 'overdue' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($inv->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-medium">Ksh {{ number_format($inv->grand_total, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-600">Ksh {{ number_format($inv->totalPaidAmount(), 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-500">Ksh {{ number_format($inv->total_cost, 2) }}</td>
                    <td class="px-4 py-3 text-right text-purple-600">Ksh {{ number_format($inv->vat_amount, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $inv->total_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Ksh {{ number_format($inv->total_profit, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center text-gray-400">No invoices recorded in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection
