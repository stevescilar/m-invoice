<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">Invoxa</h1>
            <p class="text-gray-500 text-sm mt-1">Smart Invoicing for Kenyan Businesses</p>
        </div>
        <div class="bg-white rounded-xl shadow p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>