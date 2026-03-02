@component('mail::message')

# Invoice {{ $invoice->invoice_number }}

Dear **{{ $invoice->client->name }}**,

Please find attached your invoice **{{ $invoice->invoice_number }}** from **{{ $company->name }}**. Below is a full breakdown:

---

## Invoice Details

@component('mail::table')
| | |
|:--|--:|
| Invoice # | {{ $invoice->invoice_number }} |
| Issue Date | {{ $invoice->issue_date->format('M j, Y') }} |
@if($invoice->due_date)
| Due Date | {{ $invoice->due_date->format('M j, Y') }} |
@endif
| Status | {{ ucfirst($invoice->status) }} |
@if($invoice->etr_enabled)
| Type | ETR Tax Invoice |
@endif
@endcomponent

---

## Items

@component('mail::table')
| Description | Qty | Unit Price | Total |
|:--|:--:|--:|--:|
@foreach($invoice->items as $item)
| {{ $item->description }}{{ $item->is_labour ? ' *(Labour)*' : '' }} | {{ $item->quantity }} | Ksh {{ number_format($item->unit_price, 2) }} | Ksh {{ number_format($item->total_price, 2) }} |
@endforeach
@endcomponent

---

## Summary

@component('mail::table')
| | |
|:--|--:|
| Material Cost | Ksh {{ number_format($invoice->material_cost, 2) }} |
| Labour Cost | Ksh {{ number_format($invoice->labour_cost, 2) }} |
@if($invoice->etr_enabled)
| VAT (16%) | Ksh {{ number_format($invoice->vat_amount, 2) }} |
@endif
| **Grand Total** | **Ksh {{ number_format($invoice->grand_total, 2) }}** |
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

@if($invoice->notes)
## Notes

{{ $invoice->notes }}

@endif

@if($invoice->due_date)
> **Please ensure payment is made by {{ $invoice->due_date->format('F j, Y') }}.**
@endif

{{ $company->footer_message ?? 'Thank you for your business!' }}

Regards, <br>
**{{ $company->name }}**
{{ $company->phone }}
@if($company->email) | {{ $company->email }}@endif

@endcomponent