@extends('layouts.app')
@section('title', 'Company Settings')
@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
    </div>

    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm">
        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <div x-data="{ tab: 'company' }" class="flex gap-6">

        <!-- Sidebar Tabs -->
        <div class="w-52 flex-shrink-0 space-y-1">
            <button @click="tab = 'company'"
                :class="tab === 'company' ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-500 hover:bg-gray-100'"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm text-left transition">
                <i data-lucide="building-2" class="w-4 h-4"></i> Company Info
            </button>
            <button @click="tab = 'branding'"
                :class="tab === 'branding' ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-500 hover:bg-gray-100'"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm text-left transition">
                <i data-lucide="image" class="w-4 h-4"></i> Branding
            </button>
            <button @click="tab = 'item_types'"
                :class="tab === 'item_types' ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-500 hover:bg-gray-100'"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm text-left transition">
                <i data-lucide="tag" class="w-4 h-4"></i> Item Types
            </button>
            <button @click="tab = 'payment'"
                :class="tab === 'payment' ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-500 hover:bg-gray-100'"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm text-left transition">
                <i data-lucide="credit-card" class="w-4 h-4"></i> Payment Details
            </button>
            <button @click="tab = 'password'"
                :class="tab === 'password' ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-500 hover:bg-gray-100'"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm text-left transition">
                <i data-lucide="lock" class="w-4 h-4"></i> Password
            </button>

            <!-- Referral code badge -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="bg-green-50 border border-green-100 rounded-xl p-3">
                    <p class="text-xs font-semibold text-green-700 mb-1 flex items-center gap-1">
                        <i data-lucide="share-2" class="w-3 h-3"></i> Your Referral Code
                    </p>
                    <p class="text-lg font-bold text-green-600 tracking-widest">{{ auth()->user()->company->referral_code }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->company->referral_count }} referral(s) so far</p>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="flex-1">
            <form method="POST" action="{{ route('company.settings') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <!-- Company Info Tab -->
                <div x-show="tab === 'company'" class="bg-white rounded-xl shadow p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b">
                        <i data-lucide="building-2" class="w-5 h-5 text-green-600"></i>
                        <h2 class="font-semibold text-gray-800">Company Information</h2>
                    </div>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center text-green-700 font-bold text-xl flex-shrink-0">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                {{ strtoupper(substr($company->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $company->name }}</p>
                            <p class="text-sm text-gray-500">{{ $company->email }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                            <div class="relative">
                                <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                                    class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                    class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                        <div class="relative">
                            <i data-lucide="map-pin" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                            <textarea name="address" rows="2"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('address', $company->address) }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">KRA PIN <span class="text-gray-400 font-normal">(for ETR invoices)</span></label>
                        <div class="relative">
                            <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="text" name="kra_pin" value="{{ old('kra_pin', $company->kra_pin) }}"
                                placeholder="e.g. A001234567B"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Invoice Footer Message</label>
                        <div class="relative">
                            <i data-lucide="message-square" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="text" name="footer_message" value="{{ old('footer_message', $company->footer_message) }}"
                                placeholder="e.g. Thank you for your business!"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Company Info
                    </button>
                </div>

                <!-- Branding Tab -->
                <div x-show="tab === 'branding'" class="bg-white rounded-xl shadow p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b">
                        <i data-lucide="image" class="w-5 h-5 text-green-600"></i>
                        <h2 class="font-semibold text-gray-800">Branding</h2>
                    </div>
                    <div class="p-4 border border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                            <i data-lucide="image" class="w-4 h-4 text-gray-400"></i> Company Logo
                        </p>
                        @if($company->logo)
                        <div class="flex items-center gap-4 mb-3">
                            <img src="{{ asset('storage/' . $company->logo) }}" class="h-16 w-auto rounded-lg border object-contain p-1">
                            <p class="text-xs text-gray-400">Current logo — upload a new one to replace</p>
                        </div>
                        @else
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="image-off" class="w-6 h-6 text-gray-300"></i>
                            </div>
                            <p class="text-xs text-gray-400">No logo uploaded yet</p>
                        </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-2">PNG, JPG or SVG. Max 2MB.</p>
                    </div>
                    <div class="p-4 border border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                            <i data-lucide="pen-line" class="w-4 h-4 text-gray-400"></i> Signature
                        </p>
                        @if($company->signature)
                        <div class="flex items-center gap-4 mb-3">
                            <img src="{{ asset('storage/' . $company->signature) }}" class="h-16 w-auto rounded-lg border object-contain p-1">
                            <p class="text-xs text-gray-400">Current signature — upload a new one to replace</p>
                        </div>
                        @else
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="pen-line" class="w-6 h-6 text-gray-300"></i>
                            </div>
                            <p class="text-xs text-gray-400">No signature uploaded yet</p>
                        </div>
                        @endif
                        <input type="file" name="signature" accept="image/*"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <p class="text-xs text-gray-400 mt-2">PNG with transparent background recommended.</p>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Branding
                    </button>
                </div>

                <!-- Brand Color Tab content injected inside Branding -->
                <div class="p-4 border border-gray-200 rounded-xl mt-0 -mt-5"
                    x-data="{ color: '{{ old('primary_color', $company->primary_color ?? '#16a34a') }}' }">
                    <p class="text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
                        <i data-lucide="palette" class="w-4 h-4 text-gray-400"></i> PDF Brand Color
                    </p>
                    <p class="text-xs text-gray-400 mb-3">Used for the header, totals accent, and table headers on all invoices and quotations.</p>

                    <div class="flex items-center gap-4">
                        <input type="color" name="primary_color" x-model="color"
                            :value="color"
                            class="w-14 h-14 rounded-xl cursor-pointer border-2 border-gray-200 p-1 flex-shrink-0">

                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach(['#16a34a','#2563eb','#dc2626','#7c3aed','#ea580c','#0891b2','#be185d','#111827'] as $preset)
                                <button type="button"
                                    @click="color = '{{ $preset }}'; document.querySelector('[name=primary_color]').value = '{{ $preset }}'"
                                    class="w-7 h-7 rounded-full border-2 transition hover:scale-110"
                                    :class="color === '{{ $preset }}' ? 'border-gray-800 scale-110' : 'border-transparent'"
                                    style="background: {{ $preset }}"
                                    title="{{ $preset }}">
                                </button>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400">Pick a preset or use the colour picker</p>
                        </div>

                        <!-- Live preview badge -->
                        <div class="flex-shrink-0 rounded-lg overflow-hidden border border-gray-200 text-xs w-32">
                            <div class="px-3 py-1.5 text-white font-bold text-center" :style="'background:' + color">
                                INVOICE
                            </div>
                            <div class="px-3 py-1.5 bg-white text-gray-600 text-center">
                                Grand Total
                            </div>
                            <div class="px-3 py-1 text-white font-bold text-center text-sm" :style="'background:' + color + '22; color:' + color">
                                Ksh 50,000
                            </div>
                        </div>
                    </div>
                </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center justify-center gap-2 mt-4">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Branding & Color
                    </button>
                </div>

                <!-- Payment Tab -->
                <div x-show="tab === 'payment'" class="bg-white rounded-xl shadow p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b">
                        <i data-lucide="credit-card" class="w-5 h-5 text-green-600"></i>
                        <h2 class="font-semibold text-gray-800">Payment Details</h2>
                        <span class="text-xs text-gray-400 font-normal">(shown on invoices)</span>
                    </div>
                    <div class="p-4 bg-green-50 border border-green-100 rounded-xl space-y-4">
                        <p class="text-sm font-semibold text-green-700 flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-4 h-4"></i> M-Pesa
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Paybill Number</label>
                                <input type="text" name="mpesa_paybill" value="{{ old('mpesa_paybill', $company->mpesa_paybill) }}"
                                    placeholder="e.g. 522522"
                                    class="w-full border border-green-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account Number</label>
                                <input type="text" name="mpesa_account" value="{{ old('mpesa_account', $company->mpesa_account) }}"
                                    placeholder="e.g. Company Name"
                                    class="w-full border border-green-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Till Number</label>
                                <input type="text" name="mpesa_till" value="{{ old('mpesa_till', $company->mpesa_till) }}"
                                    placeholder="e.g. 123456"
                                    class="w-full border border-green-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">M-Pesa Number</label>
                                <input type="text" name="mpesa_number" value="{{ old('mpesa_number', $company->mpesa_number) }}"
                                    placeholder="e.g. 0712345678"
                                    class="w-full border border-green-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
                        <p class="text-sm font-semibold text-blue-700 flex items-center gap-2">
                            <i data-lucide="landmark" class="w-4 h-4"></i> Bank Account
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bank Name</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $company->bank_name) }}"
                                    placeholder="e.g. Equity Bank"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account Number</label>
                                <input type="text" name="bank_account" value="{{ old('bank_account', $company->bank_account) }}"
                                    placeholder="e.g. 0123456789"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bank Branch</label>
                                <input type="text" name="bank_branch" value="{{ old('bank_branch', $company->bank_branch) }}"
                                    placeholder="e.g. Nairobi CBD"
                                    class="w-full border border-blue-200 bg-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Payment Details
                    </button>
                </div>
            </form>

            <!-- Item Types Tab (outside main form — has its own forms per action) -->
            <div x-show="tab === 'item_types'" class="bg-white rounded-xl shadow p-6 space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b">
                    <i data-lucide="tag" class="w-5 h-5 text-green-600"></i>
                    <h2 class="font-semibold text-gray-800">Item Types</h2>
                </div>
                <p class="text-sm text-gray-400">
                    Categorize line items on invoices and quotations. Each active type appears as a separate subtotal on PDFs.
                </p>

                <!-- Existing types -->
                <div class="space-y-3">
                    @foreach($itemTypes as $type)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                        <div class="w-4 h-4 rounded-full flex-shrink-0" style="background: {{ $type->color }}"></div>
                        <span class="flex-1 font-medium text-gray-700 text-sm">{{ $type->name }}</span>

                        @if($type->is_default)
                            <span class="text-xs text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full">Default</span>
                        @else
                            <!-- Inline edit -->
                            <div x-data="{ editing: false }" class="flex items-center gap-2">
                                <template x-if="editing">
                                    <form method="POST" action="{{ route('item-types.update', $type) }}"
                                        class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <input type="color" name="color" value="{{ $type->color }}"
                                            class="w-8 h-8 rounded cursor-pointer border-0">
                                        <input type="text" name="name" value="{{ $type->name }}"
                                            class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 w-36">
                                        <button type="submit"
                                            class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700">
                                            Save
                                        </button>
                                        <button type="button" @click="editing = false"
                                            class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5">
                                            Cancel
                                        </button>
                                    </form>
                                </template>
                                <template x-if="!editing">
                                    <button type="button" @click="editing = true"
                                        class="p-1.5 rounded-lg hover:bg-yellow-50 text-yellow-500 hover:text-yellow-700 transition">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                </template>
                            </div>

                            <!-- Toggle active -->
                            <form method="POST" action="{{ route('item-types.toggle', $type) }}">
                                @csrf
                                <button type="submit" title="{{ $type->is_active ? 'Deactivate' : 'Activate' }}"
                                    class="p-1.5 rounded-lg transition {{ $type->is_active ? 'text-green-500 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }}">
                                    <i data-lucide="{{ $type->is_active ? 'toggle-right' : 'toggle-left' }}" class="w-5 h-5"></i>
                                </button>
                            </form>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('item-types.destroy', $type) }}"
                                onsubmit="return confirm('Delete {{ addslashes($type->name) }}? Items using this type will be unassigned.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Add new type -->
                <div class="border-t pt-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Add New Type</h3>
                    <form method="POST" action="{{ route('item-types.store') }}"
                        class="flex items-center gap-3">
                        @csrf
                        <input type="color" name="color" value="#6b7280"
                            class="w-10 h-10 rounded-lg cursor-pointer border border-gray-200 flex-shrink-0">
                        <input type="text" name="name" placeholder="e.g. Delivery, Installation, Consultancy..."
                            class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        <button type="submit"
                            class="bg-green-600 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium flex-shrink-0">
                            <i data-lucide="plus" class="w-4 h-4"></i> Add Type
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Tab (separate form) -->
            <div x-show="tab === 'password'" class="bg-white rounded-xl shadow p-6 space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b">
                    <i data-lucide="lock" class="w-5 h-5 text-green-600"></i>
                    <h2 class="font-semibold text-gray-800">Change Password</h2>
                </div>
                @if(auth()->user()->google_id && !auth()->user()->password)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-3">
                    <i data-lucide="info" class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm text-yellow-700">You signed up with Google. You can set a password below to also enable email login.</p>
                </div>
                @endif
                <form method="POST" action="{{ route('password.update.profile') }}" class="space-y-4">
                    @csrf
                    @if(!auth()->user()->google_id || (auth()->user()->google_id && auth()->user()->password))
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Current Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="password" name="current_password"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">New Password</label>
                        <div class="relative">
                            <i data-lucide="key" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="password" name="password" minlength="8"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                                placeholder="Minimum 8 characters">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <i data-lucide="key" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                            <input type="password" name="password_confirmation"
                                class="w-full border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center justify-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4"></i> Update Password
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endsection