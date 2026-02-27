@extends('layouts.app')
@section('title', 'Add Catalog Item')
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('catalog-items.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Add Catalog Item</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('catalog-items.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-600 mb-1">Service Category <span class="text-red-500">*</span></label>
                <select name="service_category_id" required
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ (old('service_category_id', request('category_id')) == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Item Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="e.g. 3 Camera Full Color HD"
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Default Unit Price (Ksh) <span class="text-red-500">*</span></label>
                <input type="number" name="default_unit_price" value="{{ old('default_unit_price', 0) }}" required min="0" step="0.01"
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Unit of Measure <span class="text-red-500">*</span></label>
                <select name="unit_of_measure"
                    class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="piece">Piece</option>
                    <option value="metres">Metres</option>
                    <option value="lot">Lot</option>
                    <option value="kg">KG</option>
                    <option value="set">Set</option>
                    <option value="box">Box</option>
                    <option value="roll">Roll</option>
                    <option value="hour">Hour</option>
                    <option value="day">Day</option>
                </select>
            </div>
            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                Save Item
            </button>
        </form>
    </div>
</div>

@endsection