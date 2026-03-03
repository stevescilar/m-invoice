@extends('layouts.guest')
@section('title', 'Register')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">M-Invoice</h1>
            <p class="text-gray-500 text-sm mt-1">Smart invoicing for Kenyan businesses</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-1">Create your account</h2>
            <p class="text-gray-500 text-sm mb-6">3-day free trial. No credit card required.</p>

            @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <!-- Google Button -->
            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-3 w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition mb-4">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>

            <div class="relative flex items-center justify-center mb-4">
                <div class="border-t border-gray-200 w-full"></div>
                <span class="bg-white px-3 text-xs text-gray-400 absolute">or register with email</span>
            </div>

            <!-- Email Form -->
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
                    <label class="block text-sm text-gray-600 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm">
                    Create Account
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-4">
                Already have an account?
                <a href="{{ route('login') }}" class="text-green-600 hover:underline">Sign in</a>
            </p>
        </div>
    </div>
</div>

@endsection