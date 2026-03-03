@extends('layouts.app')
@section('title', 'Subscription')
@section('content')

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Subscription</h1>

    <!-- Current Status -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Current Status</h2>

        @if($subscription && $subscription->isOnTrial())
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold text-blue-700">Free Trial</p>
                <p class="text-sm text-blue-600">
                    {{ $subscription->daysLeftOnTrial() }} day(s) remaining —
                    expires {{ $subscription->trial_ends_at->format('M j, Y') }}
                </p>
            </div>
            <span class="bg-blue-100 text-blue-600 text-xs px-3 py-1 rounded-full font-medium">Trial</span>
        </div>

        @elseif($subscription && $subscription->isActive())
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold text-green-700">{{ ucfirst($subscription->plan) }} Plan — Active</p>
                @if($subscription->ends_at)
                <p class="text-sm text-green-600">Renews {{ $subscription->ends_at->format('M j, Y') }}</p>
                @endif
            </div>
            <span class="bg-green-100 text-green-600 text-xs px-3 py-1 rounded-full font-medium">Active</span>
        </div>

        @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="font-semibold text-red-700">Trial Expired</p>
            <p class="text-sm text-red-600">PDF downloads are blocked. Subscribe below to continue.</p>
        </div>
        @endif
    </div>

    <!-- Plans -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

        <!-- Per Invoice -->
        <div onclick="selectPlan('per_invoice')" id="plan-per_invoice"
            class="plan-card bg-white rounded-xl shadow p-5 border-2 border-gray-100 hover:border-green-400 transition cursor-pointer">
            <p class="font-bold text-gray-800 text-lg">Per Invoice</p>
            <p class="text-3xl font-bold text-green-600 my-2">Ksh 100</p>
            <p class="text-sm text-gray-500">Pay only when you download a PDF invoice</p>
            <ul class="text-xs text-gray-500 mt-3 space-y-1">
                <li>✓ One invoice download</li>
                <li>✓ Full invoice features</li>
                <li>✓ No commitment</li>
            </ul>
        </div>

        <!-- Monthly -->
        <div onclick="selectPlan('monthly')" id="plan-monthly"
            class="plan-card bg-white rounded-xl shadow p-5 border-2 border-gray-100 hover:border-green-400 transition cursor-pointer">
            <p class="font-bold text-gray-800 text-lg">Monthly</p>
            <p class="text-3xl font-bold text-green-600 my-2">Ksh 700</p>
            <p class="text-sm text-gray-500">Per month, billed monthly</p>
            <ul class="text-xs text-gray-500 mt-3 space-y-1">
                <li>✓ Unlimited downloads</li>
                <li>✓ All features</li>
                <li>✓ Email reminders</li>
            </ul>
        </div>

        <!-- Yearly -->
        <div onclick="selectPlan('yearly')" id="plan-yearly"
            class="plan-card bg-white rounded-xl shadow p-5 border-2 border-gray-100 hover:border-green-400 transition cursor-pointer relative">
            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">Best Value</span>
            <p class="font-bold text-gray-800 text-lg">Yearly</p>
            <p class="text-3xl font-bold text-green-600 my-2">Ksh 5,000</p>
            <p class="text-sm text-gray-500">Per year — save Ksh 3,400</p>
            <ul class="text-xs text-gray-500 mt-3 space-y-1">
                <li>✓ Unlimited downloads</li>
                <li>✓ All features</li>
                <li>✓ Priority support</li>
            </ul>
        </div>
    </div>

    <!-- Payment Form -->
    <div id="payment-form" class="hidden bg-white rounded-xl shadow p-5 mb-6">
        <h3 class="font-semibold text-gray-700 mb-3">
            Pay via M-Pesa —
            <span id="plan-label" class="text-green-600"></span>
        </h3>
        <div class="flex gap-3">
            <input type="tel" id="phone-input" placeholder="07XXXXXXXX"
                value="{{ auth()->user()->company->phone ?? '' }}"
                class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
            <button onclick="initiatePay()"
                id="pay-btn"
                class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-700">
                Pay Now
            </button>
        </div>
        <div id="pay-message" class="hidden mt-3 p-3 rounded-lg text-sm"></div>
        <div id="pay-polling" class="hidden mt-3 p-3 bg-blue-50 text-blue-700 rounded-lg text-sm">
            Waiting for M-Pesa confirmation on your phone...
        </div>
        <div id="pay-success" class="hidden mt-3 p-3 bg-green-50 text-green-700 rounded-lg text-sm font-semibold">
            Payment confirmed! Refreshing...
        </div>
    </div>

    <!-- Transaction History -->
    @if($transactions->count())
    <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">Payment History</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Plan</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-left">M-Pesa Code</th>
                    <th class="px-5 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($transactions as $tx)
                <tr>
                    <td class="px-5 py-3 text-gray-500">{{ $tx->created_at->format('M j, Y') }}</td>
                    <td class="px-5 py-3 capitalize">{{ str_replace('_', ' ', $tx->plan) }}</td>
                    <td class="px-5 py-3 text-right font-medium">Ksh {{ number_format($tx->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $tx->mpesa_code ?? '—' }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $tx->status === 'completed' ? 'bg-green-100 text-green-600' : '' }}
                            {{ $tx->status === 'pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                            {{ $tx->status === 'failed' ? 'bg-red-100 text-red-600' : '' }}">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Developer Bypass -->
    <div class="bg-gray-50 rounded-xl border border-dashed border-gray-300 p-5">
        <button onclick="document.getElementById('bypass-form').classList.toggle('hidden')"
            class="text-xs text-gray-400 hover:text-gray-600">
            Developer Access
        </button>
        <div id="bypass-form" class="hidden mt-3">
            <form method="POST" action="{{ route('subscription.bypass') }}" class="flex gap-3">
                @csrf
                <input type="password" name="bypass_code" placeholder="Enter bypass code"
                    class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                <button type="submit"
                    class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800">
                    Activate
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let selectedPlan = null;
let checkoutRequestId = null;
let pollInterval = null;

const planLabels = {
    per_invoice: 'Ksh 100',
    monthly: 'Ksh 700',
    yearly: 'Ksh 5,000'
};

function selectPlan(plan) {
    selectedPlan = plan;

    // Reset border styles
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('border-green-500');
        card.classList.add('border-gray-100');
    });

    // Highlight selected
    document.getElementById('plan-' + plan).classList.add('border-green-500');
    document.getElementById('plan-' + plan).classList.remove('border-gray-100');

    // Show payment form
    document.getElementById('payment-form').classList.remove('hidden');
    document.getElementById('plan-label').textContent = planLabels[plan];
}

async function initiatePay() {
    const phone = document.getElementById('phone-input').value;
    if (!phone || !selectedPlan) return;

    const btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.textContent = 'Sending STK Push...';

    hideMessages();

    try {
        const response = await fetch('{{ route("subscription.pay") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ plan: selectedPlan, phone: phone })
        });

        const data = await response.json();

        if (data.success) {
            checkoutRequestId = data.checkout_request_id;
            showMessage('STK push sent! Check your phone and enter your M-Pesa PIN.', true);
            document.getElementById('pay-polling').classList.remove('hidden');
            startPolling();
        } else {
            showMessage(data.message, false);
        }
    } catch (e) {
        showMessage('Something went wrong. Please try again.', false);
    }

    btn.disabled = false;
    btn.textContent = 'Pay Now';
}

function startPolling() {
    let attempts = 0;
    pollInterval = setInterval(async () => {
        attempts++;
        if (attempts > 20) {
            clearInterval(pollInterval);
            document.getElementById('pay-polling').classList.add('hidden');
            showMessage('Payment timeout. Please try again.', false);
            return;
        }

        const response = await fetch('{{ route("subscription.status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ checkout_request_id: checkoutRequestId })
        });

        const data = await response.json();

        if (data.status === 'completed') {
            clearInterval(pollInterval);
            document.getElementById('pay-polling').classList.add('hidden');
            document.getElementById('pay-success').classList.remove('hidden');
            setTimeout(() => window.location.reload(), 2000);
        } else if (data.status === 'failed') {
            clearInterval(pollInterval);
            document.getElementById('pay-polling').classList.add('hidden');
            showMessage('Payment failed or was cancelled.', false);
        }
    }, 3000);
}

function showMessage(msg, success) {
    const el = document.getElementById('pay-message');
    el.textContent = msg;
    el.className = 'mt-3 p-3 rounded-lg text-sm ' + (success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600');
    el.classList.remove('hidden');
}

function hideMessages() {
    document.getElementById('pay-message').classList.add('hidden');
    document.getElementById('pay-polling').classList.add('hidden');
    document.getElementById('pay-success').classList.add('hidden');
}
</script>

@endsection