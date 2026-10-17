<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; color: #333; }
        .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #0a0f0d; padding: 28px 32px; }
        .logo { font-size: 20px; font-weight: 900; color: #22c55e; }
        .body { padding: 32px; }
        h2 { font-size: 20px; color: #111; margin-bottom: 8px; }
        p { font-size: 14px; color: #555; line-height: 1.7; margin-bottom: 14px; }
        .highlight { background: #f0fdf4; border-left: 4px solid #16a34a; padding: 14px 18px; border-radius: 0 8px 8px 0; margin: 20px 0; }
        .overdue { background: #fff7f7; border-left-color: #dc2626; }
        .detail-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-label { color: #888; }
        .detail-val { font-weight: 700; color: #111; }
        .footer { background: #f9f9f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="logo">{{ $invoice->company->name }}</div>
        </div>
        <div class="body">
            @if($invoice->status === 'overdue')
            <h2>Overdue Payment Reminder</h2>
            <p>Dear {{ $invoice->client->name }},</p>
            <p>
                I hope you are doing well. I wanted to bring to your attention that the payment for
                the invoice below is now <strong>overdue</strong>. I'd appreciate it if you could
                arrange payment at your earliest convenience.
            </p>
            @else
            <h2>Friendly Payment Reminder</h2>
            <p>Dear {{ $invoice->client->name }},</p>
            <p>
                I hope this message finds you well. This is a friendly reminder that the following
                invoice is due for payment. I've attached a copy for your reference.
            </p>
            @endif

            <div class="highlight {{ $invoice->status === 'overdue' ? 'overdue' : '' }}">
                <div class="detail-row">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-val">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Due</span>
                    <span class="detail-val">Ksh {{ number_format($invoice->balanceDue(), 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Due Date</span>
                    <span class="detail-val" style="{{ $invoice->status === 'overdue' ? 'color:#dc2626' : '' }}">
                        {{ $invoice->due_date ? date('d M Y', strtotime($invoice->due_date)) : 'N/A' }}
                    </span>
                </div>
                <div class="detail-row" style="border:none">
                    <span class="detail-label">Status</span>
                    <span class="detail-val" style="{{ $invoice->status === 'overdue' ? 'color:#dc2626' : 'color:#16a34a' }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <a href="{{ $invoice->public_url }}"
                   style="display: inline-block; background: #16a34a; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 8px; font-size: 13px; font-weight: bold;">
                    View Invoice &amp; Pay Online
                </a>
            </div>

            @if($invoice->company->mpesa_paybill)
            <p style="font-size:13px;color:#555;">
                💚 <strong>Pay via M-Pesa Paybill:</strong> {{ $invoice->company->mpesa_paybill }}
                &nbsp;|&nbsp; Account: {{ $invoice->company->mpesa_account ?? $invoice->invoice_number }}
            </p>
            @elseif($invoice->company->mpesa_till)
            <p style="font-size:13px;color:#555;">
                💚 <strong>Pay via M-Pesa Till:</strong> {{ $invoice->company->mpesa_till }}
            </p>
            @endif

            <p>
                If you have already made payment, please disregard this message.
                Feel free to reach out if you have any questions.
            </p>

            <p>Thank you for your business.<br><strong>{{ $senderName }}</strong><br>{{ $invoice->company->name }}</p>
            @if($invoice->company->phone)
            <p style="margin-top:4px;color:#888;font-size:13px;">📞 {{ $invoice->company->phone }}</p>
            @endif
        </div>
        <div class="footer">© {{ date('Y') }} {{ $invoice->company->name }} · Powered by Invoxa</div>
    </div>
</body>
</html>