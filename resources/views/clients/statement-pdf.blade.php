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
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11.5px;
            color: #333;
            padding: 30px;
            background: #fff;
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
        .statement-badge {
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
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 22px;
        }
        .kpi-box {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 12px 14px;
            border-radius: 6px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }
        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 14px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 11px;
            font-style: italic;
        }
    </style>
</head>
<body>

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
            <div class="statement-badge">STATEMENT OF ACCOUNT</div>
            <div class="company-sub" style="margin-top: 8px;">Statement Date: <strong>{{ now()->format('d M Y') }}</strong></div>
            @if(!empty($fromDate) || !empty($toDate))
                <div class="company-sub">
                    Period: {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Beginning' }}
                    — {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Present' }}
                </div>
            @endif
        </td>
    </tr>
</table>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <div style="font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 2px;">Account Of (Client)</div>
            <div style="font-size: 15px; font-weight: bold; color: #111;">{{ $client->name }}</div>
            @if($client->phone)
                <div class="company-sub">{{ $client->phone }}</div>
            @endif
            @if($client->email)
                <div class="company-sub">{{ $client->email }}</div>
            @endif
            @if($client->address)
                <div class="company-sub">{{ $client->address }}</div>
            @endif
        </td>
        <td style="width: 40%; vertical-align: top; text-align: right;">
            @if($company->mpesa_paybill)
                <div style="font-size: 10px; text-transform: uppercase; color: #16a34a; font-weight: bold;">M-Pesa Paybill</div>
                <div style="font-size: 13px; font-weight: bold;">{{ $company->mpesa_paybill }} (Acc: {{ $company->mpesa_account ?? $client->name }})</div>
            @elseif($company->mpesa_till)
                <div style="font-size: 10px; text-transform: uppercase; color: #16a34a; font-weight: bold;">M-Pesa Till</div>
                <div style="font-size: 13px; font-weight: bold;">{{ $company->mpesa_till }}</div>
            @endif
            @if($company->bank_name)
                <div style="font-size: 11px; color: #555; margin-top: 4px;">
                    Bank: <strong>{{ $company->bank_name }}</strong> · Acc: <strong>{{ $company->bank_account }}</strong>
                </div>
            @endif
        </td>
    </tr>
</table>

<table class="kpi-table">
    <tr>
        <td style="width: 33.3%; padding-right: 8px;">
            <div class="kpi-box">
                <div style="font-size: 10px; text-transform: uppercase; color: #6b7280;">Total Invoiced</div>
                <div style="font-size: 16px; font-weight: bold; color: #111; margin-top: 4px;">Ksh {{ number_format($totalBilled, 2) }}</div>
            </div>
        </td>
        <td style="width: 33.3%; padding: 0 4px;">
            <div class="kpi-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                <div style="font-size: 10px; text-transform: uppercase; color: #15803d;">Total Paid</div>
                <div style="font-size: 16px; font-weight: bold; color: #16a34a; margin-top: 4px;">Ksh {{ number_format($totalPaid, 2) }}</div>
            </div>
        </td>
        <td style="width: 33.3%; padding-left: 8px;">
            <div class="kpi-box" style="background: {{ $balanceDue > 0 ? '#fef2f2' : '#f0fdf4' }}; border-color: {{ $balanceDue > 0 ? '#fecaca' : '#bbf7d0' }};">
                <div style="font-size: 10px; text-transform: uppercase; color: {{ $balanceDue > 0 ? '#dc2626' : '#15803d' }};">Balance Due</div>
                <div style="font-size: 16px; font-weight: bold; color: {{ $balanceDue > 0 ? '#dc2626' : '#16a34a' }}; margin-top: 4px;">Ksh {{ number_format($balanceDue, 2) }}</div>
            </div>
        </td>
    </tr>
</table>

<div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; margin-bottom: 6px;">Invoice &amp; Payment Activity</div>
<table class="items-table">
    <thead>
        <tr>
            <th>Invoice #</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: right;">Billed (Ksh)</th>
            <th style="text-align: right;">Paid (Ksh)</th>
            <th style="text-align: right;">Balance (Ksh)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $inv)
        <tr>
            <td style="font-weight: bold;">{{ $inv->invoice_number }}</td>
            <td>{{ $inv->issue_date->format('d M Y') }}</td>
            <td>{{ $inv->due_date?->format('d M Y') ?? '—' }}</td>
            <td style="text-align: center; text-transform: uppercase; font-size: 10px; font-weight: bold; color: {{ $inv->status === 'paid' ? '#16a34a' : ($inv->status === 'overdue' ? '#dc2626' : '#555') }};">
                {{ $inv->status }}
            </td>
            <td style="text-align: right;">{{ number_format($inv->grand_total, 2) }}</td>
            <td style="text-align: right; color: #16a34a;">{{ number_format($inv->totalPaidAmount(), 2) }}</td>
            <td style="text-align: right; font-weight: bold; color: {{ $inv->balanceDue() > 0 ? '#dc2626' : '#16a34a' }};">
                {{ number_format($inv->balanceDue(), 2) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align: center; padding: 18px; color: #999;">No invoices found for this period.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="4" style="padding: 10px; text-align: right; text-transform: uppercase; font-size: 10px;">Totals</td>
            <td style="padding: 10px; text-align: right;">Ksh {{ number_format($totalBilled, 2) }}</td>
            <td style="padding: 10px; text-align: right; color: #16a34a;">Ksh {{ number_format($totalPaid, 2) }}</td>
            <td style="padding: 10px; text-align: right; color: {{ $balanceDue > 0 ? '#dc2626' : '#16a34a' }};">Ksh {{ number_format($balanceDue, 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($signaturePath)
<div style="margin-top: 28px;">
    <img src="{{ $signaturePath }}" style="max-height: 48px;" alt="Signature">
    <div style="border-top: 1px solid #ccc; width: 160px; margin-top: 6px;"></div>
    <div style="font-size: 10px; color: #999; margin-top: 3px;">Authorized Signature · {{ $company->name }}</div>
</div>
@endif

<div class="footer">
    {{ $company->footer_message ?? 'Thank you for your business!' }}
</div>

</body>
</html>

