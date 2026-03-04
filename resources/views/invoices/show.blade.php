@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800">{{ $invoice->invoice_number }}</h1>
            <span
                class="text-xs px-3 py-1 rounded-full
            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-600' : '' }}
            {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-600' : '' }}
            {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-600' : '' }}
            {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
            {{ $invoice->status === 'stalled' ? 'bg-yellow-100 text-yellow-600' : '' }}">
                {{ ucfirst($invoice->status) }}
            </span>
            @if ($invoice->etr_enabled)
                <span class="text-xs bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full">ETR</span>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('invoices.download', $invoice) }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                Download PDF
            </a>
            @if ($invoice->status !== 'paid')
                <a href="{{ route('invoices.edit', $invoice) }}"
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                    Edit
                </a>
            @endif
            <form action="{{ route('invoices.duplicate', $invoice) }}" method="POST">
                @csrf
                <button class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-sm hover:bg-blue-100">
                    Duplicate
                </button>
            </form>
        </div>
    </div>

   

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- Invoice Info -->
        <div class="bg-white rounded-xl shadow p-5 md:col-span-2">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase mb-1">Invoice For</p>
                    <p class="font-semibold text-gray-800">{{ $invoice->client->name }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->client->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase mb-1">Payable To</p>
                    <p class="font-semibold text-gray-800">{{ $company->name }}</p>
                    <p class="text-sm text-gray-500">{{ $company->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $company->email }}</p>
                    @if ($invoice->etr_enabled && $company->kra_pin)
                        <p class="text-sm text-gray-500">KRA PIN: {{ $company->kra_pin }}</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase">Issue Date</p>
                    <p class="font-medium">{{ $invoice->issue_date->format('M j, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Due Date</p>
                    <p class="font-medium {{ $invoice->isOverdue() ? 'text-red-500' : '' }}">
                        {{ $invoice->due_date?->format('M j, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase">Invoice #</p>
                    <p class="font-medium">{{ $invoice->invoice_number }}</p>
                </div>
            </div>

            <!-- Line Items -->
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
                    @foreach ($invoice->items as $item)
                        <tr class="{{ $item->is_labour ? 'bg-orange-50' : '' }}">
                            <td class="px-4 py-3">
                                {{ $item->description }}
                                @if ($item->is_labour)
                                    <span class="text-xs text-orange-500 ml-1">(Labour)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">Ksh {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium">Ksh {{ number_format($item->total_price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="space-y-2 border-t pt-4 max-w-xs ml-auto text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Material Cost</span>
                    <span>Ksh {{ number_format($invoice->material_cost, 2) }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Labour Cost</span>
                    <span>Ksh {{ number_format($invoice->labour_cost, 2) }}</span>
                </div>
                @if ($invoice->etr_enabled)
                    <div class="flex justify-between text-purple-600">
                        <span>VAT (16%)</span>
                        <span>Ksh {{ number_format($invoice->vat_amount, 2) }}</span>
                    </div>
                @endif
                
                <div class="flex justify-between font-bold text-gray-800 border-t pt-2 text-base">
                    <span>Grand Total</span>
                    <span class="sensitive">Ksh {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
            </div>

            @if ($invoice->notes)
                <div class="mt-4 border-t pt-4">
                    <p class="text-xs text-gray-400 uppercase mb-1">Notes</p>
                    <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                </div>
            @endif

            @if ($company->footer_message)
                <p class="text-center text-sm text-gray-400 mt-6 italic">{{ $company->footer_message }}</p>
            @endif
        </div>

        <!-- Actions Panel -->
        <div class="space-y-4">

            <!-- Status Actions -->
            @if ($invoice->status !== 'paid')
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold text-gray-700 mb-3">Actions</h3>

                    @if ($invoice->status === 'draft')
                        <form action="{{ route('invoices.send', $invoice) }}" method="POST" class="mb-2">
                            @csrf
                            <button class="w-full bg-blue-500 text-white py-2 rounded-lg text-sm hover:bg-blue-600">
                                Mark as Sent
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" x-data="{ open: false }">
                        @csrf
                        <button type="button" @click="open = !open"
                            class="w-full bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700 mb-2">
                            Mark as Paid
                        </button>
                        <div x-show="open" x-cloak class="mt-2 space-y-2">
                            <input type="text" name="mpesa_code" placeholder="M-Pesa code (optional)"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            <button type="submit"
                                class="w-full bg-green-700 text-white py-2 rounded-lg text-sm hover:bg-green-800">
                                Confirm Payment
                            </button>
                        </div>
                    </form>

                    <!-- Change to Stalled -->
                    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="mt-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="status_only" value="stalled">
                        <input type="hidden" name="client_id" value="{{ $invoice->client_id }}">
                        <input type="hidden" name="issue_date" value="{{ $invoice->issue_date->format('Y-m-d') }}">
                        @foreach ($invoice->items as $i => $item)
                            <input type="hidden" name="items[{{ $i }}][description]"
                                value="{{ $item->description }}">
                            <input type="hidden" name="items[{{ $i }}][quantity]"
                                value="{{ $item->quantity }}">
                            <input type="hidden" name="items[{{ $i }}][unit_price]"
                                value="{{ $item->unit_price }}">
                            <input type="hidden" name="items[{{ $i }}][is_labour]"
                                value="{{ $item->is_labour ? 1 : 0 }}">
                        @endforeach
                    </form>
                </div>
            @endif

            @if ($invoice->status === 'paid')
                <div class="bg-green-50 rounded-xl p-5 border border-green-200">
                    <p class="text-green-700 font-semibold text-sm">✅ Paid</p>
                    <p class="text-xs text-green-600 mt-1">{{ $invoice->paid_at?->format('M j, Y g:i A') }}</p>
                    @if ($invoice->mpesa_code)
                        <p class="text-xs text-green-600">M-Pesa: {{ $invoice->mpesa_code }}</p>
                    @endif
                </div>
            @endif
            
            @if($invoice->is_recurring)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-4">
                <p class="text-sm font-semibold text-blue-700 flex items-center gap-2 mb-3">
                    <i data-lucide="repeat" class="w-4 h-4"></i>
                    Recurring Invoice
                    <span class="text-xs font-normal text-blue-500 ml-1">
                        {{ ucfirst($invoice->recurring_frequency) }} ·
                        Next: {{ $invoice->recurring_next_date?->format('M j, Y') ?? '—' }}
                        {{ $invoice->recurring_ends_at ? '· Ends: ' . $invoice->recurring_ends_at->format('M j, Y') : '· No end date' }}
                    </span>
                </p>
                <div class="flex gap-2">
                    @if($invoice->recurring_active)
                    <form method="POST" action="{{ route('invoices.recurring.pause', $invoice) }}">
                        @csrf
                        <button class="flex items-center gap-1.5 px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-medium hover:bg-yellow-200">
                            <i data-lucide="pause-circle" class="w-3.5 h-3.5"></i> Pause
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('invoices.recurring.resume', $invoice) }}">
                        @csrf
                        <button class="flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200">
                            <i data-lucide="play-circle" class="w-3.5 h-3.5"></i> Resume
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('invoices.recurring.cancel', $invoice) }}"
                        onsubmit="return confirm('Cancel recurring? No more invoices will be generated.')">
                        @csrf
                        <button class="flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-600 rounded-lg text-xs font-medium hover:bg-red-200">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Cancel Recurring
                        </button>
                    </form>
                </div>
            </div>
            @endif
            <!-- Invoice Meta -->
            <div class="bg-white rounded-xl shadow p-5 text-sm space-y-2">
                <h3 class="font-semibold text-gray-700 mb-2">Details</h3>
                <div class="flex justify-between text-gray-500">
                    <span>Created by</span>
                    <span>{{ $invoice->createdBy->name }}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Created</span>
                    <span>{{ $invoice->created_at->format('M j, Y') }}</span>
                </div>
                @if ($invoice->is_recurring)
                    <div class="flex justify-between text-gray-500">
                        <span>Recurrence</span>
                        <span>{{ ucfirst($invoice->recurrence_interval) }}</span>
                    </div>
                @endif
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-semibold text-red-500 mb-3 text-sm">Danger Zone</h3>
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                    onsubmit="return confirm('Delete this invoice permanently?')">
                    @csrf @method('DELETE')
                    <button class="w-full border border-red-300 text-red-500 py-2 rounded-lg text-sm hover:bg-red-50">
                        Delete Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection
