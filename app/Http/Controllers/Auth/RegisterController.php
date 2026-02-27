<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Create user first without company
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner',
                'is_active' => true,
            ]);

            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'owner_id' => $user->id,
            ]);

            // Attach company to user
            $user->update(['company_id' => $company->id]);

            // Create free subscription
            Subscription::create([
                'company_id' => $company->id,
                'plan' => 'free',
                'status' => 'active',
                'auto_renew' => false,
                'starts_at' => now(),
                'ends_at' => null,
            ]);

            Auth::login($user);
        });

        return redirect()->route('company.setup');
    }
}
