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
    <!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

<!-- Navbar -->
<nav class="bg-white shadow-sm px-6 py-3 flex justify-between items-center sticky top-0 z-50">
    <div class="font-bold text-lg text-green-600 tracking-tight">
        {{ auth()->user()->company->name ?? config('app.name') }}
    </div>

    <div class="flex items-center gap-1 text-sm">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
        </a>
        <a href="{{ route('clients.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('clients*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="users" class="w-4 h-4"></i> Clients
        </a>
        <a href="{{ route('invoices.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('invoices*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="file-text" class="w-4 h-4"></i> Invoices
        </a>
        <a href="{{ route('quotations.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('quotations*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i> Quotations
        </a>
        <a href="{{ route('expenses.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('expenses*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="wallet" class="w-4 h-4"></i> Expenses
        </a>
        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('categories*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="package" class="w-4 h-4"></i> Catalog
        </a>
        @if(auth()->user()->isOwner())
        <a href="{{ route('staff.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('staff*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="user-check" class="w-4 h-4"></i> Staff
        </a>
        @endif
        <a href="{{ route('subscription.index') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('subscription*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="credit-card" class="w-4 h-4"></i> Subscription
        </a>
        <a href="{{ route('company.settings') }}"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg transition {{ request()->routeIs('company.settings*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' }}">
            <i data-lucide="settings" class="w-4 h-4"></i> Settings
        </a>

        <!-- Divider -->
        <div class="w-px h-6 bg-gray-200 mx-1"></div>

        <!-- User avatar + logout -->
        <div class="flex items-center gap-2 pl-1">
            <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold text-xs flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition text-sm">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
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

    <!-- Auto logout warning -->
<div id="inactivity-warning" class="hidden fixed bottom-4 right-4 bg-yellow-50 border border-yellow-300 rounded-xl shadow-lg p-4 z-50 max-w-sm">
    <p class="text-sm font-semibold text-yellow-800">⚠️ Still there?</p>
    <p class="text-xs text-yellow-600 mt-1">You'll be logged out in <span id="countdown">60</span> seconds due to inactivity.</p>
    <button onclick="resetTimer()"
        class="mt-3 w-full bg-yellow-500 text-white py-1.5 rounded-lg text-sm hover:bg-yellow-600">
        Keep me logged in
    </button>
</div>

<script>
const INACTIVE_LIMIT = 44 * 60 * 1000; // 44 minutes — warn at 44, logout at 45
const WARNING_TIME   = 60; // 60 second countdown before logout

let inactivityTimer;
let countdownTimer;
let countdownValue = WARNING_TIME;
const warning     = document.getElementById('inactivity-warning');
const countdownEl = document.getElementById('countdown');

function startTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(showWarning, INACTIVE_LIMIT);
}

function showWarning() {
    countdownValue = WARNING_TIME;
    warning.classList.remove('hidden');
    countdownTimer = setInterval(() => {
        countdownValue--;
        countdownEl.textContent = countdownValue;
        if (countdownValue <= 0) {
            clearInterval(countdownTimer);
            logoutUser();
        }
    }, 1000);
}

function resetTimer() {
    clearInterval(countdownTimer);
    warning.classList.add('hidden');
    startTimer();
}

function logoutUser() {
    const form  = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("logout") }}';
    const csrf  = document.createElement('input');
    csrf.type   = 'hidden';
    csrf.name   = '_token';
    csrf.value  = '{{ csrf_token() }}';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}

// Reset on any activity including form typing
const activityEvents = [
    'mousemove', 'keydown', 'keypress', 'click',
    'scroll', 'touchstart', 'input', 'change', 'focus', 'select'
];

activityEvents.forEach(event => {
    document.addEventListener(event, () => {
        if (!warning.classList.contains('hidden')) return;
        startTimer();
    }, { passive: true });
});

// Start on page load
startTimer();
</script>
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