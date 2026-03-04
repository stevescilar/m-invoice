@extends('layouts.app')
@section('title', 'Staff Management')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Staff Management</h1>
</div>

<!-- Invite Form -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="font-semibold text-gray-700 mb-4">Invite a Staff Member</h2>
    <form method="POST" action="{{ route('staff.invite') }}" class="flex gap-3 flex-wrap">
        @csrf
        <input type="text" name="name" placeholder="Full Name" required
            value="{{ old('name') }}"
            class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 flex-1 min-w-48">
        <input type="email" name="email" placeholder="Email Address" required
            value="{{ old('email') }}"
            class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 flex-1 min-w-48">
        <button type="submit"
            class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
            Send Invitation
        </button>
    </form>
</div>

<!-- Pending Invitations -->
@if($pendingInvitations->count())
<div class="bg-white rounded-xl shadow overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-700">Pending Invitations</h2>
        <span class="text-xs text-gray-400">{{ $pendingInvitations->count() }} pending</span>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-center">Expires</th>
                <th class="px-5 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($pendingInvitations as $invitation)
            <tr>
                <td class="px-5 py-3 font-medium">{{ $invitation->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $invitation->email }}</td>
                <td class="px-5 py-3 text-center text-xs text-gray-400">
                    {{ $invitation->expires_at->diffForHumans() }}
                </td>
                <td class="px-5 py-3 text-center flex gap-3 justify-center">
                    <form method="POST" action="{{ route('staff.invitations.resend', $invitation) }}">
                        @csrf
                        <button class="text-blue-500 hover:underline text-xs">Resend</button>
                    </form>
                    <form method="POST" action="{{ route('staff.invitations.revoke', $invitation) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:underline text-xs">Revoke</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Active Staff -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Team Members</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-center">Role</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-center">Joined</th>
                <th class="px-5 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($staff as $member)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium">{{ $member->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $member->email }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Staff</span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $member->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center text-gray-400 text-xs">
                    {{ $member->created_at->format('M j, Y') }}
                </td>
                <td class="px-5 py-3 text-center flex gap-3 justify-center">
                    <form method="POST" action="{{ route('staff.deactivate', $member) }}">
                        @csrf
                        <button class="text-xs {{ $member->is_active ? 'text-yellow-500' : 'text-green-500' }} hover:underline">
                            {{ $member->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('staff.destroy', $member) }}"
                        onsubmit="return confirm('Remove {{ $member->name }} from your team?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:underline text-xs">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                    No staff members yet. Invite someone above.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection