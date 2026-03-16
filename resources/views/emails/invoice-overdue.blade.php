@component('mail::message')

# ⚠️ Invoice Overdue

Hi **{{ $invoice->company->owner->name }}**,

Invoice **{{ $invoice->invoice_number }}** for **{{ $invoice->client->name }}** was due on **{{ $invoice->due_date->format('F j, Y') }}** and has been automatically marked as **Overdue**.

@component('mail::table')
| | |
|:---|:---|
| **Invoice #** | {{ $invoice->invoice_number }} |
| **Client** | {{ $invoice->client->name }} |
| **Amount** | Ksh {{ number_format($invoice->grand_total, 2) }} |
| **Due Date** | {{ $invoice->due_date->format('F j, Y') }} |
| **Days Overdue** | {{ $invoice->due_date->diffInDays(today()) }} day(s) |
@endcomponent

You may want to follow up with the client or record a payment if it has already been settled.

@component('mail::button', ['url' => route('invoices.show', $invoice), 'color' => 'green'])
View Invoice
@endcomponent

Regards,
**M-Invoice**

@endcomponent