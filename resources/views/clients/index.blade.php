@extends('layouts.app')
@section('title', 'Clients')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Clients</h1>
        <a href="{{ route('clients.create') }}"
            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
            + Add Client
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Phone</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-right">Total Billed</th>
                    <th class="px-6 py-3 text-right">Outstanding</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <a href="{{ route('clients.show', $client) }}" class="hover:text-green-600">
                                {{ $client->name }}
                            </a>
                            @if ($client->is_flagged)
                                <span class="ml-1 text-xs text-red-500">⚑ Flagged</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $client->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $client->email ?? '—' }}</td>
                        <td class="px-6 py-4 text-right font-medium">Ksh {{ number_format($client->totalBilled(), 2) }}</td>
                        <td
                            class="px-6 py-4 text-right {{ $client->outstandingBalance() > 0 ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                            Ksh {{ number_format($client->outstandingBalance(), 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 py-1 rounded-full text-xs {{ $client->is_flagged ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $client->is_flagged ? 'Flagged' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('clients.show', $client) }}"
                                class="text-blue-500 hover:underline mr-2">View</a>
                            <a href="{{ route('clients.edit', $client) }}"
                                class="text-yellow-500 hover:underline mr-2">Edit</a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline"
                                onsubmit="return confirm('Delete this client?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            No clients yet. <a href="{{ route('clients.create') }}" class="text-green-600">Add your first
                                client</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $clients->links() }}
        </div>
    </div>

@endsection
