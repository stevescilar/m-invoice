<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)
            ->latest()
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Client::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client added successfully.');
    }

    public function show(Client $client)
    {
        $this->authorizeClient($client);

        $invoices = $client->invoices()->latest()->paginate(10);
        $quotations = $client->quotations()->latest()->paginate(5);

        return view('clients.show', compact('client', 'invoices', 'quotations'));
    }

    public function edit(Client $client)
    {
        $this->authorizeClient($client);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'is_flagged' => 'boolean',
            'flag_reason' => 'nullable|string|max:255',
        ]);

        $client->update($request->only(['name', 'phone', 'email', 'address', 'is_flagged', 'flag_reason']));

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeClient($client);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    private function authorizeClient(Client $client)
    {
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}