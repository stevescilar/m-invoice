@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">M-Invoice</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Reset Password</h2>

            @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">New Password</label>
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
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>

@endsection