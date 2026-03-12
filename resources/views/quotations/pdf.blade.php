<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 0; padding-bottom: 15px; border-bottom: 3px solid #16a34a; }
        .company-name { font-size: 20px; font-weight: bold; color: #16a34a; }
        .title { font-size: 24px; font-weight: bold; color: #333; margin: 20px 0; }
        .badge { background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; margin-left: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background-color: #2563eb; color: white; }
        th { padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        th.right { text-align: right; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        td.right { text-align: right; }
        td.center { text-align: center; }
        .labour-row { background-color: #fff7ed; }
        .totals { max-width: 250px; margin-left: auto; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
        .totals-row.grand { border-top: 2px solid #333; padding-top: 10px; font-weight: bold; font-size: 14px; color: #2563eb; }
        .meta-label { font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 3px; }
        .meta-value { font-weight: bold; font-size: 13px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-style: italic; color: #999; font-size: 11px; }
        .validity-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 15px; margin-bottom: 20px; font-size: 11px; color: #1d4ed8; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-draft { background: #f3f4f6; color: #6b7280; }
        .status-sent { background: #eff6ff; color: #2563eb; }
        .status-approved { background: #f0fdf4; color: #16a34a; }
        .status-rejected { background: #fef2f2; color: #dc2626; }
        .status-converted { background: #f5f3ff; color: #7c3aed; }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(22, 163, 74, 0.06);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            letter-spacing: 10px;
        }
        body > *:not(.watermark) {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

    @php $hasLabour = $quotation->items->where('is_labour', true)->count() > 0; @endphp

    <div class="watermark">{{ strtoupper($company->name) }}</div>

<!-- Header -->
<table class="header-table">
    <tr>
        <td style="width: 50%; vertical-align: middle; padding: 0 0 15px 0; border: none;">
            @if($company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height: 60px; max-width: 160px;" alt="Logo">
            @endif
            <div style="margin-top: 6px; font-size: 11px; color: #666;">{{ $company->phone }}</div>
            <div style="font-size: 11px; color: #666;">{{ $company->email }}</div>
        </td>
        <td style="width: 50%; vertical-align: middle; text-align: right; padding: 0 0 15px 0; border: none;">
            <div class="company-name">{{ $company->name }}</div>
            <div style="font-size: 11px; color: #666; margin-top: 4px;">{{ $company->address }}</div>
        </td>
    </tr>
</table>

<!-- Title -->
<div style="display: flex; align-items: center; margin: 20px 0;">
    <span class="title">QUOTATION</span>
    <span class="badge">{{ strtoupper($quotation->status) }}</span>
</div>

<!-- Validity Notice -->
@if($quotation->expiry_date)
<div class="validity-box">
    This quotation is valid until {{ $quotation->expiry_date->format('F j, Y') }}.
    Please confirm acceptance before the expiry date.
</div>
@endif

<!-- Meta -->
<table style="width: 100%; margin-bottom: 30px; border: none;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding: 0;">
            <div class="meta-label">Quotation For</div>
            <div class="meta-value">{{ $quotation->client->name }}</div>
            <div style="color: #666; font-size: 11px;">{{ $quotation->client->phone }}</div>
            <div style="color: #666; font-size: 11px;">{{ $quotation->client->email }}</div>
            <div style="color: #666; font-size: 11px;">{{ $quotation->client->address }}</div>
        </td>
        <td style="width: 50%; vertical-align: top; padding: 0;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="padding: 4px 8px; background: #f9fafb; border-bottom: 1px solid #eee;">
                        <span class="meta-label">Quotation #</span>
                    </td>
                    <td style="padding: 4px 8px; background: #f9fafb; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
                        {{ $quotation->quotation_number }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 8px; border-bottom: 1px solid #eee;">
                        <span class="meta-label">Issue Date</span>
                    </td>
                    <td style="padding: 4px 8px; border-bottom: 1px solid #eee; text-align: right;">
                        {{ $quotation->issue_date->format('F j, Y') }}
                    </td>
                </tr>
                @if($quotation->expiry_date)
                <tr>
                    <td style="padding: 4px 8px; border-bottom: 1px solid #eee;">
                        <span class="meta-label">Valid Until</span>
                    </td>
                    <td style="padding: 4px 8px; border-bottom: 1px solid #eee; text-align: right;">
                        {{ $quotation->expiry_date->format('F j, Y') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 4px 8px;">
                        <span class="meta-label">Status</span>
                    </td>
                    <td style="padding: 4px 8px; text-align: right;">
                        <span class="status-badge status-{{ $quotation->status }}">
                            {{ strtoupper($quotation->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Items Table -->
<table>
    <thead>
        <tr>
            <th>Description</th>
            <th class="right">Qty</th>
            <th class="right">Unit Price</th>
            <th class="right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotation->items as $item)
        <tr class="{{ $item->is_labour ? 'labour-row' : '' }}">
            <td>{{ $item->description }}{{ $item->is_labour ? ' (Labour)' : '' }}</td>
            <td class="center">{{ $item->quantity }}</td>
            <td class="right">Ksh {{ number_format($item->unit_price, 2) }}</td>
            <td class="right">Ksh {{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Totals -->
<div class="totals">
    @if($hasLabour)
    <div class="totals-row">
        <span>Material Cost</span>
        <span>Ksh {{ number_format($quotation->material_cost, 2) }}</span>
    </div>
    <div class="totals-row">
        <span>Labour Cost</span>
        <span>Ksh {{ number_format($quotation->labour_cost, 2) }}</span>
    </div>
    @endif
    <div class="totals-row grand">
        <span>Grand Total</span>
        <span>Ksh {{ number_format($quotation->grand_total, 2) }}</span>
    </div>
</div>

<!-- Notes -->
@if($quotation->notes)
<div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 6px; border: 1px solid #eee;">
    <div style="font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 5px;">Terms & Notes</div>
    <div style="font-size: 11px; color: #555;">{{ $quotation->notes }}</div>
</div>
@endif

<!-- Signature -->
@if($company->signature)
<div style="margin-top: 40px;">
    <img src="{{ public_path('storage/' . $company->signature) }}" style="max-height: 50px;" alt="Signature">
    <div style="border-top: 1px solid #ccc; width: 150px; margin-top: 5px;"></div>
    <div style="font-size: 10px; color: #999;">Authorized Signature</div>
</div>
@endif

<!-- Footer -->
@if($company->footer_message)
<div class="footer">{{ $company->footer_message }}</div>
@endif

</body>
</html>