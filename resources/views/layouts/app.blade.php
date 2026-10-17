<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        @media print { body { display: none !important; } }

        body::after {
            content: '';
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 9999;
            background: repeating-linear-gradient(45deg, transparent, transparent 200px, rgba(0,0,0,0.015) 200px, rgba(0,0,0,0.015) 201px);
        }

        .sensitive { filter: blur(4px); transition: filter 0.2s; cursor: pointer; user-select: none; }
        .sensitive:hover { filter: blur(0); }
        .sensitive::after { content: ' 👁'; font-size: 10px; opacity: 0.4; }
        .no-select { -webkit-user-select: none; user-select: none; }

        .nav-link {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 11px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            color: #6b7280; white-space: nowrap; transition: all 0.15s;
            text-decoration: none;
        }
        .nav-link:hover { background: #f3f4f6; color: #111827; }
        .nav-link.active { background: #f0fdf4; color: #15803d; font-weight: 600; }

        .m-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 10px;
            font-size: 15px; font-weight: 500; color: #374151;
            transition: all 0.15s; text-decoration: none;
        }
        .m-link:hover { background: #f9fafb; }
        .m-link.active { background: #f0fdf4; color: #15803d; font-weight: 600; }
    </style>
     <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-35ZRVY4HXP"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-35ZRVY4HXP');
</script>
</head>

<body class="bg-gray-100 min-h-screen" x-data="{ open: false }">

    {{-- ════ NAVBAR ════ --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">

                {{-- Brand --}}
                <a href="{{ route('dashboard') }}"
                    class="font-extrabold text-base text-green-600 tracking-tight flex-shrink-0 truncate max-w-[150px] sm:max-w-xs">
                    {{ auth()->user()->company->name ?? config('app.name') }}
                </a>

                {{-- Desktop links --}}
                <div class="hidden lg:flex items-center gap-0.5 flex-1 justify-center">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="nav-link {{ request()->routeIs('clients*') ? 'active' : '' }}">
                        <i data-lucide="users" class="w-4 h-4"></i> Clients
                    </a>
                    <a href="{{ route('invoices.index') }}"
                        class="nav-link {{ request()->routeIs('invoices*') ? 'active' : '' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Invoices
                    </a>
                    <a href="{{ route('quotations.index') }}"
                        class="nav-link {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i> Quotations
                    </a>
                    <a href="{{ route('expenses.index') }}"
                        class="nav-link {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                        <i data-lucide="wallet" class="w-4 h-4"></i> Expenses
                    </a>
                    <a href="{{ route('categories.index') }}"
                        class="nav-link {{ request()->routeIs('categories*') ? 'active' : '' }}">
                        <i data-lucide="package" class="w-4 h-4"></i> Catalog
                    </a>
                    @if(auth()->user()->isOwner())
                    <a href="{{ route('staff.index') }}"
                        class="nav-link {{ request()->routeIs('staff*') ? 'active' : '' }}">
                        <i data-lucide="user-check" class="w-4 h-4"></i> Staff
                    </a>
                    @endif
                    <a href="{{ route('subscription.index') }}"
                        class="nav-link {{ request()->routeIs('subscription*') ? 'active' : '' }}">
                        <i data-lucide="credit-card" class="w-4 h-4"></i> Plan
                    </a>
                    <a href="{{ route('company.settings') }}"
                        class="nav-link {{ request()->routeIs('company.settings*') ? 'active' : '' }}"
                        title="Settings">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </a>
                </div>

                {{-- Desktop right --}}
                <div class="hidden lg:flex items-center gap-1 flex-shrink-0">
                    <div class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="nav-link text-red-400 hover:!bg-red-50 hover:!text-red-600" title="Logout">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

                {{-- Mobile: avatar + hamburger --}}
                <div class="flex lg:hidden items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <button @click="open = !open"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition focus:outline-none">
                        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="lg:hidden border-t border-gray-100 bg-white px-4 pt-3 pb-4 shadow-xl"
            @click.outside="open = false">

            {{-- User info --}}
            <div class="flex items-center gap-3 px-1 mb-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>

            {{-- Nav links grid --}}
            <div class="grid grid-cols-2 gap-1">
                <a href="{{ route('dashboard') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('clients.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('clients*') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Clients</span>
                </a>
                <a href="{{ route('invoices.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('invoices*') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Invoices</span>
                </a>
                <a href="{{ route('quotations.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Quotations</span>
                </a>
                <a href="{{ route('expenses.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                    <i data-lucide="wallet" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Expenses</span>
                </a>
                <a href="{{ route('categories.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('categories*') ? 'active' : '' }}">
                    <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Catalog</span>
                </a>
                @if(auth()->user()->isOwner())
                <a href="{{ route('staff.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('staff*') ? 'active' : '' }}">
                    <i data-lucide="user-check" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Staff</span>
                </a>
                @endif
                <a href="{{ route('subscription.index') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('subscription*') ? 'active' : '' }}">
                    <i data-lucide="credit-card" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Subscription</span>
                </a>
                <a href="{{ route('company.settings') }}" @click="open=false"
                    class="m-link {{ request()->routeIs('company.settings*') ? 'active' : '' }}">
                    <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Settings</span>
                </a>
            </div>

            {{-- Logout --}}
            <div class="mt-3 pt-3 border-t border-gray-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-red-500 bg-red-50 font-semibold text-sm hover:bg-red-100 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Sign out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Subscription banners --}}
    @if(auth()->check() && auth()->user()->company?->subscription?->isOnTrial())
    <div class="bg-blue-600 text-white text-center text-sm py-2 px-4">
        🎉 Free trial —
        <strong>{{ auth()->user()->company->subscription->daysLeftOnTrial() }} day(s) left</strong>.
        <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Upgrade now</a>
    </div>
    @elseif(auth()->check() && !auth()->user()->company?->subscription?->isActive())
    <div class="bg-red-600 text-white text-center text-sm py-2 px-4">
        ⚠️ Trial expired. PDF downloads are disabled.
        <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Subscribe now</a>
    </div>
    @endif

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6">
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- Page content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @yield('content')
    </main>

    {{-- Inactivity warning --}}
    <div id="inactivity-warning"
        class="hidden fixed bottom-4 right-4 left-4 sm:left-auto sm:w-80 bg-yellow-50 border border-yellow-300 rounded-xl shadow-lg p-4 z-50">
        <p class="text-sm font-semibold text-yellow-800">⚠️ Still there?</p>
        <p class="text-xs text-yellow-600 mt-1">You'll be logged out in <span id="countdown">60</span> seconds.</p>
        <button onclick="resetTimer()"
            class="mt-3 w-full bg-yellow-500 text-white py-2 rounded-lg text-sm hover:bg-yellow-600 font-medium">
            Keep me logged in
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

        // Inactivity
        const LIMIT = 44 * 60 * 1000;
        let inactivityTimer, countdownTimer, cv = 60;
        const warn = document.getElementById('inactivity-warning');
        const cEl  = document.getElementById('countdown');

        function startTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                cv = 60; warn.classList.remove('hidden');
                countdownTimer = setInterval(() => {
                    cEl.textContent = --cv;
                    if (cv <= 0) { clearInterval(countdownTimer); doLogout(); }
                }, 1000);
            }, LIMIT);
        }
        function resetTimer() {
            clearInterval(countdownTimer); warn.classList.add('hidden'); startTimer();
        }
        function doLogout() {
            const f = document.createElement('form');
            f.method = 'POST'; f.action = '{{ route("logout") }}';
            const t = document.createElement('input');
            t.type = 'hidden'; t.name = '_token'; t.value = '{{ csrf_token() }}';
            f.appendChild(t); document.body.appendChild(f); f.submit();
        }
        ['mousemove','keydown','click','scroll','touchstart','input'].forEach(e =>
            document.addEventListener(e, () => { if (!warn.classList.contains('hidden')) return; startTimer(); }, { passive: true })
        );
        startTimer();

        // Security
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.key === 'PrintScreen') { navigator.clipboard?.writeText(''); e.preventDefault(); }
            if ((e.ctrlKey && e.key === 'p') || (e.ctrlKey && e.shiftKey && e.key === 'I') || e.key === 'F12') e.preventDefault();
        });
    </script>

@include('partials.chatbot')
</body>
</html>