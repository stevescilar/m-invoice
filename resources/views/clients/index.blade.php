@extends('layouts.app')
@section('title', 'Clients')
@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6 text-green-600"></i> Clients
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">Manage your client relationships</p>
    </div>
    <a href="{{ route('clients.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Add Client
    </a>
</div>

<!-- Stats Strip -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $clients->total() }}</p>
            <p class="text-xs text-gray-500">Total Clients</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">Ksh {{ number_format($totalBilled, 0) }}</p>
            <p class="text-xs text-gray-500">Total Billed</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
        <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-red-500">Ksh {{ number_format($totalOutstanding, 0) }}</p>
            <p class="text-xs text-gray-500">Outstanding</p>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <form method="GET" action="{{ route('clients.index') }}" class="flex gap-3 flex-wrap">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, phone or email..."
                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <select name="filter" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="all" {{ request('filter', 'all') === 'all' ? 'selected' : '' }}>All Clients</option>
            <option value="flagged" {{ request('filter') === 'flagged' ? 'selected' : '' }}>Flagged Only</option>
            <option value="outstanding" {{ request('filter') === 'outstanding' ? 'selected' : '' }}>Has Outstanding</option>
        </select>
        <select name="sort" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest First</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A–Z</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-1.5">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request('search') || request('filter') || request('sort'))
        <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-600 px-3 py-2 text-sm flex items-center gap-1">
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
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Billed</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Outstanding</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($clients as $client)
            <tr class="hover:bg-green-50/30 transition">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0
                            {{ $client->is_flagged ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700' }}">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>
                        <div>
                            <a href="{{ route('clients.show', $client) }}"
                                class="font-semibold text-gray-800 hover:text-green-600 transition">
                                {{ $client->name }}
                            </a>
                            @if($client->is_flagged)
                            <div class="flex items-center gap-1 text-xs text-red-400 mt-0.5">
                                <i data-lucide="flag" class="w-3 h-3"></i> Flagged
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <div class="space-y-0.5">
                        @if($client->phone)
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <i data-lucide="phone" class="w-3 h-3 text-gray-300"></i>
                            {{ $client->phone }}
                        </div>
                        @endif
                        @if($client->email)
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <i data-lucide="mail" class="w-3 h-3 text-gray-300"></i>
                            {{ $client->email }}
                        </div>
                        @endif
                        @if(!$client->phone && !$client->email)
                        <span class="text-gray-300 text-xs">No contact info</span>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <span class="font-semibold text-gray-800">Ksh {{ number_format($client->totalBilled(), 2) }}</span>
                </td>
                <td class="px-5 py-3.5 text-right">
                    @if($client->outstandingBalance() > 0)
                    <span class="font-bold text-red-500">Ksh {{ number_format($client->outstandingBalance(), 2) }}</span>
                    @else
                    <span class="text-green-600 font-medium flex items-center justify-end gap-1 text-xs">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Clear
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-center">
                    @if($client->is_flagged)
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-red-100 text-red-600">
                        <i data-lucide="flag" class="w-3 h-3"></i> Flagged
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-green-100 text-green-600">
                        <i data-lucide="check-circle" class="w-3 h-3"></i> Active
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('clients.show', $client) }}" title="View"
                            class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 hover:text-blue-700 transition">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" title="Edit"
                            class="p-1.5 rounded-lg hover:bg-yellow-50 text-yellow-500 hover:text-yellow-700 transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST"
                            onsubmit="return confirm('Delete {{ addslashes($client->name) }}? This cannot be undone.')">
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
                            <i data-lucide="users" class="w-6 h-6 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-medium">No clients found</p>
                        <a href="{{ route('clients.create') }}"
                            class="text-sm text-green-600 hover:underline flex items-center gap-1">
                            <i data-lucide="user-plus" class="w-4 h-4"></i> Add your first client
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($clients->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Showing {{ $clients->firstItem() }}–{{ $clients->lastItem() }} of {{ $clients->total() }} clients
        </p>
        {{ $clients->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection