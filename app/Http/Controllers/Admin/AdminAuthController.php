<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminLoginOtpMail;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminAuthController extends Controller
{
    // ── Step 1: Show login form ──────────────────────────────
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    // ── Step 2: Validate credentials → send OTP ─────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::guard('admin')->validate($credentials)) {
            return back()->withErrors(['email' => 'Invalid admin credentials.'])->onlyInput('email');
        }

        $admin = AdminUser::where('email', $credentials['email'])->first();

        // Generate 6-digit OTP
        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = now()->addMinutes(10)->timestamp;

        // Store in session
        session([
            'admin_otp'         => hash('sha256', $otp),
            'admin_otp_expires' => $expires,
            'admin_otp_email'   => $admin->email,
            'admin_remember'    => $request->boolean('remember'),
        ]);

        // Send OTP email
        Mail::to($admin->email)->send(new AdminLoginOtpMail($otp, $admin->name));

        return redirect()->route('admin.otp');
    }

    // ── Step 3: Show OTP form ────────────────────────────────
    public function showOtp()
    {
        if (!session('admin_otp_email')) {
            return redirect()->route('admin.login');
        }
        return view('admin.auth.otp');
    }

    // ── Step 4: Verify OTP ───────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        if (!session('admin_otp_email')) {
            return redirect()->route('admin.login');
        }

        if (now()->timestamp > session('admin_otp_expires')) {
            session()->forget(['admin_otp', 'admin_otp_expires', 'admin_otp_email']);
            return back()->withErrors(['otp' => 'Code expired. Please log in again.']);
        }

        if (!hash_equals(session('admin_otp'), hash('sha256', $request->otp))) {
            return back()->withErrors(['otp' => 'Incorrect code. Please try again.']);
        }

        $admin = AdminUser::where('email', session('admin_otp_email'))->first();
        Auth::guard('admin')->login($admin, session('admin_remember', false));

        session()->forget(['admin_otp', 'admin_otp_expires', 'admin_otp_email', 'admin_remember']);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    // ── Resend OTP ───────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        if (!session('admin_otp_email')) {
            return redirect()->route('admin.login');
        }

        $admin   = AdminUser::where('email', session('admin_otp_email'))->first();
        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = now()->addMinutes(10)->timestamp;

        session([
            'admin_otp'         => hash('sha256', $otp),
            'admin_otp_expires' => $expires,
        ]);

        Mail::to($admin->email)->send(new AdminLoginOtpMail($otp, $admin->name));

        return back()->with('resent', true);
    }

    // ── Logout ───────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session(['admin_gate_passed' => true]);
        return redirect()->route('admin.login');
    }

    // ── Profile ──────────────────────────────────────────────
    public function showProfile()
    {
        return view('admin.profile', ['admin' => Auth::guard('admin')->user()]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,' . $admin->id,
        ]);

        $admin->update(['name' => $request->name, 'email' => $request->email]);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}