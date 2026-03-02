@extends('layouts.app')
@section('title', 'Edit Quotation')
@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('quotations.show', $quotation) }}" class="text-gray-400 hover:text-gray-600">← Back</a>
    <h1 class="text-2xl font-bold text-gray-800">Edit Quotation {{ $quotation->quotation_number }}</h1>
</div>

@if($errors->any())
<div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
</div>
@endif

<form method="POST" action="{{ route('quotations.update', $quotation) }}" x-data="quotationEditForm()" class="space-y-6">
@csrf @method('PUT')

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white rounded-xl shadow p-5 space-y-4">
        <h2 class="font-semibold text-gray-700">Quotation Details</h2>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Client <span class="text-red-500">*</span></label>
            <select name="client_id" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $quotation->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Issue Date</label>
            <input type="date" name="issue_date" value="{{ $quotation->issue_date->format('Y-m-d') }}" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Expiry Date</label>
            <input type="date" name="expiry_date" value="{{ $quotation->expiry_date?->format('Y-m-d') }}"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ $quotation->notes }}</textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-gray-700">Line Items</h2>
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100">
                    + From Catalog
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 top-8 w-72 bg-white border rounded-xl shadow-lg z-10 max-h-80 overflow-y-auto">
                    @foreach($categories as $category)
                    <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                        {{ $category->name }}
                    </div>
                    @foreach($category->catalogItems as $catalogItem)
                    <button type="button"
                        @click="addCatalogItem({{ $catalogItem->id }}, '{{ addslashes($catalogItem->name) }}', {{ $catalogItem->default_unit_price }}, {{ $catalogItem->default_buying_price }}); open = false"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 hover:text-green-700">
                        {{ $catalogItem->name }}
                        <span class="text-gray-400 text-xs ml-1">Ksh {{ number_format($catalogItem->default_unit_price, 2) }}</span>
                    </button>
                    @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-2 mb-4">
            <template x-for="(item, index) in items" :key="index">
                <div class="border rounded-lg p-3 space-y-2 bg-gray-50">
                    <input type="hidden" :name="`items[${index}][catalog_item_id]`" :value="item.catalog_item_id">
                    
                    <!-- Description Row -->
                    <div class="flex gap-2">
                        <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                            placeholder="Item description" required
                            class="flex-1 border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                        <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                            <input type="checkbox" :name="`items[${index}][is_labour]`" value="1" x-model="item.is_labour"
                                class="accent-orange-500">
                            Labour
                        </label>
                        <button type="button" @click="removeItem(index)"
                            class="text-red-400 hover:text-red-600 text-lg font-bold">✕</button>
                    </div>
            
                    <!-- Pricing Row -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Qty</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity"
                                @input="calculateTotals" min="0.01" step="0.01" required
                                class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                        </div>
                        <div>
                            <label class="text-xs text-blue-500 font-medium">Buying Price (Ksh)</label>
                            <input type="number" :name="`items[${index}][buying_price]`" x-model.number="item.buying_price"
                                @input="calculateTotals" min="0" step="0.01" placeholder="0"
                                class="w-full border border-blue-200 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 bg-blue-50">
                        </div>
                        <div>
                            <label class="text-xs text-green-600 font-medium">Selling Price (Ksh)</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price"
                                @input="calculateTotals" min="0" step="0.01" required
                                class="w-full border border-green-200 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Sell Total</label>
                            <p class="px-3 py-1.5 text-sm font-semibold text-gray-700 bg-white border rounded">
                                Ksh <span x-text="((item.quantity||0) * (item.unit_price||0)).toLocaleString('en-KE', {minimumFractionDigits: 2})"></span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium"
                                :class="((item.unit_price||0) - (item.buying_price||0)) >= 0 ? 'text-green-600' : 'text-red-500'">
                                Profit
                                <span x-text="item.unit_price > 0 ? '(' + (((item.unit_price - item.buying_price) / item.unit_price) * 100).toFixed(1) + '%)' : ''"></span>
                            </label>
                            <p class="px-3 py-1.5 text-sm font-bold border rounded"
                                :class="((item.quantity||0)*((item.unit_price||0)-(item.buying_price||0))) >= 0 ? 'text-green-600 bg-green-50 border-green-200' : 'text-red-500 bg-red-50 border-red-200'">
                                Ksh <span x-text="((item.quantity||0)*((item.unit_price||0)-(item.buying_price||0))).toLocaleString('en-KE', {minimumFractionDigits: 2})"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <button type="button" @click="addItem"
            class="w-full border-2 border-dashed border-gray-300 text-gray-400 py-2 rounded-lg text-sm hover:border-green-400 hover:text-green-600">
            + Add Item Manually
        </button>

        <!-- Totals -->
        <div class="mt-6 space-y-2 border-t pt-4">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Material Cost</span>
                <span>Ksh <span x-text="materialCost.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Labour Cost</span>
                <span>Ksh <span x-text="labourCost.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                <span>Grand Total</span>
                <span class="text-green-600">Ksh <span x-text="grandTotal.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
            </div>

            <!-- Private Profit Summary -->
            <div class="mt-3 p-3 rounded-lg border space-y-2"
                :class="totalProfit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Profit Summary <span class="text-gray-400 font-normal">(private — not on PDF)</span></p>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Total Cost</span>
                    <span class="text-red-500 font-medium">Ksh <span x-text="totalCost.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <span>Total Profit</span>
                    <span :class="totalProfit >= 0 ? 'text-green-600' : 'text-red-500'">
                        Ksh <span x-text="totalProfit.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span>
                    </span>
                </div>
                <div class="flex justify-between text-sm font-bold">
                    <span>Overall Margin</span>
                    <span :class="overallMargin >= 0 ? 'text-green-600' : 'text-red-500'"
                        x-text="overallMargin.toFixed(1) + '%'">
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3 justify-end">
    <a href="{{ route('quotations.show', $quotation) }}"
        class="px-6 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
    <button type="submit"
        class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 font-medium">
        Update Quotation
    </button>
</div>

</form>

<script>
    function quotationEditForm() {
        return {
            items: @json($quotationItems),
            materialCost: {{ $quotation->material_cost }},
            labourCost: {{ $quotation->labour_cost }},
            grandTotal: {{ $quotation->grand_total }},
            totalCost: {{ $quotation->total_cost ?? 0 }},
            totalProfit: {{ $quotation->total_profit ?? 0 }},
            overallMargin: {{ $quotation->overall_margin ?? 0 }},
    
            addItem() {
                this.items.push({ catalog_item_id: null, description: '', quantity: 1, unit_price: 0, buying_price: 0, is_labour: false });
            },
    
            addCatalogItem(id, name, price, buyingPrice) {
                this.items.push({ catalog_item_id: id, description: name, quantity: 1, unit_price: price, buying_price: buyingPrice, is_labour: false });
                this.calculateTotals();
            },
    
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                }
            },
    
            calculateTotals() {
                this.materialCost = 0;
                this.labourCost   = 0;
                this.totalCost    = 0;
    
                this.items.forEach(item => {
                    const qty       = item.quantity     || 0;
                    const sell      = item.unit_price   || 0;
                    const buy       = item.buying_price || 0;
                    const sellTotal = qty * sell;
                    const costTotal = qty * buy;
                    this.totalCost += costTotal;
                    if (item.is_labour) {
                        this.labourCost += sellTotal;
                    } else {
                        this.materialCost += sellTotal;
                    }
                });
    
                this.grandTotal    = this.materialCost + this.labourCost;
                this.totalProfit   = this.grandTotal - this.totalCost;
                this.overallMargin = this.grandTotal > 0 ? (this.totalProfit / this.grandTotal) * 100 : 0;
            },
    
            init() {
                this.$watch('items', () => this.calculateTotals(), { deep: true });
            }
        }
    }
    </script>

@endsection