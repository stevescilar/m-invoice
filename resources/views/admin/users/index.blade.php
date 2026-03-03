@extends('layouts.admin')
@section('title', 'Users')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Users</h1>
    <span class="text-sm text-gray-500">{{ $users->total() }} total</span>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-5 py-3 text-left">Name</th>
                <th class="px-5 py-3 text-left">Email</th>
                <th class="px-5 py-3 text-left">Company</th>
                <th class="px-5 py-3 text-center">Role</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-center">Joined</th>
                <th class="px-5 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $user->company?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ $user->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center text-gray-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3 text-center">
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                        @csrf
                        <button class="text-xs {{ $user->is_active ? 'text-red-400' : 'text-green-500' }} hover:underline">
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-10 text-center text-gray-400">No users yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-4">{{ $users->links() }}</div>
</div>

@endsection