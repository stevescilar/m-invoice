@extends('layouts.app')
@section('title', $category->name)
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 text-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Categories
        </a>
        <span class="text-gray-300">/</span>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="folder-open" class="w-6 h-6 text-green-600"></i>
                {{ $category->name }}
            </h1>
            @if($category->description)
            <p class="text-xs text-gray-400 mt-0.5">{{ $category->description }}</p>
            @endif
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('categories.edit', $category) }}"
            class="flex items-center gap-1.5 px-4 py-2 text-sm border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 transition">
            <i data-lucide="pencil" class="w-4 h-4"></i> Edit Category
        </a>
        <a href="{{ route('catalog-items.create') }}?category_id={{ $category->id }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Item
        </a>
    </div>
</div>

{{-- Stats strip --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="package" class="w-5 h-5 text-green-600"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">{{ $items->total() }}</p>
            <p class="text-xs text-gray-400">Total Items</p>
        </div>
    </div>
    @if(auth()->user()->isOwner())
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">
                Ksh {{ number_format($items->getCollection()->avg('default_unit_price'), 0) }}
            </p>
            <p class="text-xs text-gray-400">Avg. Selling Price</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="percent" class="w-5 h-5 text-purple-600"></i>
        </div>
        <div>
            @php
                $withBuying = $items->getCollection()->filter(fn($i) => $i->default_buying_price > 0 && $i->default_unit_price > 0);
                $avgMargin = $withBuying->count() > 0
                    ? $withBuying->avg(fn($i) => (($i->default_unit_price - $i->default_buying_price) / $i->default_unit_price) * 100)
                    : null;
            @endphp
            <p class="text-xl font-bold {{ $avgMargin !== null ? ($avgMargin >= 20 ? 'text-green-600' : 'text-yellow-600') : 'text-gray-400' }}">
                {{ $avgMargin !== null ? number_format($avgMargin, 0).'%' : '—' }}
            </p>
            <p class="text-xs text-gray-400">Avg. Margin</p>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">
                Ksh {{ number_format($items->getCollection()->avg('default_unit_price'), 0) }}
            </p>
            <p class="text-xs text-gray-400">Avg. Selling Price</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="arrow-up-down" class="w-5 h-5 text-gray-500"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-gray-800">
                Ksh {{ number_format($items->getCollection()->min('default_unit_price'), 0) }} – {{ number_format($items->getCollection()->max('default_unit_price'), 0) }}
            </p>
            <p class="text-xs text-gray-400">Price Range</p>
        </div>
    </div>
    @endif
</div>

{{-- Search + Sort bar --}}
<form method="GET" action="{{ route('categories.show', $category) }}" class="flex gap-3 mb-4 flex-wrap">
    <div class="relative flex-1 min-w-52">
        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search items in {{ $category->name }}..."
            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
    </div>

    <select name="sort" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white">
        <option value="name"       {{ request('sort','name') === 'name'       ? 'selected' : '' }}>Name A–Z</option>
        <option value="price_high" {{ request('sort') === 'price_high'        ? 'selected' : '' }}>Price: High–Low</option>
        <option value="price_low"  {{ request('sort') === 'price_low'         ? 'selected' : '' }}>Price: Low–High</option>
        <option value="newest"     {{ request('sort') === 'newest'            ? 'selected' : '' }}>Newest First</option>
    </select>

    <button type="submit"
        class="bg-green-600 text-white px-5 py-2.5 rounded-xl text-sm hover:bg-green-700 flex items-center gap-1.5 font-medium">
        <i data-lucide="search" class="w-4 h-4"></i> Search
    </button>

    @if(request('search') || request('sort'))
    <a href="{{ route('categories.show', $category) }}"
        class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl">
        <i data-lucide="x" class="w-4 h-4"></i> Clear
    </a>
    @endif
</form>

{{-- Items table --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item Name</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                @if(auth()->user()->isOwner())
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Buying</th>
                @endif
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Selling</th>
                @if(auth()->user()->isOwner())
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Margin</th>
                @endif
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($items as $item)
            <tr class="hover:bg-green-50/30 transition">
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                </td>
                <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $item->unit_of_measure }}</td>
                @if(auth()->user()->isOwner())
                <td class="px-5 py-3.5 text-right">
                    @if($item->default_buying_price > 0)
                        <span class="text-blue-600 font-medium">Ksh {{ number_format($item->default_buying_price, 2) }}</span>
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </td>
                @endif
                <td class="px-5 py-3.5 text-right">
                    <span class="font-bold text-gray-800">Ksh {{ number_format($item->default_unit_price, 2) }}</span>
                </td>
                @if(auth()->user()->isOwner())
                <td class="px-5 py-3.5 text-right">
                    @if($item->default_buying_price > 0 && $item->default_unit_price > 0)
                        @php $margin = (($item->default_unit_price - $item->default_buying_price) / $item->default_unit_price) * 100; @endphp
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full
                            {{ $margin >= 30 ? 'bg-green-100 text-green-700' : ($margin >= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-600') }}">
                            {{ number_format($margin, 0) }}%
                        </span>
                    @else
                        <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
                @endif
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('catalog-items.edit', $item) }}"
                            title="Edit"
                            class="p-1.5 rounded-lg hover:bg-yellow-50 text-yellow-500 hover:text-yellow-700 transition">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('catalog-items.destroy', $item) }}" method="POST"
                            onsubmit="return confirm('Delete {{ addslashes($item->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete"
                                class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->isOwner() ? 6 : 4 }}" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="package" class="w-6 h-6 text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 font-medium">
                            {{ request('search') ? 'No items match your search' : 'No items in this category yet' }}
                        </p>
                        <a href="{{ route('catalog-items.create') }}?category_id={{ $category->id }}"
                            class="text-sm text-green-600 hover:underline flex items-center gap-1">
                            <i data-lucide="plus" class="w-4 h-4"></i> Add first item
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-400">
            Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} items
        </p>
        {{ $items->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection