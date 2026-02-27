@extends('layouts.auth')
@section('title', 'Company Setup')
@section('content')
    <h2 class="text-xl font-semibold mb-2 text-gray-700">Complete Your Company Profile</h2>
    <p class="text-sm text-gray-400 mb-6">This information will appear on your invoices.</p>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/company/setup" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Company Logo</label>
            <input type="file" name="logo" accept="image/*" class="w-full border rounded-lg px-4 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Business Email</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Address</label>
            <textarea name="address" rows="2"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('address', $company->address) }}</textarea>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">KRA PIN <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="kra_pin" value="{{ old('kra_pin', $company->kra_pin) }}"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Invoice Footer Message</label>
            <input type="text" name="footer_message"
                value="{{ old('footer_message', $company->footer_message ?? 'Thank you for choosing our services') }}"
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Signature <span class="text-gray-400">(optional)</span></label>
            <input type="file" name="signature" accept="image/*" class="w-full border rounded-lg px-4 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
            Save & Continue
        </button>
    </form>
@endsection
