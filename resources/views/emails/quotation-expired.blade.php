@component('mail::message')

# 📋 Quotation Expired

Hi **{{ $quotation->company->owner->name }}**,

Quotation **{{ $quotation->quotation_number }}** for **{{ $quotation->client->name }}** expired on **{{ $quotation->expiry_date->format('F j, Y') }}** without being accepted or converted to an invoice. It has been automatically marked as **Expired**.

@component('mail::table')
| | |
|:---|:---|
| **Quotation #** | {{ $quotation->quotation_number }} |
| **Client** | {{ $quotation->client->name }} |
| **Amount** | Ksh {{ number_format($quotation->grand_total, 2) }} |
| **Expiry Date** | {{ $quotation->expiry_date->format('F j, Y') }} |
@endcomponent

If the client is still interested, you can duplicate the quotation with a new expiry date.

@component('mail::button', ['url' => route('quotations.show', $quotation), 'color' => 'green'])
View Quotation
@endcomponent

Regards,
**M-Invoice**

@endcomponent