@php $brandColor = $company->primary_color ?? '#16a34a'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }} — {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">

    <div class="max-w-4xl mx-auto">
        {{-- Top Action Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Online Quotation Portal</p>
                <h1 class="text-xl font-extrabold text-gray-800">{{ $company->name }}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('public.quotation.pdf', $quotation->public_token) }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm transition">
                    ⬇ Download Quotation PDF
                </a>
                @if($quotation->convertedInvoice)
                <a href="{{ $quotation->convertedInvoice->public_url }}"
                    class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-700 shadow-sm transition">
                    View Invoice {{ $quotation->convertedInvoice->invoice_number }}
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

        {{-- Main Quotation Card --}}
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
                    </div>
                    <div class="sm:text-right">
                        <div class="inline-flex items-center gap-2">
                            <span class="text-2xl font-black tracking-tight text-gray-900">QUOTATION</span>
                            <span class="text-xs px-3 py-1 rounded-full font-bold uppercase
                                {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-700' : ($quotation->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700') }}">
                                {{ $quotation->status }}
                            </span>
                        </div>
                        <p class="text-lg font-bold text-gray-700 mt-1">{{ $quotation->quotation_number }}</p>
                        <p class="text-xs text-gray-500 mt-1">Issued: {{ $quotation->issue_date->format('d M Y') }}</p>
                        @if($quotation->expiry_date)
                            <p class="text-xs {{ $quotation->expiry_date->isPast() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                Valid Until: {{ $quotation->expiry_date->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                {{-- Prepared For & Total --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400 mb-1">Prepared For</p>
                        <p class="text-base font-bold text-gray-800">{{ $quotation->client->name }}</p>
                        @if($quotation->client->phone)
                            <p class="text-sm text-gray-500">{{ $quotation->client->phone }}</p>
                        @endif
                        @if($quotation->client->email)
                            <p class="text-sm text-gray-500">{{ $quotation->client->email }}</p>
                        @endif
                        @if($quotation->client->address)
                            <p class="text-sm text-gray-500">{{ $quotation->client->address }}</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 flex flex-col justify-center md:text-right">
                        <p class="text-xs uppercase font-bold text-gray-400">Quoted Total</p>
                        <p class="text-2xl font-extrabold text-green-600">
                            Ksh {{ number_format($quotation->grand_total, 2) }}
                        </p>
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
                            @foreach($quotation->items as $item)
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

                {{-- Client Approval & Totals --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-200">
                    <div>
                        @if(!in_array($quotation->status, ['approved', 'converted']))
                        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                            <h3 class="text-sm font-bold text-green-900 mb-1">Ready to Proceed?</h3>
                            <p class="text-xs text-green-700 mb-3">Approve this quotation online and we will get started right away.</p>
                            <form method="POST" action="{{ route('public.quotation.respond', $quotation->public_token) }}" class="space-y-2.5">
                                @csrf
                                <input type="text" name="comment" placeholder="Optional note or instruction..."
                                    class="w-full border border-green-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <div class="flex gap-2">
                                    <button type="submit" name="action" value="approve"
                                        class="flex-1 bg-green-600 text-white py-2.5 px-4 rounded-lg text-sm font-bold hover:bg-green-700 transition">
                                        ✅ Approve Quotation
                                    </button>
                                    <button type="submit" name="action" value="reject"
                                        onclick="return confirm('Decline this quotation?')"
                                        class="px-4 py-2.5 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                        Decline
                                    </button>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                            <p class="text-green-800 font-bold text-sm">✅ Quotation Approved</p>
                            <p class="text-xs text-green-700 mt-1">
                                Thank you! This quotation has been approved{{ $quotation->approved_at ? ' on ' . $quotation->approved_at->format('d M Y, g:i A') : '' }}.
                            </p>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Material Cost</span>
                            <span>Ksh {{ number_format($quotation->material_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Labour Cost</span>
                            <span>Ksh {{ number_format($quotation->labour_cost, 2) }}</span>
                        </div>
                        @if($quotation->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>- Ksh {{ number_format($quotation->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 text-base border-t pt-2">
                            <span>Grand Total</span>
                            <span class="text-green-600">Ksh {{ number_format($quotation->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($quotation->notes)
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Notes &amp; Terms</p>
                    <p class="text-sm text-gray-600">{{ $quotation->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            {{ $company->footer_message ?? 'Thank you for considering our services!' }} · Powered by Invoxa
        </p>
    </div>

</body>
</html>

