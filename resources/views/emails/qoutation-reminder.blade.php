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
        .highlight strong { color: #111; font-size: 15px; }
        .detail-row { display: flex; justify-content: space-between; font-size: 13px; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-label { color: #888; }
        .detail-val { font-weight: 700; color: #111; }
        .footer { background: #f9f9f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="logo">{{ $quotation->company->name }}</div>
        </div>
        <div class="body">
            <h2>Following up on your quotation</h2>
            <p>Dear {{ $quotation->client->name }},</p>
            <p>
                I hope this message finds you well. I wanted to follow up on the quotation I sent you
                and check if you had a chance to review it. I've attached it again for your convenience.
            </p>

            <div class="highlight">
                <div class="detail-row">
                    <span class="detail-label">Quotation Number</span>
                    <span class="detail-val">{{ $quotation->quotation_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-val">Ksh {{ number_format($quotation->grand_total, 2) }}</span>
                </div>
                <div class="detail-row" style="border:none">
                    <span class="detail-label">Valid Until</span>
                    <span class="detail-val">{{ $quotation->expiry_date ? date('d M Y', strtotime($quotation->expiry_date)) : 'N/A' }}</span>
                </div>
            </div>

            <p>
                Please feel free to reach out if you have any questions or would like to discuss
                the details further. I look forward to hearing from you.
            </p>

            <p>Warm regards,<br><strong>{{ $senderName }}</strong><br>{{ $quotation->company->name }}</p>
            @if($quotation->company->phone)
            <p style="margin-top:4px;color:#888;font-size:13px;">📞 {{ $quotation->company->phone }}</p>
            @endif
        </div>
        <div class="footer">© {{ date('Y') }} {{ $quotation->company->name }} · Powered by Invoxa</div>
    </div>
</body>
</html>