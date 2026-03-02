@extends('layouts.app')
@section('title', 'Add Client')
@section('content')

    <div class="max-w-xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-600">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800">Add New Client</h1>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            @if ($errors->any())
                <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">{{ old('address') }}</textarea>
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                    Save Client
                </button>
            </form>
        </div>
    </div>

@endsection
