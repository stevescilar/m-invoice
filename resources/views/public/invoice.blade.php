@php $brandColor = $company->primary_color ?? '#16a34a'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} — {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">

    <div class="max-w-4xl mx-auto">
        {{-- Top Action Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Online Invoice Portal</p>
                <h1 class="text-xl font-extrabold text-gray-800">{{ $company->name }}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('public.invoice.pdf', $invoice->public_token) }}"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 shadow-sm transition">
                    ⬇ Download Invoice PDF
                </a>
                @if($invoice->totalPaidAmount() > 0)
                <a href="{{ route('public.invoice.receipt', $invoice->public_token) }}"
                    class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-100 transition">
                    🧾 Download Payment Receipt
                </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        {{-- Main Invoice Card --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Header Banner --}}
            <div class="p-6 sm:p-8 border-b-4" style="border-color: {{ $brandColor }};">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="h-14 object-contain mb-2">
                        @endif
                        <h2 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $company->address }}</p>
                        <p class="text-sm text-gray-500">{{ $company->phone }} {{ $company->email ? '· ' . $company->email : '' }}</p>
                        @if($invoice->etr_enabled && $company->kra_pin)
                            <p class="text-xs text-gray-500 mt-1 font-medium">KRA PIN: {{ $company->kra_pin }}</p>
                        @endif
                    </div>
                    <div class="sm:text-right">
                        <div class="inline-flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-gray-900">INVOICE</span>
                            <span class="text-xs px-3 py-1 rounded-full font-bold uppercase
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->isPartiallyPaid() ? 'bg-amber-100 text-amber-700' : ($invoice->isOverdue() ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700')) }}">
                                {{ $invoice->isPartiallyPaid() ? 'Partially Paid' : $invoice->status }}
                            </span>
                        </div>
                        <p class="text-lg font-bold text-gray-700 mt-1">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-500 mt-1">Issued: {{ $invoice->issue_date->format('d M Y') }}</p>
                        @if($invoice->due_date)
                            <p class="text-xs {{ $invoice->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                Due: {{ $invoice->due_date->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                {{-- Bill To & Balance Banner --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400 mb-1">Billed To</p>
                        <p class="text-base font-bold text-gray-800">{{ $invoice->client->name }}</p>
                        @if($invoice->client->phone)
                            <p class="text-sm text-gray-500">{{ $invoice->client->phone }}</p>
                        @endif
                        @if($invoice->client->email)
                            <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                        @endif
                        @if($invoice->client->address)
                            <p class="text-sm text-gray-500">{{ $invoice->client->address }}</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 flex flex-col justify-center md:text-right">
                        <p class="text-xs uppercase font-bold text-gray-400">Balance Due</p>
                        <p class="text-2xl font-extrabold {{ $invoice->balanceDue() > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Ksh {{ number_format($invoice->balanceDue(), 2) }}
                        </p>
                        @if($invoice->totalPaidAmount() > 0)
                            <p class="text-xs text-green-600 font-medium mt-1">
                                Paid to date: Ksh {{ number_format($invoice->totalPaidAmount(), 2) }} of Ksh {{ number_format($invoice->grand_total, 2) }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-y border-gray-200">
                            <tr>
                                <th class="py-3 px-4 text-left">Description</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="py-3 px-4 font-medium text-gray-800">
                                    {{ $item->description }}
                                    @if($item->is_labour)
                                        <span class="text-xs text-orange-500 ml-1">(Labour)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center text-gray-600">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                <td class="py-3 px-4 text-right text-gray-600">Ksh {{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-800">Ksh {{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals & Payment Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-200">
                    {{-- Payment Instructions & Confirm Form --}}
                    <div class="space-y-4">
                        @if($company->mpesa_paybill || $company->mpesa_till || $company->mpesa_number || $company->bank_name)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <h3 class="text-xs font-bold uppercase text-green-800 tracking-wide mb-2">How to Pay</h3>
                            <div class="space-y-1.5 text-sm text-gray-700">
                                @if($company->mpesa_paybill)
                                    <p>💚 <strong>M-Pesa Paybill:</strong> <span class="font-mono font-bold text-gray-900">{{ $company->mpesa_paybill }}</span> · Account: <span class="font-mono font-bold text-gray-900">{{ $company->mpesa_account ?? $invoice->invoice_number }}</span></p>
                                @endif
                                @if($company->mpesa_till)
                                    <p>💚 <strong>M-Pesa Buy Goods Till:</strong> <span class="font-mono font-bold text-gray-900">{{ $company->mpesa_till }}</span></p>
                                @endif
                                @if($company->mpesa_number)
                                    <p>📱 <strong>M-Pesa Send Money:</strong> <span class="font-mono font-bold text-gray-900">{{ $company->mpesa_number }}</span></p>
                                @endif
                                @if($company->bank_name)
                                    <p>🏦 <strong>Bank ({{ $company->bank_name }}):</strong> Acc <span class="font-mono font-bold text-gray-900">{{ $company->bank_account }}</span> {{ $company->bank_branch ? '(' . $company->bank_branch . ')' : '' }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($invoice->balanceDue() > 0)
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4" x-data="{ openPay: false }">
                            <button type="button" @click="openPay = !openPay"
                                class="w-full bg-green-600 text-white py-2.5 px-4 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                                💳 Already Paid? Submit M-Pesa / Bank Reference
                            </button>
                            <form x-show="openPay" method="POST" action="{{ route('public.invoice.notify-payment', $invoice->public_token) }}" class="mt-3 space-y-2.5">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">M-Pesa Code / Transaction Ref *</label>
                                    <input type="text" name="mpesa_code" required placeholder="e.g. QKH45T9XYZ"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Amount Paid (Ksh)</label>
                                    <input type="number" step="0.01" name="amount" value="{{ $invoice->balanceDue() }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Note (Optional)</label>
                                    <input type="text" name="notes" placeholder="e.g. Deposit payment"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <button type="submit" class="w-full bg-gray-900 text-white py-2 rounded-lg text-sm font-semibold hover:bg-gray-800">
                                    Submit Payment Confirmation
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Totals Summary --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Material Cost</span>
                            <span>Ksh {{ number_format($invoice->material_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Labour Cost</span>
                            <span>Ksh {{ number_format($invoice->labour_cost, 2) }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>- Ksh {{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($invoice->etr_enabled && $invoice->vat_amount > 0)
                        <div class="flex justify-between text-purple-600">
                            <span>VAT (16%)</span>
                            <span>Ksh {{ number_format($invoice->vat_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 text-base border-t pt-2">
                            <span>Grand Total</span>
                            <span>Ksh {{ number_format($invoice->grand_total, 2) }}</span>
                        </div>
                        @if($invoice->totalPaidAmount() > 0)
                        <div class="flex justify-between text-green-600 font-medium">
                            <span>Amount Paid</span>
                            <span>- Ksh {{ number_format($invoice->totalPaidAmount(), 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-base border-t border-dashed pt-2 {{ $invoice->balanceDue() > 0 ? 'text-red-600' : 'text-green-600' }}">
                            <span>Balance Due</span>
                            <span>Ksh {{ number_format($invoice->balanceDue(), 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($invoice->notes)
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Notes &amp; Terms</p>
                    <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            {{ $company->footer_message ?? 'Thank you for your business!' }} · Powered by Invoxa
        </p>
    </div>

</body>
</html>
