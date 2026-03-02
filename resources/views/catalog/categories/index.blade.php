@extends('layouts.app')
@section('title', 'Service Categories')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Catalog</h1>
        <div class="flex gap-2">
            <a href="{{ route('catalog-items.index') }}"
                class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                View All Items
            </a>
            <a href="{{ route('categories.create') }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                + Add Category
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($categories as $category)
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="font-semibold text-gray-800">{{ $category->name }}</h2>
                    <span class="text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full">
                        {{ $category->catalog_items_count }} items
                    </span>
                </div>
                <p class="text-sm text-gray-400 mb-4">{{ $category->description ?? 'No description' }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('categories.show', $category) }}" class="text-sm text-blue-500 hover:underline">View
                        Items</a>
                    <a href="{{ route('catalog-items.create') }}?category_id={{ $category->id }}"
                        class="text-sm text-green-600 hover:underline">+ Add Item</a>
                    <a href="{{ route('categories.edit', $category) }}"
                        class="text-sm text-yellow-500 hover:underline">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                        onsubmit="return confirm('Delete this category and all its items?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-xl shadow p-10 text-center text-gray-400">
                No categories yet.
                <a href="{{ route('categories.create') }}" class="text-green-600 ml-1">Create your first category</a>
            </div>
        @endforelse
    </div>

@endsection
