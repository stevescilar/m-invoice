<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — M-Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; }
        .left-panel {
            background: #0a0a0a;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.5rem;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(22,163,74,0.2), transparent 70%);
            top: -100px; right: -100px;
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(249,115,22,0.1), transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .trial-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(22,163,74,0.15);
            border: 1px solid rgba(22,163,74,0.3);
            color: #22c55e;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        .trial-badge::before {
            content: '';
            width: 6px; height: 6px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }
    </style>
</head>
<body>
<div style="display:grid;grid-template-columns:1fr 1fr;min-height:100vh;">

    <!-- LEFT — Dark panel -->
    <div class="left-panel">
        <div class="grid-bg"></div>

        <!-- Logo -->
        <div style="position:relative;z-index:1;">
            <a href="/" style="text-decoration:none;">
                <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;color:#fff;">M-<span style="background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Invoice</span></span>
            </a>
        </div>

        <!-- Center content -->
        <div style="position:relative;z-index:1;">
            <div class="trial-badge">🎉 3-day free trial — no card needed</div>
            <h2 style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:1rem;">
                Start invoicing<br>
                <span style="background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">professionally today.</span>
            </h2>
            <p style="color:rgba(255,255,255,0.5);font-size:0.95rem;line-height:1.7;margin-bottom:2rem;">
                Create your first invoice in under 3 minutes. Track profits, manage clients, and get paid via M-Pesa.
            </p>

            <!-- Referral bonus notice -->
            @if(isset($ref) && $ref)
            <div style="background:rgba(250,204,21,0.1);border:1px solid rgba(250,204,21,0.3);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
                <p style="color:#facc15;font-size:0.85rem;font-weight:600;margin-bottom:0.25rem;">🎁 Referral Bonus!</p>
                <p style="color:rgba(255,255,255,0.6);font-size:0.8rem;">You've been referred! You get <strong style="color:#fff;">4 days free</strong> instead of 3.</p>
            </div>
            @endif

            <!-- QR Code -->
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:1.25rem;display:flex;align-items:center;gap:1rem;">
                <div style="background:#fff;padding:0.75rem;border-radius:10px;flex-shrink:0;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&color=16a34a&data={{ urlencode(url('/register')) }}"
                        alt="QR" style="width:80px;height:80px;display:block;">
                </div>
                <div>
                    <p style="color:#fff;font-size:0.85rem;font-weight:600;margin-bottom:0.25rem;">Scan to try M-Invoice</p>
                    <p style="color:rgba(255,255,255,0.4);font-size:0.75rem;line-height:1.5;">Share this QR code with other business owners. When they sign up you earn +1 free day.</p>
                </div>
            </div>
        </div>

        <!-- Bottom -->
        <div style="position:relative;z-index:1;">
            <p style="color:rgba(255,255,255,0.2);font-size:0.75rem;">© {{ date('Y') }} M-Invoice · Built for Kenya 🇰🇪</p>
        </div>
    </div>

    <!-- RIGHT — Light panel -->
    <div style="background:#fff;display:flex;flex-direction:column;justify-content:center;padding:3rem;position:relative;overflow-y:auto;">

        <!-- Home button -->
        <a href="/" style="position:absolute;top:1.5rem;left:1.5rem;display:flex;align-items:center;gap:0.4rem;color:#6b7280;text-decoration:none;font-size:0.85rem;"
            onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Back to home
        </a>

        <div style="max-width:400px;margin:0 auto;width:100%;">
            <h1 style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#111;margin-bottom:0.5rem;">Create account</h1>
            <p style="color:#6b7280;font-size:0.9rem;margin-bottom:1.5rem;">
                Start your {{ isset($ref) && $ref ? '4-day' : '3-day' }} free trial today
            </p>

            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:0.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.875rem;">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <!-- Google -->
            <a href="{{ route('auth.google') }}{{ isset($ref) && $ref ? '?ref='.$ref : '' }}"
                style="display:flex;align-items:center;justify-content:center;gap:0.75rem;width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.75rem;font-size:0.9rem;font-weight:500;color:#374151;text-decoration:none;transition:all 0.2s;margin-bottom:1.25rem;background:#fff;"
                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>

            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
                <span style="color:#9ca3af;font-size:0.8rem;">or register with email</span>
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            </div>

            <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:0.9rem;">
                @csrf
                @if(isset($ref) && $ref)
                <input type="hidden" name="ref" value="{{ $ref }}">
                @endif

                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Password</label>
                    <input type="password" name="password" required minlength="8"
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <button type="submit"
                    style="width:100%;background:#16a34a;color:#fff;border:none;border-radius:10px;padding:0.85rem;font-size:0.95rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:background 0.2s;margin-top:0.25rem;"
                    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    Create Account — Start Free Trial
                </button>
            </form>

            <p style="text-align:center;font-size:0.8rem;color:#9ca3af;margin-top:1rem;">
                By signing up you agree to our terms of service.
            </p>

            <p style="text-align:center;font-size:0.85rem;color:#6b7280;margin-top:0.75rem;">
                Already have an account?
                <a href="{{ route('login') }}" style="color:#16a34a;text-decoration:none;font-weight:500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Sign in</a>
            </p>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    body > div { grid-template-columns: 1fr !important; }
    .left-panel { display: none !important; }
}
</style>

</body>
</html>