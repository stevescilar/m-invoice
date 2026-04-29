<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoxa Admin — Verify Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="text-2xl font-black text-white tracking-tight mb-1">Invoxa <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded font-bold">ADMIN</span></div>
            <p class="text-gray-500 text-sm">Two-factor verification</p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-green-900/30 border border-green-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-white font-bold text-lg">Check your email</h2>
                <p class="text-gray-500 text-sm mt-1">We sent a 6-digit code to<br><span class="text-gray-300 font-medium">{{ session('admin_otp_email') }}</span></p>
            </div>

            @if(session('resent'))
            <div class="bg-green-900/30 border border-green-800 text-green-400 text-sm rounded-lg px-4 py-2 mb-4 text-center">
                New code sent!
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-900/30 border border-red-800 text-red-400 text-sm rounded-lg px-4 py-2 mb-4 text-center">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.otp.verify') }}">
                @csrf
                <div class="mb-5">
                    <input type="text" name="otp" inputmode="numeric" pattern="\d{6}" maxlength="6"
                        placeholder="000000"
                        autofocus
                        class="w-full bg-gray-800 border border-gray-700 text-white text-center text-3xl font-mono tracking-[0.5em] rounded-xl px-4 py-4 focus:outline-none focus:ring-2 focus:ring-green-500 placeholder-gray-700"
                        autocomplete="one-time-code">
                </div>
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl transition text-sm">
                    Verify & Sign in
                </button>
            </form>

            <form method="POST" action="{{ route('admin.otp.resend') }}" class="mt-4 text-center">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-300 transition">
                    Didn't receive it? <span class="text-green-500 font-medium">Resend code</span>
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('admin.login') }}" class="text-xs text-gray-600 hover:text-gray-400 transition">← Back to login</a>
            </div>
        </div>

        <p class="text-center text-gray-700 text-xs mt-6">Code expires in 10 minutes</p>
    </div>
</body>
</html>