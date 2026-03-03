<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — M-Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-green-400">M-Invoice</h1>
        <p class="text-gray-400 text-sm mt-1">Admin Panel</p>
    </div>

    <div class="bg-gray-800 rounded-2xl shadow-xl p-8">
        <h2 class="text-white font-semibold text-lg mb-6">Sign in to Admin</h2>

        @if(session('error'))
        <div class="bg-red-900 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-900 text-red-300 px-4 py-3 rounded-lg mb-4 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-2.5 rounded-lg hover:bg-green-700 font-medium text-sm mt-2">
                Sign In
            </button>
        </form>
    </div>
</div>

</body>
</html>