<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Block print screen / CSS print */
        @media print {
            body { display: none !important; }
        }
    
        /* Screenshot deterrent overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 9999;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 200px,
                rgba(0,0,0,0.015) 200px,
                rgba(0,0,0,0.015) 201px
            );
        }
    
        /* Blur sensitive data */
        .sensitive {
            filter: blur(4px);
            transition: filter 0.2s ease;
            cursor: pointer;
            user-select: none;
        }
        .sensitive:hover {
            filter: blur(0);
        }
        .sensitive::after {
            content: ' 👁';
            font-size: 10px;
            opacity: 0.4;
        }
    
        /* Disable text selection on financial figures */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
    </style>
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
            <a href="{{ route('subscription.index') }}" class="text-gray-600 hover:text-green-600">Subscription</a>
            <a href="{{ route('company.settings') }}" class="text-gray-600 hover:text-green-600">Settings</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-red-500 hover:text-red-700">Logout</button>
            </form>
        </div>
    </nav>
    @if(auth()->check() && auth()->user()->company?->subscription?->isOnTrial())
    <div class="bg-blue-600 text-white text-center text-sm py-2 px-4">
        🎉 You are on a free trial —
        <strong>{{ auth()->user()->company->subscription->daysLeftOnTrial() }} day(s) left</strong>.
        <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Upgrade now</a>
    </div>
    @elseif(auth()->check() && !auth()->user()->company?->subscription?->isActive())
    <div class="bg-red-600 text-white text-center text-sm py-2 px-4">
        ⚠️ Your trial has expired. PDF downloads are disabled.
        <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Subscribe now</a>
    </div>
    @endif
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


    <script>
        // Disable right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    
        // Disable common screenshot shortcuts
        document.addEventListener('keydown', function(e) {
            // PrintScreen
            if (e.key === 'PrintScreen') {
                navigator.clipboard.writeText('');
                e.preventDefault();
            }
            // Ctrl+P (print)
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
            }
            // Ctrl+Shift+I (devtools)
            if (e.ctrlKey && e.shiftKey && e.key === 'I') {
                e.preventDefault();
            }
            // F12 (devtools)
            if (e.key === 'F12') {
                e.preventDefault();
            }
        });
    
        // Detect devtools open
        (function() {
            const threshold = 160;
            setInterval(function() {
                if (window.outerWidth - window.innerWidth > threshold ||
                    window.outerHeight - window.innerHeight > threshold) {
                    document.body.innerHTML = '<div style="display:flex;height:100vh;align-items:center;justify-content:center;font-family:sans-serif;"><h2>Please close developer tools to continue.</h2></div>';
                }
            }, 1000);
        })();
    </script>
</body>

</html>
