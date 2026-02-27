<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 20px;
        }

        .company-logo {
            max-height: 60px;
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
}

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 3px;
        }

        .meta-value {
            font-weight: bold;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #16a34a;
            color: white;
        }

        th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        th.right {
            text-align: right;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }

        td.right {
            text-align: right;
        }

        td.center {
            text-align: center;
        }

        .labour-row {
            background-color: #fff7ed;
        }

        .totals {
            max-width: 250px;
            margin-left: auto;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12px;
        }

        .totals-row.grand {
            border-top: 2px solid #333;
            padding-top: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #16a34a;
        }

        .vat-row {
            color: #7c3aed;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-style: italic;
            color: #999;
            font-size: 11px;
        }

        .signature {
            margin-top: 40px;
        }

        .signature img {
            max-height: 50px;
        }

        .etr-badge {
            background: #ede9fe;
            color: #7c3aed;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-left: 8px;
        }

        .kra-pin {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <div>
            @if ($company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" class="company-logo" alt="Logo">
            @endif
            <div style="margin-top: 8px;">
                <div style="font-size: 11px; color: #666;">{{ $company->phone }}</div>
                <div style="font-size: 11px; color: #666;">{{ $company->email }}</div>
                @if ($invoice->etr_enabled && $company->kra_pin)
                    <div class="kra-pin">KRA PIN: {{ $company->kra_pin }}</div>
                @endif
            </div>
        </div>
        <div class="company-info">
            <div class="company-name">{{ $company->name }}</div>
            <div style="font-size: 11px; color: #666; margin-top: 4px;">{{ $company->address }}</div>
        </div>
    </div>

    <!-- Title -->
    <div style="display: flex; align-items: center; margin-bottom: 20px;">
        <span class="invoice-title">INVOICE</span>
        @if ($invoice->etr_enabled)
            <span class="etr-badge">ETR TAX INVOICE</span>
        @endif
    </div>

    <!-- Meta -->
    <!-- Meta -->
    <table style="width: 100%; margin-bottom: 30px; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 0;">
                <div class="meta-label">Invoice For</div>
                <div class="meta-value">{{ $invoice->client->name }}</div>
                <div style="color: #666; font-size: 11px;">{{ $invoice->client->phone }}</div>
                <div style="color: #666; font-size: 11px;">{{ $invoice->client->email }}</div>
                <div style="color: #666; font-size: 11px;">{{ $invoice->client->address }}</div>
            </td>
            <td style="width: 50%; vertical-align: top; padding: 0;">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="padding: 4px 8px; background: #f9fafb; border-bottom: 1px solid #eee;">
                            <span class="meta-label">Invoice #</span>
                        </td>
                        <td
                            style="padding: 4px 8px; background: #f9fafb; border-bottom: 1px solid #eee; text-align: right; font-weight: bold;">
                            {{ $invoice->invoice_number }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #eee;">
                            <span class="meta-label">Issue Date</span>
                        </td>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #eee; text-align: right;">
                            {{ $invoice->issue_date->format('F j, Y') }}
                        </td>
                    </tr>
                    @if ($invoice->due_date)
                        <tr>
                            <td style="padding: 4px 8px; border-bottom: 1px solid #eee;">
                                <span class="meta-label">Due Date</span>
                            </td>
                            <td style="padding: 4px 8px; border-bottom: 1px solid #eee; text-align: right;">
                                {{ $invoice->due_date->format('F j, Y') }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding: 4px 8px;">
                            <span class="meta-label">Status</span>
                        </td>
                        <td
                            style="padding: 4px 8px; text-align: right; font-weight: bold;
                        color: {{ $invoice->status === 'paid' ? '#16a34a' : ($invoice->status === 'overdue' ? '#dc2626' : '#666') }}">
                            {{ strtoupper($invoice->status) }}
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
            @foreach ($invoice->items as $item)
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
        <div class="totals-row">
            <span>Material Cost</span>
            <span>Ksh {{ number_format($invoice->material_cost, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>Labour Cost</span>
            <span>Ksh {{ number_format($invoice->labour_cost, 2) }}</span>
        </div>
        @if ($invoice->etr_enabled)
            <div class="totals-row vat-row">
                <span>VAT (16%)</span>
                <span>Ksh {{ number_format($invoice->vat_amount, 2) }}</span>
            </div>
        @endif
        <div class="totals-row grand">
            <span>Grand Total</span>
            <span>Ksh {{ number_format($invoice->grand_total, 2) }}</span>
        </div>
    </div>

    <!-- Signature -->
    @if ($company->signature)
        <div class="signature">
            <img src="{{ public_path('storage/' . $company->signature) }}" alt="Signature">
            <div style="border-top: 1px solid #ccc; width: 150px; margin-top: 5px;"></div>
            <div style="font-size: 10px; color: #999;">Authorized Signature</div>
        </div>
    @endif

    <!-- Footer -->
    @if ($company->footer_message)
        <div class="footer">{{ $company->footer_message }}</div>
    @endif

</body>

</html>
