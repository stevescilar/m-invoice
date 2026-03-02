@extends('layouts.app')
@section('title', 'Expenses')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Expenses</h1>
    <a href="{{ route('expenses.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
        + Log Expense
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">This Month</p>
        <p class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['total_this_month'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">This Year</p>
        <p class="text-2xl font-bold text-red-500">Ksh {{ number_format($stats['total_this_year'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-xs text-gray-500 uppercase mb-1">All Time</p>
        <p class="text-2xl font-bold text-gray-700">Ksh {{ number_format($stats['total_all'], 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <!-- Chart -->
    <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
        <h2 class="font-semibold text-gray-700 mb-4">Monthly Expenses (Last 6 Months)</h2>
        <canvas id="expenseChart" height="120"></canvas>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-4">By Category</h2>
        @forelse($byCategory as $cat)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600">{{ $cat->category }}</span>
            <span class="font-semibold text-red-500">Ksh {{ number_format($cat->total, 2) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400">No expenses yet.</p>
        @endforelse
    </div>
</div>

<!-- Expenses Table -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Date</th>
                <th class="px-6 py-3 text-left">Category</th>
                <th class="px-6 py-3 text-left">Description</th>
                <th class="px-6 py-3 text-right">Amount</th>
                <th class="px-6 py-3 text-center">Receipt</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($expenses as $expense)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-600">{{ $expense->expense_date->format('M j, Y') }}</td>
                <td class="px-6 py-4">
                    <span class="bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full">
                        {{ $expense->category }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $expense->description ?? '—' }}</td>
                <td class="px-6 py-4 text-right font-semibold text-red-500">
                    Ksh {{ number_format($expense->amount, 2) }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($expense->receipt_path)
                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"
                            class="text-blue-500 hover:underline text-xs">View</a>
                    @else
                        <span class="text-gray-300 text-xs">None</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center flex gap-2 justify-center">
                    <a href="{{ route('expenses.edit', $expense) }}" class="text-yellow-500 hover:underline">Edit</a>
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this expense?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    No expenses yet. <a href="{{ route('expenses.create') }}" class="text-green-600">Log your first expense</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $expenses->links() }}</div>
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

@endsection