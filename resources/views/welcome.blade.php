<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoxa — Smart Invoicing for Kenyan Businesses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800;900&family=Satoshi:wght@300;400;500;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #16a34a;
            --green-light: #22c55e;
            --dark: #0a0f0d;
            --card: #111814;
            --border: rgba(255,255,255,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Satoshi', sans-serif; background: var(--dark); color: #fff; overflow-x: hidden; }

        /* Noise */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 9999;
        }

        .font-display { font-family: 'Cabinet Grotesk', sans-serif; }

        /* NAV */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 20px 48px;
            display: flex; align-items: center; justify-content: space-between;
            transition: all 0.3s;
        }
        nav.scrolled {
            background: rgba(10,15,13,0.88);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 48px;
        }
        .logo {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-weight: 900; font-size: 22px; letter-spacing: -0.5px;
            color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px;
        }
        .logo-tag {
            background: var(--green); color: #fff;
            padding: 2px 8px; border-radius: 6px;
            font-size: 11px; font-weight: 800; letter-spacing: 0.5px;
        }
        .nav-links { display: flex; gap: 36px; list-style: none; }
        .nav-links a { color: rgba(255,255,255,0.55); text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover { color: #fff; }
        .nav-actions { display: flex; gap: 10px; align-items: center; }

        /* BUTTONS */
        .btn {
            padding: 11px 22px; border-radius: 10px;
            font-size: 14px; font-weight: 700; text-decoration: none;
            display: inline-flex; align-items: center; gap: 7px;
            transition: all 0.2s; cursor: pointer; border: none;
        }
        .btn-green {
            background: var(--green); color: #fff;
        }
        .btn-green:hover {
            background: var(--green-light);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(22,163,74,0.3);
        }
        .btn-glass {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            color: rgba(255,255,255,0.7);
        }
        .btn-glass:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .btn-lg { padding: 15px 30px; font-size: 16px; border-radius: 12px; }

        /* HERO */
        .hero {
            position: relative; min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center; padding: 140px 24px 80px;
            overflow: hidden;
        }
        .blob {
            position: absolute; border-radius: 50%;
            filter: blur(130px); pointer-events: none;
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.25);
            color: #4ade80; padding: 6px 16px; border-radius: 100px;
            font-size: 13px; font-weight: 600; margin-bottom: 28px;
            animation: fadeUp 0.6s ease both;
        }
        .dot-pulse {
            width: 6px; height: 6px; border-radius: 50%;
            background: #4ade80; animation: pulse 2s infinite;
        }
        .hero-h1 {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: clamp(50px, 8.5vw, 92px);
            font-weight: 900; line-height: 1.0; letter-spacing: -3px;
            max-width: 900px;
            animation: fadeUp 0.6s 0.1s ease both;
        }
        .hero-h1 em {
            font-family: 'Instrument Serif', serif;
            font-style: italic; font-weight: 400;
            color: var(--green-light); letter-spacing: -1px;
        }
        .hero-p {
            font-size: clamp(15px, 2vw, 19px);
            color: rgba(255,255,255,0.5); max-width: 480px;
            line-height: 1.7; margin-top: 20px;
            animation: fadeUp 0.6s 0.2s ease both;
        }
        .hero-btns {
            display: flex; gap: 12px; margin-top: 36px;
            flex-wrap: wrap; justify-content: center;
            animation: fadeUp 0.6s 0.3s ease both;
        }
        .hero-social {
            margin-top: 40px; display: flex; align-items: center;
            gap: 14px; color: rgba(255,255,255,0.35); font-size: 13px;
            animation: fadeUp 0.6s 0.4s ease both;
        }
        .avatars { display: flex; }
        .avatars div {
            width: 28px; height: 28px; border-radius: 50%;
            border: 2px solid var(--dark); font-size: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: #fff; margin-left: -8px;
        }
        .avatars div:first-child { margin-left: 0; }

        /* DASHBOARD PREVIEW */
        .preview-wrap {
            width: 100%; max-width: 1080px; margin: 64px auto 0;
            animation: fadeUp 0.8s 0.5s ease both;
        }
        .preview-shell {
            background: #0e1812;
            border: 1px solid rgba(22,163,74,0.18);
            border-radius: 18px; overflow: hidden;
            box-shadow: 0 40px 120px rgba(0,0,0,0.65), 0 0 80px rgba(22,163,74,0.07);
        }
        .preview-titlebar {
            background: #090d0b; padding: 11px 16px;
            display: flex; align-items: center; gap: 7px;
            border-bottom: 1px solid var(--border);
        }
        .tbar-dot { width: 10px; height: 10px; border-radius: 50%; }
        .tbar-url {
            flex: 1; background: rgba(255,255,255,0.04);
            border-radius: 5px; padding: 3px 12px;
            font-size: 11px; color: rgba(255,255,255,0.25);
            text-align: center; max-width: 280px; margin: 0 auto;
        }
        .preview-body {
            display: grid; grid-template-columns: 190px 1fr;
            height: 320px;
        }
        .p-sidebar {
            background: #080c0a; padding: 16px 12px;
            border-right: 1px solid var(--border); overflow: hidden;
        }
        .p-logo { font-family: 'Cabinet Grotesk', sans-serif; font-weight: 900; font-size: 13px; color: #4ade80; margin-bottom: 18px; }
        .p-nav { padding: 6px 10px; border-radius: 6px; font-size: 11px; color: rgba(255,255,255,0.35); margin-bottom: 2px; display: flex; align-items: center; gap: 7px; }
        .p-nav.on { background: rgba(22,163,74,0.14); color: #4ade80; }
        .p-ndot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
        .p-main { background: #111814; padding: 16px; overflow: hidden; }
        .p-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .p-title { font-size: 12px; font-weight: 700; color: #fff; }
        .p-newbtn { background: var(--green); color: #fff; padding: 4px 10px; border-radius: 5px; font-size: 9px; font-weight: 800; }
        .p-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 7px; margin-bottom: 12px; }
        .p-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 7px; padding: 9px; }
        .p-card-l { font-size: 8px; color: rgba(255,255,255,0.35); margin-bottom: 3px; }
        .p-card-v { font-size: 14px; font-weight: 900; font-family: 'Cabinet Grotesk', sans-serif; }
        .p-row { display: grid; grid-template-columns: 1fr 75px 55px; gap: 6px; padding: 6px 6px; border-radius: 4px; align-items: center; }
        .p-row.hdr { font-size: 8px; color: rgba(255,255,255,0.25); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .p-row.r { font-size: 9px; color: rgba(255,255,255,0.6); background: rgba(255,255,255,0.02); margin-bottom: 2px; }
        .badge { padding: 2px 6px; border-radius: 20px; font-size: 7px; font-weight: 800; }
        .b-paid { background: rgba(22,163,74,0.15); color: #4ade80; }
        .b-sent { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .b-draft { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.4); }

        /* STATS */
        .stats { border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 48px 0; }
        .stats-row { display: grid; grid-template-columns: repeat(4,1fr); }
        .stat { text-align: center; padding: 12px 20px; border-right: 1px solid var(--border); }
        .stat:last-child { border-right: none; }
        .stat-n { font-family: 'Cabinet Grotesk', sans-serif; font-size: 46px; font-weight: 900; letter-spacing: -2px; line-height: 1; }
        .stat-n span { color: var(--green-light); }
        .stat-l { font-size: 13px; color: rgba(255,255,255,0.38); margin-top: 6px; }

        /* SECTION COMMON */
        .sec { padding: 100px 0; position: relative; overflow: hidden; }
        .container { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; color: var(--green-light); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 14px; }
        .eyebrow::before { content: ''; width: 18px; height: 2px; background: var(--green-light); border-radius: 2px; }
        .sec-h { font-family: 'Cabinet Grotesk', sans-serif; font-size: clamp(30px, 5vw, 50px); font-weight: 900; letter-spacing: -1.5px; line-height: 1.1; max-width: 560px; }
        .sec-h em { font-family: 'Instrument Serif', serif; font-style: italic; font-weight: 400; color: rgba(255,255,255,0.45); }

        /* FEATURES */
        .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-top: 56px; }
        .feat-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; padding: 26px;
            transition: all 0.3s; position: relative; overflow: hidden;
        }
        .feat-card:hover { border-color: rgba(22,163,74,0.25); transform: translateY(-3px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .feat-card:hover::before { opacity: 1; }
        .feat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(22,163,74,0.5), transparent); opacity: 0; transition: opacity 0.3s; }
        .feat-ico { width: 42px; height: 42px; background: rgba(22,163,74,0.1); border: 1px solid rgba(22,163,74,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 19px; margin-bottom: 16px; }
        .feat-card h3 { font-family: 'Cabinet Grotesk', sans-serif; font-size: 16px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.3px; }
        .feat-card p { font-size: 13.5px; color: rgba(255,255,255,0.42); line-height: 1.7; }

        /* VIDEO WALKTHROUGH */
        .video-sec { padding: 100px 0; background: linear-gradient(180deg, transparent, rgba(22,163,74,0.025), transparent); }
        .video-hdr { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: end; margin-bottom: 48px; }
        .video-hdr p { font-size: 15px; color: rgba(255,255,255,0.42); line-height: 1.75; }

        .video-frame {
            position: relative; border-radius: 20px; overflow: hidden;
            aspect-ratio: 16/9;
            border: 1px solid rgba(22,163,74,0.2);
            box-shadow: 0 40px 100px rgba(0,0,0,0.55), 0 0 100px rgba(22,163,74,0.05);
            background: #090d0b; cursor: pointer;
        }
        .video-thumb {
            position: absolute; inset: 0;
            background: linear-gradient(145deg, #0d1a11, #0a0f0d);
            display: flex; align-items: center; justify-content: center;
        }
        .video-thumb-inner {
            width: 82%; height: 78%;
            display: grid; grid-template-columns: 170px 1fr;
            border-radius: 8px; overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05); opacity: 0.45;
        }
        .vt-sidebar { background: #080c0a; padding: 12px; border-right: 1px solid rgba(255,255,255,0.05); }
        .vt-main { background: #111814; padding: 12px; }

        .video-overlay {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: rgba(8,13,10,0.5);
            backdrop-filter: blur(3px);
            transition: all 0.3s; z-index: 10;
        }
        .video-overlay.gone { opacity: 0; pointer-events: none; }

        .play-circle {
            width: 76px; height: 76px; background: var(--green);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            transition: all 0.3s; margin-bottom: 18px;
            animation: ripple 2.5s infinite;
        }
        .play-circle svg { width: 28px; height: 28px; fill: #fff; margin-left: 5px; }
        .video-overlay:hover .play-circle { transform: scale(1.08); background: var(--green-light); }
        .video-title-label { font-family: 'Cabinet Grotesk', sans-serif; font-size: 17px; font-weight: 800; letter-spacing: -0.3px; }
        .video-sub-label { font-size: 13px; color: rgba(255,255,255,0.4); margin-top: 4px; }

        .vid-embed { display: none; position: absolute; inset: 0; z-index: 5; }
        .vid-embed iframe { width: 100%; height: 100%; display: block; border: none; }

        /* Chapters */
        .chapters { display: grid; grid-template-columns: repeat(4,1fr); gap: 10px; margin-top: 14px; }
        .ch {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 12px; padding: 14px; cursor: pointer;
            transition: all 0.2s;
        }
        .ch:hover, .ch.on { border-color: rgba(22,163,74,0.3); background: rgba(22,163,74,0.06); }
        .ch.on .ch-n { color: var(--green-light); }
        .ch-n { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.22); margin-bottom: 4px; transition: color 0.2s; }
        .ch-t { font-size: 12px; font-weight: 600; color: #fff; line-height: 1.35; }
        .ch-s { font-size: 11px; color: rgba(255,255,255,0.28); margin-top: 3px; }

        /* PRICING */
        .price-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-top: 56px; }
        .price-c {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 20px; padding: 34px 26px;
            transition: transform 0.3s;
        }
        .price-c:hover { transform: translateY(-4px); }
        .price-c.pop {
            background: linear-gradient(155deg, rgba(22,163,74,0.11), rgba(22,163,74,0.03));
            border-color: rgba(22,163,74,0.28); position: relative;
        }
        .pop-tag {
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: var(--green); color: #fff; padding: 4px 16px;
            border-radius: 100px; font-size: 10px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;
        }
        .p-name { font-family: 'Cabinet Grotesk', sans-serif; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); margin-bottom: 10px; }
        .p-amt { font-family: 'Cabinet Grotesk', sans-serif; font-size: 50px; font-weight: 900; letter-spacing: -3px; line-height: 1; }
        .p-amt sup { font-size: 20px; font-weight: 700; letter-spacing: 0; vertical-align: top; margin-top: 9px; display: inline-block; color: rgba(255,255,255,0.45); }
        .p-per { font-size: 12px; color: rgba(255,255,255,0.3); margin-top: 5px; margin-bottom: 24px; }
        .p-div { height: 1px; background: var(--border); margin-bottom: 22px; }
        .p-feats { list-style: none; margin-bottom: 26px; }
        .p-feats li { display: flex; align-items: flex-start; gap: 9px; font-size: 13.5px; color: rgba(255,255,255,0.58); padding: 5px 0; }
        .p-feats li::before { content: '✓'; width: 17px; height: 17px; min-width: 17px; background: rgba(22,163,74,0.14); color: #4ade80; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 800; margin-top: 1px; }
        .btn-outline-w { display: block; width: 100%; padding: 13px; text-align: center; border-radius: 10px; border: 1px solid var(--border); color: rgba(255,255,255,0.6); font-weight: 700; font-size: 14px; text-decoration: none; transition: all 0.2s; }
        .btn-outline-w:hover { border-color: rgba(255,255,255,0.2); color: #fff; background: rgba(255,255,255,0.04); }
        .btn-green-full { display: block; width: 100%; padding: 13px; text-align: center; border-radius: 10px; background: var(--green); color: #fff; font-weight: 700; font-size: 14px; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-green-full:hover { background: var(--green-light); box-shadow: 0 8px 24px rgba(22,163,74,0.3); }

        /* TESTIMONIALS */
        .test-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-top: 56px; }
        .test-c { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 26px; }
        .stars { color: #fbbf24; font-size: 13px; letter-spacing: 2px; margin-bottom: 12px; }
        .test-q { font-size: 14.5px; color: rgba(255,255,255,0.65); line-height: 1.7; font-style: italic; margin-bottom: 18px; }
        .test-auth { display: flex; align-items: center; gap: 11px; }
        .test-av { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; }
        .test-name { font-size: 13px; font-weight: 700; color: #fff; }
        .test-role { font-size: 11px; color: rgba(255,255,255,0.3); }

        /* CTA */
        .cta-wrap { padding: 100px 24px; }
        .cta-box {
            max-width: 680px; margin: 0 auto;
            background: linear-gradient(140deg, rgba(22,163,74,0.1), rgba(22,163,74,0.03));
            border: 1px solid rgba(22,163,74,0.22); border-radius: 28px;
            padding: 64px 40px; text-align: center; position: relative; overflow: hidden;
        }
        .cta-box::before { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(22,163,74,0.08) 0%, transparent 70%); pointer-events: none; }
        .cta-h { font-family: 'Cabinet Grotesk', sans-serif; font-size: clamp(30px, 5vw, 50px); font-weight: 900; letter-spacing: -1.5px; margin-bottom: 14px; position: relative; }
        .cta-p { font-size: 15px; color: rgba(255,255,255,0.42); margin-bottom: 34px; position: relative; }
        .cta-btns { display: flex; gap: 12px; justify-content: center; position: relative; flex-wrap: wrap; }

        /* FOOTER */
        footer { border-top: 1px solid var(--border); padding: 44px 0; }
        .foot-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .foot-copy { font-size: 13px; color: rgba(255,255,255,0.28); }
        .foot-links { display: flex; gap: 22px; }
        .foot-links a { font-size: 13px; color: rgba(255,255,255,0.28); text-decoration: none; transition: color 0.2s; }
        .foot-links a:hover { color: rgba(255,255,255,0.65); }

        /* ANIMATIONS */
        @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.35} }
        @keyframes ripple { 0%{box-shadow:0 0 0 0 rgba(22,163,74,0.45)} 70%{box-shadow:0 0 0 22px rgba(22,163,74,0)} 100%{box-shadow:0 0 0 0 rgba(22,163,74,0)} }

        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.vis { opacity: 1; transform: none; }

        @media(max-width:768px) {
            nav { padding: 16px 20px; }
            .nav-links { display: none; }
            .feat-grid, .price-grid, .test-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .video-hdr { grid-template-columns: 1fr; gap: 20px; }
            .chapters { grid-template-columns: 1fr 1fr; }
            .preview-body { grid-template-columns: 1fr; }
            .p-sidebar { display: none; }
        }
    </style>
</head>
<body>

{{-- NAV --}}
<nav id="nav">
    <a href="/" class="logo">Invoxa <span class="logo-tag">BETA</span></a>
    <ul class="nav-links">
        <li><a href="#features">Features</a></li>
        <li><a href="#walkthrough">See it in action</a></li>
        <li><a href="#pricing">Pricing</a></li>
    </ul>
    <div class="nav-actions">
        <a href="{{ route('login') }}" class="btn btn-glass">Sign in</a>
        <a href="{{ route('register') }}" class="btn btn-green">Start free trial →</a>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="blob" style="width:700px;height:700px;background:rgba(22,163,74,0.06);top:-250px;left:-250px;"></div>
    <div class="blob" style="width:500px;height:500px;background:rgba(22,163,74,0.05);bottom:-200px;right:-150px;"></div>

    <div class="hero-eyebrow">
        <div class="dot-pulse"></div>
        🇰🇪 Built for Kenyan businesses
    </div>

    <h1 class="hero-h1">
        Invoice smarter,<br>get paid <em>faster</em>
    </h1>

    <p class="hero-p">
        Professional invoices, quotations, M-Pesa payments, profit tracking — all in one clean tool built for Kenya.
    </p>

    <div class="hero-btns">
        <a href="{{ route('register') }}" class="btn btn-green btn-lg">
            Start free — 3 days trial
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#walkthrough" class="btn btn-glass btn-lg">▶&nbsp; Watch demo</a>
    </div>

    <div class="hero-social">
        <div class="avatars">
            <div style="background:#16a34a">JK</div>
            <div style="background:#0891b2">AW</div>
            <div style="background:#7c3aed">BM</div>
            <div style="background:#ea580c">FM</div>
        </div>
        <span>Trusted by 200+ Kenyan businesses</span>
    </div>

    {{-- Dashboard Preview --}}
    <div class="preview-wrap">
        <div class="preview-shell">
            <div class="preview-titlebar">
                <div class="tbar-dot" style="background:#ff5f57"></div>
                <div class="tbar-dot" style="background:#febc2e"></div>
                <div class="tbar-dot" style="background:#28c840"></div>
                <div class="tbar-url">invoxa.co.ke/dashboard</div>
            </div>
            <div class="preview-body">
                <div class="p-sidebar">
                    <div class="p-logo">Invoxa</div>
                    <div class="p-nav on"><div class="p-ndot"></div>Dashboard</div>
                    <div class="p-nav"><div class="p-ndot"></div>Invoices</div>
                    <div class="p-nav"><div class="p-ndot"></div>Quotations</div>
                    <div class="p-nav"><div class="p-ndot"></div>Clients</div>
                    <div class="p-nav"><div class="p-ndot"></div>Expenses</div>
                    <div class="p-nav"><div class="p-ndot"></div>Catalog</div>
                    <div class="p-nav"><div class="p-ndot"></div>Reports</div>
                </div>
                <div class="p-main">
                    <div class="p-topbar">
                        <div class="p-title">Overview · December 2024</div>
                        <div class="p-newbtn">+ New Invoice</div>
                    </div>
                    <div class="p-cards">
                        <div class="p-card">
                            <div class="p-card-l">Revenue</div>
                            <div class="p-card-v" style="color:#4ade80">485K</div>
                        </div>
                        <div class="p-card">
                            <div class="p-card-l">Invoices</div>
                            <div class="p-card-v">24</div>
                        </div>
                        <div class="p-card">
                            <div class="p-card-l">Pending</div>
                            <div class="p-card-v" style="color:#fbbf24">68K</div>
                        </div>
                        <div class="p-card">
                            <div class="p-card-l">Profit</div>
                            <div class="p-card-v" style="color:#4ade80">42%</div>
                        </div>
                    </div>
                    <div class="p-row hdr"><div>Client</div><div>Amount</div><div>Status</div></div>
                    <div class="p-row r"><div>Safaricom Ltd</div><div>Ksh 85,000</div><div><span class="badge b-paid">Paid</span></div></div>
                    <div class="p-row r"><div>KCB Bank</div><div>Ksh 42,500</div><div><span class="badge b-sent">Sent</span></div></div>
                    <div class="p-row r"><div>Equity Bank</div><div>Ksh 120,000</div><div><span class="badge b-paid">Paid</span></div></div>
                    <div class="p-row r"><div>Naivas Ltd</div><div>Ksh 28,000</div><div><span class="badge b-draft">Draft</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<div class="stats">
    <div class="container">
        <div class="stats-row">
            <div class="stat"><div class="stat-n">200<span>+</span></div><div class="stat-l">Active businesses</div></div>
            <div class="stat"><div class="stat-n">12<span>K+</span></div><div class="stat-l">Invoices sent</div></div>
            <div class="stat"><div class="stat-n">3<span>min</span></div><div class="stat-l">To create & send an invoice</div></div>
            <div class="stat"><div class="stat-n">100<span>%</span></div><div class="stat-l">Built for Kenya</div></div>
        </div>
    </div>
</div>

{{-- FEATURES --}}
<section class="sec" id="features">
    <div class="container">
        <div class="reveal">
            <div class="eyebrow">What's inside</div>
            <h2 class="sec-h">Everything your business <em>actually needs</em></h2>
        </div>
        <div class="feat-grid reveal">
            <div class="feat-card">
                <div class="feat-ico">📄</div>
                <h3>Professional Invoices & Quotes</h3>
                <p>Beautiful branded PDFs in seconds. M-Pesa details, ETR support, custom brand colors — all included.</p>
            </div>
            <div class="feat-card">
                <div class="feat-ico">💰</div>
                <h3>Profit Tracking</h3>
                <p>Know your real margins on every job. Track buying vs selling price and see your profit automatically.</p>
            </div>
            <div class="feat-card">
                <div class="feat-ico">📱</div>
                <h3>M-Pesa Payment Badges</h3>
                <p>Paybill, till number and send money details print beautifully on every invoice. No more manual writing.</p>
            </div>
            <div class="feat-card">
                <div class="feat-ico">🔁</div>
                <h3>Recurring Invoices</h3>
                <p>Set it once. Auto-generate invoices weekly, monthly or yearly for your repeat clients without lifting a finger.</p>
            </div>
            <div class="feat-card">
                <div class="feat-ico">📊</div>
                <h3>Expense Tracking</h3>
                <p>Log business expenses, categorize them, and see your real net profit position every month.</p>
            </div>
            <div class="feat-card">
                <div class="feat-ico">⚡</div>
                <h3>Smart Reminders</h3>
                <p>Automatic email reminders before and after due dates. Stop chasing clients — let Invoxa do it.</p>
            </div>
        </div>
    </div>
</section>

{{-- VIDEO WALKTHROUGH --}}
<section class="video-sec" id="walkthrough">
    <div class="container">
        <div class="video-hdr reveal">
            <div>
                <div class="eyebrow">See it in action</div>
                <h2 class="sec-h" style="max-width:420px">Watch how it <em>all works</em></h2>
            </div>
            <p>A 3-minute walkthrough showing how to create your first invoice, set up M-Pesa payment details, and send it to a client — all from one screen.</p>
        </div>

        <div class="reveal">
            {{-- Video player --}}
            <div class="video-frame" id="videoFrame" onclick="playVideo()">

                {{-- Thumbnail --}}
                <div class="video-thumb">
                    <div class="video-thumb-inner">
                        <div class="vt-sidebar">
                            <div style="font-size:9px;color:#4ade80;font-weight:900;margin-bottom:12px;">Invoxa</div>
                            <div style="background:rgba(22,163,74,0.15);padding:4px 8px;border-radius:4px;font-size:7px;color:#4ade80;margin-bottom:3px;">Dashboard</div>
                            <div style="padding:4px 8px;font-size:7px;color:rgba(255,255,255,0.25);margin-bottom:3px;">Invoices</div>
                            <div style="padding:4px 8px;font-size:7px;color:rgba(255,255,255,0.25);margin-bottom:3px;">Quotations</div>
                            <div style="padding:4px 8px;font-size:7px;color:rgba(255,255,255,0.25);">Clients</div>
                        </div>
                        <div class="vt-main">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                <div style="font-size:8px;font-weight:700;color:#fff;">Invoices</div>
                                <div style="background:#16a34a;color:#fff;padding:2px 7px;border-radius:3px;font-size:6px;font-weight:800;">+ New</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 60px 50px;gap:4px;font-size:6px;color:rgba(255,255,255,0.25);padding:0 3px;margin-bottom:5px;text-transform:uppercase;letter-spacing:0.5px;">
                                <div>Client</div><div>Amount</div><div>Status</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 60px 50px;gap:4px;padding:5px 3px;background:rgba(255,255,255,0.025);border-radius:3px;margin-bottom:2px;">
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">Safaricom</div>
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">85,000</div>
                                <div style="font-size:6px;color:#4ade80;font-weight:800;">Paid</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 60px 50px;gap:4px;padding:5px 3px;background:rgba(255,255,255,0.025);border-radius:3px;margin-bottom:2px;">
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">KCB Bank</div>
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">42,500</div>
                                <div style="font-size:6px;color:#60a5fa;font-weight:800;">Sent</div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 60px 50px;gap:4px;padding:5px 3px;background:rgba(255,255,255,0.025);border-radius:3px;">
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">Naivas Ltd</div>
                                <div style="font-size:7px;color:rgba(255,255,255,0.6)">28,000</div>
                                <div style="font-size:6px;color:rgba(255,255,255,0.35);font-weight:800;">Draft</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Play overlay --}}
                <div class="video-overlay" id="overlay">
                    <div class="play-circle">
                        <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <div class="video-title-label">Watch the full walkthrough</div>
                    <div class="video-sub-label">3 min &nbsp;·&nbsp; No signup needed</div>
                </div>

                {{-- Embed (injected on play) --}}
                <div class="vid-embed" id="vidEmbed">
                    {{--
                        TO ADD YOUR VIDEO:
                        1. Upload your screen recording to YouTube
                        2. Get the video ID from the URL (e.g. youtube.com/watch?v=ABC123)
                        3. Replace YOUR_VIDEO_ID below with that ID
                    --}}
                    <iframe id="vidIframe" src="" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                </div>
            </div>

            {{-- Chapter markers --}}
            <div class="chapters">
                <div class="ch on" onclick="chapter(this, 0)">
                    <div class="ch-n">01 · 0:00</div>
                    <div class="ch-t">Creating your first invoice</div>
                    <div class="ch-s">0:00 – 0:55</div>
                </div>
                <div class="ch" onclick="chapter(this, 55)">
                    <div class="ch-n">02 · 0:55</div>
                    <div class="ch-t">Setting up M-Pesa payments</div>
                    <div class="ch-s">0:55 – 1:40</div>
                </div>
                <div class="ch" onclick="chapter(this, 100)">
                    <div class="ch-n">03 · 1:40</div>
                    <div class="ch-t">Quotations & conversions</div>
                    <div class="ch-s">1:40 – 2:20</div>
                </div>
                <div class="ch" onclick="chapter(this, 140)">
                    <div class="ch-n">04 · 2:20</div>
                    <div class="ch-t">Dashboard & profit view</div>
                    <div class="ch-s">2:20 – 3:00</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PRICING --}}
<section class="sec" id="pricing">
    <div class="container">
        <div class="reveal" style="text-align:center">
            <div class="eyebrow" style="justify-content:center">Pricing</div>
            <h2 class="sec-h" style="max-width:100%;text-align:center">Simple, <em>honest</em> pricing</h2>
            <p style="color:rgba(255,255,255,0.38);margin-top:12px;font-size:15px">No hidden fees. Cancel anytime. 3-day free trial on all plans.</p>
        </div>
        <div class="price-grid reveal">
            <div class="price-c">
                <div class="p-name">Starter</div>
                <div class="p-amt"><sup>Ksh</sup>100</div>
                <div class="p-per">per download · pay as you go</div>
                <div class="p-div"></div>
                <ul class="p-feats">
                    <li>Unlimited invoices & quotes</li>
                    <li>PDF downloads (Ksh 100 each)</li>
                    <li>M-Pesa payment badges</li>
                    <li>Client management</li>
                    <li>Email sending</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-outline-w">Get started free</a>
            </div>
            <div class="price-c pop">
                <div class="pop-tag">Most Popular</div>
                <div class="p-name">Monthly</div>
                <div class="p-amt"><sup>Ksh</sup>700</div>
                <div class="p-per">per month · unlimited downloads</div>
                <div class="p-div"></div>
                <ul class="p-feats">
                    <li>Everything in Starter</li>
                    <li>Unlimited PDF downloads</li>
                    <li>Profit & expense tracking</li>
                    <li>Recurring invoices</li>
                    <li>Smart payment reminders</li>
                    <li>Custom brand colors on PDFs</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-green-full">Start 3-day free trial</a>
            </div>
            <div class="price-c">
                <div class="p-name">Yearly</div>
                <div class="p-amt"><sup>Ksh</sup>5K</div>
                <div class="p-per">per year · save Ksh 3,400</div>
                <div class="p-div"></div>
                <ul class="p-feats">
                    <li>Everything in Monthly</li>
                    <li>Best value — 40% off</li>
                    <li>Priority support</li>
                    <li>Staff accounts</li>
                    <li>Early access to new features</li>
                    <li>Referral bonuses</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-outline-w">Get started free</a>
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
<section class="sec">
    <div class="container">
        <div class="reveal" style="text-align:center">
            <div class="eyebrow" style="justify-content:center">From our users</div>
            <h2 class="sec-h" style="max-width:100%;text-align:center">Loved by Kenyan <em>businesses</em></h2>
        </div>
        <div class="test-grid reveal">
            <div class="test-c">
                <div class="stars">★★★★★</div>
                <p class="test-q">"Before Invoxa I was writing invoices in Word. Now I send a professional PDF with M-Pesa details in under 2 minutes. My clients actually pay faster."</p>
                <div class="test-auth">
                    <div class="test-av" style="background:#16a34a">JK</div>
                    <div><div class="test-name">James Kamau</div><div class="test-role">IT Consultant, Nairobi</div></div>
                </div>
            </div>
            <div class="test-c">
                <div class="stars">★★★★★</div>
                <p class="test-q">"The profit tracking is everything. I finally know which clients are actually making me money and which ones I need to reprice."</p>
                <div class="test-auth">
                    <div class="test-av" style="background:#0891b2">AW</div>
                    <div><div class="test-name">Aisha Wanjiru</div><div class="test-role">Events Planner, Westlands</div></div>
                </div>
            </div>
            <div class="test-c">
                <div class="stars">★★★★★</div>
                <p class="test-q">"Recurring invoices alone saved me 2 hours a week. I have 12 monthly retainer clients and Invoxa handles all of them automatically."</p>
                <div class="test-auth">
                    <div class="test-av" style="background:#7c3aed">BM</div>
                    <div><div class="test-name">Brian Mutua</div><div class="test-role">CCTV & Security, Mombasa</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<div class="cta-wrap">
    <div class="cta-box reveal">
        <h2 class="cta-h">Ready to get paid faster?</h2>
        <p class="cta-p">Join 200+ Kenyan businesses on Invoxa. Start your free 3-day trial — no card required.</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn btn-green btn-lg">Create free account →</a>
            <a href="{{ route('login') }}" class="btn btn-glass btn-lg">Sign in</a>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<footer>
    <div class="container">
        <div class="foot-row">
            <div>
                <div class="logo" style="margin-bottom:5px">Invoxa <span class="logo-tag">BETA</span></div>
                <div class="foot-copy">© {{ date('Y') }} Invoxa. Built with ❤️ in Kenya.</div>
            </div>
            <div class="foot-links">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="#pricing">Pricing</a>
                <a href="mailto:support@invoxa.co.ke">Support</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Nav scroll
    window.addEventListener('scroll', () => {
        document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 40);
    });

    // Scroll reveal
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
            if (e.isIntersecting) {
                setTimeout(() => e.target.classList.add('vis'), i * 100);
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

    // ── VIDEO ──────────────────────────────────────────────────
    // Replace with your YouTube video ID once you record the walkthrough
    // Example: if URL is youtube.com/watch?v=dQw4w9WgXcQ  → VIDEO_ID = 'dQw4w9WgXcQ'
    const VIDEO_ID = 'YOUR_VIDEO_ID';

    function playVideo(startAt = 0) {
        if (VIDEO_ID === 'YOUR_VIDEO_ID') {
            alert('📹 Video coming soon!\n\nTo add your walkthrough:\n1. Record a screen capture of Invoxa\n2. Upload to YouTube\n3. In welcome.blade.php set VIDEO_ID to your YouTube video ID');
            return;
        }
        const iframe = document.getElementById('vidIframe');
        iframe.src = `https://www.youtube.com/embed/${VIDEO_ID}?autoplay=1&start=${startAt}&rel=0&modestbranding=1&color=white`;
        document.getElementById('overlay').classList.add('gone');
        document.getElementById('vidEmbed').style.display = 'block';
    }

    function chapter(el, seconds) {
        document.querySelectorAll('.ch').forEach(c => c.classList.remove('on'));
        el.classList.add('on');
        playVideo(seconds);
    }
</script>
</body>
</html>