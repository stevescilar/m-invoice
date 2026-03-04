@component('mail::message')

# New Recurring Invoice Generated 🔄

Hi **{{ $newInvoice->company->owner->name }}**,

A new invoice has been automatically generated from your recurring invoice **{{ $invoice->invoice_number }}**.

| | |
|---|---|
| **New Invoice #** | {{ $newInvoice->invoice_number }} |
| **Client** | {{ $newInvoice->client->name }} |
| **Amount** | Ksh {{ number_format($newInvoice->grand_total, 2) }} |
| **Due Date** | {{ $newInvoice->due_date?->format('M j, Y') ?? '—' }} |
| **Status** | Draft — review before sending |

@component('mail::button', ['url' => route('invoices.show', $newInvoice), 'color' => 'green'])
Review Invoice
@endcomponent

The next invoice in this series will be generated on **{{ $invoice->getNextRecurringDate()->format('M j, Y') }}**.

Regards,
**M-Invoice**

@endcomponent