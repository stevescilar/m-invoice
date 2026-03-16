@extends('layouts.app')
@section('title', 'Catalog')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="package" class="w-6 h-6 text-green-600"></i> Catalog
        </h1>
        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($totalItems) }} items across {{ $categories->count() }} categories</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('categories.create') }}"
            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 flex items-center gap-1.5 font-medium">
            <i data-lucide="folder-plus" class="w-4 h-4"></i> New Category
        </a>
        <a href="{{ route('catalog-items.create') }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2 font-medium shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Item
        </a>
    </div>
</div>

<div class="flex gap-6">

    {{-- ── SIDEBAR: Categories ── --}}
    <div class="w-56 flex-shrink-0 space-y-1">
        <a href="{{ route('catalog-items.index', array_merge(request()->except('category', 'page'), [])) }}"
            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition font-medium
                {{ !request('category') ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4"></i> All Items
            </span>
            <span class="text-xs {{ !request('category') ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full font-semibold">
                {{ number_format($totalItems) }}
            </span>
        </a>

        <div class="pt-2 pb-1 px-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Categories</p>
        </div>

        @forelse($categories as $cat)
        <a href="{{ route('catalog-items.index', array_merge(request()->except('category', 'page'), ['category' => $cat->id])) }}"
            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition
                {{ request('category') == $cat->id ? 'bg-green-600 text-white shadow-sm font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            <span class="flex items-center gap-2 truncate">
                <i data-lucide="folder" class="w-4 h-4 flex-shrink-0"></i>
                <span class="truncate">{{ $cat->name }}</span>
            </span>
            <span class="text-xs flex-shrink-0 {{ request('category') == $cat->id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full font-semibold ml-1">
                {{ $cat->catalog_items_count }}
            </span>
        </a>
        @empty
        <div class="px-3 py-4 text-center">
            <p class="text-xs text-gray-400">No categories yet</p>
            <a href="{{ route('categories.create') }}" class="text-xs text-green-600 hover:underline mt-1 block">+ Create one</a>
        </div>
        @endforelse

        <div class="pt-3 border-t border-gray-100">
            <a href="{{ route('categories.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition">
                <i data-lucide="settings-2" class="w-3.5 h-3.5"></i> Manage Categories
            </a>
        </div>
    </div>

    {{-- ── MAIN: Items ── --}}
    <div class="flex-1 min-w-0">

        {{-- Search + Sort bar --}}
        <form method="GET" action="{{ route('catalog-items.index') }}" class="flex gap-3 mb-4 flex-wrap">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <div class="relative flex-1 min-w-52">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search items by name..."
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
            <a href="{{ route('catalog-items.index', request('category') ? ['category' => request('category')] : []) }}"
                class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl">
                <i data-lucide="x" class="w-4 h-4"></i> Clear
            </a>
            @endif
        </form>

        {{-- Active filter pill --}}
        @if(request('category'))
        @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
        @if($activeCat)
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs text-gray-500">Showing:</span>
            <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs px-3 py-1 rounded-full font-medium border border-green-200">
                <i data-lucide="folder" class="w-3 h-3"></i>
                {{ $activeCat->name }}
                <a href="{{ route('catalog-items.index', request()->except(['category','page'])) }}"
                    class="ml-1 text-green-500 hover:text-green-700">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
            </span>
        </div>
        @endif
        @endif

        {{-- Items table --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
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
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium bg-blue-50 text-blue-600">
                                <i data-lucide="folder" class="w-3 h-3"></i>
                                {{ $item->serviceCategory->name }}
                            </span>
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
                                @php
                                    $margin = (($item->default_unit_price - $item->default_buying_price) / $item->default_unit_price) * 100;
                                @endphp
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
                        <td colspan="{{ auth()->user()->isOwner() ? 7 : 5 }}" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="package" class="w-6 h-6 text-gray-300"></i>
                                </div>
                                <p class="text-gray-400 font-medium">
                                    {{ request('search') ? 'No items match your search' : 'No items in this category' }}
                                </p>
                                <a href="{{ route('catalog-items.create') }}"
                                    class="text-sm text-green-600 hover:underline flex items-center gap-1">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Add your first item
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

    </div>{{-- /main --}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection