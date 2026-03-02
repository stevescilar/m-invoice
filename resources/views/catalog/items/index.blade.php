@extends('layouts.app')
@section('title', 'Catalog Items')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">All Catalog Items</h1>
        <div class="flex gap-2">
            <a href="{{ route('categories.index') }}"
                class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                Categories
            </a>
            <a href="{{ route('catalog-items.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                + Add Item
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Item Name</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Unit</th>
                    <th class="px-6 py-3 text-right">Default Price</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-600 text-xs px-2 py-0.5 rounded-full">
                                {{ $item->serviceCategory->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $item->unit_of_measure }}</td>
                        <td class="px-6 py-4 text-right font-medium">Ksh {{ number_format($item->default_unit_price, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('catalog-items.edit', $item) }}"
                                class="text-yellow-500 hover:underline mr-2">Edit</a>
                            <form action="{{ route('catalog-items.destroy', $item) }}" method="POST" class="inline"
                                onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                            No items yet. <a href="{{ route('catalog-items.create') }}" class="text-green-600">Add first
                                item</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $items->links() }}</div>
    </div>

@endsection
