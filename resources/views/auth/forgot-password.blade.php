@extends('layouts.guest')
@section('title', 'Forgot Password')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">M-Invoice</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Forgot Password?</h2>
            <p class="text-gray-500 text-sm mb-6">Enter your email and we'll send you a reset link.</p>

            @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm">
                    Send Reset Link
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-4">
                <a href="{{ route('login') }}" class="text-green-600 hover:underline">Back to login</a>
            </p>
        </div>
    </div>
</div>

@endsection