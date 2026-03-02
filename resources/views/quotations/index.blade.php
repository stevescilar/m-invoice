@extends('layouts.app')
@section('title', 'Quotations')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Quotations</h1>
    <a href="{{ route('quotations.create') }}"
        class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
        + New Quotation
    </a>
</div>

<!-- Status Tabs -->
<div class="flex gap-2 mb-6 flex-wrap">
    @foreach(['all' => 'All', 'draft' => 'Draft', 'sent' => 'Sent', 'approved' => 'Approved', 'rejected' => 'Rejected', 'converted' => 'Converted'] as $key => $label)
    <a href="{{ route('quotations.index') }}?status={{ $key }}"
        class="px-4 py-1.5 rounded-full text-sm border
        {{ request('status', 'all') === $key ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400' }}">
        {{ $label }} <span class="ml-1 font-semibold">{{ $stats[$key] }}</span>
    </a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Quote #</th>
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Issue Date</th>
                <th class="px-6 py-3 text-left">Expiry Date</th>
                <th class="px-6 py-3 text-right">Amount</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($quotations as $quotation)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">
                    <a href="{{ route('quotations.show', $quotation) }}" class="hover:text-green-600">
                        {{ $quotation->quotation_number }}
                    </a>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $quotation->client->name }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $quotation->issue_date->format('M j, Y') }}</td>
                <td class="px-6 py-4 text-gray-500">
                    {{ $quotation->expiry_date?->format('M j, Y') ?? '—' }}
                    @if($quotation->expiry_date && $quotation->expiry_date->isPast() && $quotation->status !== 'converted')
                        <span class="text-red-500 text-xs ml-1">Expired</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right font-semibold">Ksh {{ number_format($quotation->grand_total, 2) }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-600' : '' }}
                        {{ $quotation->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $quotation->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $quotation->status === 'converted' ? 'bg-purple-100 text-purple-600' : '' }}">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center flex gap-2 justify-center">
                    <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-500 hover:underline">View</a>
                    @if(!in_array($quotation->status, ['converted']))
                    <a href="{{ route('quotations.edit', $quotation) }}" class="text-yellow-500 hover:underline">Edit</a>
                    @endif
                    <a href="{{ route('quotations.download', $quotation) }}" class="text-blue-500 hover:underline">PDF</a>                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                    No quotations yet. <a href="{{ route('quotations.create') }}" class="text-green-600">Create your first quotation</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $quotations->links() }}</div>
</div>

@endsection