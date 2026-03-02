@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')

    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Category</h1>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('description', $category->description) }}</textarea>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                    Update Category
                </button>
            </form>
        </div>
    </div>

@endsection
