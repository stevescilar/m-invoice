<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background: #f3f4f6; padding: 24px; color: #1f2937;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="background: {{ $company->primary_color ?? '#16a34a' }}; color: #ffffff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 20px;">Statement of Account</h2>
            <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">{{ $company->name }}</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">Dear <strong>{{ $client->name }}</strong>,</p>
            <p>Please find attached your latest <strong>Statement of Account</strong> from <strong>{{ $company->name }}</strong>.</p>

            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0;">
                <p style="margin: 4px 0; font-size: 14px;"><strong>Total Invoiced:</strong> Ksh {{ number_format($totalBilled, 2) }}</p>
                <p style="margin: 4px 0; font-size: 14px; color: #15803d;"><strong>Total Paid:</strong> Ksh {{ number_format($totalPaid, 2) }}</p>
                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 10px 0;">
                <p style="margin: 4px 0; font-size: 16px;">
                    <strong>Outstanding Balance:</strong>
                    <span style="color: {{ $balanceDue > 0 ? '#dc2626' : '#15803d' }}; font-weight: bold;">
                        Ksh {{ number_format($balanceDue, 2) }}
                    </span>
                </p>
            </div>

            @if($balanceDue > 0 && ($company->mpesa_paybill || $company->mpesa_till || $company->bank_name))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px; margin-bottom: 18px; font-size: 13px;">
                <strong style="color: #15803d;">Payment Details:</strong><br>
                @if($company->mpesa_paybill)
                    M-Pesa Paybill: <strong>{{ $company->mpesa_paybill }}</strong> · Account: <strong>{{ $company->mpesa_account ?? $client->name }}</strong><br>
                @elseif($company->mpesa_till)
                    M-Pesa Till Number: <strong>{{ $company->mpesa_till }}</strong><br>
                @endif
                @if($company->bank_name)
                    Bank: <strong>{{ $company->bank_name }}</strong> · Acc: <strong>{{ $company->bank_account }}</strong> {{ $company->bank_branch ? '(' . $company->bank_branch . ')' : '' }}
                @endif
            </div>
            @endif

            <p style="font-size: 13px; color: #6b7280; margin-top: 24px;">
                Best regards,<br>
                <strong>{{ $company->name }}</strong><br>
                {{ $company->phone }} {{ $company->email ? '· ' . $company->email : '' }}
            </p>
        </div>
    </div>
</body>
</html>
