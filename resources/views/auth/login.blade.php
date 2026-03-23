<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Invoxa</title>
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
            top: -100px; left: -100px;
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(250,204,21,0.1), transparent 70%);
            bottom: -100px; right: -100px;
            pointer-events: none;
        }

        /* Animated grid */
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .stat-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 1.25rem;
            backdrop-filter: blur(10px);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255,255,255,0.6);
            font-size: 0.875rem;
            padding: 0.4rem 0;
        }

        .feature-dot {
            width: 20px; height: 20px;
            background: rgba(22,163,74,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #22c55e;
            font-size: 0.7rem;
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
            <a href="/" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;">
                <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.4rem;color:#fff;">M-<span style="background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Invoice</span></span>
            </a>
        </div>

        <!-- Center content -->
        <div style="position:relative;z-index:1;">
            <h2 style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:1rem;">
                Your business,<br>
                <span style="background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">fully in control.</span>
            </h2>
            <p style="color:rgba(255,255,255,0.5);font-size:0.95rem;line-height:1.7;margin-bottom:2rem;">
                Join hundreds of Kenyan businesses managing invoices, tracking profits, and getting paid faster with Invoxa.
            </p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
                <div class="stat-card">
                    <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">500+</div>
                    <div style="color:rgba(255,255,255,0.4);font-size:0.8rem;margin-top:0.25rem;">Active businesses</div>
                </div>
                <div class="stat-card">
                    <div style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;background:linear-gradient(135deg,#22c55e,#facc15);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Ksh 10M+</div>
                    <div style="color:rgba(255,255,255,0.4);font-size:0.8rem;margin-top:0.25rem;">Invoiced this year</div>
                </div>
            </div>

            <div>
                <div class="feature-item"><div class="feature-dot">✓</div> Real-time profit tracking per item</div>
                <div class="feature-item"><div class="feature-dot">✓</div> M-Pesa payment details on invoices</div>
                <div class="feature-item"><div class="feature-dot">✓</div> Automated payment reminders</div>
                <div class="feature-item"><div class="feature-dot">✓</div> Convert quotes to invoices in 1 click</div>
            </div>
        </div>

        <!-- Bottom -->
        <div style="position:relative;z-index:1;">
            <p style="color:rgba(255,255,255,0.2);font-size:0.75rem;">© {{ date('Y') }} Invoxa · Built for Kenya 🇰🇪</p>
        </div>
    </div>

    <!-- RIGHT — Light panel -->
    <div style="background:#fff;display:flex;flex-direction:column;justify-content:center;padding:3rem;position:relative;">

        <!-- Home button -->
        <a href="/" style="position:absolute;top:1.5rem;left:1.5rem;display:flex;align-items:center;gap:0.4rem;color:#6b7280;text-decoration:none;font-size:0.85rem;transition:color 0.2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Back to home
        </a>

        <div style="max-width:400px;margin:0 auto;width:100%;">
            <h1 style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#111;margin-bottom:0.5rem;">Welcome back</h1>
            <p style="color:#6b7280;font-size:0.9rem;margin-bottom:2rem;">Sign in to your Invoxa account</p>

            @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:0.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.875rem;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:0.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.875rem;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:0.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.875rem;">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            <!-- Google -->
            <a href="{{ route('auth.google') }}"
                style="display:flex;align-items:center;justify-content:center;gap:0.75rem;width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.75rem;font-size:0.9rem;font-weight:500;color:#374151;text-decoration:none;transition:all 0.2s;margin-bottom:1.25rem;background:#fff;"
                onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'"
                onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
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
                <span style="color:#9ca3af;font-size:0.8rem;">or</span>
                <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            </div>

            <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:0.85rem;font-weight:500;color:#374151;margin-bottom:0.4rem;">Password</label>
                    <input type="password" name="password" required
                        style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:0.7rem 1rem;font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:#6b7280;cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:#16a34a;">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" style="font-size:0.85rem;color:#16a34a;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Forgot password?</a>
                </div>

                <button type="submit"
                    style="width:100%;background:#16a34a;color:#fff;border:none;border-radius:10px;padding:0.8rem;font-size:0.95rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:background 0.2s;"
                    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    Sign in
                </button>
            </form>

            <p style="text-align:center;font-size:0.85rem;color:#6b7280;margin-top:1.5rem;">
                Don't have an account?
                <a href="{{ route('register') }}" style="color:#16a34a;text-decoration:none;font-weight:500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Sign up free</a>
            </p>
        </div>
    </div>
</div>

@media (max-width: 768px) {
    /* handled below */
}
<style>
@media (max-width: 768px) {
    body > div { grid-template-columns: 1fr !important; }
    .left-panel { display: none !important; }
}
</style>

</body>
</html>