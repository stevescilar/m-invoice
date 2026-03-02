@component('mail::message')

@if($reminderType === 'before_due')
# Payment Reminder
@elseif($reminderType === 'on_due')
# Payment Due Today
@else
# Overdue Payment Notice
@endif

Dear **{{ $invoice->client->name }}**,

@if($reminderType === 'before_due')
This is a friendly reminder that invoice **{{ $invoice->invoice_number }}** from **{{ $company->name }}** is due on **{{ $invoice->due_date->format('F j, Y') }}**.
@elseif($reminderType === 'on_due')
This is a reminder that invoice **{{ $invoice->invoice_number }}** from **{{ $company->name }}** is **due today, {{ $invoice->due_date->format('F j, Y') }}**.
@else
Invoice **{{ $invoice->invoice_number }}** from **{{ $company->name }}** was due on **{{ $invoice->due_date->format('F j, Y') }}** and remains unpaid. Please arrange payment at your earliest convenience.
@endif

---

## Invoice Details

@component('mail::table')
| Description | Qty | Unit Price | Total |
|:--|:--:|--:|--:|
@foreach($invoice->items as $item)
| {{ $item->description }}{{ $item->is_labour ? ' *(Labour)*' : '' }} | {{ $item->quantity }} | Ksh {{ number_format($item->unit_price, 2) }} | Ksh {{ number_format($item->total_price, 2) }} |
@endforeach
@endcomponent

## Summary

@component('mail::table')
| | |
|:--|--:|
| Material Cost | Ksh {{ number_format($invoice->material_cost, 2) }} |
| Labour Cost | Ksh {{ number_format($invoice->labour_cost, 2) }} |
@if($invoice->etr_enabled)
| VAT (16%) | Ksh {{ number_format($invoice->vat_amount, 2) }} |
@endif
| **Amount Due** | **Ksh {{ number_format($invoice->grand_total, 2) }}** |
@endcomponent

---

@if($company->mpesa_paybill || $company->mpesa_till || $company->mpesa_number || $company->bank_name)
## Payment Details

@if($company->mpesa_paybill)
- **Paybill:** {{ $company->mpesa_paybill }} | Account: {{ $company->mpesa_account }}
@endif
@if($company->mpesa_till)
- **Till Number:** {{ $company->mpesa_till }}
@endif
@if($company->mpesa_number)
- **M-Pesa Number:** {{ $company->mpesa_number }}
@endif
@if($company->bank_name)
- **Bank:** {{ $company->bank_name }} | Account: {{ $company->bank_account }}@if($company->bank_branch) | Branch: {{ $company->bank_branch }}@endif
@endif

@endif

@if($reminderType === 'after_due')
> If you have already made payment, please disregard this notice or send us your M-Pesa confirmation code.
@endif

{{ $company->footer_message ?? 'Thank you for your business!' }}

Regards,
**{{ $company->name }}**
{{ $company->phone }}
@if($company->email) | {{ $company->email }}@endif

@endcomponent