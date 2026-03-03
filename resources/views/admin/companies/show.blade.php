@extends('layouts.admin')
@section('title', $company->name)
@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.companies.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
    <h1 class="text-2xl font-bold text-gray-800">{{ $company->name }}</h1>
    @if($company->is_bypass)
        <span class="bg-yellow-100 text-yellow-600 text-xs px-3 py-1 rounded-full">Bypass Active</span>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <!-- Company Info -->
    <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
        <h2 class="font-semibold text-gray-700 mb-4">Company Details</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase">Owner</p>
                <p class="font-medium">{{ $company->owner?->name }}</p>
                <p class="text-gray-500">{{ $company->owner?->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Contact</p>
                <p>{{ $company->phone }}</p>
                <p class="text-gray-500">{{ $company->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Address</p>
                <p>{{ $company->address ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">KRA PIN</p>
                <p>{{ $company->kra_pin ?? '—' }}</p>
            </div>
        </div>

        <!-- Users -->
        <h3 class="font-semibold text-gray-700 mt-6 mb-3">Users ({{ $company->users->count() }})</h3>
        @foreach($company->users as $user)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <div>
                <p class="font-medium">{{ $user->name }}</p>
                <p class="text-gray-400 text-xs">{{ $user->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($user->role) }}
                </span>
                <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                    @csrf
                    <button class="text-xs {{ $user->is_active ? 'text-red-400' : 'text-green-500' }} hover:underline">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Actions Panel -->
    <div class="space-y-4">

        <!-- Subscription -->
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Subscription</h3>
            @if($company->subscription)
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Plan</span>
                    <span class="font-medium">{{ ucfirst($company->subscription->plan) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-medium">{{ ucfirst($company->subscription->status) }}</span>
                </div>
                @if($company->subscription->isOnTrial())
                <div class="flex justify-between">
                    <span class="text-gray-500">Trial Ends</span>
                    <span class="font-medium">{{ $company->subscription->trial_ends_at->format('M j, Y') }}</span>
                </div>
                @endif
                @if($company->subscription->ends_at)
                <div class="flex justify-between">
                    <span class="text-gray-500">Expires</span>
                    <span class="font-medium">{{ $company->subscription->ends_at->format('M j, Y') }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="mt-4 space-y-2">
                <form action="{{ route('admin.companies.bypass', $company) }}" method="POST">
                    @csrf
                    <button class="w-full py-2 rounded-lg text-sm font-medium
                        {{ $company->is_bypass ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $company->is_bypass ? 'Remove Bypass' : 'Activate Bypass' }}
                    </button>
                </form>
                <form action="{{ route('admin.companies.extend-trial', $company) }}" method="POST">
                    @csrf
                    <button class="w-full bg-blue-50 text-blue-600 py-2 rounded-lg text-sm hover:bg-blue-100">
                        Extend Trial +3 Days
                    </button>
                </form>
            </div>
        </div>

        <!-- Invoice Stats -->
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Invoice Stats</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Invoices</span>
                    <span class="font-medium">{{ $invoiceStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Paid</span>
                    <span class="font-medium text-green-600">{{ $invoiceStats['paid'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Overdue</span>
                    <span class="font-medium text-red-500">{{ $invoiceStats['overdue'] }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-500">Total Revenue</span>
                    <span class="font-bold text-green-600">Ksh {{ number_format($invoiceStats['revenue'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-red-500 mb-3 text-sm">Danger Zone</h3>
            <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
                onsubmit="return confirm('Delete {{ $company->name }} and ALL their data permanently?')">
                @csrf @method('DELETE')
                <button class="w-full border border-red-300 text-red-500 py-2 rounded-lg text-sm hover:bg-red-50">
                    Delete Company
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Transaction History -->
@if($transactions->count())
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Payment History</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Date</th>
                <th class="px-5 py-3 text-left">Plan</th>
                <th class="px-5 py-3 text-right">Amount</th>
                <th class="px-5 py-3 text-left">M-Pesa Code</th>
                <th class="px-5 py-3 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($transactions as $tx)
            <tr>
                <td class="px-5 py-3 text-gray-500">{{ $tx->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3 capitalize">{{ str_replace('_', ' ', $tx->plan) }}</td>
                <td class="px-5 py-3 text-right font-medium">Ksh {{ number_format($tx->amount, 2) }}</td>
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
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection