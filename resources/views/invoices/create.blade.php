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

    {{-- Pass item types to Alpine --}}
    <script>
        const itemTypes = @json($itemTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'color' => $t->color]));
        const defaultTypeId = {{ $itemTypes->firstWhere('is_default', true)?->id ?? 'null' }};
    </script>

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
                            <label class="block text-sm text-gray-600 mb-1">Client <span class="text-red-500">*</span></label>
                            <select name="client_id" required
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('clients.create') }}" class="text-xs text-green-600 mt-1 inline-block">+ Add new client</a>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Issue Date <span class="text-red-500">*</span></label>
                            <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Due Date</label>
                            <input type="date" name="due_date"
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
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
                        <!-- ETR Toggle -->
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-700">ETR / Tax Invoice</p>
                                <p class="text-xs text-gray-400">Adds 16% VAT on material items</p>
                            </div>
                            <input type="hidden" name="etr_enabled" value="0">
                            <input type="checkbox" name="etr_enabled" value="1" x-model="etrEnabled"
                                class="w-5 h-5 accent-green-600">
                        </label>
                    </div>
                </div>

                <!-- Recurring Options -->
                <div class="bg-white rounded-xl shadow p-5" x-data="{ recurring: {{ old('is_recurring') ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-2 cursor-pointer mb-2">
                        <input type="checkbox" name="is_recurring" value="1"
                            x-model="recurring"
                            class="w-4 h-4 accent-green-600">
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="repeat" class="w-4 h-4 text-blue-500"></i>
                            Make this a recurring invoice
                        </span>
                    </label>

                    <div x-show="recurring" x-transition class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-4 mt-3">
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
                                    <option value="monthly"   {{ old('recurring_frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('recurring_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
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
                                        @click="addCatalogItem({{ $catalogItem->id }}, '{{ addslashes($catalogItem->name) }}', {{ $catalogItem->default_unit_price }}); open = false"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 hover:text-green-700">
                                        {{ $catalogItem->name }}
                                        <span class="text-gray-400 text-xs ml-1">Ksh {{ number_format($catalogItem->default_unit_price, 2) }}</span>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="space-y-3 mb-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="border rounded-xl p-3 space-y-2 bg-gray-50">
                            <input type="hidden" :name="`items[${index}][catalog_item_id]`" :value="item.catalog_item_id">
                            <input type="hidden" :name="`items[${index}][is_labour]`" :value="item.is_labour ? 1 : 0">

                            <!-- Description row -->
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                placeholder="Item description" required
                                class="w-full border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">

                            <!-- Type + Qty + Price + Total -->
                            <div class="flex gap-2 items-end">
                                <!-- Item Type dropdown -->
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-0.5 block">Type</label>
                                    <select :name="`items[${index}][item_type_id]`" x-model="item.item_type_id"
                                        @change="onTypeChange(item)"
                                        class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                                        <template x-for="type in itemTypes" :key="type.id">
                                            <option :value="type.id" x-text="type.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <!-- Qty -->
                                <div style="width:70px">
                                    <label class="text-xs text-gray-500 mb-0.5 block">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`"
                                        x-model.number="item.quantity" @input="calculateTotals"
                                        min="0.01" step="0.01" required
                                        class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                </div>
                                <!-- Unit Price -->
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-0.5 block">Unit Price (Ksh)</label>
                                    <input type="number" :name="`items[${index}][unit_price]`"
                                        x-model.number="item.unit_price" @input="calculateTotals"
                                        min="0" step="0.01" required
                                        class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                </div>
                                @if(auth()->user()->isOwner())
                                <!-- Buying Price -->
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-0.5 block">Buying (Ksh)</label>
                                    <input type="number" :name="`items[${index}][buying_price]`"
                                        x-model.number="item.buying_price" @input="calculateTotals"
                                        min="0" step="0.01"
                                        class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400">
                                </div>
                                @endif
                                <!-- Total display -->
                                <div style="width:90px">
                                    <label class="text-xs text-gray-500 mb-0.5 block">Total</label>
                                    <p class="px-2 py-1.5 text-sm font-semibold text-gray-700 bg-white border rounded-lg">
                                        <span x-text="(item.quantity * item.unit_price).toLocaleString('en-KE', {minimumFractionDigits: 0})"></span>
                                    </p>
                                </div>
                                <!-- Remove -->
                                <button type="button" @click="removeItem(index)"
                                    class="text-red-400 hover:text-red-600 pb-1 text-lg flex-shrink-0">✕</button>
                            </div>

                            <!-- Color dot showing type -->
                            <div class="flex items-center gap-1.5 mt-1">
                                <div class="w-2.5 h-2.5 rounded-full" :style="`background: ${getTypeColor(item.item_type_id)}`"></div>
                                <span class="text-xs text-gray-400" x-text="getTypeName(item.item_type_id)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addItem"
                    class="w-full border-2 border-dashed border-gray-300 text-gray-400 py-2 rounded-lg text-sm hover:border-green-400 hover:text-green-600">
                    + Add Item
                </button>

                <!-- Totals -->
                <div class="mt-6 space-y-2 border-t pt-4">
                    <template x-for="(amount, typeName) in typeBreakdown" :key="typeName">
                        <div class="flex justify-between text-sm text-gray-600" x-show="amount > 0">
                            <span x-text="typeName"></span>
                            <span>Ksh <span x-text="amount.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                        </div>
                    </template>
                    <div x-show="etrEnabled" class="flex justify-between text-sm text-purple-600">
                        <span>VAT (16%)</span>
                        <span>Ksh <span x-text="vatAmount.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                        <span>Grand Total</span>
                        <span class="text-green-600">Ksh <span x-text="grandTotal.toLocaleString('en-KE', {minimumFractionDigits: 2})"></span></span>
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
                    buying_price: 0,
                    item_type_id: defaultTypeId,
                    is_labour: false,
                }],
                etrEnabled: false,
                typeBreakdown: {},
                vatAmount: 0,
                grandTotal: 0,

                getTypeColor(id) {
                    const t = itemTypes.find(t => t.id == id);
                    return t ? t.color : '#6b7280';
                },

                getTypeName(id) {
                    const t = itemTypes.find(t => t.id == id);
                    return t ? t.name : '';
                },

                onTypeChange(item) {
                    // Keep is_labour in sync for backward compatibility
                    const t = itemTypes.find(t => t.id == item.item_type_id);
                    item.is_labour = t ? t.name.toLowerCase() === 'labour' : false;
                    this.calculateTotals();
                },

                addItem() {
                    this.items.push({
                        catalog_item_id: null,
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        buying_price: 0,
                        item_type_id: defaultTypeId,
                        is_labour: false,
                    });
                },

                addCatalogItem(id, name, price) {
                    this.items.push({
                        catalog_item_id: id,
                        description: name,
                        quantity: 1,
                        unit_price: price,
                        buying_price: 0,
                        item_type_id: defaultTypeId,
                        is_labour: false,
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
                    const breakdown = {};

                    this.items.forEach(item => {
                        const total = (item.quantity || 0) * (item.unit_price || 0);
                        const typeName = this.getTypeName(item.item_type_id) || 'Material';
                        breakdown[typeName] = (breakdown[typeName] || 0) + total;
                    });

                    this.typeBreakdown = breakdown;

                    // VAT applies on Material type only
                    const materialTotal = breakdown['Material'] || 0;
                    this.vatAmount = this.etrEnabled ? Math.round(materialTotal * 0.16 * 100) / 100 : 0;

                    const subtotal = Object.values(breakdown).reduce((a, b) => a + b, 0);
                    this.grandTotal = subtotal + this.vatAmount;
                },

                init() {
                    this.$watch('etrEnabled', () => this.calculateTotals());
                    this.$watch('items', () => this.calculateTotals(), { deep: true });
                }
            }
        }
    </script>

@endsection