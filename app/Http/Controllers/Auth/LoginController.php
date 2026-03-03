<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
        {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');
            $remember    = $request->boolean('remember');

            if (!Auth::attempt($credentials, $remember)) {
                return back()->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Your account has been deactivated.');
            }

            return redirect()->intended(route('dashboard'));
        }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}