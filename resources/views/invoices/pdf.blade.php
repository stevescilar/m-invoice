@php $brandColor = $company->primary_color ?? '#16a34a'; @endphp
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

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(22, 163, 74, 0.05);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 10px;
        }

        /* ── Layout helpers ── */
        .w100  { width: 100%; }
        .w50   { width: 50%; }
        .w30   { width: 30%; }
        .w70   { width: 70%; }
        .vtop  { vertical-align: top; }
        .vmid  { vertical-align: middle; }
        .tright { text-align: right; }
        .tcenter { text-align: center; }
        .nopad { padding: 0; }
        .noborder { border: none; }

        /* ── Header ── */
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

        /* ── Title bar ── */
        .title-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #111;
            letter-spacing: 2px;
        }

        .etr-badge {
            background: #ede9fe;
            color: #7c3aed;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        /* ── Meta info table ── */
        .meta-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 2px;
        }

        .meta-value {
            font-weight: bold;
            font-size: 13px;
        }

        .meta-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-info-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        .meta-info-table tr:nth-child(odd) td {
            background: #f9fafb;
        }

        /* ── Items table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .items-table thead tr {
            background-color: {{ $brandColor }};
            color: white;
        }

        .items-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        /* type dot inside table */
        .type-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
        }

        /* ── Totals section ── */
        .totals-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border-top: 2px solid #e5e7eb;
        }

        .totals-inner {
            width: 260px;
            float: right; /* DomPDF-safe alternative to margin-left:auto */
            border-collapse: collapse;
        }

        /* DomPDF doesn't support float well inside block; use right-aligned table instead */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .totals-table td {
            padding: 5px 8px;
            font-size: 12px;
            border: none;
        }

        .totals-table .label-cell { text-align: left;  color: #555; }
        .totals-table .value-cell { text-align: right; color: #333; font-weight: 500; }

        .totals-table .breakdown-section td {
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        .totals-table .vat-row td   { color: #7c3aed; }

        .totals-table .grand-row td {
            border-top: 2px solid #333;
            padding-top: 10px;
            font-size: 15px;
            font-weight: bold;
            color: {{ $brandColor }};
        }

        /* colour swatch in breakdown */
        .swatch {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* ── Payment section ── */
        .payment-section {
            margin-top: 28px;
            padding: 14px 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
        }

        .payment-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: {{ $brandColor }};
            margin-bottom: 10px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-table td {
            border: none;
            padding: 2px 16px 2px 0;
            vertical-align: top;
            font-size: 11px;
        }

        .payment-label {
            color: #999;
            font-size: 10px;
            text-transform: uppercase;
        }

        .payment-value {
            font-weight: bold;
            color: #333;
            font-size: 12px;
        }

        /* ── Notes ── */
        .notes-box {
            margin-top: 24px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .notes-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
        }

        /* ── Signature ── */
        .signature-block {
            margin-top: 36px;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px solid #eee;
            font-style: italic;
            color: #aaa;
            font-size: 11px;
        }

        /* ── Paid stamp ── */
        .paid-stamp {
            position: fixed;
            top: 120px;
            right: 40px;
            border: 4px solid {{ $brandColor }};
            color: {{ $brandColor }};
            font-size: 36px;
            font-weight: bold;
            padding: 6px 18px;
            border-radius: 6px;
            opacity: 0.18;
            transform: rotate(-15deg);
            letter-spacing: 4px;
            z-index: 0;
        }
    </style>
</head>
<body>

@php
    /* ── Type breakdown ── */
    $typeBreakdown = $invoice->items
        ->groupBy(fn($item) => optional($item->itemType)->name ?? 'Material')
        ->map(fn($grp, $name) => [
            'name'  => $name,
            'total' => $grp->sum('total_price'),
            'color' => optional($grp->first()->itemType)->color ?? $brandColor,
        ]);

    $multiType = $typeBreakdown->count() > 1;
@endphp

<div class="watermark">{{ strtoupper($company->name) }}</div>

@if($invoice->status === 'paid')
    <div class="paid-stamp">PAID</div>
@endif

{{-- ══ HEADER ══ --}}
<table class="header-table">
    <tr>
        <td class="w50 vmid nopad noborder" style="padding-bottom: 16px;">
            @if($company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}"
                     style="max-height: 64px; max-width: 180px;" alt="Logo">
            @else
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="company-sub">{{ $company->phone }}</div>
            <div class="company-sub">{{ $company->email }}</div>
            @if($invoice->etr_enabled && $company->kra_pin)
                <div class="company-sub">KRA PIN: {{ $company->kra_pin }}</div>
            @endif
        </td>
        <td class="w50 vmid tright nopad noborder" style="padding-bottom: 16px;">
            @if($company->logo)
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="company-sub" style="margin-top: 4px;">{{ $company->address }}</div>
        </td>
    </tr>
</table>

{{-- ══ TITLE BAR ══ --}}
<table class="title-bar">
    <tr>
        <td class="vtop nopad noborder">
            <span class="invoice-title">INVOICE</span>
            @if($invoice->etr_enabled)
                &nbsp;<span class="etr-badge">ETR TAX INVOICE</span>
            @endif
        </td>
        <td class="vtop tright nopad noborder" style="font-size: 11px; color: #999; padding-top: 6px;">
            Generated {{ now()->format('d M Y, H:i') }}
        </td>
    </tr>
</table>

{{-- ══ META ══ --}}
<table class="meta-outer">
    <tr>
        {{-- Bill To --}}
        <td class="w50 vtop nopad noborder" style="padding-right: 20px;">
            <div class="meta-label">Invoice For</div>
            <div class="meta-value">{{ $invoice->client->name }}</div>
            @if($invoice->client->phone)
                <div class="company-sub">{{ $invoice->client->phone }}</div>
            @endif
            @if($invoice->client->email)
                <div class="company-sub">{{ $invoice->client->email }}</div>
            @endif
            @if($invoice->client->address)
                <div class="company-sub">{{ $invoice->client->address }}</div>
            @endif
        </td>
        {{-- Invoice details --}}
        <td class="w50 vtop nopad noborder">
            <table class="meta-info-table" style="border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                <tr>
                    <td class="meta-label" style="width:45%">Invoice #</td>
                    <td class="tright" style="font-weight: bold;">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Issue Date</td>
                    <td class="tright">{{ $invoice->issue_date->format('d M Y') }}</td>
                </tr>
                @if($invoice->due_date)
                <tr>
                    <td class="meta-label">Due Date</td>
                    <td class="tright"
                        style="{{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'color:#dc2626;font-weight:bold;' : '' }}">
                        {{ $invoice->due_date->format('d M Y') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="meta-label">Status</td>
                    <td class="tright" style="font-weight: bold;
                        color: {{ $invoice->status === 'paid' ? $brandColor : ($invoice->status === 'overdue' ? '#dc2626' : '#555') }}">
                        {{ strtoupper($invoice->status) }}
                    </td>
                </tr>
                @if($invoice->mpesa_code)
                <tr>
                    <td class="meta-label">M-Pesa Ref</td>
                    <td class="tright" style="color: {{ $brandColor }}; font-weight: bold;">{{ $invoice->mpesa_code }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

{{-- ══ ITEMS TABLE ══ --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 40%;">Description</th>
            <th style="width: 18%;">Type</th>
            <th class="tcenter" style="width: 10%;">Qty</th>
            <th class="tright" style="width: 16%;">Unit Price</th>
            <th class="tright" style="width: 16%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        @php
            $typeName  = optional($item->itemType)->name  ?? 'Material';
            $typeColor = optional($item->itemType)->color ?? $brandColor;
        @endphp
        <tr>
            <td>{{ $item->description }}</td>
            <td>
                <span class="type-dot" style="background: {{ $typeColor }};"></span>
                {{ $typeName }}
            </td>
            <td class="tcenter">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
            <td class="tright">Ksh {{ number_format($item->unit_price, 2) }}</td>
            <td class="tright">Ksh {{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ══ TOTALS ══ --}}
@php $subtotal = $invoice->items->sum('total_price'); @endphp
<table class="totals-table" style="margin-top: 4px;">
    <tr>
        <td style="width: 55%; border: none; padding: 0 16px 0 0; vertical-align: top;">

            {{-- ── PAYMENT BADGES ── --}}
            @php
                $hasMpesa = $company->mpesa_paybill || $company->mpesa_till || $company->mpesa_number;
                $hasBank  = $company->bank_name;
            @endphp

            @if($hasMpesa || $hasBank)
            <div style="margin-top: 4px;">
                <div style="font-size: 9px; text-transform: uppercase; color: #999; letter-spacing: 0.5px; margin-bottom: 8px; font-weight: bold;">Payment Details</div>

                {{-- Paybill badge --}}
                @if($company->mpesa_paybill)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border-radius: 6px; overflow: hidden; border: 1.5px solid #16a34a;">
                    <tr>
                        <td colspan="2" style="border: none; background: #16a34a; padding: 4px 10px; text-align: center;">
                            <span style="font-size: 10px; font-weight: bold; color: #fff; letter-spacing: 1px; text-transform: uppercase;">LIPA NA M-PESA · PAYBILL</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; background: #f0fdf4; padding: 6px 10px; text-align: center; width: 50%;">
                            <div style="font-size: 9px; color: #16a34a; text-transform: uppercase; font-weight: bold;">Paybill Number</div>
                            <div style="font-size: 18px; font-weight: bold; color: #111; letter-spacing: 3px;">{{ $company->mpesa_paybill }}</div>
                        </td>
                        <td style="border: none; background: #f0fdf4; padding: 6px 10px; text-align: center; width: 50%; border-left: 1px solid #bbf7d0;">
                            <div style="font-size: 9px; color: #16a34a; text-transform: uppercase; font-weight: bold;">Account Number</div>
                            <div style="font-size: 13px; font-weight: bold; color: #111;">{{ $company->mpesa_account ?? $company->name }}</div>
                        </td>
                    </tr>
                </table>
                @endif

                {{-- Till badge --}}
                @if($company->mpesa_till)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 1.5px solid #16a34a;">
                    <tr>
                        <td style="border: none; background: #16a34a; padding: 4px 10px; text-align: center;">
                            <span style="font-size: 10px; font-weight: bold; color: #fff; letter-spacing: 1px; text-transform: uppercase;">LIPA NA M-PESA · BUY GOODS</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; background: #f0fdf4; padding: 6px 10px; text-align: center;">
                            <div style="font-size: 9px; color: #16a34a; text-transform: uppercase; font-weight: bold;">Till Number</div>
                            <div style="font-size: 22px; font-weight: bold; color: #111; letter-spacing: 4px;">{{ $company->mpesa_till }}</div>
                        </td>
                    </tr>
                </table>
                @endif

                {{-- Send Money badge --}}
                @if($company->mpesa_number)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 1.5px solid #16a34a;">
                    <tr>
                        <td style="border: none; background: #16a34a; padding: 4px 10px; text-align: center;">
                            <span style="font-size: 10px; font-weight: bold; color: #fff; letter-spacing: 1px; text-transform: uppercase;">M-PESA · SEND MONEY</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; background: #f0fdf4; padding: 6px 10px; text-align: center;">
                            <div style="font-size: 9px; color: #16a34a; text-transform: uppercase; font-weight: bold;">Phone Number</div>
                            <div style="font-size: 18px; font-weight: bold; color: #111; letter-spacing: 3px;">{{ $company->mpesa_number }}</div>
                        </td>
                    </tr>
                </table>
                @endif

                {{-- Bank badge --}}
                @if($company->bank_name)
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 1.5px solid #1d4ed8;">
                    <tr>
                        <td colspan="2" style="border: none; background: #1d4ed8; padding: 4px 10px; text-align: center;">
                            <span style="font-size: 10px; font-weight: bold; color: #fff; letter-spacing: 1px; text-transform: uppercase;">BANK TRANSFER · {{ strtoupper($company->bank_name) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; background: #eff6ff; padding: 6px 10px; text-align: center; width: 50%;">
                            <div style="font-size: 9px; color: #1d4ed8; text-transform: uppercase; font-weight: bold;">Account Number</div>
                            <div style="font-size: 14px; font-weight: bold; color: #111; letter-spacing: 2px;">{{ $company->bank_account ?? '—' }}</div>
                        </td>
                        @if($company->bank_branch)
                        <td style="border: none; background: #eff6ff; padding: 6px 10px; text-align: center; width: 50%; border-left: 1px solid #bfdbfe;">
                            <div style="font-size: 9px; color: #1d4ed8; text-transform: uppercase; font-weight: bold;">Branch</div>
                            <div style="font-size: 12px; font-weight: bold; color: #111;">{{ $company->bank_branch }}</div>
                        </td>
                        @endif
                    </tr>
                </table>
                @endif

            </div>
            @endif

        </td>
        <td style="width: 45%; border: none; padding: 0; vertical-align: top;">
            <table style="width: 100%; border-collapse: collapse;">

                {{-- Breakdown header --}}
                @if($multiType)
                <tr>
                    <td colspan="2" style="border: none; padding: 6px 8px 2px; font-size: 10px; text-transform: uppercase; color: #999; letter-spacing: 0.5px;">
                        Cost Breakdown
                    </td>
                </tr>
                @endif

                {{-- Per-type rows --}}
                @foreach($typeBreakdown as $row)
                @if($row['total'] > 0)
                <tr>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; color: #555; border-top: 1px solid #f0f0f0;">
                        <span class="swatch" style="background: {{ $row['color'] }};"></span>
                        {{ $row['name'] }}
                    </td>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; text-align: right; border-top: 1px solid #f0f0f0;">
                        Ksh {{ number_format($row['total'], 2) }}
                    </td>
                </tr>
                @endif
                @endforeach

                {{-- Discount --}}
                @if($invoice->discount_amount > 0)
                <tr>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; color: #16a34a; border-top: 1px solid #f0f0f0;">
                        Discount
                        @if($invoice->discount_percentage > 0)
                            <span style="font-size: 9px; color: #999;">({{ number_format($invoice->discount_percentage, 1) }}%)</span>
                        @endif
                    </td>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; color: #16a34a; text-align: right; border-top: 1px solid #f0f0f0;">
                        - Ksh {{ number_format($invoice->discount_amount, 2) }}
                    </td>
                </tr>
                @endif

                {{-- VAT --}}
                @if($invoice->etr_enabled && $invoice->vat_amount > 0)
                <tr>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; color: #7c3aed; border-top: 1px solid #f0f0f0;">
                        VAT (16%)
                    </td>
                    <td style="border: none; padding: 4px 8px; font-size: 11px; color: #7c3aed; text-align: right; border-top: 1px solid #f0f0f0;">
                        Ksh {{ number_format($invoice->vat_amount, 2) }}
                    </td>
                </tr>
                @endif

                {{-- Grand Total --}}
                <tr>
                    <td style="border: none; padding: 10px 8px 6px; font-size: 15px; font-weight: bold; color: #111; border-top: 2px solid #333;">
                        Grand Total
                    </td>
                    <td style="border: none; padding: 10px 8px 6px; font-size: 15px; font-weight: bold; color: {{ $brandColor }}; text-align: right; border-top: 2px solid #333;">
                        Ksh {{ number_format($invoice->grand_total, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Payment details moved into the totals left cell above --}}

{{-- ══ NOTES ══ --}}
@if($invoice->notes)
<div class="notes-box" style="margin-top: 20px;">
    <div class="notes-label">Terms &amp; Notes</div>
    <div style="font-size: 11px; color: #555; line-height: 1.5;">{{ $invoice->notes }}</div>
</div>
@endif

{{-- ══ SIGNATURE ══ --}}
@if($company->signature)
<div class="signature-block">
    <img src="{{ public_path('storage/' . $company->signature) }}"
         style="max-height: 54px;" alt="Signature">
    <div style="border-top: 1px solid #ccc; width: 160px; margin-top: 6px;"></div>
    <div style="font-size: 10px; color: #999; margin-top: 3px;">Authorized Signature</div>
</div>
@endif

{{-- ══ FOOTER ══ --}}
@if($company->footer_message)
<div class="footer">{{ $company->footer_message }}</div>
@endif

</body>
</html>