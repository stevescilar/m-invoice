<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
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

        Auth::login($user);

        // Redirect to setup company profile
        return redirect()->route('dashboard')
        ->with('success', 'Welcome to M-Invoice! Please complete your company profile in Settings.');
    }
}