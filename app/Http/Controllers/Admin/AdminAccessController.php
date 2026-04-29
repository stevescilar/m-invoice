<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAccessController extends Controller
{
    // Secret token — change this to something only you know
    private const SECRET_TOKEN = 'msl-admin-9x7k2p';

    public function gate(Request $request, string $token)
    {
        if ($token !== self::SECRET_TOKEN) {
            abort(404); // looks like a normal 404
        }

        // Set a short-lived cookie that the login middleware checks
        return redirect()->route('admin.login')
            ->withCookie(cookie('admin_gate', hash('sha256', self::SECRET_TOKEN), 30)); // 30 min
    }
}