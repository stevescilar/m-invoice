<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Invoice — Smart Invoicing for Kenyan Businesses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #16a34a;
            --green-light: #22c55e;
            --yellow: #facc15;
            --orange: #f97316;
            --dark: #0a0a0a;
            --card: #111111;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: #fff;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-display { font-family: 'Syne', sans-serif; }

        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* Nav */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            background: rgba(10,10,10,0.8);
        }

        .nav-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-logo span {
            background: linear-gradient(135deg, var(--green-light), var(--yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: #fff; }

        .nav-cta {
            background: var(--green);
            color: #fff;
            padding: 0.6rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background 0.2s, transform 0.2s;
        }

        .nav-cta:hover {
            background: var(--green-light);
            transform: translateY(-1px);
        }

        /* Hero */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            text-align: center;
        }

        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            pointer-events: none;
        }

        .blob-green {
            width: 600px; height: 600px;
            background: var(--green);
            top: -100px; left: -100px;
        }

        .blob-yellow {
            width: 400px; height: 400px;
            background: var(--yellow);
            bottom: 0; right: -100px;
        }

        .blob-orange {
            width: 300px; height: 300px;
            background: var(--orange);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(22, 163, 74, 0.15);
            border: 1px solid rgba(22, 163, 74, 0.3);
            color: var(--green-light);
            padding: 0.4rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 2rem;
            animation: fadeUp 0.6s ease both;
        }

        .hero-badge::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--green-light);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, var(--green-light) 0%, var(--yellow) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.6);
            max-width: 560px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-primary {
            background: var(--green);
            color: #fff;
            padding: 0.9rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: var(--green-light);
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(22, 163, 74, 0.3);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.05);
            color: #fff;
            padding: 0.9rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .hero-note {
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.35);
            font-size: 0.85rem;
            animation: fadeUp 0.6s 0.4s ease both;
        }

        /* Stats bar */
        .stats-bar {
            background: rgba(255,255,255,0.03);
            border-top: 1px solid rgba(255,255,255,0.06);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }

        .stat-item .stat-number {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--green-light), var(--yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-item .stat-label {
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Section styles */
        section {
            padding: 6rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-tag {
            display: inline-block;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.2);
            color: var(--green-light);
            padding: 0.3rem 0.8rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .section-sub {
            color: rgba(255,255,255,0.5);
            font-size: 1.1rem;
            max-width: 560px;
            line-height: 1.7;
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(22,163,74,0.05), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            border-color: rgba(22, 163, 74, 0.3);
            transform: translateY(-4px);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Big feature */
        .big-feature {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-top: 4rem;
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 24px;
            padding: 3rem;
            overflow: hidden;
            position: relative;
        }

        .big-feature::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(22,163,74,0.1), transparent 70%);
            right: -100px; top: -100px;
            pointer-events: none;
        }

        .big-feature-mock {
            background: #1a1a1a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1.5rem;
            font-size: 0.85rem;
        }

        .mock-row {
            display: grid;
            grid-template-columns: 1fr 80px 80px 110px;
            gap: 0.5rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.7);
            text-align: right;
        }

        .mock-row span:first-child { text-align: left; }

        .mock-profit {
            color: var(--green-light);
            font-weight: 600;
        }

        .mock-loss { color: #ef4444; font-weight: 600; }

        .mock-header {
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.5rem;
            display: grid;
            grid-template-columns: 1fr 80px 80px 110px;
            gap: 0.5rem;
            text-align: right;
        }
        .mock-header span:first-child { text-align: left; }

        .profit-summary {
            margin-top: 1rem;
            background: rgba(22,163,74,0.1);
            border: 1px solid rgba(22,163,74,0.2);
            border-radius: 10px;
            padding: 1rem;
        }

        .profit-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            padding: 0.25rem 0;
        }

        /* Pricing */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 4rem;
        }

        .pricing-card {
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            transition: all 0.3s;
        }

        .pricing-card.featured {
            border-color: var(--green);
            background: rgba(22, 163, 74, 0.05);
        }

        .pricing-card.featured::before {
            content: 'BEST VALUE';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--green);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            padding: 0.25rem 1rem;
            border-radius: 100px;
        }

        .pricing-card:hover {
            transform: translateY(-4px);
            border-color: rgba(22, 163, 74, 0.4);
        }

        .pricing-plan {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .pricing-price {
            font-family: 'Syne', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .pricing-price span {
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255,255,255,0.4);
        }

        .pricing-period {
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .pricing-features {
            list-style: none;
            space-y: 0.75rem;
            margin-bottom: 2rem;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            padding: 0.4rem 0;
        }

        .pricing-features li::before {
            content: '✓';
            color: var(--green-light);
            font-weight: 700;
            flex-shrink: 0;
        }

        .pricing-btn {
            display: block;
            text-align: center;
            padding: 0.85rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .pricing-btn-outline {
            border: 1px solid rgba(255,255,255,0.15);
            color: #fff;
        }

        .pricing-btn-outline:hover {
            background: rgba(255,255,255,0.05);
        }

        .pricing-btn-solid {
            background: var(--green);
            color: #fff;
        }

        .pricing-btn-solid:hover {
            background: var(--green-light);
            box-shadow: 0 10px 30px rgba(22,163,74,0.3);
        }

        /* Testimonials */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 4rem;
        }

        .testimonial-card {
            background: var(--card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            border-color: rgba(22,163,74,0.2);
            transform: translateY(-2px);
        }

        .testimonial-stars {
            color: var(--yellow);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            letter-spacing: 2px;
        }

        .testimonial-text {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .testimonial-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .testimonial-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .testimonial-role {
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
        }

        /* FAQ */
        .faq-list {
            margin-top: 3rem;
            max-width: 720px;
        }

        .faq-item {
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            text-align: left;
            padding: 1.5rem 0;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .faq-question:hover { color: var(--green-light); }

        .faq-icon {
            width: 24px; height: 24px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .faq-answer {
            color: rgba(255,255,255,0.5);
            font-size: 0.95rem;
            line-height: 1.7;
            padding-bottom: 1.5rem;
            display: none;
        }

        .faq-item.open .faq-answer { display: block; }
        .faq-item.open .faq-icon {
            background: var(--green);
            border-color: var(--green);
            transform: rotate(45deg);
        }

        /* CTA Banner */
        .cta-section {
            padding: 6rem 2rem;
        }

        .cta-inner {
            max-width: 1200px;
            margin: 0 auto;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 50%, #166534 100%);
            border-radius: 28px;
            padding: 5rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-inner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .cta-inner h2 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
        }

        .cta-inner p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .cta-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
        }

        .btn-white {
            background: #fff;
            color: var(--green);
            padding: 0.9rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .btn-ghost {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.9rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.2s;
        }

        .btn-ghost:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 3rem 2rem;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .footer-links a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: #fff; }

        .footer-copy {
            color: rgba(255,255,255,0.3);
            font-size: 0.85rem;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .features-grid, .pricing-grid, .testimonials-grid {
                grid-template-columns: 1fr;
            }
            .stats-bar { grid-template-columns: repeat(2, 1fr); }
            .big-feature { grid-template-columns: 1fr; gap: 2rem; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="nav-logo">
        M-<span>Invoice</span>
    </a>
    <ul class="nav-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#pricing">Pricing</a></li>
        <li><a href="#testimonials">Testimonials</a></li>
        <li><a href="#faq">FAQ</a></li>
    </ul>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <a href="/login" style="color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.9rem;">Sign in</a>
        <a href="/register" class="nav-cta">Start Free Trial →</a>
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div class="hero-blob blob-green"></div>
    <div class="hero-blob blob-yellow"></div>
    <div class="hero-blob blob-orange"></div>

    <div style="position:relative;z-index:1;">
        <div class="hero-badge">🇰🇪 Built for Kenyan Businesses</div>
        <h1>
            Invoice Smarter.<br>
            <span class="highlight">Get Paid Faster.</span>
        </h1>
        <p>
            Create professional invoices and quotations, track profits in real-time,
            and get paid via M-Pesa — all in one place.
        </p>
        <div class="hero-actions">
            <a href="/register" class="btn-primary">
                Start Free Trial
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="/login" class="btn-secondary">Sign in to your account</a>
        </div>
        <p class="hero-note">3-day free trial · No credit card required · Cancel anytime</p>
    </div>
</div>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-number">500+</div>
        <div class="stat-label">Businesses onboarded</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">Ksh 10M+</div>
        <div class="stat-label">Invoiced through M-Invoice</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">98%</div>
        <div class="stat-label">Customer satisfaction</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">3 min</div>
        <div class="stat-label">Average invoice creation time</div>
    </div>
</div>

<!-- FEATURES -->
<section id="features">
    <div class="fade-up">
        <div class="section-tag">Features</div>
        <h2 class="section-title">Everything you need to<br><span style="color:var(--green-light)">run your business</span></h2>
        <p class="section-sub">From quotations to payment collection — M-Invoice handles it all so you can focus on the work.</p>
    </div>

    <div class="features-grid fade-up">
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(22,163,74,0.15);">📄</div>
            <h3>Professional Invoices</h3>
            <p>Create beautiful PDF invoices in seconds. Add your logo, payment details, and ETR tax information.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(250,204,21,0.15);">💰</div>
            <h3>Profit Tracking</h3>
            <p>Enter buying and selling prices per item. See real-time profit margins so you never underprice again.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(249,115,22,0.15);">📱</div>
            <h3>M-Pesa Payments</h3>
            <p>Accept payments via M-Pesa Paybill, Till Number, or direct M-Pesa. Payment details auto-print on invoices.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(139,92,246,0.15);">📊</div>
            <h3>Business Dashboard</h3>
            <p>Weekly, monthly, quarterly, and yearly revenue and profit analytics. Know your numbers at a glance.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(236,72,153,0.15);">⏰</div>
            <h3>Automated Reminders</h3>
            <p>Automatically email clients before due date, on due date, and after. Never chase payments manually again.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background:rgba(20,184,166,0.15);">🔄</div>
            <h3>Quotations → Invoices</h3>
            <p>Convert approved quotations to invoices in one click. All line items and profit data carry over automatically.</p>
        </div>
    </div>

    <!-- Big feature — profit calculator -->
    <div class="big-feature fade-up">
        <div>
            <div class="section-tag">Profit Calculator</div>
            <h3 style="font-size:2rem;font-weight:800;margin-bottom:1rem;line-height:1.2;">
                Know your profit<br>before you quote
            </h3>
            <p style="color:rgba(255,255,255,0.5);line-height:1.7;margin-bottom:1.5rem;">
                Enter buying price and selling price per line item. M-Invoice calculates your profit margin in real-time as you type — so you always quote confidently.
            </p>
            <ul style="list-style:none;space-y:0.5rem;">
                <li style="color:rgba(255,255,255,0.7);font-size:0.9rem;padding:0.3rem 0;display:flex;gap:0.5rem;align-items:center;">
                    <span style="color:var(--green-light)">✓</span> Per-item profit and margin %
                </li>
                <li style="color:rgba(255,255,255,0.7);font-size:0.9rem;padding:0.3rem 0;display:flex;gap:0.5rem;align-items:center;">
                    <span style="color:var(--green-light)">✓</span> Overall margin summary
                </li>
                <li style="color:rgba(255,255,255,0.7);font-size:0.9rem;padding:0.3rem 0;display:flex;gap:0.5rem;align-items:center;">
                    <span style="color:var(--green-light)">✓</span> Private — not shown to clients
                </li>
                <li style="color:rgba(255,255,255,0.7);font-size:0.9rem;padding:0.3rem 0;display:flex;gap:0.5rem;align-items:center;">
                    <span style="color:var(--green-light)">✓</span> Saved for profit analytics on dashboard
                </li>
            </ul>
        </div>
        <div class="big-feature-mock">
            <div class="mock-header">
                <span>Item</span>
                <span>Buy</span>
                <span>Sell</span>
                <span>Profit</span>
            </div>
            <div class="mock-row">
                <span>Dahua Camera</span>
                <span>2,000</span>
                <span>2,800</span>
                <span class="mock-profit">+800 (29%)</span>
            </div>
            <div class="mock-row">
                <span>30m Cable</span>
                <span>350</span>
                <span>600</span>
                <span class="mock-profit">+250 (42%)</span>
            </div>
            <div class="mock-row">
                <span>Installation</span>
                <span>0</span>
                <span>1,500</span>
                <span class="mock-profit">+1,500 (100%)</span>
            </div>
            
            <div class="profit-summary">
                <div class="profit-summary-row">
                    <span style="color:rgba(255,255,255,0.5)">Grand Total</span>
                    <span style="font-weight:700;">Ksh 4,900</span>
                </div>
                <div class="profit-summary-row">
                    <span style="color:rgba(255,255,255,0.5)">Total Cost</span>
                    <span style="color:#ef4444;font-weight:600;">Ksh 2,350</span>
                </div>
                <div class="profit-summary-row">
                    <span style="color:rgba(255,255,255,0.5)">Total Profit</span>
                    <span style="color:var(--green-light);font-weight:700;">Ksh 2,550</span>
                </div>
                <div class="profit-summary-row">
                    <span style="color:rgba(255,255,255,0.5)">Margin</span>
                    <span style="color:var(--green-light);font-weight:700;">52%</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section id="pricing" style="background:rgba(255,255,255,0.01);border-top:1px solid rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.04);max-width:100%;padding:6rem 2rem;">
<div style="max-width:1200px;margin:0 auto;">
    <div class="fade-up" style="text-align:center;">
        <div class="section-tag">Pricing</div>
        <h2 class="section-title">Simple, transparent pricing</h2>
        <p class="section-sub" style="margin:0 auto;">Start with a 3-day free trial. No credit card required.</p>
    </div>

    <div class="pricing-grid fade-up">
        <!-- Per Invoice -->
        <div class="pricing-card">
            <div class="pricing-plan">Pay As You Go</div>
            <div class="pricing-price">Ksh 100 <span>/ download</span></div>
            <div class="pricing-period">Pay only when you download a PDF</div>
            <ul class="pricing-features">
                <li>Unlimited invoice creation</li>
                <li>Pay per PDF download</li>
                <li>M-Pesa payment</li>
                <li>Email to clients</li>
                <li>All core features</li>
            </ul>
            <a href="/register" class="pricing-btn pricing-btn-outline">Get Started Free</a>
        </div>

        <!-- Monthly -->
        <div class="pricing-card featured">
            <div class="pricing-plan">Monthly</div>
            <div class="pricing-price" style="background:linear-gradient(135deg,var(--green-light),var(--yellow));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Ksh 700 <span style="-webkit-text-fill-color:rgba(255,255,255,0.4)">/ month</span></div>
            <div class="pricing-period">Billed monthly · Cancel anytime</div>
            <ul class="pricing-features">
                <li>Unlimited PDF downloads</li>
                <li>All features included</li>
                <li>Automated reminders</li>
                <li>Profit analytics</li>
                <li>Expense tracking</li>
                <li>Priority support</li>
            </ul>
            <a href="/register" class="pricing-btn pricing-btn-solid">Start Free Trial</a>
        </div>

        <!-- Yearly -->
        <div class="pricing-card">
            <div class="pricing-plan">Yearly</div>
            <div class="pricing-price">Ksh 5,000 <span>/ year</span></div>
            <div class="pricing-period">Save Ksh 3,400 vs monthly</div>
            <ul class="pricing-features">
                <li>Everything in Monthly</li>
                <li>2 months free</li>
                <li>Recurring invoices</li>
                <li>Advanced analytics</li>
                <li>Priority support</li>
                <li>Early access to features</li>
            </ul>
            <a href="/register" class="pricing-btn pricing-btn-outline">Get Started Free</a>
        </div>
    </div>
</div>
</section>

<!-- TESTIMONIALS -->
<section id="testimonials">
    <div class="fade-up">
        <div class="section-tag">Testimonials</div>
        <h2 class="section-title">Trusted by Kenyan<br><span style="color:var(--green-light)">business owners</span></h2>
    </div>

    <div class="testimonials-grid fade-up">
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"I used to spend hours making invoices in Word. Now I do it in 3 minutes and send directly to my client's email with M-Pesa details. Game changer."</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar" style="background:rgba(22,163,74,0.2);color:var(--green-light);">JM</div>
                <div>
                    <div class="testimonial-name">James Mwangi</div>
                    <div class="testimonial-role">Electrical Contractor, Nairobi</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"The profit calculator is incredible. I realized I was undercharging for labour. After using M-Invoice I increased my margins by 20% without losing clients."</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar" style="background:rgba(250,204,21,0.2);color:var(--yellow);">SW</div>
                <div>
                    <div class="testimonial-name">Sarah Wanjiku</div>
                    <div class="testimonial-role">CCTV Installer, Mombasa</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"The automated payment reminders alone are worth it. My clients now pay on time because the system follows up automatically. I don't have to chase anyone."</p>
            <div class="testimonial-author">
                <div class="testimonial-avatar" style="background:rgba(249,115,22,0.2);color:var(--orange);">PO</div>
                <div>
                    <div class="testimonial-name">Peter Otieno</div>
                    <div class="testimonial-role">IT Services, Kisumu</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section id="faq" style="max-width:1200px;margin:0 auto;padding:6rem 2rem;">
    <div class="fade-up" style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:start;">
        <div>
            <div class="section-tag">FAQ</div>
            <h2 class="section-title">Questions?<br>We got answers.</h2>
            <p class="section-sub">Can't find what you're looking for? Email us at support@minvoice.co.ke</p>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    What happens after the 3-day trial?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">After your trial ends, you can still create invoices and quotations but PDF downloads will be locked. Choose any plan to unlock downloads. No data is lost.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Do clients see my buying prices?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">Never. Buying prices and profit margins are completely private to you. Client PDFs only show descriptions, quantities, and selling prices.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I accept M-Pesa payments?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">Yes! Add your M-Pesa Paybill, Till Number, or M-Pesa number in settings. It will automatically appear on all your invoices and emails sent to clients.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I add team members?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">Yes. You can invite staff members to your company account. You control their access level — they can create invoices but won't see profit data unless you allow it.</div>
            </div>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Is my data safe?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">Your data is fully isolated per company — no other business can access your invoices or client data. We use industry-standard encryption and secure backups.</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA BANNER -->
<div class="cta-section">
    <div class="cta-inner fade-up">
        <h2>Ready to get paid faster?</h2>
        <p>Join hundreds of Kenyan businesses already using M-Invoice. Start your free trial today.</p>
        <div class="cta-actions">
            <a href="/register" class="btn-white">Start Free Trial — It's Free</a>
            <a href="/login" class="btn-ghost">Sign in →</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <a href="/" class="nav-logo" style="font-size:1.1rem;">M-<span>Invoice</span></a>
        <ul class="footer-links">
            <li><a href="#features">Features</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#faq">FAQ</a></li>
            <li><a href="/login">Login</a></li>
            <li><a href="/register">Register</a></li>
        </ul>
        <p class="footer-copy">© {{ date('Y') }} M-Invoice. Built for Kenya 🇰🇪</p>
    </div>
</footer>

<script>
// Scroll animations
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('visible'), i * 100);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

// FAQ toggle
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href'))?.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>

</body>
</html>