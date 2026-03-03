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
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'company_name'          => 'required|string|max:255',
        ]);

        $company = Company::create([
            'name'  => $request->company_name,
            'email' => $request->email,
            'phone' => '',
            'slug'  => Str::slug($request->company_name . '-' . Str::random(6)),
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
            'trial_ends_at' => now()->addDays(3),
            'starts_at'     => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}