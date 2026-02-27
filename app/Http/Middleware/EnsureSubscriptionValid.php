<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSubscriptionValid
{
    public function handle(Request $request, Closure $next)
    {
        $company = $request->user()->company;
        $subscription = $company?->subscription;

        if (!$subscription) {
            return redirect()->route('subscription.index')
                ->withErrors(['No active subscription found.']);
        }

        return $next($request);
    }
}