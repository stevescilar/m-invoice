@component('mail::message')

# Quotation {{ $quotation->quotation_number }}

Dear **{{ $quotation->client->name }}**,

Please find attached your quotation **{{ $quotation->quotation_number }}** from **{{ $company->name }}**. Below is a full breakdown:

---

## Quotation Details

@component('mail::table')
| | |
|:--|--:|
| Quotation # | {{ $quotation->quotation_number }} |
| Issue Date | {{ $quotation->issue_date->format('M j, Y') }} |
@if($quotation->expiry_date)
| Valid Until | {{ $quotation->expiry_date->format('M j, Y') }} |
@endif
| Status | {{ ucfirst($quotation->status) }} |
@endcomponent

---

## Items

@component('mail::table')
| Description | Qty | Unit Price | Total |
|:--|:--:|--:|--:|
@foreach($quotation->items as $item)
| {{ $item->description }}{{ $item->is_labour ? ' *(Labour)*' : '' }} | {{ $item->quantity }} | Ksh {{ number_format($item->unit_price, 2) }} | Ksh {{ number_format($item->total_price, 2) }} |
@endforeach
@endcomponent

---

## Summary

@component('mail::table')
| | |
|:--|--:|
| Material Cost | Ksh {{ number_format($quotation->material_cost, 2) }} |
| Labour Cost | Ksh {{ number_format($quotation->labour_cost, 2) }} |
| **Grand Total** | **Ksh {{ number_format($quotation->grand_total, 2) }}** |
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

@if($quotation->notes)
## Terms & Notes

{{ $quotation->notes }}

@endif

Please review and let us know if you have any questions or would like to proceed.

{{ $company->footer_message ?? 'Thank you for considering our services!' }}

Regards, <br>
**{{ $company->name }}**
{{ $company->phone }}
@if($company->email) | {{ $company->email }}@endif

@endcomponent