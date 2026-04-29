<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrap { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #0a0f0d; padding: 28px 32px; text-align: center; }
        .logo { font-size: 22px; font-weight: 900; color: #22c55e; letter-spacing: -0.5px; }
        .body { padding: 32px; }
        h2 { font-size: 18px; color: #111; margin-bottom: 8px; }
        p { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 20px; }
        .otp-box { background: #f0fdf4; border: 2px dashed #16a34a; border-radius: 10px; padding: 20px; text-align: center; margin: 24px 0; }
        .otp { font-size: 38px; font-weight: 900; letter-spacing: 10px; color: #16a34a; font-family: monospace; }
        .otp-note { font-size: 12px; color: #999; margin-top: 8px; }
        .footer { padding: 20px 32px; background: #f9f9f9; text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="logo">Invoxa</div>
        </div>
        <div class="body">
            <h2>Admin Login Verification</h2>
            <p>Hi {{ $adminName }}, use the code below to complete your login. It expires in <strong>10 minutes</strong>.</p>
            <div class="otp-box">
                <div class="otp">{{ $otp }}</div>
                <div class="otp-note">Do not share this code with anyone</div>
            </div>
            <p style="margin-bottom:0">If you didn't attempt to log in, ignore this email and consider changing your password.</p>
        </div>
        <div class="footer">© {{ date('Y') }} Invoxa · invoice.microsilsystem.co.ke</div>
    </div>
</body>
</html>