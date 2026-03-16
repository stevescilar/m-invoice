@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quotation_number)
@section('content')

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('quotations.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $quotation->quotation_number }}</h1>
        <span class="text-xs px-3 py-1 rounded-full font-medium
            {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-600' : '' }}
            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-600' : '' }}
            {{ $quotation->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
            {{ $quotation->status === 'draft' ? 'bg-yellow-100 text-yellow-600' : '' }}
            {{ $quotation->status === 'converted' ? 'bg-purple-100 text-purple-600' : '' }}">
            {{ ucfirst($quotation->status) }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('quotations.download', $quotation) }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            Download PDF
        </a>
        @if(!in_array($quotation->status, ['converted']))
        <a href="{{ route('quotations.edit', $quotation) }}"
            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            Edit
        </a>
        @endif
        <form method="POST" action="{{ route('quotations.duplicate', $quotation) }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium transition">
                <i data-lucide="copy" class="w-4 h-4"></i> Duplicate
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Quotation Details -->
    <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-xs text-gray-400 uppercase mb-1">Quotation For</p>
                <p class="font-semibold text-gray-800">{{ $quotation->client->name }}</p>
                <p class="text-sm text-gray-500">{{ $quotation->client->phone }}</p>
                <p class="text-sm text-gray-500">{{ $quotation->client->email }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 uppercase mb-1">From</p>
                <p class="font-semibold text-gray-800">{{ $company->name }}</p>
                <p class="text-sm text-gray-500">{{ $company->phone }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase">Issue Date</p>
                <p class="font-medium">{{ $quotation->issue_date->format('M j, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Expiry Date</p>
                <p class="font-medium {{ $quotation->expiry_date?->isPast() ? 'text-red-500' : '' }}">
                    {{ $quotation->expiry_date?->format('M j, Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase">Quote #</p>
                <p class="font-medium">{{ $quotation->quotation_number }}</p>
            </div>
        </div>

        <!-- Items -->
        <table class="w-full text-sm mb-4">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-center">Qty</th>
                    <th class="px-4 py-2 text-right">Unit Price</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($quotation->items as $item)
                <tr class="{{ $item->is_labour ? 'bg-orange-50' : '' }}">
                    <td class="px-4 py-3">
                        {{ $item->description }}
                        @if($item->is_labour)
                            <span class="text-xs text-orange-500 ml-1">(Labour)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right">Ksh {{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-4 py-3 text-right font-medium">Ksh {{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="space-y-2 border-t pt-4 max-w-xs ml-auto text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Material Cost</span>
                <span>Ksh {{ number_format($quotation->material_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Labour Cost</span>
                <span>Ksh {{ number_format($quotation->labour_cost, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 border-t pt-2 text-base">
                <span>Grand Total</span>
                <span class="text-green-600">Ksh {{ number_format($quotation->grand_total, 2) }}</span>
            </div>
        </div>

        @if($quotation->notes)
        <div class="mt-4 border-t pt-4">
            <p class="text-xs text-gray-400 uppercase mb-1">Notes</p>
            <p class="text-sm text-gray-600">{{ $quotation->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Actions Panel -->
    <div class="space-y-4">

        @if(!in_array($quotation->status, ['converted', 'rejected']))
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Actions</h3>
            
            @if($quotation->status === 'draft')
            <form action="{{ route('quotations.send', $quotation) }}" method="POST" class="mb-2">
                @csrf
                <button class="w-full bg-blue-500 text-white py-2 rounded-lg text-sm hover:bg-blue-600">
                    Send Quotation
                </button>
            </form>
            @endif

            @if(in_array($quotation->status, ['sent', 'draft']))
            <form action="{{ route('quotations.update', $quotation) }}" method="POST" class="mb-2">
                @csrf @method('PUT')
                <input type="hidden" name="client_id" value="{{ $quotation->client_id }}">
                <input type="hidden" name="issue_date" value="{{ $quotation->issue_date->format('Y-m-d') }}">
                <input type="hidden" name="status_change" value="approved">
                @foreach($quotation->items as $i => $item)
                    <input type="hidden" name="items[{{ $i }}][description]" value="{{ $item->description }}">
                    <input type="hidden" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}">
                    <input type="hidden" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}">
                    <input type="hidden" name="items[{{ $i }}][is_labour]" value="{{ $item->is_labour ? 1 : 0 }}">
                @endforeach
                <button class="w-full bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700 mb-2">
                    Mark as Approved
                </button>
            </form>

            <form action="{{ route('quotations.convert', $quotation) }}" method="POST"
                onsubmit="return confirm('Convert this quotation to an invoice?')">
                @csrf
                <button class="w-full bg-purple-600 text-white py-2 rounded-lg text-sm hover:bg-purple-700">
                    Convert to Invoice
                </button>
            </form>
            @endif
        </div>
        @endif

        @if($quotation->status === 'converted' && $quotation->convertedInvoice)
        <div class="bg-purple-50 rounded-xl p-5 border border-purple-200">
            <p class="text-purple-700 font-semibold text-sm">Converted to Invoice</p>
            <a href="{{ route('invoices.show', $quotation->convertedInvoice) }}"
                class="text-sm text-purple-600 hover:underline mt-1 inline-block">
                View {{ $quotation->convertedInvoice->invoice_number }}
            </a>
        </div>
        @endif

        <!-- Danger Zone -->
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-red-500 mb-3 text-sm">Danger Zone</h3>
            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST"
                onsubmit="return confirm('Delete this quotation permanently?')">
                @csrf @method('DELETE')
                <button class="w-full border border-red-300 text-red-500 py-2 rounded-lg text-sm hover:bg-red-50">
                    Delete Quotation
                </button>
            </form>
        </div>
    </div>
</div>

@endsection