@php
    $brandColor = $company->primary_color ?? '#16a34a';
    $logoPath = null;
    if ($company->logo) {
        $cand1 = storage_path('app/public/' . $company->logo);
        $cand2 = public_path('storage/' . $company->logo);
        $logoPath = file_exists($cand1) ? $cand1 : (file_exists($cand2) ? $cand2 : null);
    }
    $signaturePath = null;
    if ($company->signature) {
        $sig1 = storage_path('app/public/' . $company->signature);
        $sig2 = public_path('storage/' . $company->signature);
        $signaturePath = file_exists($sig1) ? $sig1 : (file_exists($sig2) ? $sig2 : null);
    }

    $receiptNumber = $payment?->receipt_number ?? ('RCP-' . str_replace('INV-', '', $invoice->invoice_number));
    $receiptDate   = $payment?->paid_at ?? ($invoice->paid_at ?? now());
    $receiptAmount = $payment ? (float) $payment->amount : (float) $invoice->totalPaidAmount();
    $receiptMethod = $payment ? $payment->methodLabel() : ($invoice->mpesa_code ? 'M-Pesa' : 'Payment');
    $receiptRef    = $payment?->reference ?? $invoice->mpesa_code;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
            background: #fff;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 74px;
            font-weight: bold;
            color: rgba(22, 163, 74, 0.05);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid {{ $brandColor }};
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: {{ $brandColor }};
        }
        .company-sub {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        .receipt-badge {
            display: inline-block;
            background: #f0fdf4;
            border: 2px solid {{ $brandColor }};
            color: {{ $brandColor }};
            font-weight: bold;
            font-size: 12px;
            padding: 5px 14px;
            border-radius: 6px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .summary-box td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .amount-highlight {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-bottom: 22px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .items-table thead tr {
            background-color: {{ $brandColor }};
            color: #fff;
        }
        .items-table th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 36px;
            padding-top: 14px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 11px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="watermark">OFFICIAL RECEIPT</div>

<table class="header-table">
    <tr>
        <td style="width: 55%; vertical-align: middle; padding-bottom: 16px;">
            @if($logoPath)
                <img src="{{ $logoPath }}" style="max-height: 65px; max-width: 190px;" alt="Logo">
            @else
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="company-sub">{{ $company->address }}</div>
            <div class="company-sub">{{ $company->phone }} {{ $company->email ? '· ' . $company->email : '' }}</div>
            @if($company->kra_pin)
                <div class="company-sub">KRA PIN: {{ $company->kra_pin }}</div>
            @endif
        </td>
        <td style="width: 45%; vertical-align: middle; text-align: right; padding-bottom: 16px;">
            <div class="receipt-badge">PAYMENT RECEIPT</div>
            <div style="font-size: 15px; font-weight: bold; margin-top: 8px; color: #111;">{{ $receiptNumber }}</div>
            <div class="company-sub">Date: {{ \Carbon\Carbon::parse($receiptDate)->format('d M Y, g:i A') }}</div>
        </td>
    </tr>
</table>

<div class="amount-highlight">
    <div style="font-size: 10px; text-transform: uppercase; color: #15803d; font-weight: bold; letter-spacing: 1px;">Amount Received</div>
    <div style="font-size: 28px; font-weight: bold; color: {{ $brandColor }}; margin-top: 4px;">
        Ksh {{ number_format($receiptAmount, 2) }}
    </div>
    <div style="font-size: 11px; color: #555; margin-top: 4px;">
        Paid via <strong>{{ $receiptMethod }}</strong>
        @if($receiptRef)
            &nbsp;·&nbsp; Ref: <strong>{{ $receiptRef }}</strong>
        @endif
    </div>
</div>

<table class="summary-box">
    <tr>
        <td style="width: 35%; color: #666; font-size: 11px; text-transform: uppercase;">Received From (Client)</td>
        <td style="font-weight: bold; font-size: 13px; color: #111;">
            {{ $invoice->client->name }}
            @if($invoice->client->phone)
                <span style="font-weight: normal; font-size: 11px; color: #666;">({{ $invoice->client->phone }})</span>
            @endif
        </td>
    </tr>
    <tr>
        <td style="color: #666; font-size: 11px; text-transform: uppercase;">For Invoice #</td>
        <td style="font-weight: bold;">{{ $invoice->invoice_number }} (Issued {{ $invoice->issue_date->format('d M Y') }})</td>
    </tr>
    <tr>
        <td style="color: #666; font-size: 11px; text-transform: uppercase;">Invoice Grand Total</td>
        <td>Ksh {{ number_format($invoice->grand_total, 2) }}</td>
    </tr>
    <tr>
        <td style="color: #666; font-size: 11px; text-transform: uppercase;">Total Paid to Date</td>
        <td style="color: #16a34a; font-weight: bold;">Ksh {{ number_format($invoice->totalPaidAmount(), 2) }}</td>
    </tr>
    <tr>
        <td style="color: #666; font-size: 11px; text-transform: uppercase; border-bottom: none;">Remaining Balance Due</td>
        <td style="font-weight: bold; border-bottom: none; color: {{ $invoice->balanceDue() > 0 ? '#dc2626' : '#16a34a' }};">
            Ksh {{ number_format($invoice->balanceDue(), 2) }}
            @if($invoice->balanceDue() <= 0)
                &nbsp;(PAID IN FULL)
            @else
                &nbsp;(PARTIAL PAYMENT / DEPOSIT)
            @endif
        </td>
    </tr>
</table>

@if($payment?->notes)
<div style="margin-bottom: 18px; padding: 10px 14px; background: #f9fafb; border-left: 3px solid {{ $brandColor }}; font-size: 11px; color: #555;">
    <strong>Payment Notes:</strong> {{ $payment->notes }}
</div>
@endif

<div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; margin-top: 16px;">Invoice Items Summary</div>
<table class="items-table">
    <thead>
        <tr>
            <th>Description</th>
            <th style="text-align: center; width: 15%;">Qty</th>
            <th style="text-align: right; width: 20%;">Unit Price</th>
            <th style="text-align: right; width: 20%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td style="text-align: center;">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
            <td style="text-align: right;">Ksh {{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right;">Ksh {{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($signaturePath)
<div style="margin-top: 32px;">
    <img src="{{ $signaturePath }}" style="max-height: 50px;" alt="Signature">
    <div style="border-top: 1px solid #ccc; width: 160px; margin-top: 6px;"></div>
    <div style="font-size: 10px; color: #999; margin-top: 3px;">Authorized Signature · {{ $company->name }}</div>
</div>
@endif

<div class="footer">
    {{ $company->footer_message ?? 'Thank you for your business!' }}
</div>

</body>
</html>

