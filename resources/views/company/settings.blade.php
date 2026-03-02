@extends('layouts.app')
@section('title', 'Company Settings')
@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800">Company Settings</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('company.settings') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 border-b pb-2">Basic Information</h2>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $company->email) }}"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('address', $company->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">KRA PIN <span class="text-gray-400">(for ETR
                            invoices)</span></label>
                    <input type="text" name="kra_pin" value="{{ old('kra_pin', $company->kra_pin) }}"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Invoice Footer Message</label>
                    <input type="text" name="footer_message"
                        value="{{ old('footer_message', $company->footer_message) }}"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
            </div>

            <!-- Branding -->
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 border-b pb-2">Branding</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Company Logo</label>
                        @if ($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" class="h-12 mb-2 rounded">
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="w-full border rounded-lg px-4 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Signature</label>
                        @if ($company->signature)
                            <img src="{{ asset('storage/' . $company->signature) }}" class="h-12 mb-2 rounded">
                        @endif
                        <input type="file" name="signature" accept="image/*"
                            class="w-full border rounded-lg px-4 py-2 text-sm">
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h2 class="font-semibold text-gray-700 border-b pb-2">Payment Details <span
                        class="text-gray-400 text-sm font-normal">(shown on invoices)</span></h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">M-Pesa Paybill</label>
                        <input type="text" name="mpesa_paybill"
                            value="{{ old('mpesa_paybill', $company->mpesa_paybill) }}" placeholder="e.g. 522522"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Account Number</label>
                        <input type="text" name="mpesa_account"
                            value="{{ old('mpesa_account', $company->mpesa_account) }}" placeholder="e.g. Company Name"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">M-Pesa Till</label>
                        <input type="text" name="mpesa_till" value="{{ old('mpesa_till', $company->mpesa_till) }}"
                            placeholder="e.g. 123456"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">M-Pesa Number</label>
                        <input type="text" name="mpesa_number" value="{{ old('mpesa_number', $company->mpesa_number) }}"
                            placeholder="e.g. 0712345678"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $company->bank_name) }}"
                            placeholder="e.g. Equity Bank"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Bank Account Number</label>
                        <input type="text" name="bank_account" value="{{ old('bank_account', $company->bank_account) }}"
                            placeholder="e.g. 0123456789"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Bank Branch</label>
                        <input type="text" name="bank_branch" value="{{ old('bank_branch', $company->bank_branch) }}"
                            placeholder="e.g. Nairobi CBD"
                            class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                Save Settings
            </button>
        </form>
    </div>

@endsection
