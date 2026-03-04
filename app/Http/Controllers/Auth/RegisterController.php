<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $ref = $request->query('ref');
        return view('auth.register', compact('ref'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        // Check referral code
        $referrer    = null;
        $bonusDays   = 0;
        $trialDays   = 3;

        if ($request->ref) {
            $referrer = Company::where('referral_code', $request->ref)->first();
            if ($referrer) {
                $bonusDays = 1;
                $trialDays = 4;
            }
        }

        $company = Company::create([
            'name'          => $request->company_name,
            'email'         => $request->email,
            'phone'         => '',
            'slug'          => Str::slug($request->company_name . '-' . Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'owner',
            'is_active'  => true,
        ]);

        $company->update(['owner_id' => $user->id]);

        Subscription::create([
            'company_id'    => $company->id,
            'plan'          => 'trial',
            'status'        => 'trial',
            'on_trial'      => true,
            'trial_ends_at' => now()->addDays($trialDays),
            'starts_at'     => now(),
        ]);

        // Reward referrer with +1 day
        if ($referrer) {
            $referrer->increment('referral_count');
            $sub = $referrer->subscription;
            if ($sub && $sub->isOnTrial()) {
                $sub->update(['trial_ends_at' => $sub->trial_ends_at->addDay()]);
            } elseif ($sub && $sub->isActive() && $sub->ends_at) {
                $sub->update(['ends_at' => $sub->ends_at->addDay()]);
            }
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}