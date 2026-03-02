@extends('layouts.auth')
@section('title', 'Register')
@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-700">Create your account</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>
        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
            Create Account
        </button>
    </form>
    <p class="text-sm text-center mt-4 text-gray-500">
        Already have an account? <a href="{{ route('login') }}" class="text-green-600 font-medium">Login</a>
    </p>
@endsection
