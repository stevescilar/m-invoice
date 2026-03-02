<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->company_id) {
            return redirect()->route('company.setup');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['Account is inactive. Contact your administrator.']);
        }

        return $next($request);
    }
}
