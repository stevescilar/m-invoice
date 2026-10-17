@php $brandColor = $company->primary_color ?? '#16a34a'; @endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 24px;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid {{ $brandColor }};
            margin-bottom: 16px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: {{ $brandColor }};
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .kpi-box {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 10px 12px;
            border-radius: 6px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .items-table thead tr {
            background-color: {{ $brandColor }};
            color: #fff;
        }
        .items-table th {
            padding: 7px 8px;
            text-align: left;
            font-size: 9.5px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 60%; padding-bottom: 12px;">
            <div class="company-name">{{ $company->name }}</div>
            <div style="font-size: 11px; color: #666;">
                {{ $company->phone }} {{ $company->email ? '· ' . $company->email : '' }}
                {{ $company->kra_pin ? '· KRA PIN: ' . $company->kra_pin : '' }}
            </div>
        </td>
        <td style="width: 40%; text-align: right; padding-bottom: 12px;">
            <div style="font-size: 15px; font-weight: bold; color: #111;">FINANCIAL, PROFIT &amp; KRA VAT REPORT</div>
            <div style="font-size: 11px; color: #666; margin-top: 3px;">
                Period: {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
            </div>
        </td>
    </tr>
</table>

<table class="kpi-table">
    <tr>
        <td style="width: 25%; padding-right: 6px;">
            <div class="kpi-box">
                <div style="font-size: 9px; text-transform: uppercase; color: #6b7280;">Total Invoiced</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">Ksh {{ number_format($totalBilled, 2) }}</div>
                <div style="font-size: 9.5px; color: #16a34a; margin-top: 2px;">Collected: Ksh {{ number_format($totalCollected, 2) }}</div>
            </div>
        </td>
        <td style="width: 25%; padding: 0 3px;">
            <div class="kpi-box">
                <div style="font-size: 9px; text-transform: uppercase; color: #6b7280;">Buying Cost + Expenses</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">Ksh {{ number_format($totalBuyingCost + $totalExpenses, 2) }}</div>
                <div style="font-size: 9.5px; color: #666; margin-top: 2px;">COGS: Ksh {{ number_format($totalBuyingCost, 2) }} · Exp: {{ number_format($totalExpenses, 2) }}</div>
            </div>
        </td>
        <td style="width: 25%; padding: 0 3px;">
            <div class="kpi-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                <div style="font-size: 9px; text-transform: uppercase; color: #15803d;">Net Profit ({{ $netMargin }}%)</div>
                <div style="font-size: 14px; font-weight: bold; color: #16a34a; margin-top: 3px;">Ksh {{ number_format($netProfit, 2) }}</div>
                <div style="font-size: 9.5px; color: #15803d; margin-top: 2px;">Gross Invoice Profit: Ksh {{ number_format($grossInvoiceProfit, 2) }}</div>
            </div>
        </td>
        <td style="width: 25%; padding-left: 6px;">
            <div class="kpi-box" style="background: #f5f3ff; border-color: #ddd6fe;">
                <div style="font-size: 9px; text-transform: uppercase; color: #6d28d9;">KRA ETR VAT (16%)</div>
                <div style="font-size: 14px; font-weight: bold; color: #6d28d9; margin-top: 3px;">Ksh {{ number_format($vatBilled, 2) }}</div>
                <div style="font-size: 9.5px; color: #6d28d9; margin-top: 2px;">Collected VAT: Ksh {{ number_format($vatCollected, 2) }}</div>
            </div>
        </td>
    </tr>
</table>

<div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #555; margin-bottom: 4px;">Invoices Breakdown ({{ $invoices->count() }})</div>
<table class="items-table">
    <thead>
        <tr>
            <th>Invoice #</th>
            <th>Date</th>
            <th>Client</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: right;">Total (Ksh)</th>
            <th style="text-align: right;">Paid (Ksh)</th>
            <th style="text-align: right;">Balance (Ksh)</th>
            <th style="text-align: right;">Buying Cost</th>
            <th style="text-align: right;">VAT (16%)</th>
            <th style="text-align: right;">Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $inv)
        <tr>
            <td style="font-weight: bold;">{{ $inv->invoice_number }} {{ $inv->etr_enabled ? '(ETR)' : '' }}</td>
            <td>{{ $inv->issue_date->format('d M Y') }}</td>
            <td>{{ $inv->client->name ?? '—' }}</td>
            <td style="text-align: center; text-transform: uppercase; font-weight: bold;">{{ $inv->status }}</td>
            <td style="text-align: right;">{{ number_format($inv->grand_total, 2) }}</td>
            <td style="text-align: right; color: #16a34a;">{{ number_format($inv->totalPaidAmount(), 2) }}</td>
            <td style="text-align: right; color: {{ $inv->balanceDue() > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($inv->balanceDue(), 2) }}</td>
            <td style="text-align: right;">{{ number_format($inv->total_cost, 2) }}</td>
            <td style="text-align: right; color: #6d28d9;">{{ number_format($inv->vat_amount, 2) }}</td>
            <td style="text-align: right; font-weight: bold; color: #16a34a;">{{ number_format($inv->total_profit, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>

