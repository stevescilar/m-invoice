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

    $docNumber = $documentNumber ?? ($invoice->invoice_number ?? $quotation->quotation_number ?? 'DOC');
    $dnNumber  = 'DN-' . preg_replace('/^(INV-|QUO-)/', '', $docNumber);
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
            font-size: 68px;
            font-weight: bold;
            color: rgba(22, 163, 74, 0.04);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 6px;
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
        .dn-badge {
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
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
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
            padding: 9px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }
        .checkbox-box {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1.5px solid #6b7280;
            border-radius: 2px;
        }
        .signoff-box {
            margin-top: 28px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            padding: 16px;
            background: #f9fafb;
        }
        .signoff-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: {{ $brandColor }};
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .sign-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .sign-grid td {
            width: 50%;
            vertical-align: top;
            padding: 8px 12px 4px 0;
            font-size: 11px;
        }
        .sign-line {
            border-bottom: 1px dotted #6b7280;
            height: 22px;
            margin-bottom: 6px;
        }
        .footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 11px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="watermark">DELIVERY NOTE</div>

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
            <div class="dn-badge">DELIVERY NOTE &amp; JOB SIGN-OFF</div>
            <div style="font-size: 16px; font-weight: bold; margin-top: 8px; color: #111;">{{ $dnNumber }}</div>
            <div class="company-sub">Ref Document: <strong>{{ $docNumber }}</strong></div>
            <div class="company-sub">Date: {{ now()->format('d M Y') }}</div>
        </td>
    </tr>
</table>

<table class="meta-table">
    <tr>
        <td style="width: 55%; vertical-align: top; padding-right: 20px;">
            <div style="font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 3px;">Deliver To / Installation Site</div>
            <div style="font-size: 14px; font-weight: bold; color: #111;">{{ $client->name }}</div>
            @if($client->phone)
                <div class="company-sub">Phone: {{ $client->phone }}</div>
            @endif
            @if($client->email)
                <div class="company-sub">Email: {{ $client->email }}</div>
            @endif
            @if($client->address)
                <div class="company-sub">Address / Site: {{ $client->address }}</div>
            @endif
        </td>
        <td style="width: 45%; vertical-align: top;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 6px;">
                <tr>
                    <td style="padding: 6px 10px; font-size: 10px; text-transform: uppercase; color: #888; border-bottom: 1px solid #eee;">Delivery Note #</td>
                    <td style="padding: 6px 10px; font-size: 11px; font-weight: bold; text-align: right; border-bottom: 1px solid #eee;">{{ $dnNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 10px; font-size: 10px; text-transform: uppercase; color: #888; border-bottom: 1px solid #eee;">Order / Ref #</td>
                    <td style="padding: 6px 10px; font-size: 11px; font-weight: bold; text-align: right; border-bottom: 1px solid #eee;">{{ $docNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 10px; font-size: 10px; text-transform: uppercase; color: #888;">Total Line Items</td>
                    <td style="padding: 6px 10px; font-size: 11px; font-weight: bold; text-align: right;">{{ $items->count() }} item(s)</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 8%; text-align: center;">#</th>
            <th style="width: 54%;">Item / Service Description</th>
            <th style="width: 16%;">Category</th>
            <th style="width: 12%; text-align: center;">Qty</th>
            <th style="width: 10%; text-align: center;">Checked</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $index => $item)
        @php
            $typeName = optional($item->itemType)->name ?? ($item->is_labour ? 'Labour / Installation' : 'Material / Hardware');
        @endphp
        <tr>
            <td style="text-align: center; color: #666;">{{ $index + 1 }}</td>
            <td style="font-weight: 500;">{{ $item->description }}</td>
            <td style="color: #555;">{{ $typeName }}</td>
            <td style="text-align: center; font-weight: bold;">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
            <td style="text-align: center;"><span class="checkbox-box"></span></td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(!empty($notes))
<div style="margin-top: 16px; padding: 10px 14px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; font-size: 11px; color: #78350f;">
    <strong>Remarks / Notes:</strong> {{ $notes }}
</div>
@endif

<div class="signoff-box">
    <div class="signoff-title">Delivery &amp; Installation Completion Sign-Off</div>
    <p style="font-size: 11px; color: #555; line-height: 1.4;">
        I/We confirm that the above listed items, equipment, and/or installation works have been delivered, tested, and received in good working condition.
    </p>

    <table class="sign-grid">
        <tr>
            <td style="padding-right: 24px;">
                <div style="font-weight: bold; color: #111; margin-bottom: 10px;">Delivered / Installed By ({{ $company->name }})</div>
                <div>Technician / Rep Name:</div>
                <div class="sign-line"></div>
                <div>Signature &amp; Stamp:</div>
                @if($signaturePath)
                    <div style="margin: 4px 0;">
                        <img src="{{ $signaturePath }}" style="max-height: 40px;" alt="Signature">
                    </div>
                @else
                    <div class="sign-line"></div>
                @endif
                <div>Date: {{ now()->format('d / m / Y') }}</div>
            </td>
            <td style="padding-left: 24px; border-left: 1px solid #e5e7eb;">
                <div style="font-weight: bold; color: #111; margin-bottom: 10px;">Received &amp; Accepted By (Client)</div>
                <div>Client / Representative Name:</div>
                <div class="sign-line"></div>
                <div>Phone / ID Number:</div>
                <div class="sign-line"></div>
                <div>Client Signature &amp; Date:</div>
                <div class="sign-line"></div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    {{ $company->footer_message ?? 'Thank you for choosing ' . $company->name . '!' }}
</div>

</body>
</html>

