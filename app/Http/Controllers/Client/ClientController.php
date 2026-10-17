<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ClientStatementMail;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->company;
        $search  = $request->get('search');
        $filter  = $request->get('filter', 'all');
        $sort    = $request->get('sort', 'latest');

        $query = Client::where('company_id', $company->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filter === 'flagged') {
            $query->where('is_flagged', true);
        }

        match($sort) {
            'oldest' => $query->oldest(),
            'name'   => $query->orderBy('name'),
            default  => $query->latest(),
        };

        $clients = $query->paginate(15);

        // Outstanding filter (done after paginate won't work, use collection for small datasets)
        $allClients   = Client::where('company_id', $company->id)->get();
        $totalBilled      = $allClients->sum(fn($c) => $c->totalBilled());
        $totalOutstanding = $allClients->sum(fn($c) => $c->outstandingBalance());

        return view('clients.index', compact('clients', 'totalBilled', 'totalOutstanding'));
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

        $isFlagged = $request->boolean('is_flagged');

        $client->update([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'address'     => $request->address,
            'is_flagged'  => $isFlagged,
            'flag_reason' => $isFlagged ? $request->flag_reason : null,
        ]);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeClient($client);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    public function downloadStatement(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        [$invoices, $totalBilled, $totalPaid, $balanceDue, $fromDate, $toDate] = $this->buildStatementData($request, $client);
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('clients.statement-pdf', compact(
            'client', 'company', 'invoices', 'totalBilled', 'totalPaid', 'balanceDue', 'fromDate', 'toDate'
        ));

        $slug = Str::slug($client->name ?: 'client');
        return $pdf->download("statement-of-account-{$slug}.pdf");
    }

    public function emailStatement(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        if (!$client->email) {
            return back()->with('error', 'This client has no email address on record.');
        }

        [$invoices, $totalBilled, $totalPaid, $balanceDue, $fromDate, $toDate] = $this->buildStatementData($request, $client);
        $company = Auth::user()->company;

        try {
            $company->sendMail(
                $client->email,
                new ClientStatementMail($client, $company, $invoices, $totalBilled, $totalPaid, $balanceDue, $fromDate, $toDate),
                Auth::user()
            );

            $copies = $company->getCopyRecipients($client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Statement of Account sent to ' . $client->email . $copyNote);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send statement: ' . $e->getMessage());
        }
    }

    private function buildStatementData(Request $request, Client $client): array
    {
        $fromDate = $request->get('from');
        $toDate   = $request->get('to');

        $query = $client->invoices()->orderBy('issue_date', 'asc')->orderBy('id', 'asc');
        if ($fromDate) {
            $query->whereDate('issue_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        $invoices    = $query->get();
        $totalBilled = (float) $invoices->sum('grand_total');
        $totalPaid   = (float) $invoices->sum(fn ($inv) => $inv->totalPaidAmount());
        $balanceDue  = max(0, $totalBilled - $totalPaid);

        return [$invoices, $totalBilled, $totalPaid, $balanceDue, $fromDate, $toDate];
    }

    private function authorizeClient(Client $client)
    {
        if ($client->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}