<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicDocumentController extends Controller
{
    public function showInvoice(string $token): View
    {
        $invoice = Invoice::with(['client', 'company', 'items', 'items.itemType', 'payments'])
            ->where('public_token', $token)
            ->firstOrFail();

        $company = $invoice->company;

        return view('public.invoice', compact('invoice', 'company'));
    }

    public function downloadInvoicePdf(string $token)
    {
        $invoice = Invoice::with(['client', 'company', 'items', 'items.itemType'])
            ->where('public_token', $token)
            ->firstOrFail();

        $company = $invoice->company;

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function downloadInvoiceReceipt(string $token)
    {
        $invoice = Invoice::with(['client', 'company', 'items', 'items.itemType', 'payments'])
            ->where('public_token', $token)
            ->firstOrFail();

        if ($invoice->totalPaidAmount() <= 0) {
            abort(404, 'No payment recorded for this invoice yet.');
        }

        $company = $invoice->company;
        $payment = $invoice->payments->first();

        $pdf = Pdf::loadView('invoices.receipt-pdf', compact('invoice', 'company', 'payment'));

        return $pdf->download('receipt-' . $invoice->invoice_number . '.pdf');
    }

    public function notifyInvoicePayment(Request $request, string $token): RedirectResponse
    {
        $invoice = Invoice::with(['client', 'company.owner'])
            ->where('public_token', $token)
            ->firstOrFail();

        $request->validate([
            'mpesa_code' => 'required|string|max:40',
            'amount'     => 'nullable|numeric|min:1',
            'notes'      => 'nullable|string|max:300',
        ]);

        $company = $invoice->company;
        $code    = strtoupper(trim($request->mpesa_code));
        $amount  = $request->amount ? number_format((float) $request->amount, 2) : number_format($invoice->balanceDue(), 2);

        // Save reference in invoice notes or mpesa_code if not yet set
        if (empty($invoice->mpesa_code)) {
            $invoice->update(['mpesa_code' => substr($code, 0, 20)]);
        }

        // Notify the company owner by email
        $ownerEmail = $company->notification_email ?: ($company->email ?: $company->owner?->email);
        if ($ownerEmail && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $subject = "Payment Reference Submitted for {$invoice->invoice_number} ({$code})";
                $body = "Hello {$company->name},\n\n"
                    . "Client {$invoice->client->name} has submitted a payment confirmation on the online invoice portal:\n\n"
                    . "Invoice #: {$invoice->invoice_number}\n"
                    . "Reference / M-Pesa Code: {$code}\n"
                    . "Amount Reported: Ksh {$amount}\n"
                    . ($request->notes ? "Client Note: {$request->notes}\n" : "")
                    . "\nView & confirm payment in your dashboard: " . route('invoices.show', $invoice);

                Mail::raw($body, function ($msg) use ($ownerEmail, $subject) {
                    $msg->to($ownerEmail)->subject($subject);
                });
            } catch (\Throwable $e) {
                Log::warning('Failed to send client payment notification email: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Thank you! Payment reference {$code} has been submitted to {$company->name} for verification.");
    }

    public function showQuotation(string $token): View
    {
        $quotation = Quotation::with(['client', 'company', 'items', 'items.itemType', 'convertedInvoice'])
            ->where('public_token', $token)
            ->firstOrFail();

        $company = $quotation->company;

        return view('public.quotation', compact('quotation', 'company'));
    }

    public function downloadQuotationPdf(string $token)
    {
        $quotation = Quotation::with(['client', 'company', 'items', 'items.itemType'])
            ->where('public_token', $token)
            ->firstOrFail();

        $company = $quotation->company;

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company'));

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function respondQuotation(Request $request, string $token): RedirectResponse
    {
        $quotation = Quotation::with(['client', 'company.owner'])
            ->where('public_token', $token)
            ->firstOrFail();

        $request->validate([
            'action'  => 'required|in:approve,reject',
            'comment' => 'nullable|string|max:500',
        ]);

        if (in_array($quotation->status, ['converted'])) {
            return back()->with('error', 'This quotation has already been converted to an invoice.');
        }

        $company = $quotation->company;

        if ($request->action === 'approve') {
            $quotation->update([
                'status'      => 'approved',
                'approved_at' => now(),
            ]);
            $statusMsg = "Thank you! Quotation {$quotation->quotation_number} has been approved. {$company->name} has been notified.";
        } else {
            $quotation->update([
                'status' => 'rejected',
            ]);
            $statusMsg = "Your response for Quotation {$quotation->quotation_number} has been recorded.";
        }

        // Notify the company owner
        $ownerEmail = $company->notification_email ?: ($company->email ?: $company->owner?->email);
        if ($ownerEmail && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $actionLabel = $request->action === 'approve' ? 'APPROVED' : 'DECLINED / CHANGE REQUESTED';
                $subject = "Quotation {$quotation->quotation_number} {$actionLabel} by {$quotation->client->name}";
                $body = "Hello {$company->name},\n\n"
                    . "Client {$quotation->client->name} has {$actionLabel} Quotation {$quotation->quotation_number} (Ksh " . number_format($quotation->grand_total, 2) . ") via the online portal.\n"
                    . ($request->comment ? "\nClient Comment: {$request->comment}\n" : "")
                    . "\nView quotation in your dashboard: " . route('quotations.show', $quotation);

                Mail::raw($body, function ($msg) use ($ownerEmail, $subject) {
                    $msg->to($ownerEmail)->subject($subject);
                });
            } catch (\Throwable $e) {
                Log::warning('Failed to send quotation response notification: ' . $e->getMessage());
            }
        }

        return back()->with('success', $statusMsg);
    }
}

