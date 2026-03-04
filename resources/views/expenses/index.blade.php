@extends('layouts.app')
@section('title', 'Expenses')
@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="wallet" class="w-6 h-6 text-red-500"></i> Expenses
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">Track and manage your business expenses</p>
    </div>
    <a href="{{ route('expenses.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> Log Expense
    </a>
</div>

<!-- Stats Strip -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="calendar" class="w-5 h-5 text-red-500"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['total_this_month'], 2) }}</p>
            <p class="text-xs text-gray-500">This Month</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-down" class="w-5 h-5 text-orange-500"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-orange-500">Ksh {{ number_format($stats['total_this_year'], 2) }}</p>
            <p class="text-xs text-gray-500">This Year</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="receipt" class="w-5 h-5 text-gray-500"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-700">Ksh {{ number_format($stats['total_all'], 2) }}</p>
            <p class="text-xs text-gray-500">All Time</p>
        </div>
    </div>
</div>

<!-- Chart + Category Breakdown -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
        <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="w-4 h-4 text-red-400"></i>
            Monthly Expenses (Last 6 Months)
        </h2>
        <canvas id="expenseChart" height="120"></canvas>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-red-400"></i>
            By Category
        </h2>
        @php
            $categoryColors = [
                'bg-red-100 text-red-600',
                'bg-orange-100 text-orange-600',
                'bg-yellow-100 text-yellow-600',
                'bg-purple-100 text-purple-600',
                'bg-blue-100 text-blue-600',
                'bg-pink-100 text-pink-600',
            ];
        @endphp
        @forelse($byCategory as $i => $cat)
        <div class="flex justify-between items-center py-2 border-b last:border-0">
            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium {{ $categoryColors[$i % count($categoryColors)] }}">
                <i data-lucide="tag" class="w-3 h-3"></i>
                {{ $cat->category }}
            </span>
            <span class="font-semibold text-red-500 text-sm">Ksh {{ number_format($cat->total, 2) }}</span>
        </div>
        @empty
        <div class="flex flex-col items-center py-6 text-center">
            <i data-lucide="pie-chart" class="w-8 h-8 text-gray-200 mb-2"></i>
            <p class="text-sm text-gray-400">No expenses yet</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <form method="GET" action="{{ route('expenses.index') }}" class="flex gap-3 flex-wrap">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by description or category..."
                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <select name="category" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="">All Categories</option>
            @foreach($byCategory as $cat)
            <option value="{{ $cat->category }}" {{ request('category') === $cat->category ? 'selected' : '' }}>
                {{ $cat->category }}
            </option>
            @endforeach
        </select>
        <select name="period" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="all" {{ request('period', 'all') === 'all' ? 'selected' : '' }}>All Time</option>
            <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="last_month" {{ request('period') === 'last_month' ? 'selected' : '' }}>Last Month</option>
            <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>This Year</option>
        </select>
        <select name="sort" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest First</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
            <option value="amount_high" {{ request('sort') === 'amount_high' ? 'selected' : '' }}>Amount: High to Low</option>
            <option value="amount_low" {{ request('sort') === 'amount_low' ? 'selected' : '' }}>Amount: Low to High</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-1.5">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request('search') || request('category') || request('period') || request('sort'))
        <a href="{{ route('expenses.index') }}" class="text-gray-400 hover:text-gray-600 px-3 py-2 text-sm flex items-center gap-1">
            <i data-lucide="x" class="w-4 h-4"></i> Clear
        </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Receipt</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($expenses as $expense)
            <tr class="hover:bg-red-50/20 transition">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-1.5 text-gray-600 text-xs">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-300"></i>
                        <span class="font-medium">{{ $expense->expense_date->format('M j, Y') }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5 ml-5">{{ $expense->expense_date->diffForHumans() }}</p>
                </td>
                <td class="px-5 py-3.5">
                    @php
                        $catColor = match(strtolower($expense->category)) {
                            'fuel', 'transport' => 'bg-orange-100 text-orange-600',
                            'rent', 'office'    => 'bg-blue-100 text-blue-600',
                            'salary', 'staff'   => 'bg-purple-100 text-purple-600',
                            'utilities'         => 'bg-yellow-100 text-yellow-600',
                            'marketing'         => 'bg-pink-100 text-pink-600',
                            default             => 'bg-red-100 text-red-600',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium {{ $catColor }}">
                        <i data-lucide="tag" class="w-3 h-3"></i>
                        {{ $expense->category }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-gray-500 text-sm">
                    {{ $expense->description ?? '—' }}
                </td>
                <td class="px-5 py-3.5 text-right">
                    <span class="font-bold text-red-500">Ksh {{ number_format($expense->amount, 2) }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($expense->receipt_path)
                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs text-blue-500 hover:text-blue-700 bg-blue-50 px-2 py-1 rounded-full transition">
                        <i data-lucide="file" class="w-3 h-3"></i> View
                    </a>
                    @else
                    <span class="text-xs text-gray-300 flex items-center justify-center gap-1">
                        <i data-lucide="minus" class="w-3 h-3"></i> None
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('expenses.edit', $expense) }}" title="Edit"
                            class="p-1.5 rounded-lg hover:bg-yellow-50 text-yellow-500 hover:text-yellow-700 transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                            onsubmit="return confirm('Delete this expense? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete"
                                class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="wallet" class="w-6 h-6 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-medium">No expenses found</p>
                        <a href="{{ route('expenses.create') }}"
                            class="text-sm text-green-600 hover:underline flex items-center gap-1">
                            <i data-lucide="plus" class="w-4 h-4"></i> Log your first expense
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($expenses->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses
        </p>
        {{ $expenses->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('expenseChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json(array_column($monthlyExpenses, 'month')),
        datasets: [{
            label: 'Expenses',
            data: @json(array_column($monthlyExpenses, 'amount')),
            backgroundColor: 'rgba(239, 68, 68, 0.7)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => 'Ksh ' + value.toLocaleString() }
            }
        }
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection