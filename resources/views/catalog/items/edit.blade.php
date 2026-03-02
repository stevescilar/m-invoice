@extends('layouts.app')
@section('title', 'Edit Item')
@section('content')

    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('catalog-items.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Item</h1>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('catalog-items.update', $item) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Service Category <span
                            class="text-red-500">*</span></label>
                    <select name="service_category_id" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('service_category_id', $item->service_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Default Unit Price (Ksh)</label>
                    <input type="number" name="default_unit_price"
                        value="{{ old('default_unit_price', $item->default_unit_price) }}" min="0"
                        step="0.01"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Unit of Measure</label>
                    <select name="unit_of_measure"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        @foreach (['piece', 'metres', 'lot', 'kg', 'set', 'box', 'roll', 'hour', 'day'] as $unit)
                            <option value="{{ $unit }}"
                                {{ old('unit_of_measure', $item->unit_of_measure) === $unit ? 'selected' : '' }}>
                                {{ ucfirst($unit) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                    Update Item
                </button>
            </form>
        </div>
    </div>

@endsection
