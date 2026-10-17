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
        [x-cloak] { display: none !important; }
        @media print { body { display: none !important; } }

        body::after {
            content: '';
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 9999;
            background: repeating-linear-gradient(45deg, transparent, transparent 200px, rgba(0,0,0,0.012) 200px, rgba(0,0,0,0.012) 201px);
        }

        .sensitive { filter: blur(4px); transition: filter 0.2s; cursor: pointer; user-select: none; }
        .sensitive:hover { filter: blur(0); }
        .sensitive::after { content: ' 👁'; font-size: 10px; opacity: 0.4; }
        .no-select { -webkit-user-select: none; user-select: none; }

        .side-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 10px;
            font-size: 13.5px; font-weight: 500;
            color: #4b5563; transition: all 0.15s ease;
            text-decoration: none;
        }
        .side-link:hover { background: #f3f4f6; color: #111827; }
        .side-link.active {
            background: #f0fdf4; color: #15803d; font-weight: 600;
            box-shadow: inset 3px 0 0 #16a34a;
        }
        .side-section-label {
            font-size: 10.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: #9ca3af; padding: 0 12px; margin-bottom: 6px;
        }
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

@php
    $navCompany = auth()->user()->company;
    $navUnreadCount = $navCompany ? \App\Models\Notification::where('company_id', $navCompany->id)->where('is_read', false)->count() : 0;
    $navRecentNotifs = $navCompany ? \App\Models\Notification::where('company_id', $navCompany->id)->latest()->take(6)->get() : collect();
@endphp

<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        {{-- ════ MOBILE SIDEBAR BACKDROP ════ --}}
        <div x-show="sidebarOpen" x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 lg:hidden">
        </div>

        {{-- ════ LEFT SIDEBAR ════ --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200/80 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex-shrink-0">

            {{-- Brand Header --}}
            <div class="h-16 px-4 border-b border-gray-100 flex items-center justify-between gap-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                    @if($navCompany?->logo)
                        <img src="{{ Storage::url($navCompany->logo) }}" alt="Logo" class="w-9 h-9 rounded-lg object-contain border border-gray-100 bg-white p-0.5 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-green-600 text-white flex items-center justify-center font-extrabold text-sm shadow-xs flex-shrink-0">
                            {{ strtoupper(substr($navCompany->name ?? config('app.name'), 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-sm text-gray-900 truncate leading-tight">
                            {{ $navCompany->name ?? config('app.name') }}
                        </p>
                        <p class="text-[11px] text-gray-400 truncate">Invoxa Billing</p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Navigation Links --}}
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">

                {{-- Core Operations --}}
                <div>
                    <p class="side-section-label">Overview &amp; Sales</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('clients.index') }}" class="side-link {{ request()->routeIs('clients*') ? 'active' : '' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Clients</span>
                        </a>
                        <a href="{{ route('quotations.index') }}" class="side-link {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                            <i data-lucide="clipboard-list" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Quotations</span>
                        </a>
                        <a href="{{ route('invoices.index') }}" class="side-link {{ request()->routeIs('invoices*') ? 'active' : '' }}">
                            <i data-lucide="file-text" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Invoices</span>
                        </a>
                    </div>
                </div>

                {{-- Finance & Catalog --}}
                <div>
                    <p class="side-section-label">Finance &amp; Catalog</p>
                    <div class="space-y-1">
                        <a href="{{ route('expenses.index') }}" class="side-link {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                            <i data-lucide="wallet" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Expenses</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="side-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Reports &amp; KRA Tax</span>
                        </a>
                        <a href="{{ route('categories.index') }}" class="side-link {{ request()->routeIs('categories*') ? 'active' : '' }}">
                            <i data-lucide="package" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Catalog &amp; Pricing</span>
                        </a>
                    </div>
                </div>

                {{-- Workspace & Settings --}}
                <div>
                    <p class="side-section-label">Workspace</p>
                    <div class="space-y-1">
                        <a href="{{ route('notifications.index') }}" class="side-link justify-between {{ request()->routeIs('notifications*') ? 'active' : '' }}">
                            <span class="flex items-center gap-2.5">
                                <i data-lucide="bell" class="w-4 h-4 flex-shrink-0"></i>
                                <span>Client Responses</span>
                            </span>
                            @if($navUnreadCount > 0)
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    {{ $navUnreadCount > 99 ? '99+' : $navUnreadCount }}
                                </span>
                            @endif
                        </a>
                        @if(auth()->user()->isOwner())
                        <a href="{{ route('staff.index') }}" class="side-link {{ request()->routeIs('staff*') ? 'active' : '' }}">
                            <i data-lucide="user-check" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Team &amp; Staff</span>
                        </a>
                        @endif
                        <a href="{{ route('subscription.index') }}" class="side-link {{ request()->routeIs('subscription*') ? 'active' : '' }}">
                            <i data-lucide="credit-card" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Subscription Plan</span>
                        </a>
                        <a href="{{ route('company.settings') }}" class="side-link {{ request()->routeIs('company.settings*') ? 'active' : '' }}">
                            <i data-lucide="settings" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Company Settings</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Sidebar Footer: User Account & Logout --}}
            <div class="p-3 border-t border-gray-100 bg-gray-50/60">
                <div class="flex items-center justify-between gap-2 px-2 py-1.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" title="Sign out"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ════ MAIN CONTENT COLUMN ════ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Subscription Trial / Expiry Strip --}}
            @if(auth()->check() && $navCompany?->subscription?->isOnTrial())
            <div class="bg-blue-600 text-white text-center text-xs sm:text-sm py-2 px-4 font-medium">
                🎉 Free trial —
                <strong>{{ $navCompany->subscription->daysLeftOnTrial() }} day(s) left</strong>.
                <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Upgrade now →</a>
            </div>
            @elseif(auth()->check() && !$navCompany?->subscription?->isActive())
            <div class="bg-red-600 text-white text-center text-xs sm:text-sm py-2 px-4 font-medium">
                ⚠️ Subscription expired. PDF downloads are paused.
                <a href="{{ route('subscription.index') }}" class="underline ml-2 font-semibold">Renew now →</a>
            </div>
            @endif

            {{-- ════ TOP HEADER BAR ════ --}}
            <header class="bg-white border-b border-gray-200/80 sticky top-0 z-30 h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">

                {{-- Left: Hamburger + Page Title --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" @click="sidebarOpen = true"
                        class="lg:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800 truncate">@yield('title', 'Dashboard')</h2>
                    </div>
                </div>

                {{-- Right: Quick Create + Notification Bell + Settings --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- Quick Create Dropdown --}}
                    <div class="relative" x-data="{ createOpen: false }" @click.outside="createOpen = false">
                        <button type="button" @click="createOpen = !createOpen"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-semibold px-3 sm:px-3.5 py-2 rounded-lg shadow-xs flex items-center gap-1.5 transition">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Quick Create</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-80"></i>
                        </button>

                        <div x-show="createOpen" x-cloak x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50 text-sm">
                            <a href="{{ route('invoices.create') }}"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium">
                                <i data-lucide="file-plus" class="w-4 h-4 text-green-600"></i> New Invoice
                            </a>
                            <a href="{{ route('quotations.create') }}"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium">
                                <i data-lucide="clipboard-plus" class="w-4 h-4 text-blue-600"></i> New Quotation
                            </a>
                            <a href="{{ route('clients.create') }}"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium">
                                <i data-lucide="user-plus" class="w-4 h-4 text-purple-600"></i> Add Client
                            </a>
                            <div class="my-1 border-t border-gray-100"></div>
                            <a href="{{ route('expenses.create') }}"
                                class="flex items-center gap-2.5 px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-700 font-medium">
                                <i data-lucide="wallet" class="w-4 h-4 text-red-500"></i> Log Expense
                            </a>
                        </div>
                    </div>

                    {{-- Notification Bell Dropdown --}}
                    <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                        <button type="button" @click="notifOpen = !notifOpen"
                            class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition focus:outline-none"
                            title="Notifications & Client Responses">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($navUnreadCount > 0)
                                <span class="absolute top-1 right-1 min-w-[17px] h-[17px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                    {{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="notifOpen" x-cloak x-transition
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-700">
                                    Client Notifications
                                    @if($navUnreadCount > 0)
                                        <span class="ml-1 bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full text-[10px]">{{ $navUnreadCount }} new</span>
                                    @endif
                                </span>
                                @if($navUnreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-green-600 hover:underline font-medium">Mark all read</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                @forelse($navRecentNotifs as $n)
                                    <a href="{{ route('notifications.open', $n) }}"
                                        class="block px-4 py-3 hover:bg-gray-50 transition {{ !$n->is_read ? 'bg-green-50/50' : '' }}">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-bold text-gray-800 truncate">{{ $n->title }}</span>
                                            <span class="text-[10px] text-gray-400 flex-shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $n->message }}</p>
                                        @if($n->client_comment)
                                            <p class="text-[11px] text-amber-800 bg-amber-50 border border-amber-100 rounded px-2 py-1 mt-1 truncate">
                                                💬 "{{ $n->client_comment }}"
                                            </p>
                                        @endif
                                    </a>
                                @empty
                                    <div class="py-8 px-4 text-center text-xs text-gray-400">
                                        No client responses or notifications yet.
                                    </div>
                                @endforelse
                            </div>

                            <a href="{{ route('notifications.index') }}"
                                class="block text-center py-2.5 bg-gray-50 hover:bg-gray-100 text-xs font-semibold text-green-600 border-t border-gray-100">
                                View All Notifications →
                            </a>
                        </div>
                    </div>

                </div>
            </header>

            {{-- Flash Messages (Auto-dismissing with manual close button) --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition
                class="max-w-7xl w-full mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-600 hover:text-green-800 p-1">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                class="max-w-7xl w-full mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800 p-1">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Main Page Content --}}
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

        </div>
    </div>

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