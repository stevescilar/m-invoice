<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <div class="font-bold text-xl text-green-600">{{ auth()->user()->company->name ?? config('app.name') }}</div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600">Dashboard</a>
            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-green-600">Clients</a>
            <a href="{{ route('invoices.index') }}" class="text-gray-600 hover:text-green-600">Invoices</a>
            <a href="{{ route('quotations.index') }}" class="text-gray-600 hover:text-green-600">Quotations</a>
            <a href="{{ route('expenses.index') }}" class="text-gray-600 hover:text-green-600">Expenses</a>
            <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-green-600">Catalog</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-red-500 hover:text-red-700">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto mt-4 px-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
    </main>

</body>

</html>
