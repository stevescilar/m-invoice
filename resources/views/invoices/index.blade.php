@extends('layouts.app')
@section('title', 'Invoices')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Invoices</h1>
        <a href="{{ route('invoices.create') }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
            + New Invoice
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach (['all' => 'All', 'draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid', 'overdue' => 'Overdue', 'stalled' => 'Stalled'] as $key => $label)
            <a href="{{ route('invoices.index') }}?status={{ $key }}"
                class="px-4 py-1.5 rounded-full text-sm border
        {{ request('status', 'all') === $key ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-green-400' }}">
                {{ $label }}
                <span class="ml-1 font-semibold">{{ $stats[$key] }}</span>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Invoice #</th>
                    <th class="px-6 py-3 text-left">Client</th>
                    <th class="px-6 py-3 text-left">Issue Date</th>
                    <th class="px-6 py-3 text-left">Due Date</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <a href="{{ route('invoices.show', $invoice) }}" class="hover:text-green-600">
                                {{ $invoice->invoice_number }}
                            </a>
                            @if ($invoice->is_recurring)
                                <span class="ml-1 text-xs text-blue-500">↻</span>
                            @endif
                            @if ($invoice->etr_enabled)
                                <span class="ml-1 text-xs text-purple-500">ETR</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $invoice->client->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $invoice->issue_date->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $invoice->due_date?->format('M j, Y') ?? '—' }}
                            @if ($invoice->due_date && $invoice->status !== 'paid' && $invoice->due_date->isPast())
                                <span class="text-red-500 text-xs ml-1">Overdue</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">Ksh {{ number_format($invoice->grand_total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
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
                        <td class="px-6 py-4 text-center flex gap-2 justify-center">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500 hover:underline">View</a>
                            <a href="{{ route('invoices.download', $invoice) }}"
                                class="text-green-600 hover:underline">PDF</a>
                            @if ($invoice->status !== 'paid')
                                <a href="{{ route('invoices.edit', $invoice) }}"
                                    class="text-yellow-500 hover:underline">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            No invoices yet. <a href="{{ route('invoices.create') }}" class="text-green-600">Create your
                                first invoice</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $invoices->links() }}</div>
    </div>

@endsection
