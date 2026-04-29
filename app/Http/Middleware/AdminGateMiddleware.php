<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminGateMiddleware
{
    private const SECRET_TOKEN = 'msl-admin-9x7k2p'; // must match AdminAccessController

    public function handle(Request $request, Closure $next)
    {
        $expected = hash('sha256', self::SECRET_TOKEN);
        $cookie   = $request->cookie('admin_gate');

        if ($cookie !== $expected) {
            abort(404);
        }

        return $next($request);
    }
}