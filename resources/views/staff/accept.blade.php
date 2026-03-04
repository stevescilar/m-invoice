@extends('layouts.guest')
@section('title', 'Accept Invitation')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">M-Invoice</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">You're invited!</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Join <strong>{{ $invitation->company->name }}</strong> as a staff member
                </p>
            </div>

            @if($errors->any())
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('staff.accept.submit', $invitation->token) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Full Name</label>
                    <input type="text" value="{{ $invitation->name }}" disabled
                        class="w-full border rounded-lg px-4 py-2 text-sm bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email Address</label>
                    <input type="email" value="{{ $invitation->email }}" disabled
                        class="w-full border rounded-lg px-4 py-2 text-sm bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Set Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                        placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm">
                    Accept & Join Team
                </button>
            </form>
        </div>
    </div>
</div>

@endsection