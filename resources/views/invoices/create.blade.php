@extends('layouts.app')
@section('title', 'New Invoice')
@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">New Invoice</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Left Column -->
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-semibold text-gray-700 mb-4">Invoice Details</h2>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" required
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Client <span
                                    class="text-red-500">*</span></label>
                            <select name="client_id" required
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('clients.create') }}" class="text-xs text-green-600 mt-1 inline-block">+ Add
                                new client</a>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Issue Date <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Due Date</label>
                            <input type="date" name="due_date"
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <!-- Recurring Options -->
                        <div class="border-t pt-4 mt-4" x-data="{ recurring: {{ old('is_recurring') ? 'true' : 'false' }} }">
                            <label class="flex items-center gap-2 cursor-pointer mb-4">
                                <input type="checkbox" name="is_recurring" value="1"
                                    x-model="recurring"
                                    {{ old('is_recurring') ? 'checked' : '' }}
                                    class="w-4 h-4 accent-green-600">
                                <span class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="repeat" class="w-4 h-4 text-blue-500"></i>
                                    Make this a recurring invoice
                                </span>
                            </label>

                            <div x-show="recurring" x-transition class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-4">
                                <p class="text-xs text-blue-600 font-medium flex items-center gap-1">
                                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                    A new draft invoice will be auto-generated at each interval
                                </p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Frequency</label>
                                        <select name="recurring_frequency"
                                            class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <option value="weekly"    {{ old('recurring_frequency') === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly"   {{ old('recurring_frequency', 'monthly') === 'monthly'   ? 'selected' : '' }}>Monthly</option>
                                            <option value="quarterly" {{ old('recurring_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly (Every 3 months)</option>
                                            <option value="yearly"    {{ old('recurring_frequency') === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">First Recurrence Date</label>
                                        <input type="date" name="recurring_next_date"
                                            value="{{ old('recurring_next_date') }}"
                                            class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            End Date <span class="text-gray-400 font-normal">(optional — leave blank to recur forever)</span>
                                        </label>
                                        <input type="date" name="recurring_ends_at"
                                            value="{{ old('recurring_ends_at') }}"
                                            class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    </div>
                                </div>
                            </div>
</div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-semibold text-gray-700 mb-4">Options</h2>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-700">ETR / Tax Invoice</p>
                                <p class="text-xs text-gray-400">Adds 16% VAT on material items</p>
                            </div>
                            <input type="hidden" name="etr_enabled" value="0">
                            <input type="checkbox" name="etr_enabled" value="1" x-model="etrEnabled"
                                class="w-5 h-5 accent-green-600">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Recurring Invoice</p>
                                <p class="text-xs text-gray-400">Auto-generate on a schedule</p>
                            </div>
                            <input type="checkbox" x-model="isRecurring" class="w-5 h-5 accent-green-600">
                        </label>
                        <div x-show="isRecurring" x-cloak>
                            <input type="hidden" name="is_recurring" :value="isRecurring ? 1 : 0">
                            <label class="block text-sm text-gray-600 mb-1">Recurrence Interval</label>
                            <select name="recurrence_interval"
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Line Items -->
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-700">Line Items</h2>
                    <!-- Catalog Picker -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100">
                            + From Catalog
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 top-8 w-72 bg-white border rounded-xl shadow-lg z-10 max-h-80 overflow-y-auto">
                            @foreach ($categories as $category)
                                <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">
                                    {{ $category->name }}
                                </div>
                                @foreach ($category->catalogItems as $catalogItem)
                                    <button type="button"
                                        @click="addCatalogItem({{ $catalogItem->id }}, '{{ addslashes($catalogItem->name) }}', {{ $catalogItem->default_unit_price }}, '{{ $catalogItem->unit_of_measure }}'); open = false"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 hover:text-green-700">
                                        {{ $catalogItem->name }}
                                        <span class="text-gray-400 text-xs ml-1">Ksh
                                            {{ number_format($catalogItem->default_unit_price, 2) }}</span>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="space-y-2 mb-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="border rounded-lg p-3 space-y-2">
                            <input type="hidden" :name="`items[${index}][catalog_item_id]`" :value="item.catalog_item_id">
                            <div class="flex gap-2">
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                    placeholder="Item description" required
                                    class="flex-1 border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                                    <input type="checkbox" :name="`items[${index}][is_labour]`" value="1"
                                        x-model="item.is_labour" class="accent-orange-500">
                                    Labour
                                </label>
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity" @input="calculateTotals" min="0.01"
                                        step="0.01" required
                                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Unit Price (Ksh)</label>
                                    <input type="number" :name="`items[${index}][unit_price]`"
                                        x-model.number="item.unit_price" @input="calculateTotals" min="0"
                                        step="0.01" required
                                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500">Total</label>
                                    <p class="px-3 py-1.5 text-sm font-semibold text-gray-700">
                                        Ksh <span
                                            x-text="(item.quantity * item.unit_price).toLocaleString('en-KE', {minimumFractionDigits: 2})"></span>
                                    </p>
                                </div>
                                <button type="button" @click="removeItem(index)"
                                    class="text-red-400 hover:text-red-600 mt-4 text-lg">✕</button>
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
                        <span>Ksh <span
                                x-text="materialCost.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Labour Cost</span>
                        <span>Ksh <span
                                x-text="labourCost.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                    </div>
                    <div x-show="etrEnabled" class="flex justify-between text-sm text-purple-600">
                        <span>VAT (16%)</span>
                        <span>Ksh <span
                                x-text="vatAmount.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                        <span>Grand Total</span>
                        <span class="text-green-600">Ksh <span
                                x-text="grandTotal.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('invoices.index') }}"
                class="px-6 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 font-medium">
                Create Invoice
            </button>
        </div>

    </form>

    <script>
        function invoiceForm() {
            return {
                items: [{
                    catalog_item_id: null,
                    description: '',
                    quantity: 1,
                    unit_price: 0,
                    is_labour: false
                }],
                etrEnabled: false,
                isRecurring: false,
                materialCost: 0,
                labourCost: 0,
                vatAmount: 0,
                grandTotal: 0,

                addItem() {
                    this.items.push({
                        catalog_item_id: null,
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        is_labour: false
                    });
                },

                addCatalogItem(id, name, price, unit) {
                    this.items.push({
                        catalog_item_id: id,
                        description: name,
                        quantity: 1,
                        unit_price: price,
                        is_labour: false
                    });
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
                    this.labourCost = 0;

                    this.items.forEach(item => {
                        const total = (item.quantity || 0) * (item.unit_price || 0);
                        if (item.is_labour) {
                            this.labourCost += total;
                        } else {
                            this.materialCost += total;
                        }
                    });

                    this.vatAmount = this.etrEnabled ? Math.round(this.materialCost * 0.16 * 100) / 100 : 0;
                    this.grandTotal = this.materialCost + this.labourCost + this.vatAmount;
                },

                init() {
                    this.$watch('etrEnabled', () => this.calculateTotals());
                    this.$watch('items', () => this.calculateTotals(), {
                        deep: true
                    });
                }
            }
        }
    </script>

@endsection
