@extends('layouts.app')
@section('title', 'Quotations')
@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-6 h-6 text-green-600"></i> Quotations
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">Create and manage client quotations</p>
    </div>
    <a href="{{ route('quotations.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i> New Quotation
    </a>
</div>

<!-- Stats Strip -->
<div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
    @foreach([
        'all'       => ['label' => 'All',       'color' => 'bg-gray-100 text-gray-700'],
        'draft'     => ['label' => 'Draft',     'color' => 'bg-gray-100 text-gray-600'],
        'sent'      => ['label' => 'Sent',      'color' => 'bg-blue-100 text-blue-600'],
        'approved'  => ['label' => 'Approved',  'color' => 'bg-green-100 text-green-600'],
        'rejected'  => ['label' => 'Rejected',  'color' => 'bg-red-100 text-red-600'],
        'converted' => ['label' => 'Converted', 'color' => 'bg-purple-100 text-purple-600'],
    ] as $key => $meta)
    <a href="{{ route('quotations.index') }}?status={{ $key }}&search={{ request('search') }}"
        class="rounded-xl p-3 text-center border-2 transition
        {{ request('status', 'all') === $key ? 'border-green-500 bg-white shadow' : 'border-transparent bg-white hover:border-green-200' }}">
        <p class="text-xl font-bold text-gray-800">{{ $stats[$key] }}</p>
        <span class="text-xs px-2 py-0.5 rounded-full {{ $meta['color'] }} font-medium">{{ $meta['label'] }}</span>
    </a>
    @endforeach
</div>

<!-- Search & Filter Bar -->
<div class="bg-white rounded-xl shadow p-4 mb-4 flex gap-3 flex-wrap items-center">
    <form method="GET" action="{{ route('quotations.index') }}" class="flex gap-3 flex-1 flex-wrap">
        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by client or quote number..."
                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <select name="sort" class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
            <option value="amount_high" {{ request('sort') === 'amount_high' ? 'selected' : '' }}>Amount: High to Low</option>
            <option value="amount_low" {{ request('sort') === 'amount_low' ? 'selected' : '' }}>Amount: Low to High</option>
            <option value="expiry_soon" {{ request('sort') === 'expiry_soon' ? 'selected' : '' }}>Expiring Soon</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-1.5">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request('search') || request('sort'))
        <a href="{{ route('quotations.index') }}" class="text-gray-400 hover:text-gray-600 px-3 py-2 text-sm flex items-center gap-1">
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
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Quote #</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Issue Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry Date</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($quotations as $quotation)
            <tr class="hover:bg-green-50/30 transition">
                <td class="px-5 py-3.5">
                    <a href="{{ route('quotations.show', $quotation) }}"
                        class="font-semibold text-gray-800 hover:text-green-600 transition">
                        {{ $quotation->quotation_number }}
                    </a>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($quotation->client->name, 0, 1)) }}
                        </div>
                        <span class="text-gray-700 font-medium">{{ $quotation->client->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-gray-500 text-xs">
                    <div class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-300"></i>
                        {{ $quotation->issue_date->format('M j, Y') }}
                    </div>
                </td>
                <td class="px-5 py-3.5 text-xs">
                    @if($quotation->expiry_date)
                        @if($quotation->expiry_date->isPast() && $quotation->status !== 'converted')
                        <div class="flex items-center gap-1 text-red-500 font-medium">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $quotation->expiry_date->format('M j, Y') }}
                            <span class="text-xs">(Expired)</span>
                        </div>
                        @elseif($quotation->expiry_date->diffInDays(now()) <= 3 && $quotation->status !== 'converted')
                        <div class="flex items-center gap-1 text-orange-500 font-medium">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            {{ $quotation->expiry_date->format('M j, Y') }}
                        </div>
                        @else
                        <div class="flex items-center gap-1 text-gray-500">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-300"></i>
                            {{ $quotation->expiry_date->format('M j, Y') }}
                        </div>
                        @endif
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <span class="font-bold text-gray-800">Ksh {{ number_format($quotation->grand_total, 2) }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    @php
                        $statusConfig = [
                            'approved'  => ['bg-green-100 text-green-700', 'check-circle'],
                            'rejected'  => ['bg-red-100 text-red-600', 'x-circle'],
                            'sent'      => ['bg-blue-100 text-blue-600', 'send'],
                            'draft'     => ['bg-gray-100 text-gray-600', 'file'],
                            'converted' => ['bg-purple-100 text-purple-600', 'refresh-cw'],
                        ];
                        $config = $statusConfig[$quotation->status] ?? ['bg-gray-100 text-gray-600', 'file'];
                    @endphp
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium {{ $config[0] }}">
                        <i data-lucide="{{ $config[1] }}" class="w-3 h-3"></i>
                        {{ ucfirst($quotation->status) }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('quotations.show', $quotation) }}"
                            title="View"
                            class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 hover:text-blue-700 transition">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('quotations.download', $quotation) }}"
                            title="Download PDF"
                            class="p-1.5 rounded-lg hover:bg-green-50 text-green-600 hover:text-green-700 transition">
                            <i data-lucide="download" class="w-4 h-4"></i>
                        </a>
                        @if(!in_array($quotation->status, ['converted']))
                        <a href="{{ route('quotations.edit', $quotation) }}"
                            title="Edit"
                            class="p-1.5 rounded-lg hover:bg-yellow-50 text-yellow-500 hover:text-yellow-700 transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        @endif
                        @if($quotation->status === 'approved')
                        <form method="POST" action="{{ route('quotations.convert', $quotation) }}">
                            @csrf
                            <button type="submit"
                                title="Convert to Invoice"
                                class="p-1.5 rounded-lg hover:bg-purple-50 text-purple-500 hover:text-purple-700 transition">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="clipboard-list" class="w-6 h-6 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-medium">No quotations found</p>
                        <a href="{{ route('quotations.create') }}"
                            class="text-sm text-green-600 hover:underline flex items-center gap-1">
                            <i data-lucide="plus" class="w-4 h-4"></i> Create your first quotation
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($quotations->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Showing {{ $quotations->firstItem() }}–{{ $quotations->lastItem() }} of {{ $quotations->total() }} quotations
        </p>
        {{ $quotations->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection