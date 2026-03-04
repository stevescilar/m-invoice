<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\StaffInvitationMail;
use App\Models\StaffInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::where('company_id', Auth::user()->company_id)
            ->where('role', 'staff')
            ->latest()
            ->get();

        $pendingInvitations = StaffInvitation::where('company_id', Auth::user()->company_id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return view('staff.index', compact('staff', 'pendingInvitations'));
    }

    public function invite(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:staff_invitations,email',
        ]);

        $company = Auth::user()->company;

        // Check if already invited
        $existing = StaffInvitation::where('company_id', $company->id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()->with('error', 'An invitation has already been sent to this email.');
        }

        $invitation = StaffInvitation::create([
            'company_id'  => $company->id,
            'invited_by'  => Auth::id(),
            'email'       => $request->email,
            'name'        => $request->name,
            'token'       => Str::random(64),
            'status'      => 'pending',
            'expires_at'  => now()->addHours(48),
        ]);

        try {
            Mail::to($invitation->email)->send(new StaffInvitationMail($invitation));
            return back()->with('success', 'Invitation sent to ' . $invitation->email);
        } catch (\Exception $e) {
            $invitation->delete();
            return back()->with('error', 'Failed to send invitation: ' . $e->getMessage());
        }
    }

    public function showAcceptForm(string $token)
    {
        $invitation = StaffInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return view('staff.invitation-expired');
        }

        return view('staff.accept', compact('invitation'));
    }

    public function acceptInvitation(Request $request, string $token)
    {
        $invitation = StaffInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')->with('error', 'This invitation has expired.');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'company_id'        => $invitation->company_id,
            'name'              => $invitation->name,
            'email'             => $invitation->email,
            'password'          => Hash::make($request->password),
            'role'              => 'staff',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to ' . $invitation->company->name . '!');
    }

    public function resendInvitation(StaffInvitation $invitation)
    {
        if ($invitation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $invitation->update([
            'token'      => Str::random(64),
            'expires_at' => now()->addHours(48),
            'status'     => 'pending',
        ]);

        try {
            Mail::to($invitation->email)->send(new StaffInvitationMail($invitation));
            return back()->with('success', 'Invitation resent to ' . $invitation->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend: ' . $e->getMessage());
        }
    }

    public function revokeInvitation(StaffInvitation $invitation)
    {
        if ($invitation->company_id !== Auth::user()->company_id) abort(403);
        $invitation->delete();
        return back()->with('success', 'Invitation revoked.');
    }

    public function deactivate(User $user)
    {
        if ($user->company_id !== Auth::user()->company_id) abort(403);
        if ($user->role === 'owner') return back()->with('error', 'Cannot deactivate owner.');
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Staff member ' . ($user->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(User $user)
    {
        if ($user->company_id !== Auth::user()->company_id) abort(403);
        if ($user->role === 'owner') return back()->with('error', 'Cannot delete owner.');
        $user->delete();
        return back()->with('success', 'Staff member removed.');
    }
}