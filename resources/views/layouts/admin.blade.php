<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — @yield('title', 'M-Invoice')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<nav class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center">
    <div class="flex items-center gap-8">
        <span class="font-bold text-lg text-green-400">M-Invoice Admin</span>
        <div class="flex gap-6 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-green-400 {{ request()->routeIs('admin.dashboard') ? 'text-green-400' : '' }}">Dashboard</a>
            <a href="{{ route('admin.companies.index') }}" class="hover:text-green-400 {{ request()->routeIs('admin.companies*') ? 'text-green-400' : '' }}">Companies</a>
            <a href="{{ route('admin.users.index') }}" class="hover:text-green-400 {{ request()->routeIs('admin.users*') ? 'text-green-400' : '' }}">Users</a>
            <a href="{{ route('admin.transactions.index') }}" class="hover:text-green-400 {{ request()->routeIs('admin.transactions*') ? 'text-green-400' : '' }}">Transactions</a>
        </div>
    </div>
    <div class="flex items-center gap-4 text-sm">
        <span class="text-gray-400">{{ auth('admin')->user()->name }}</span>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button class="text-red-400 hover:text-red-300">Logout</button>
        </form>
    </div>
</nav>

<!-- Flash Messages -->
<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
    @endif
</div>

<!-- Content -->
<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>