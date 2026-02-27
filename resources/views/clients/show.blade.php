@extends('layouts.app')
@section('title', $client->name)
@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $client->name }}</h1>
        @if ($client->is_flagged)
            <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">⚑ Flagged</span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Client Info -->
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Contact Details</h2>
            <p class="text-sm text-gray-500 mb-2">📞 {{ $client->phone ?? 'No phone' }}</p>
            <p class="text-sm text-gray-500 mb-2">✉️ {{ $client->email ?? 'No email' }}</p>
            <p class="text-sm text-gray-500 mb-4">📍 {{ $client->address ?? 'No address' }}</p>
            <a href="{{ route('clients.edit', $client) }}"
                class="text-sm bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200">Edit Client</a>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Financial Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Billed</span>
                    <span class="font-semibold">Ksh {{ number_format($client->totalBilled(), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Paid</span>
                    <span class="font-semibold text-green-600">Ksh {{ number_format($client->totalPaid(), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm border-t pt-2">
                    <span class="text-gray-500">Outstanding</span>
                    <span class="font-bold {{ $client->outstandingBalance() > 0 ? 'text-red-500' : 'text-green-600' }}">
                        Ksh {{ number_format($client->outstandingBalance(), 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}"
                    class="block w-full text-center bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700">
                    + New Invoice
                </a>
                <a href="{{ route('quotations.create') }}?client_id={{ $client->id }}"
                    class="block w-full text-center bg-blue-500 text-white py-2 rounded-lg text-sm hover:bg-blue-600">
                    + New Quotation
                </a>
            </div>
        </div>
    </div>

    <!-- Invoices -->
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Invoices</h2>
        <table class="w-full text-sm">
            <thead class="text-gray-500 text-xs uppercase border-b">
                <tr>
                    <th class="pb-2 text-left">Invoice #</th>
                    <th class="pb-2 text-left">Date</th>
                    <th class="pb-2 text-left">Due</th>
                    <th class="pb-2 text-right">Amount</th>
                    <th class="pb-2 text-center">Status</th>
                    <th class="pb-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="py-3">{{ $invoice->invoice_number }}</td>
                        <td class="py-3">{{ $invoice->issue_date->format('M j, Y') }}</td>
                        <td class="py-3">{{ $invoice->due_date?->format('M j, Y') ?? '—' }}</td>
                        <td class="py-3 text-right font-medium">Ksh {{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="py-3 text-center">
                            <span
                                class="text-xs px-2 py-0.5 rounded-full
                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-600' : '' }}
                        {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $invoice->status === 'stalled' ? 'bg-yellow-100 text-yellow-600' : '' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-center">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-400">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>

@endsection
