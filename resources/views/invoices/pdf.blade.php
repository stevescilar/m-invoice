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

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 20px;
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
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-style: italic;
            color: #999;
            font-size: 11px;
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

        .etr-badge {
            background: #ede9fe;
            color: #7c3aed;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .payment-section {
            margin-top: 30px;
            padding: 15px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
        }

        .payment-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #16a34a;
            margin-bottom: 10px;
        }

        .payment-grid {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .payment-item {
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
        }
    </style>
</head>

<body>


    <!-- Header -->
    <table class="header-table" style="margin-bottom: 0; padding-bottom: 15px;">
        <tr>
            <td style="width: 50%; vertical-align: middle; padding: 0 0 15px 0; border: none;">
                @if ($company->logo)
                    <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height: 60px; max-width: 160px;"
                        alt="Logo">
                @endif
                <div style="margin-top: 6px; font-size: 11px; color: #666;">{{ $company->phone }}</div>
                <div style="font-size: 11px; color: #666;">{{ $company->email }}</div>
                @if ($invoice->etr_enabled && $company->kra_pin)
                    <div style="font-size: 11px; color: #666;">KRA PIN: {{ $company->kra_pin }}</div>
                @endif
            </td>
            <td style="width: 50%; vertical-align: middle; text-align: right; padding: 0 0 15px 0; border: none;">
                <div class="company-name">{{ $company->name }}</div>
                <div style="font-size: 11px; color: #666; margin-top: 4px;">{{ $company->address }}</div>
            </td>
        </tr>
    </table>

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
    <!-- Payment Details -->
    @if ($company->mpesa_paybill || $company->mpesa_till || $company->mpesa_number || $company->bank_name)
        <div class="payment-section">
            <div class="payment-title">💳 Payment Details</div>
            <table style="width: 100%; margin: 0;">
                <tr>
                    @if ($company->mpesa_paybill)
                        <td style="border: none; padding: 4px 12px 4px 0; vertical-align: top; width: 25%;">
                            <div class="payment-label">Paybill</div>
                            <div class="payment-value">{{ $company->mpesa_paybill }}</div>
                            @if ($company->mpesa_account)
                                <div class="payment-label" style="margin-top: 2px;">Account</div>
                                <div class="payment-value">{{ $company->mpesa_account }}</div>
                            @endif
                        </td>
                    @endif
                    @if ($company->mpesa_till)
                        <td style="border: none; padding: 4px 12px 4px 0; vertical-align: top; width: 25%;">
                            <div class="payment-label">Till Number</div>
                            <div class="payment-value">{{ $company->mpesa_till }}</div>
                        </td>
                    @endif
                    @if ($company->mpesa_number)
                        <td style="border: none; padding: 4px 12px 4px 0; vertical-align: top; width: 25%;">
                            <div class="payment-label">M-Pesa Number</div>
                            <div class="payment-value">{{ $company->mpesa_number }}</div>
                        </td>
                    @endif
                    @if ($company->bank_name)
                        <td style="border: none; padding: 4px 0; vertical-align: top; width: 25%;">
                            <div class="payment-label">Bank</div>
                            <div class="payment-value">{{ $company->bank_name }}</div>
                            @if ($company->bank_account)
                                <div class="payment-label" style="margin-top: 2px;">Account</div>
                                <div class="payment-value">{{ $company->bank_account }}</div>
                            @endif
                            @if ($company->bank_branch)
                                <div class="payment-label" style="margin-top: 2px;">Branch</div>
                                <div class="payment-value">{{ $company->bank_branch }}</div>
                            @endif
                        </td>
                    @endif
                </tr>
            </table>
        </div>
    @endif
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
