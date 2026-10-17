@extends('layouts.app')
@section('title', 'Client Notifications & Responses')
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2.5">
            <h1 class="text-2xl font-bold text-gray-800">Notifications &amp; Client Responses</h1>
            @if($unreadCount > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $unreadCount }} Unread</span>
            @endif
        </div>
        <p class="text-xs text-gray-400 mt-0.5">Track quotation approvals, declines, client comments, and submitted M-Pesa payment references.</p>
    </div>

    @if($unreadCount > 0)
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
        @csrf
        <button type="submit"
            class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
            <i data-lucide="check-check" class="w-4 h-4 text-green-600"></i> Mark All as Read
        </button>
    </form>
    @endif
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-5">
    @php
        $tabs = [
            'all'                => 'All Activity',
            'unread'             => 'Unread (' . $unreadCount . ')',
            'quotation_approved' => '✅ Approved Quotes',
            'quotation_rejected' => '❌ Declined Quotes',
            'payment_submitted'  => '💳 Payment Confirmations',
        ];
    @endphp
    @foreach($tabs as $key => $label)
        <a href="{{ route('notifications.index', ['filter' => $key]) }}"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition
            {{ $filter === $key ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Notifications Feed --}}
<div class="bg-white rounded-xl shadow divide-y divide-gray-100 overflow-hidden">
    @forelse($notifications as $notif)
        @php
            $badge = match($notif->type) {
                'quotation_approved' => ['bg-green-100 text-green-700', 'check-circle-2', 'Approved'],
                'quotation_rejected' => ['bg-red-100 text-red-600', 'x-circle', 'Declined'],
                'payment_submitted'  => ['bg-emerald-100 text-emerald-700', 'credit-card', 'Payment Ref'],
                default              => ['bg-blue-100 text-blue-600', 'bell', 'Alert'],
            };
        @endphp
        <div class="p-4 sm:p-5 flex items-start justify-between gap-4 transition {{ !$notif->is_read ? 'bg-green-50/40' : 'hover:bg-gray-50/60' }}">
            <div class="flex items-start gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $badge[0] }}">
                    <i data-lucide="{{ $badge[1] }}" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('notifications.open', $notif) }}"
                            class="text-sm font-bold text-gray-900 hover:text-green-600 transition">
                            {{ $notif->title }}
                        </a>
                        @if(!$notif->is_read)
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block" title="Unread"></span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-sm text-gray-600 mt-0.5">{{ $notif->message }}</p>

                    @if($notif->client_comment)
                        <div class="mt-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900">
                            <span class="font-bold">💬 Client Comment:</span> "{{ $notif->client_comment }}"
                        </div>
                    @endif

                    <div class="mt-2.5 flex items-center gap-3">
                        <a href="{{ route('notifications.open', $notif) }}"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 hover:underline">
                            View Document →
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('notifications.destroy', $notif) }}"
                onsubmit="return confirm('Delete this notification?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition" title="Delete">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    @empty
        <div class="py-16 px-6 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i data-lucide="bell-off" class="w-6 h-6 text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium text-sm">No client notifications yet</p>
            <p class="text-xs text-gray-400 mt-1">When a client approves a quotation or submits an M-Pesa payment reference online, it will appear right here.</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->appends(request()->query())->links() }}
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

@endsection
