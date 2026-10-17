<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1500']);

        $userMessage = trim($request->message);
        $history     = $request->input('history', []);

        // Build real-time business context from user's data if logged in
        $context      = $this->buildContext();
        $systemPrompt = $this->buildSystemPrompt($context);

        $geminiKey   = config('services.gemini.key');
        $geminiModel = config('services.gemini.model', 'gemini-3.8-flash');

        // Guard against deprecated Gemini models per Gemini API guidelines
        if (preg_match('/^gemini-(1\.5|2\.0|2\.5)/i', (string) $geminiModel)) {
            $geminiModel = 'gemini-3.8-flash';
        }

        if (!empty($geminiKey)) {
            return $this->chatWithGemini($geminiKey, $geminiModel, $systemPrompt, $history, $userMessage);
        }

        // Fallback to Groq if GEMINI_API_KEY is not configured yet but GROQ_API_KEY exists
        $groqKey = config('services.groq.key');
        if (!empty($groqKey)) {
            return $this->chatWithGroq($groqKey, $systemPrompt, $history, $userMessage);
        }

        return response()->json([
            'error' => 'AI Assistant key is not configured yet. Please add GEMINI_API_KEY to your .env file.',
        ], 500);
    }

    /**
     * Call Google Gemini API using the v1beta Interactions endpoint (Api-Revision: 2026-05-20)
     * with model gemini-3.8-flash.
     */
    private function chatWithGemini(string $apiKey, string $model, string $systemPrompt, array $history, string $userMessage)
    {
        $inputSteps = [];

        foreach ($history as $h) {
            if (!isset($h['role'], $h['content']) || !is_string($h['content']) || trim($h['content']) === '') {
                continue;
            }
            if ($h['role'] === 'user') {
                $inputSteps[] = [
                    'type'    => 'user_input',
                    'content' => [
                        ['type' => 'text', 'text' => $h['content']],
                    ],
                ];
            } elseif ($h['role'] === 'assistant') {
                $inputSteps[] = [
                    'type'    => 'model_output',
                    'content' => [
                        ['type' => 'text', 'text' => $h['content']],
                    ],
                ];
            }
        }

        $inputSteps[] = [
            'type'    => 'user_input',
            'content' => [
                ['type' => 'text', 'text' => $userMessage],
            ],
        ];

        try {
            $response = Http::timeout(30)->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type'   => 'application/json',
                'Api-Revision'   => '2026-05-20',
            ])->post('https://generativelanguage.googleapis.com/v1beta/interactions', [
                'model'              => $model,
                'system_instruction' => $systemPrompt,
                'store'              => false,
                'input'              => $inputSteps,
                'generation_config'  => [
                    'thinking_level' => 'low',
                ],
            ]);

            if ($response->failed()) {
                Log::warning('Gemini Interactions API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                $errMsg = $response->json('error.message') ?? 'Could not reach Gemini AI Assistant. Please verify your GEMINI_API_KEY.';
                return response()->json(['error' => $errMsg], 500);
            }

            $data  = $response->json();
            $reply = $this->extractGeminiInteractionText($data);

            return response()->json([
                'reply' => $reply ?: 'Sorry, I could not generate a response right now.',
                'model' => $model,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gemini Chatbot exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Connection to Gemini AI timed out. Please try again.',
            ], 500);
        }
    }

    /**
     * Extract text from the Gemini v1beta/interactions steps response.
     */
    private function extractGeminiInteractionText(array $data): string
    {
        $texts = [];

        if (!empty($data['steps']) && is_array($data['steps'])) {
            foreach ($data['steps'] as $step) {
                if (($step['type'] ?? '') === 'model_output' && !empty($step['content']) && is_array($step['content'])) {
                    foreach ($step['content'] as $part) {
                        if (($part['type'] ?? '') === 'text' && isset($part['text'])) {
                            $texts[] = $part['text'];
                        }
                    }
                }
            }
        }

        if (!empty($texts)) {
            return trim(implode("\n", $texts));
        }

        // Defensive fallback if output_text is ever returned at root
        if (!empty($data['output_text']) && is_string($data['output_text'])) {
            return trim($data['output_text']);
        }

        return '';
    }

    /**
     * Legacy fallback if only GROQ_API_KEY is present.
     */
    private function chatWithGroq(string $groqKey, string $systemPrompt, array $history, string $userMessage)
    {
        $messages = [];
        foreach ($history as $h) {
            if (isset($h['role'], $h['content']) && in_array($h['role'], ['user', 'assistant'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::timeout(25)->withHeaders([
            'Authorization' => 'Bearer ' . $groqKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'      => 'llama-3.1-8b-instant',
            'max_tokens' => 700,
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

        // Recent Invoices with client names & balances
        $invoices = $company->invoices()
            ->with('client:id,name,phone')
            ->latest()
            ->limit(8)
            ->get(['id', 'client_id', 'invoice_number', 'grand_total', 'amount_paid', 'status', 'due_date', 'client_payment_note', 'created_at']);

        // Overdue Invoices
        $overdueInvoices = $company->invoices()
            ->with('client:id,name,phone')
            ->where('status', 'overdue')
            ->latest()
            ->limit(6)
            ->get(['id', 'client_id', 'invoice_number', 'grand_total', 'amount_paid', 'due_date']);

        // Recent Quotations
        $quotations = $company->quotations()
            ->with('client:id,name')
            ->latest()
            ->limit(6)
            ->get(['id', 'client_id', 'quotation_number', 'grand_total', 'status', 'expiry_date', 'client_response_note', 'created_at']);

        // Catalog Items for instant quote drafting & pricing lookups
        $catalogItems = $company->catalogItems()
            ->orderBy('name')
            ->limit(35)
            ->get(['name', 'default_unit_price', 'default_buying_price', 'unit_of_measure']);

        // Financial Metrics
        $totalRevenue      = $company->invoices()->where('status', 'paid')->sum('grand_total');
        $revenueThisMonth  = $company->invoices()->where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('grand_total');
        $profitThisMonth   = $company->invoices()->where('status', 'paid')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('total_profit');
        $vatThisMonth      = $company->invoices()->where('status', 'paid')->where('etr_enabled', true)->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('tax_amount');
        $expensesThisMonth = $company->expenses()->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');
        $netThisMonth      = $profitThisMonth - $expensesThisMonth;

        $unpaidInvoices = $company->invoices()->whereIn('status', ['sent', 'overdue', 'stalled'])->get(['grand_total', 'amount_paid']);
        $totalOutstanding = $unpaidInvoices->sum(fn($inv) => max(0, (float) $inv->grand_total - (float) $inv->amount_paid));

        $overdueCount = $company->invoices()->where('status', 'overdue')->count();
        $clientCount  = $company->clients()->count();
        $invoiceCount = $company->invoices()->count();
        $quoteCount   = $company->quotations()->count();

        return [
            'authenticated'       => true,
            'company'             => true,
            'user_name'           => $user->name,
            'company_name'        => $company->name,
            'total_revenue'       => number_format($totalRevenue, 2),
            'revenue_this_month'  => number_format($revenueThisMonth, 2),
            'profit_this_month'   => number_format($profitThisMonth, 2),
            'expenses_this_month' => number_format($expensesThisMonth, 2),
            'net_this_month'      => number_format($netThisMonth, 2),
            'vat_this_month'      => number_format($vatThisMonth, 2),
            'total_outstanding'   => number_format($totalOutstanding, 2),
            'overdue_count'       => $overdueCount,
            'client_count'        => $clientCount,
            'invoice_count'       => $invoiceCount,
            'quote_count'         => $quoteCount,
            'recent_invoices'     => $invoices,
            'overdue_invoices'    => $overdueInvoices,
            'recent_quotations'   => $quotations,
            'catalog_items'       => $catalogItems,
            'subscription'        => $company->subscription?->plan?->name ?? 'Trial / Active',
        ];
    }

    private function buildSystemPrompt(array $ctx): string
    {
        $base = <<<PROMPT
You are **Invoxa AI** (powered by Google Gemini) — an intelligent, friendly business copilot built into the Invoxa invoicing & operations platform for Kenyan businesses.

Your style & capabilities:
- Warm, concise, practical, and action-oriented.
- Use clear formatting: bold key numbers (`**Ksh ...**`), short bullet lists when listing invoices/items, and markdown links like `[INV-0001](/invoices/1)` or `[QUO-0001](/quotations/1)` so the user can click directly to open documents.
- You can analyze revenue, gross profit, expenses, KRA 16% VAT / ETR, overdue balances, and client activity.
- You can draft itemised quotations or estimates using the user's actual Catalog Items and prices.
- You can write polite or firm WhatsApp/Email payment follow-up messages tailored to specific overdue clients.
- You can guide the user on Invoxa features: Public Client Portal links (where clients approve quotes or submit M-Pesa payment confirmations), Partial Payments & Receipts, Delivery Notes, Client Statements of Account, and KRA VAT / Profit Reports.
PROMPT;

        if (!$ctx['authenticated']) {
            $base .= "\n\nThe user is a visitor (not logged in). Help them understand Invoxa's features (invoicing, quotations, M-Pesa STK push & Paybill/Till integration, KRA ETR 16% VAT, profit tracking, delivery notes) and encourage them to sign up for the free trial.";
            return $base;
        }

        if (empty($ctx['company'])) {
            $base .= "\n\nThe user is logged in but hasn't finished setting up their company profile yet. Guide them to complete their company setup.";
            return $base;
        }

        $monthName = now()->format('F Y');
        $base .= "\n\n### Live Business Snapshot for {$ctx['company_name']} (User: {$ctx['user_name']}, Current Month: {$monthName})\n";
        $base .= "- **This Month Revenue (Paid)**: Ksh {$ctx['revenue_this_month']}\n";
        $base .= "- **This Month Gross Profit**: Ksh {$ctx['profit_this_month']}\n";
        $base .= "- **This Month Expenses**: Ksh {$ctx['expenses_this_month']}\n";
        $base .= "- **This Month Net Profit**: Ksh {$ctx['net_this_month']}\n";
        $base .= "- **This Month KRA VAT (16% ETR)**: Ksh {$ctx['vat_this_month']}\n";
        $base .= "- **All-Time Paid Revenue**: Ksh {$ctx['total_revenue']}\n";
        $base .= "- **Total Outstanding (Unpaid Balances)**: Ksh {$ctx['total_outstanding']} ({$ctx['overdue_count']} overdue invoices)\n";
        $base .= "- **Counts**: {$ctx['client_count']} clients, {$ctx['invoice_count']} invoices, {$ctx['quote_count']} quotations\n";

        if ($ctx['overdue_invoices']->isNotEmpty()) {
            $base .= "\n### Overdue Invoices:\n";
            foreach ($ctx['overdue_invoices'] as $inv) {
                $bal = number_format(max(0, (float) $inv->grand_total - (float) $inv->amount_paid), 2);
                $clientName = $inv->client->name ?? 'Client';
                $due = $inv->due_date ? $inv->due_date->format('M j, Y') : 'N/A';
                $base .= "- [{$inv->invoice_number}](/invoices/{$inv->id}) — {$clientName}: Balance **Ksh {$bal}** (Due {$due})\n";
            }
        }

        if ($ctx['recent_invoices']->isNotEmpty()) {
            $base .= "\n### Recent Invoices:\n";
            foreach ($ctx['recent_invoices'] as $inv) {
                $bal = number_format(max(0, (float) $inv->grand_total - (float) $inv->amount_paid), 2);
                $clientName = $inv->client->name ?? 'Client';
                $note = $inv->client_payment_note ? " | Client Payment Note: \"{$inv->client_payment_note}\"" : '';
                $base .= "- [{$inv->invoice_number}](/invoices/{$inv->id}) — {$clientName}: Total Ksh " . number_format($inv->grand_total, 2) . " (Bal: Ksh {$bal}, Status: {$inv->status}{$note})\n";
            }
        }

        if ($ctx['recent_quotations']->isNotEmpty()) {
            $base .= "\n### Recent Quotations:\n";
            foreach ($ctx['recent_quotations'] as $quo) {
                $clientName = $quo->client->name ?? 'Client';
                $note = $quo->client_response_note ? " | Client Comment: \"{$quo->client_response_note}\"" : '';
                $base .= "- [{$quo->quotation_number}](/quotations/{$quo->id}) — {$clientName}: Ksh " . number_format($quo->grand_total, 2) . " (Status: {$quo->status}{$note})\n";
            }
        }

        if ($ctx['catalog_items']->isNotEmpty()) {
            $base .= "\n### Catalog Items (Selling Price / Buying Cost):\n";
            foreach ($ctx['catalog_items'] as $item) {
                $base .= "- {$item->name}: Sell Ksh " . number_format($item->default_unit_price, 0) . " / Buy Ksh " . number_format($item->default_buying_price, 0) . " per {$item->unit_of_measure}\n";
            }
        }

        $base .= "\nAlways use these exact figures and clickable markdown links when answering questions about their business.";

        return $base;
    }
}