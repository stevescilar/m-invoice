@extends('layouts.guest')
@section('title', 'Verify Email')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">M-Invoice</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-2">Verify your email</h2>
            <p class="text-gray-500 text-sm mb-6">
                We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
                Please check your inbox and click the link to activate your account.
            </p>

            @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="text-sm text-gray-400 hover:text-gray-600">Sign out</button>
            </form>
        </div>
    </div>
</div>

@endsection