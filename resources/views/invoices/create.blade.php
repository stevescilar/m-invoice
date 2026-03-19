@extends('layouts.app')
@section('title', 'New Invoice')
@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">New Invoice</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, d M Y • H:i') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    {{-- Item types must be defined BEFORE x-data initialises --}}
    <script>
        const itemTypes     = @json($itemTypes->map(fn($t) => ['id' => (string)$t->id, 'name' => $t->name, 'color' => $t->color]));
        const defaultTypeId = "{{ (string)($itemTypes->firstWhere('is_default', true)?->id ?? $itemTypes->first()?->id ?? '') }}";
    </script>

    <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- ── LEFT COLUMN ── --}}
            <div class="space-y-4">

                {{-- Invoice Details --}}
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
                        <div class="grid grid-cols-2 gap-3">
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
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Options --}}
                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="font-semibold text-gray-700 mb-3">Options</h2>
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-gray-700">ETR / Tax Invoice</p>
                            <p class="text-xs text-gray-400">Adds 16% VAT on Material items</p>
                        </div>
                        <input type="hidden" name="etr_enabled" value="0">
                        <input type="checkbox" name="etr_enabled" value="1" x-model="etrEnabled"
                            class="w-5 h-5 accent-green-600">
                    </label>
                </div>

                {{-- Recurring --}}
                <div class="bg-white rounded-xl shadow p-5" x-data="{ recurring: {{ old('is_recurring') ? 'true' : 'false' }} }">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" x-model="recurring"
                            class="w-4 h-4 accent-green-600">
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="repeat" class="w-4 h-4 text-blue-500"></i>
                            Make this a recurring invoice
                        </span>
                    </label>
                    <div x-show="recurring" x-transition class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-3 mt-3">
                        <p class="text-xs text-blue-600 font-medium flex items-center gap-1">
                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                            A new draft invoice will be auto-generated at each interval
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Frequency</label>
                                <select name="recurring_frequency"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="weekly"    {{ old('recurring_frequency') === 'weekly'    ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly"   {{ old('recurring_frequency','monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('recurring_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly"    {{ old('recurring_frequency') === 'yearly'    ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Recurrence</label>
                                <input type="date" name="recurring_next_date" value="{{ old('recurring_next_date') }}"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-1">
                                    End Date <span class="text-gray-400 font-normal">(optional — leave blank to recur forever)</span>
                                </label>
                                <input type="date" name="recurring_ends_at" value="{{ old('recurring_ends_at') }}"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- /left --}}

            {{-- ── RIGHT COLUMN ── --}}
            <div class="bg-white rounded-xl shadow p-5 flex flex-col">

                {{-- Header --}}
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-gray-700">
                        Line Items
                        <span class="ml-1 text-xs font-normal text-gray-400"
                              x-text="`(${items.length} item${items.length !== 1 ? 's' : ''})`"></span>
                    </h2>
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100">
                            + From Catalog
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 top-8 w-72 bg-white border rounded-xl shadow-lg z-10 max-h-80 overflow-y-auto">
                            @foreach ($categories as $category)
                                <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase">{{ $category->name }}</div>
                                @foreach ($category->catalogItems as $ci)
                                    <button type="button"
                                        @click="addCatalogItem({{ $ci->id }}, '{{ addslashes($ci->name) }}', {{ $ci->default_unit_price }}, {{ $ci->default_buying_price ?? 0 }}); open = false"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 hover:text-green-700">
                                        {{ $ci->name }}
                                        <span class="text-gray-400 text-xs ml-1">Ksh {{ number_format($ci->default_unit_price, 2) }}</span>
                                    </button>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Items (scrollable, collapsed by default after first) --}}
                <div class="space-y-2 mb-3 overflow-y-auto max-h-[400px] pr-0.5">
                    <template x-for="(item, index) in items" :key="index">

                        <div class="border rounded-xl bg-gray-50 overflow-hidden">

                            {{-- Collapsed pill --}}
                            <div class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none hover:bg-gray-100"
                                 @click="item.expanded = !item.expanded">
                                <div class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                     :style="`background:${getTypeColor(item.item_type_id)}`"></div>
                                <span class="flex-1 text-sm text-gray-700 truncate"
                                      x-text="item.description || 'New item…'"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap"
                                      :style="`background:${getTypeColor(item.item_type_id)}22; color:${getTypeColor(item.item_type_id)}`"
                                      x-text="getTypeName(item.item_type_id)"></span>
                                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                                    Ksh&nbsp;<span x-text="((item.quantity||0)*(item.unit_price||0)).toLocaleString('en-KE',{minimumFractionDigits:0})"></span>
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-150"
                                   :class="item.expanded ? 'rotate-180':''"></i>
                            </div>

                            {{-- Expanded form --}}
                            <div x-show="item.expanded" class="border-t px-3 pb-3 pt-2 space-y-2">

                                <input type="hidden" :name="`items[${index}][catalog_item_id]`" :value="item.catalog_item_id">
                                <input type="hidden" :name="`items[${index}][is_labour]`"       :value="item.is_labour ? 1 : 0">

                                {{-- Description + remove --}}
                                <div class="flex gap-2">
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                        placeholder="Item description" required
                                        class="flex-1 border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg flex-shrink-0">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                {{-- Type + Qty --}}
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-2">
                                        <label class="text-xs text-gray-500 mb-0.5 block">Type</label>
                                        <select :name="`items[${index}][item_type_id]`"
                                            x-model="item.item_type_id"
                                            @change="onTypeChange(item)"
                                            class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                                            <template x-for="type in itemTypes" :key="type.id">
                                                <option :value="String(type.id)" x-text="type.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-0.5 block">Qty</label>
                                        <input type="number" :name="`items[${index}][quantity]`"
                                            x-model.number="item.quantity" @input="calculateTotals"
                                            min="0.01" step="0.01" required
                                            class="w-full border rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                                    </div>
                                </div>

                                {{-- Prices --}}
                                <div class="grid grid-cols-{{ auth()->user()->isOwner() ? '3' : '2' }} gap-2">
                                    <div>
                                        <label class="text-xs text-green-600 mb-0.5 block font-medium">Selling (Ksh)</label>
                                        <input type="number" :name="`items[${index}][unit_price]`"
                                            x-model.number="item.unit_price" @input="calculateTotals"
                                            min="0" step="0.01" required
                                            class="w-full border border-green-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-green-400 bg-white">
                                    </div>
                                    @if(auth()->user()->isOwner())
                                    <div>
                                        <label class="text-xs text-blue-500 mb-0.5 block font-medium">Buying (Ksh)</label>
                                        <input type="number" :name="`items[${index}][buying_price]`"
                                            x-model.number="item.buying_price" @input="calculateTotals"
                                            min="0" step="0.01" placeholder="0"
                                            class="w-full border border-blue-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 bg-blue-50">
                                    </div>
                                    @endif
                                    <div>
                                        <label class="text-xs text-gray-500 mb-0.5 block">Line Total</label>
                                        <p class="px-2 py-1.5 text-sm font-semibold text-gray-700 bg-white border rounded-lg">
                                            Ksh&nbsp;<span x-text="((item.quantity||0)*(item.unit_price||0)).toLocaleString('en-KE',{minimumFractionDigits:0})"></span>
                                        </p>
                                    </div>
                                </div>

                            </div>{{-- /expanded --}}
                        </div>{{-- /item card --}}

                    </template>
                </div>{{-- /items list --}}

                <button type="button" @click="addItem"
                    class="w-full border-2 border-dashed border-gray-300 text-gray-400 py-2 rounded-lg text-sm hover:border-green-400 hover:text-green-600 mb-4">
                    + Add Item
                </button>

                {{-- ── TOTALS ── --}}
                <div class="border-t pt-4 space-y-1.5">

                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Cost Breakdown</p>

                    <template x-for="row in typeBreakdownList" :key="row.name">
                        <div class="flex justify-between text-sm text-gray-600" x-show="row.amount > 0">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full inline-block" :style="`background:${row.color}`"></span>
                                <span x-text="row.name"></span>
                            </span>
                            <span>Ksh <span x-text="row.amount.toLocaleString('en-KE',{minimumFractionDigits:2})"></span></span>
                        </div>
                    </template>

                    <div x-show="etrEnabled" class="flex justify-between text-sm text-purple-600">
                        <span>VAT (16%)</span>
                        <span>Ksh <span x-text="vatAmount.toLocaleString('en-KE',{minimumFractionDigits:2})"></span></span>
                    </div>

                    {{-- Discount --}}
                    <div class="border border-dashed border-gray-200 rounded-lg p-3 space-y-2" x-data>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M17 17h.01M7 17L17 7M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                            Discount <span class="font-normal text-gray-400 normal-case">(optional)</span>
                        </p>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-400 mb-1">Fixed Amount (Ksh)</label>
                                <input type="number" name="discount_amount" min="0" step="0.01"
                                    x-model.number="discountAmount"
                                    @input="discountPct = 0; $el.closest('form').querySelector('[name=discount_percentage]').value = 0; calculateTotals()"
                                    placeholder="0.00"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div class="flex items-end pb-0.5 text-gray-400 text-sm font-medium">or</div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-400 mb-1">Percentage (%)</label>
                                <input type="number" name="discount_percentage" min="0" max="100" step="0.1"
                                    x-model.number="discountPct"
                                    @input="discountAmount = 0; $el.closest('form').querySelector('[name=discount_amount]').value = 0; calculateTotals()"
                                    placeholder="0"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                        <div x-show="discountValue > 0" class="flex justify-between text-sm text-green-600 font-medium">
                            <span>Discount</span>
                            <span>− Ksh <span x-text="discountValue.toLocaleString('en-KE',{minimumFractionDigits:2})"></span></span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-gray-800 border-t pt-2 mt-1">
                        <span>Grand Total</span>
                        <span class="text-green-600">Ksh <span x-text="grandTotal.toLocaleString('en-KE',{minimumFractionDigits:2})"></span></span>
                    </div>

                    {{-- Profit (owner only) --}}
                    @if(auth()->user()->isOwner())
                    <div x-show="totalCost > 0"
                         class="mt-2 p-3 rounded-lg border space-y-1.5 transition-colors"
                         :class="totalProfit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                            Profit Summary <span class="text-gray-400 font-normal normal-case">(private — not on PDF)</span>
                        </p>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Total Cost</span>
                            <span class="text-red-500 font-medium">Ksh <span x-text="totalCost.toLocaleString('en-KE',{minimumFractionDigits:2})"></span></span>
                        </div>
                        <div class="flex justify-between text-sm font-bold">
                            <span>Profit</span>
                            <span :class="totalProfit >= 0 ? 'text-green-600' : 'text-red-500'">
                                Ksh <span x-text="totalProfit.toLocaleString('en-KE',{minimumFractionDigits:2})"></span>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm font-bold">
                            <span>Margin</span>
                            <span :class="overallMargin >= 0 ? 'text-green-600' : 'text-red-500'"
                                  x-text="overallMargin.toFixed(1) + '%'"></span>
                        </div>
                    </div>
                    @endif

                </div>{{-- /totals --}}
            </div>{{-- /right --}}
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
                description:     '',
                quantity:        1,
                unit_price:      0,
                buying_price:    0,
                item_type_id:    defaultTypeId,
                is_labour:       false,
                expanded:        true,
            }],
            etrEnabled:        false,
            typeBreakdown:     {},
            typeBreakdownList: [],
            vatAmount:         0,
            grandTotal:        0,
            totalCost:         0,
            totalProfit:       0,
            overallMargin:     0,
            discountAmount:    0,
            discountPct:       0,
            discountValue:     0,

            getTypeColor(id) {
                const t = itemTypes.find(t => String(t.id) === String(id));
                return t ? t.color : '#6b7280';
            },

            getTypeName(id) {
                const t = itemTypes.find(t => String(t.id) === String(id));
                return t ? t.name : '';
            },

            onTypeChange(item) {
                const t = itemTypes.find(t => t.id == item.item_type_id);
                item.is_labour = t ? t.name.toLowerCase() === 'labour' : false;
                this.calculateTotals();
            },

            addItem() {
                this.items.forEach(i => i.expanded = false);
                this.items.push({
                    catalog_item_id: null, description: '', quantity: 1,
                    unit_price: 0, buying_price: 0,
                    item_type_id: String(defaultTypeId), is_labour: false, expanded: true,
                });
            },

            addCatalogItem(id, name, price, buyingPrice) {
                this.items.forEach(i => i.expanded = false);
                this.items.push({
                    catalog_item_id: id, description: name, quantity: 1,
                    unit_price: price, buying_price: buyingPrice || 0,
                    item_type_id: String(defaultTypeId), is_labour: false, expanded: true,
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
                this.totalCost = 0;

                this.items.forEach(item => {
                    const qty       = item.quantity     || 0;
                    const sell      = item.unit_price   || 0;
                    const buy       = item.buying_price || 0;
                    const sellTotal = qty * sell;
                    this.totalCost += qty * buy;
                    const typeName  = this.getTypeName(item.item_type_id) || 'Material';
                    breakdown[typeName] = (breakdown[typeName] || 0) + sellTotal;
                });

                this.typeBreakdown = breakdown;
                this.typeBreakdownList = Object.keys(breakdown).map(name => ({
                    name,
                    amount: breakdown[name],
                    color: this.getTypeColor(
                        this.items.find(i => this.getTypeName(i.item_type_id) === name)?.item_type_id
                    ),
                }));

                const materialTotal = breakdown['Material'] || 0;
                this.vatAmount   = this.etrEnabled ? Math.round(materialTotal * 0.16 * 100) / 100 : 0;
                const subtotal   = Object.values(breakdown).reduce((a, b) => a + b, 0);
                this.discountValue = this.discountPct > 0
                    ? Math.round(subtotal * this.discountPct / 100 * 100) / 100
                    : (this.discountAmount || 0);
                this.grandTotal  = Math.max(0, subtotal - this.discountValue + this.vatAmount);
                this.totalProfit = this.grandTotal - this.totalCost - this.vatAmount;
                this.overallMargin = this.grandTotal > 0 ? (this.totalProfit / this.grandTotal) * 100 : 0;
            },

            init() {
                this.$watch('etrEnabled', () => this.calculateTotals());
                this.$watch('items', () => this.calculateTotals(), { deep: true });
                this.calculateTotals();
            }
        }
    }
    </script>

@endsection