<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Notification;
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
        $rawAmt  = $request->amount ? (float) $request->amount : $invoice->balanceDue();
        $amount  = number_format($rawAmt, 2);

        // Save reference and client note on the invoice
        $invoice->update([
            'mpesa_code'          => $invoice->mpesa_code ?: substr($code, 0, 20),
            'client_payment_note' => trim("Ref: {$code} (Ksh {$amount})" . ($request->notes ? " — {$request->notes}" : '')),
        ]);

        // Create In-App Notification in the Notification Center
        $ownerUserId = $company->owner_id ?? $invoice->created_by ?? $company->users()->value('id');
        if ($ownerUserId) {
            Notification::create([
                'company_id'     => $company->id,
                'user_id'        => $ownerUserId,
                'type'           => 'payment_submitted',
                'title'          => "Payment Submitted — {$invoice->invoice_number}",
                'message'        => "{$invoice->client->name} submitted payment ref {$code} for Ksh {$amount}.",
                'client_comment' => $request->notes,
                'action_url'     => route('invoices.show', $invoice),
                'meta'           => [
                    'invoice_id'     => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'mpesa_code'     => $code,
                    'amount'         => $rawAmt,
                ],
                'is_read'        => false,
            ]);
        }

        // Notify the company owner by email (with sendmail fallback)
        $ownerEmail = $company->notification_email ?: ($company->email ?: $company->owner?->email);
        if ($ownerEmail && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
            $subject = "Payment Reference Submitted for {$invoice->invoice_number} ({$code})";
            $body = "Hello {$company->name},\n\n"
                . "Client {$invoice->client->name} has submitted a payment confirmation on the online invoice portal:\n\n"
                . "Invoice #: {$invoice->invoice_number}\n"
                . "Reference / M-Pesa Code: {$code}\n"
                . "Amount Reported: Ksh {$amount}\n"
                . ($request->notes ? "Client Note: {$request->notes}\n" : "")
                . "\nView & confirm payment in your dashboard: " . route('invoices.show', $invoice);

            $this->sendPlainAlert($ownerEmail, $subject, $body);
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

        $company    = $quotation->company;
        $isApproved = $request->action === 'approve';

        $quotation->update([
            'status'               => $isApproved ? 'approved' : 'rejected',
            'approved_at'          => $isApproved ? now() : $quotation->approved_at,
            'client_response_note' => $request->comment,
            'responded_at'         => now(),
        ]);

        $statusMsg = $isApproved
            ? "Thank you! Quotation {$quotation->quotation_number} has been approved. {$company->name} has been notified."
            : "Your response for Quotation {$quotation->quotation_number} has been recorded and sent to {$company->name}.";

        // Create In-App Notification in the Notification Center
        $ownerUserId = $company->owner_id ?? $quotation->created_by ?? $company->users()->value('id');
        if ($ownerUserId) {
            Notification::create([
                'company_id'     => $company->id,
                'user_id'        => $ownerUserId,
                'type'           => $isApproved ? 'quotation_approved' : 'quotation_rejected',
                'title'          => ($isApproved ? 'Quotation Approved — ' : 'Quotation Declined — ') . $quotation->quotation_number,
                'message'        => "{$quotation->client->name} " . ($isApproved ? 'approved' : 'declined') . " Quotation {$quotation->quotation_number} (Ksh " . number_format($quotation->grand_total, 2) . ").",
                'client_comment' => $request->comment,
                'action_url'     => route('quotations.show', $quotation),
                'meta'           => [
                    'quotation_id'     => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'status'           => $quotation->status,
                ],
                'is_read'        => false,
            ]);
        }

        // Notify the company owner by email (with sendmail fallback)
        $ownerEmail = $company->notification_email ?: ($company->email ?: $company->owner?->email);
        if ($ownerEmail && filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
            $actionLabel = $isApproved ? 'APPROVED' : 'DECLINED / CHANGE REQUESTED';
            $subject = "Quotation {$quotation->quotation_number} {$actionLabel} by {$quotation->client->name}";
            $body = "Hello {$company->name},\n\n"
                . "Client {$quotation->client->name} has {$actionLabel} Quotation {$quotation->quotation_number} (Ksh " . number_format($quotation->grand_total, 2) . ") via the online portal.\n"
                . ($request->comment ? "\nClient Comment: \"{$request->comment}\"\n" : "")
                . "\nView quotation in your dashboard: " . route('quotations.show', $quotation);

            $this->sendPlainAlert($ownerEmail, $subject, $body);
        }

        return back()->with('success', $statusMsg);
    }

    private function sendPlainAlert(string $toEmail, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($msg) use ($toEmail, $subject) {
                $msg->to($toEmail)->subject($subject);
            });
        } catch (\Throwable $e) {
            try {
                Mail::mailer('sendmail')->raw($body, function ($msg) use ($toEmail, $subject) {
                    $msg->to($toEmail)->subject($subject);
                });
            } catch (\Throwable $fallbackErr) {
                Log::warning('Failed to send client response email notification: ' . $e->getMessage());
            }
        }
    }
}
