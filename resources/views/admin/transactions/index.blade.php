@extends('layouts.admin')
@section('title', 'Transactions')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Transactions</h1>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xl font-bold text-green-600">Ksh {{ number_format($stats['revenue_this_month'], 0) }}</p>
        <p class="text-xs text-gray-500">This Month</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xl font-bold text-gray-700">Ksh {{ number_format($stats['total_revenue'], 0) }}</p>
        <p class="text-xs text-gray-500">All Time</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xl font-bold text-green-600">{{ $stats['total_completed'] }}</p>
        <p class="text-xs text-gray-500">Completed</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xl font-bold text-yellow-500">{{ $stats['total_pending'] }}</p>
        <p class="text-xs text-gray-500">Pending</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-xl font-bold text-red-500">{{ $stats['total_failed'] }}</p>
        <p class="text-xs text-gray-500">Failed</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Date</th>
                <th class="px-5 py-3 text-left">Company</th>
                <th class="px-5 py-3 text-left">Plan</th>
                <th class="px-5 py-3 text-right">Amount</th>
                <th class="px-5 py-3 text-left">M-Pesa Code</th>
                <th class="px-5 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $tx)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 text-gray-500">{{ $tx->created_at->format('M j, Y g:i A') }}</td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $tx->company->name }}</td>
                <td class="px-5 py-3 capitalize">{{ str_replace('_', ' ', $tx->plan) }}</td>
                <td class="px-5 py-3 text-right font-semibold text-green-600">Ksh {{ number_format($tx->amount, 2) }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $tx->mpesa_code ?? '—' }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $tx->status === 'completed' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $tx->status === 'pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                        {{ $tx->status === 'failed' ? 'bg-red-100 text-red-600' : '' }}">
                        {{ ucfirst($tx->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-10 text-center text-gray-400">No transactions yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4">{{ $transactions->links() }}</div>
</div>

@endsection