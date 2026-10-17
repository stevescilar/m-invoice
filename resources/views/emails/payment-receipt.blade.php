@php
    $receiptNo = $payment?->receipt_number ?? ('RCP-' . str_replace('INV-', '', $invoice->invoice_number));
    $amountPaid = $payment ? (float) $payment->amount : (float) $invoice->totalPaidAmount();
    $balanceDue = $invoice->balanceDue();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 24px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="background: {{ $company->primary_color ?? '#16a34a' }}; color: #ffffff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 20px;">Payment Receipt — {{ $receiptNo }}</h2>
            <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">{{ $company->name }}</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">Dear <strong>{{ $invoice->client->name }}</strong>,</p>
            <p>Thank you for your payment! Please find your official payment receipt attached to this email.</p>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin: 20px 0;">
                <p style="margin: 4px 0; font-size: 14px;"><strong>Receipt #:</strong> {{ $receiptNo }}</p>
                <p style="margin: 4px 0; font-size: 14px;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin: 4px 0; font-size: 16px; color: #15803d;"><strong>Amount Received:</strong> Ksh {{ number_format($amountPaid, 2) }}</p>
                @if($payment?->reference || $invoice->mpesa_code)
                    <p style="margin: 4px 0; font-size: 13px;"><strong>Reference:</strong> {{ $payment?->reference ?? $invoice->mpesa_code }}</p>
                @endif
                <hr style="border: none; border-top: 1px solid #bbf7d0; margin: 10px 0;">
                <p style="margin: 4px 0; font-size: 13px;"><strong>Invoice Total:</strong> Ksh {{ number_format($invoice->grand_total, 2) }}</p>
                <p style="margin: 4px 0; font-size: 13px;"><strong>Total Paid to Date:</strong> Ksh {{ number_format($invoice->totalPaidAmount(), 2) }}</p>
                <p style="margin: 4px 0; font-size: 14px;">
                    <strong>Remaining Balance:</strong>
                    <span style="color: {{ $balanceDue > 0 ? '#dc2626' : '#15803d' }}; font-weight: bold;">
                        Ksh {{ number_format($balanceDue, 2) }} {{ $balanceDue <= 0 ? '(Paid in Full)' : '' }}
                    </span>
                </p>
            </div>

            <p style="margin: 18px 0;">
                <a href="{{ $invoice->public_url }}"
                   style="display: inline-block; background: {{ $company->primary_color ?? '#16a34a' }}; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-size: 13px; font-weight: bold;">
                    View Invoice Online
                </a>
            </p>

            <p style="font-size: 13px; color: #6b7280; margin-top: 24px;">
                Best regards,<br>
                <strong>{{ $company->name }}</strong><br>
                {{ $company->phone }} {{ $company->email ? '· ' . $company->email : '' }}
            </p>
        </div>
    </div>
</body>
</html>
