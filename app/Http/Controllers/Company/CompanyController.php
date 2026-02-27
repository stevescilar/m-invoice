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
        $data = $request->only(['phone', 'email', 'address', 'footer_message', 'kra_pin']);

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
        return view('company.settings', compact('company'));
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
        $data = $request->only(['name', 'phone', 'email', 'address', 'footer_message', 'kra_pin']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('signature')) {
            $data['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $company->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }
}
