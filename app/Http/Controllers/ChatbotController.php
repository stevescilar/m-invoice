<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userMessage = $request->message;
        $history     = $request->input('history', []);

        // Build context from user's real data if logged in
        $context = $this->buildContext();

        $systemPrompt = $this->buildSystemPrompt($context);

        // Build messages array
        $messages = [];
        foreach ($history as $h) {
            if (isset($h['role'], $h['content']) && in_array($h['role'], ['user', 'assistant'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . config('services.groq.key'),
        'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'      => 'llama-3.1-8b-instant',
            'max_tokens' => 600,
            'messages'   => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $messages
            ),
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Could not reach assistant. Try again.'], 500);
        }

        $data  = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not process that.';

        return response()->json(['reply' => $reply]);
    }

    private function buildContext(): array
    {
        if (!Auth::check()) {
            return ['authenticated' => false];
        }

        $user    = Auth::user();
        $company = $user->company;

        if (!$company) {
            return ['authenticated' => true, 'company' => null];
        }

        // Pull key stats
        $invoices   = $company->invoices()->latest()->limit(5)->get(['id','invoice_number','grand_total','status','due_date','created_at']);
        $quotations = $company->quotations()->latest()->limit(5)->get(['id','quotation_number','grand_total','status','expiry_date','created_at']);

        $totalRevenue   = $company->invoices()->where('status','paid')->sum('grand_total');
        $totalPending   = $company->invoices()->whereIn('status',['sent','draft'])->sum('grand_total');
        $overdueCount   = $company->invoices()->where('status','overdue')->count();
        $expensesMonth  = $company->expenses()->whereMonth('expense_date', now()->month)->sum('amount');
        $clientCount    = $company->clients()->count();
        $invoiceCount   = $company->invoices()->count();

        return [
            'authenticated' => true,
            'company'       => true,
            'user_name'     => $user->name,
            'company_name'  => $company->name,
            'total_revenue' => number_format($totalRevenue, 2),
            'total_pending' => number_format($totalPending, 2),
            'overdue_count' => $overdueCount,
            'expenses_this_month' => number_format($expensesMonth, 2),
            'client_count'  => $clientCount,
            'invoice_count' => $invoiceCount,
            'recent_invoices'   => $invoices->toArray(),
            'recent_quotations' => $quotations->toArray(),
            'subscription'  => $company->subscription?->plan ?? 'trial',
        ];
    }

    private function buildSystemPrompt(array $ctx): string
    {
        $base = <<<PROMPT
You are Invoxa Assistant — a smart, friendly business assistant built into the Invoxa invoicing platform used by Kenyan businesses.

Your personality: warm, concise, practical. You speak like a helpful Kenyan business advisor. Use simple English. Keep responses short (2–4 sentences max unless the user needs detail). Never use excessive bullet points.

You can help with:
- Explaining invoices, quotations, payments, discounts, VAT/ETR
- Business insights and advice based on the user's data
- How to use Invoxa features (creating invoices, setting up M-Pesa, branding, etc.)
- General small business advice for Kenyan entrepreneurs
- Subscription and billing questions

Invoxa pricing: Ksh 100/PDF download, Ksh 700/month unlimited, Ksh 5,000/year.
M-Pesa integration: Paybill, Till Number, Send Money all supported on PDF.
ETR: 16% VAT is automatically calculated when ETR is enabled.

Always be encouraging. If you don't know something, say so honestly.
PROMPT;

        if (!$ctx['authenticated']) {
            $base .= "\n\nThe user is a visitor (not logged in). Help them understand Invoxa's features and encourage them to sign up for the free 3-day trial.";
            return $base;
        }

        if (empty($ctx['company'])) {
            $base .= "\n\nThe user is logged in but has no company set up yet.";
            return $base;
        }

        $base .= "\n\nThe user is logged in. Here is their business data:\n";
        $base .= "- Name: {$ctx['user_name']}\n";
        $base .= "- Company: {$ctx['company_name']}\n";
        $base .= "- Total paid revenue: Ksh {$ctx['total_revenue']}\n";
        $base .= "- Pending invoices value: Ksh {$ctx['total_pending']}\n";
        $base .= "- Overdue invoices: {$ctx['overdue_count']}\n";
        $base .= "- Expenses this month: Ksh {$ctx['expenses_this_month']}\n";
        $base .= "- Total clients: {$ctx['client_count']}\n";
        $base .= "- Total invoices created: {$ctx['invoice_count']}\n";
        $base .= "- Subscription plan: {$ctx['subscription']}\n";

        if (!empty($ctx['recent_invoices'])) {
            $base .= "- Recent invoices: ";
            foreach ($ctx['recent_invoices'] as $inv) {
                $base .= "{$inv['invoice_number']} (Ksh {$inv['grand_total']}, {$inv['status']}), ";
            }
            $base .= "\n";
        }

        $base .= "\nUse this data to give specific, helpful answers. Reference their actual numbers when relevant.";

        return $base;
    }
}