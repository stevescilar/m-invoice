@extends('layouts.app')
@section('title', 'Log Expense')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('expenses.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Log Expense</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
                <input type="text" name="category" value="{{ old('category') }}" required
                    list="categories"
                    placeholder="e.g. Transport, Fuel, Supplies, Rent..."
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <datalist id="categories">
                    <option value="Transport">
                    <option value="Fuel">
                    <option value="Supplies">
                    <option value="Rent">
                    <option value="Utilities">
                    <option value="Salaries">
                    <option value="Equipment">
                    <option value="Marketing">
                    <option value="Repairs">
                    <option value="Internet">
                    <option value="Airtime">
                    <option value="Miscellaneous">
                </datalist>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="2"
                    placeholder="Brief description of this expense..."
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Amount (Ksh) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Receipt <span class="text-gray-400">(optional)</span></label>
                <input type="file" name="receipt" accept="image/*,.pdf"
                    class="w-full border rounded-lg px-4 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF, max 2MB</p>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                Save Expense
            </button>
        </form>
    </div>
</div>

@endsection