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
            color: rgba(37, 99, 235, 0.05);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 10px;
        }

        /* ── Layout helpers ── */
        .vtop   { vertical-align: top; }
        .vmid   { vertical-align: middle; }
        .tright { text-align: right; }
        .tcenter { text-align: center; }
        .nopad  { padding: 0; }
        .noborder { border: none; }

        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 20px;
        }

        .company-name  { font-size: 22px; font-weight: bold; color: #2563eb; }
        .company-sub   { font-size: 11px; color: #666; margin-top: 2px; }

        /* ── Title ── */
        .title-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .doc-title {
            font-size: 26px;
            font-weight: bold;
            color: #111;
            letter-spacing: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-left: 8px;
            vertical-align: middle;
        }
        .status-draft     { background: #f3f4f6; color: #6b7280; }
        .status-sent      { background: #eff6ff; color: #2563eb; }
        .status-approved  { background: #f0fdf4; color: {{ $brandColor }}; }
        .status-rejected  { background: #fef2f2; color: #dc2626; }
        .status-converted { background: #f5f3ff; color: #7c3aed; }

        /* ── Validity notice ── */
        .validity-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #1d4ed8;
        }

        /* ── Meta ── */
        .meta-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .meta-label { font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 2px; }
        .meta-value { font-weight: bold; font-size: 13px; }

        .meta-info-table { width: 100%; border-collapse: collapse; }
        .meta-info-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .meta-info-table tr:nth-child(odd) td { background: #f9fafb; }

        /* ── Items table ── */
        .items-table { width: 100%; border-collapse: collapse; }

        .items-table thead tr { background: #2563eb; color: white; }
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
        .items-table tbody tr:last-child td { border-bottom: none; }
        .items-table tbody tr:nth-child(even) td { background: #f9fafb; }

        .type-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
        }

        /* ── Totals ── */
        .totals-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .swatch {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* ── Notes ── */
        .notes-box {
            margin-top: 24px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        .notes-label { font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 5px; }

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
    </style>
</head>
<body>

@php
    $typeBreakdown = $quotation->items
        ->groupBy(fn($item) => optional($item->itemType)->name ?? 'Material')
        ->map(fn($grp, $name) => [
            'name'  => $name,
            'total' => $grp->sum('total_price'),
            'color' => optional($grp->first()->itemType)->color ?? '#2563eb',
        ]);

    $multiType = $typeBreakdown->count() > 1;
@endphp

<div class="watermark">{{ strtoupper($company->name) }}</div>

{{-- ══ HEADER ══ --}}
<table class="header-table">
    <tr>
        <td style="width:50%; vertical-align:middle; padding: 0 0 16px 0; border:none;">
            @if($company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}"
                     style="max-height:64px; max-width:180px;" alt="Logo">
            @else
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="company-sub">{{ $company->phone }}</div>
            <div class="company-sub">{{ $company->email }}</div>
        </td>
        <td style="width:50%; vertical-align:middle; text-align:right; padding: 0 0 16px 0; border:none;">
            @if($company->logo)
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="company-sub" style="margin-top:4px;">{{ $company->address }}</div>
        </td>
    </tr>
</table>

{{-- ══ TITLE BAR ══ --}}
<table class="title-table">
    <tr>
        <td class="vtop nopad noborder">
            <span class="doc-title">QUOTATION</span>
            <span class="status-badge status-{{ $quotation->status }}">{{ strtoupper($quotation->status) }}</span>
        </td>
        <td class="vtop tright nopad noborder" style="font-size:11px; color:#999; padding-top:6px;">
            Generated {{ now()->format('d M Y, H:i') }}
        </td>
    </tr>
</table>

{{-- ══ VALIDITY ══ --}}
@if($quotation->expiry_date)
<div class="validity-box">
    This quotation is valid until <strong>{{ $quotation->expiry_date->format('F j, Y') }}</strong>.
    Please confirm acceptance before the expiry date.
</div>
@endif

{{-- ══ META ══ --}}
<table class="meta-outer">
    <tr>
        <td style="width:50%; vertical-align:top; padding:0; padding-right:20px; border:none;">
            <div class="meta-label">Quotation For</div>
            <div class="meta-value">{{ $quotation->client->name }}</div>
            @if($quotation->client->phone)
                <div class="company-sub">{{ $quotation->client->phone }}</div>
            @endif
            @if($quotation->client->email)
                <div class="company-sub">{{ $quotation->client->email }}</div>
            @endif
            @if($quotation->client->address)
                <div class="company-sub">{{ $quotation->client->address }}</div>
            @endif
        </td>
        <td style="width:50%; vertical-align:top; padding:0; border:none;">
            <table class="meta-info-table" style="border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                <tr>
                    <td class="meta-label" style="width:45%;">Quotation #</td>
                    <td class="tright" style="font-weight:bold;">{{ $quotation->quotation_number }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Issue Date</td>
                    <td class="tright">{{ $quotation->issue_date->format('d M Y') }}</td>
                </tr>
                @if($quotation->expiry_date)
                <tr>
                    <td class="meta-label">Valid Until</td>
                    <td class="tright"
                        style="{{ $quotation->expiry_date->isPast() ? 'color:#dc2626;font-weight:bold;' : '' }}">
                        {{ $quotation->expiry_date->format('d M Y') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="meta-label">Status</td>
                    <td class="tright">
                        <span class="status-badge status-{{ $quotation->status }}">
                            {{ strtoupper($quotation->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ══ ITEMS TABLE ══ --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width:40%;">Description</th>
            <th style="width:18%;">Type</th>
            <th class="tcenter" style="width:10%;">Qty</th>
            <th class="tright"  style="width:16%;">Unit Price</th>
            <th class="tright"  style="width:16%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $item)
        @php
            $typeName  = optional($item->itemType)->name  ?? 'Material';
            $typeColor = optional($item->itemType)->color ?? '#2563eb';
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
<table class="totals-table" style="margin-top: 4px;">
    <tr>
        <td style="width:55%; border:none; padding:0 16px 0 0; vertical-align:top;">

        </td>
        <td style="width:45%; border:none; padding:0; vertical-align:top;">
            <table style="width:100%; border-collapse:collapse;">

                @if($multiType)
                <tr>
                    <td colspan="2" style="border:none; padding:6px 8px 2px; font-size:10px; text-transform:uppercase; color:#999; letter-spacing:0.5px;">
                        Cost Breakdown
                    </td>
                </tr>
                @endif

                @foreach($typeBreakdown as $row)
                @if($row['total'] > 0)
                <tr>
                    <td style="border:none; padding:4px 8px; font-size:11px; color:#555; border-top:1px solid #f0f0f0;">
                        <span class="swatch" style="background: {{ $row['color'] }};"></span>
                        {{ $row['name'] }}
                    </td>
                    <td style="border:none; padding:4px 8px; font-size:11px; text-align:right; border-top:1px solid #f0f0f0;">
                        Ksh {{ number_format($row['total'], 2) }}
                    </td>
                </tr>
                @endif
                @endforeach

                {{-- Discount --}}
                @if($quotation->discount_amount > 0)
                <tr>
                    <td style="border:none; padding:4px 8px; font-size:11px; color:#16a34a; border-top:1px solid #f0f0f0;">
                        Discount
                        @if($quotation->discount_percentage > 0)
                            <span style="font-size:9px; color:#999;">({{ number_format($quotation->discount_percentage, 1) }}%)</span>
                        @endif
                    </td>
                    <td style="border:none; padding:4px 8px; font-size:11px; color:#16a34a; text-align:right; border-top:1px solid #f0f0f0;">
                        - Ksh {{ number_format($quotation->discount_amount, 2) }}
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="border:none; padding:10px 8px 6px; font-size:15px; font-weight:bold; color:#111; border-top:2px solid #333;">
                        Grand Total
                    </td>
                    <td style="border:none; padding:10px 8px 6px; font-size:15px; font-weight:bold; color:#2563eb; text-align:right; border-top:2px solid #333;">
                        Ksh {{ number_format($quotation->grand_total, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ══ NOTES ══ --}}
@if($quotation->notes)
<div class="notes-box">
    <div class="notes-label">Terms &amp; Notes</div>
    <div style="font-size:11px; color:#555; line-height:1.5;">{{ $quotation->notes }}</div>
</div>
@endif

{{-- ══ SIGNATURE ══ --}}
@if($company->signature)
<div style="margin-top:40px;">
    <img src="{{ public_path('storage/' . $company->signature) }}"
         style="max-height:54px;" alt="Signature">
    <div style="border-top: 1px solid #ccc; width:160px; margin-top:6px;"></div>
    <div style="font-size:10px; color:#999; margin-top:3px;">Authorized Signature</div>
</div>
@endif

{{-- ══ FOOTER ══ --}}
@if($company->footer_message)
<div class="footer">{{ $company->footer_message }}</div>
@endif

</body>
</html>