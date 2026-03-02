@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <h2 class="text-xl font-semibold mb-6 text-gray-700">Sign in to your account</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
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
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" class="text-sm text-gray-600">Remember me</label>
        </div>
        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
            Login
        </button>
    </form>
    <p class="text-sm text-center mt-4 text-gray-500">
        Don't have an account? <a href="{{ route('register') }}" class="text-green-600 font-medium">Register</a>
    </p>
@endsection
