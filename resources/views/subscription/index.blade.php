@extends('layouts.app')

@section('title', 'Subscription')

@section('content')

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Subscription</h1>

    <!-- Current Status -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-gray-700 mb-4">Current Status</h2>

        @if($subscription->isOnTrial())
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
        @elseif($subscription->isActive())
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" x-data="paymentForm()">

        <!-- Per Invoice -->
        <div class="bg-white rounded-xl shadow p-5 border-2 hover:border-green-400 transition cursor-pointer"
            :class="selectedPlan === 'per_invoice' ? 'border-green-500' : 'border-gray-100'"
            @click="selectedPlan = 'per_invoice'">
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
        <div class="bg-white rounded-xl shadow p-5 border-2 hover:border-green-400 transition cursor-pointer"
            :class="selectedPlan === 'monthly' ? 'border-green-500' : 'border-gray-100'"
            @click="selectedPlan = 'monthly'">
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
        <div class="bg-white rounded-xl shadow p-5 border-2 hover:border-green-400 transition cursor-pointer relative"
            :class="selectedPlan === 'yearly' ? 'border-green-500' : 'border-gray-100'"
            @click="selectedPlan = 'yearly'">
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

        <!-- Payment Form -->
        <div class="md:col-span-3 bg-white rounded-xl shadow p-5" x-show="selectedPlan">
            <h3 class="font-semibold text-gray-700 mb-3">
                Pay via M-Pesa
                <span x-text="selectedPlan === 'per_invoice' ? '— Ksh 100' : selectedPlan === 'monthly' ? '— Ksh 700' : '— Ksh 5,000'"
                    class="text-green-600"></span>
            </h3>

            <div class="flex gap-3">
                <input type="tel" x-model="phone" placeholder="07XXXXXXXX"
                    class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <button @click="initiatePay"
                    :disabled="loading"
                    class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-700 disabled:opacity-50">
                    <span x-show="!loading">Pay Now</span>
                    <span x-show="loading">Sending STK Push...</span>
                </button>
            </div>

            <!-- Status Messages -->
            <div x-show="message" x-cloak class="mt-3 p-3 rounded-lg text-sm"
                :class="success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'"
                x-text="message">
            </div>

            <div x-show="polling" x-cloak class="mt-3 p-3 bg-blue-50 text-blue-700 rounded-lg text-sm">
                Waiting for M-Pesa confirmation on your phone...
                <span class="ml-2 animate-pulse">●</span>
            </div>

            <div x-show="paid" x-cloak class="mt-3 p-3 bg-green-50 text-green-700 rounded-lg text-sm font-semibold">
                Payment confirmed! Refreshing...
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    @if($transactions->count())
    <div class="bg-white rounded-xl shadow overflow-hidden">
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
</div>

<!-- Developer Bypass -->
<div class="mt-6 bg-gray-50 rounded-xl border border-dashed border-gray-300 p-5" x-data="{ open: false }">
    <button @click="open = !open" class="text-xs text-gray-400 hover:text-gray-600">
        Developer Access
    </button>
    <div x-show="open" x-cloak class="mt-3">
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

<script>
function paymentForm() {
    return {
        selectedPlan: null,
        phone: '{{ auth()->user()->company->phone ?? "" }}',
        loading: false,
        polling: false,
        paid: false,
        message: '',
        success: false,
        checkoutRequestId: null,
        pollInterval: null,

        async initiatePay() {
            if (!this.phone || !this.selectedPlan) return;
            this.loading  = true;
            this.message  = '';
            this.polling  = false;

            try {
                const response = await fetch('{{ route("subscription.pay") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ plan: this.selectedPlan, phone: this.phone })
                });

                const data = await response.json();

                if (data.success) {
                    this.checkoutRequestId = data.checkout_request_id;
                    this.message = 'STK push sent! Check your phone and enter your M-Pesa PIN.';
                    this.success = true;
                    this.polling = true;
                    this.startPolling();
                } else {
                    this.message = data.message;
                    this.success = false;
                }
            } catch (e) {
                this.message = 'Something went wrong. Please try again.';
                this.success = false;
            }

            this.loading = false;
        },

        startPolling() {
            let attempts = 0;
            this.pollInterval = setInterval(async () => {
                attempts++;
                if (attempts > 20) {
                    clearInterval(this.pollInterval);
                    this.polling = false;
                    this.message = 'Payment timeout. Please try again if you were charged.';
                    this.success = false;
                    return;
                }

                const response = await fetch('{{ route("subscription.status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ checkout_request_id: this.checkoutRequestId })
                });

                const data = await response.json();

                if (data.status === 'completed') {
                    clearInterval(this.pollInterval);
                    this.polling = false;
                    this.paid    = true;
                    setTimeout(() => window.location.reload(), 2000);
                } else if (data.status === 'failed') {
                    clearInterval(this.pollInterval);
                    this.polling = false;
                    this.message = 'Payment failed or was cancelled.';
                    this.success = false;
                }
            }, 3000);
        }
    }
}