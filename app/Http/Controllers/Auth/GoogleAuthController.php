<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        session(['google_ref' => $request->query('ref')]);
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google login failed. Please try again.');
        }

        // Check if user exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
    // Mark as verified if not already
    if (!$user->email_verified_at) {
            $user->update([
                'email_verified_at' => now(),
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
            ]);
        }
        Auth::login($user);
        return redirect()->intended(route('dashboard'));
    }

        // New user — create company + user + subscription
        $company = Company::create([
            'name'  => $googleUser->getName() . "'s Business",
            'email' => $googleUser->getEmail(),
            'phone' => '',
            'slug'  => Str::slug($googleUser->getName() . '-' . Str::random(6)),
            'referral_code' => strtoupper(\Illuminate\Support\Str::random(8)),
        ]);

        $user = User::create([
            'company_id'        => $company->id,
            'name'              => $googleUser->getName(),
            'email'             => $googleUser->getEmail(),
            'google_id'         => $googleUser->getId(),
            'avatar'            => $googleUser->getAvatar(),
            'password'          => bcrypt(Str::random(32)),
            'role'              => 'owner',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        $company->update(['owner_id' => $user->id]);

        Subscription::create([
            'company_id'    => $company->id,
            'plan'          => 'trial',
            'status'        => 'trial',
            'on_trial'      => true,
            'trial_ends_at' => now()->addDays(3),
            'starts_at'     => now(),
        ]);

        // Handle referral
        $ref = session('google_ref');
        if ($ref) {
            $referrer = \App\Models\Company::where('referral_code', $ref)->first();
            if ($referrer) {
                $referrer->increment('referral_count');
                $sub = $referrer->subscription;
                if ($sub && $sub->isOnTrial()) {
                    $sub->update(['trial_ends_at' => $sub->trial_ends_at->addDay()]);
                }
                // Give new user bonus day too
                $company->subscription->update([
                    'trial_ends_at' => now()->addDays(4),
                ]);
            }
            session()->forget('google_ref');
        }

        Auth::login($user);

        // Redirect to setup company profile
        return redirect()->route('dashboard')
        ->with('success', 'Welcome to M-Invoice! Please complete your company profile in Settings.');
    }
}