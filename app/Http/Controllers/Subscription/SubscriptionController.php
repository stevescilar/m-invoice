<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $company      = Auth::user()->company;
        $subscription = $company->subscription;

        $transactions = SubscriptionTransaction::where('company_id', $company->id)
            ->latest()
            ->take(10)
            ->get();

        return view('subscription.index', compact('company', 'subscription', 'transactions'));
    }

    public function initiatePay(Request $request)
    {
        $request->validate([
            'plan'  => 'required|in:per_invoice,monthly,yearly',
            'phone' => 'required|string',
        ]);

        $plans = [
            'per_invoice' => ['amount' => 100,  'label' => 'Per Invoice Download'],
            'monthly'     => ['amount' => 700,  'label' => 'Monthly Plan'],
            'yearly'      => ['amount' => 5000, 'label' => 'Yearly Plan'],
        ];

        $plan   = $plans[$request->plan];
        $phone  = $request->phone;
        $company = Auth::user()->company;

        $mpesa     = new MpesaService();
        $reference = 'MINV-' . $company->id . '-' . time();
        $result    = $mpesa->stkPush($phone, $plan['amount'], $reference, 'M-Invoice ' . $plan['label']);

        if ($result['success']) {
            // Store pending transaction
            $transaction = SubscriptionTransaction::create([
                'company_id'          => $company->id,
                'plan'                => $request->plan,
                'amount'              => $plan['amount'],
                'status'              => 'pending',
                'checkout_request_id' => $result['checkout_request_id'],
                'reference'           => $reference,
            ]);

            // Cache checkout request for status polling
            Cache::put('mpesa_checkout_' . $result['checkout_request_id'], [
                'company_id'     => $company->id,
                'plan'           => $request->plan,
                'transaction_id' => $transaction->id,
            ], 600);

            return response()->json([
                'success'             => true,
                'message'             => $result['message'],
                'checkout_request_id' => $result['checkout_request_id'],
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message']], 422);
    }

    public function checkStatus(Request $request)
    {
        $request->validate(['checkout_request_id' => 'required|string']);

        $transaction = SubscriptionTransaction::where('checkout_request_id', $request->checkout_request_id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json(['status' => $transaction->status]);
    }

    // M-Pesa callback (called by Safaricom)
    public function callback(Request $request)
    {
        Log::info('M-Pesa callback: ' . json_encode($request->all()));

        $data     = $request->input('Body.stkCallback');
        $checkoutId = $data['CheckoutRequestID'] ?? null;
        $resultCode = $data['ResultCode'] ?? null;

        if (!$checkoutId) return response()->json(['status' => 'ok']);

        $transaction = SubscriptionTransaction::where('checkout_request_id', $checkoutId)->first();

        if (!$transaction) return response()->json(['status' => 'ok']);

        if ($resultCode == 0) {
            // Payment successful
            $metadata  = collect($data['CallbackMetadata']['Item'] ?? []);
            $mpesaCode = $metadata->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;

            $transaction->update([
                'status'     => 'completed',
                'mpesa_code' => $mpesaCode,
            ]);

            $this->activateSubscription($transaction);

        } else {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'ok']);
    }

    private function activateSubscription(SubscriptionTransaction $transaction): void
    {
        $company      = $transaction->company;
        $subscription = $company->subscription;

        if ($transaction->plan === 'monthly') {
            $subscription->update([
                'plan'     => 'monthly',
                'status'   => 'active',
                'on_trial' => false,
                'starts_at' => now(),
                'ends_at'   => now()->addMonth(),
            ]);
        } elseif ($transaction->plan === 'yearly') {
            $subscription->update([
                'plan'     => 'yearly',
                'status'   => 'active',
                'on_trial' => false,
                'starts_at' => now(),
                'ends_at'   => now()->addYear(),
            ]);
        } elseif ($transaction->plan === 'per_invoice') {
            // Per invoice — just log the transaction, handled at download time
            $subscription->update(['on_trial' => false]);
        }
    }
    public function activateBypass(Request $request)
    {
        $request->validate([
            'bypass_code' => 'required|string',
        ]);

        if ($request->bypass_code !== config('app.owner_bypass_code')) {
            return back()->with('error', 'Invalid bypass code.');
        }

        $company = Auth::user()->company;
        $company->update(['is_bypass' => true]);

        // Also set subscription to active forever
        $company->subscription->update([
            'plan'     => 'yearly',
            'status'   => 'active',
            'on_trial' => false,
            'ends_at'  => now()->addYears(10),
        ]);

        return back()->with('success', 'Developer bypass activated. Enjoy free access!');
    }
}
