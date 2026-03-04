@extends('layouts.guest')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md text-center">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Invitation Expired</h2>
            <p class="text-gray-500 text-sm mb-6">This invitation link has expired or already been used. Please ask your manager to send a new invitation.</p>
            <a href="{{ route('login') }}" class="text-green-600 hover:underline text-sm">Go to Login</a>
        </div>
    </div>
</div>

@endsection