<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function setup()
    {
        $company = Auth::user()->company;
        return view('company.setup', compact('company'));
    }

    public function storeSetup(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'footer_message' => 'nullable|string|max:255',
            'kra_pin' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $company = Auth::user()->company;
        $data = $request->only([
            'phone',
            'email',
            'address',
            'footer_message',
            'kra_pin',
            'mpesa_paybill',
            'mpesa_account',
            'mpesa_till',
            'mpesa_number',
            'bank_name',
            'bank_account',
            'bank_branch'
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('signature')) {
            $data['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $company->update($data);

        return redirect()->route('dashboard')->with('success', 'Company setup complete!');
    }

    public function settings()
    {
        $company = Auth::user()->company;
        $itemTypes = auth()->user()->company->itemTypes()->get();
        return view('company.settings', compact('itemTypes','company'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'footer_message' => 'nullable|string|max:255',
            'kra_pin' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $company = Auth::user()->company;
        $data = $request->only([
            'name',
            'phone',
            'email',
            'address',
            'footer_message',
            'kra_pin',
            'mpesa_paybill',
            'mpesa_account',
            'mpesa_till',
            'mpesa_number',
            'bank_name',
            'bank_account',
            'bank_branch'
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('signature')) {
            $data['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updatePassword(Request $request)
{
    $user = auth()->user();

    // Google user with no real password (has google_id and null password)
    $isGoogleOnly = $user->google_id && !$user->password;

    $rules = [
        'password' => 'required|min:8|confirmed',
    ];

    // Only require current password if user has an actual password set
    if (!$isGoogleOnly) {
        $rules['current_password'] = 'required';
    }

    $request->validate($rules);

    // Verify current password if applicable
    if (!$isGoogleOnly) {
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.')->withInput();
        }
    }

    $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);

    return back()->with('success', 'Password updated successfully.');
}
}
