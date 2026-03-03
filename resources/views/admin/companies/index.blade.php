@extends('layouts.admin')
@section('title', 'Companies')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Companies</h1>
    <span class="text-sm text-gray-500">{{ $companies->total() }} total</span>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Company</th>
                <th class="px-5 py-3 text-left">Owner</th>
                <th class="px-5 py-3 text-center">Users</th>
                <th class="px-5 py-3 text-center">Invoices</th>
                <th class="px-5 py-3 text-center">Clients</th>
                <th class="px-5 py-3 text-center">Plan</th>
                <th class="px-5 py-3 text-center">Bypass</th>
                <th class="px-5 py-3 text-center">Joined</th>
                <th class="px-5 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($companies as $company)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">
                    <a href="{{ route('admin.companies.show', $company) }}" class="hover:text-green-600">
                        {{ $company->name }}
                    </a>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $company->owner?->email }}</td>
                <td class="px-5 py-3 text-center">{{ $company->users_count }}</td>
                <td class="px-5 py-3 text-center">{{ $company->invoices_count }}</td>
                <td class="px-5 py-3 text-center">{{ $company->clients_count }}</td>
                <td class="px-5 py-3 text-center">
                    @if($company->subscription)
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $company->subscription->isOnTrial() ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $company->subscription->isActive() && !$company->subscription->isOnTrial() ? 'bg-green-100 text-green-600' : '' }}
                        {{ !$company->subscription->isActive() ? 'bg-red-100 text-red-600' : '' }}">
                        {{ $company->subscription->isOnTrial() ? 'Trial' : ucfirst($company->subscription->plan) }}
                    </span>
                    @else
                    <span class="text-xs text-gray-400">None</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-center">
                    @if($company->is_bypass)
                        <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded-full">Yes</span>
                    @else
                        <span class="text-xs text-gray-300">No</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-center text-gray-500 text-xs">{{ $company->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('admin.companies.show', $company) }}"
                            class="text-blue-500 hover:underline text-xs">View</a>
                        <form action="{{ route('admin.companies.bypass', $company) }}" method="POST">
                            @csrf
                            <button class="text-yellow-500 hover:underline text-xs">
                                {{ $company->is_bypass ? 'Remove Bypass' : 'Bypass' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.companies.extend-trial', $company) }}" method="POST">
                            @csrf
                            <button class="text-green-500 hover:underline text-xs">+Trial</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-5 py-10 text-center text-gray-400">No companies yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4">{{ $companies->links() }}</div>
</div>

@endsection