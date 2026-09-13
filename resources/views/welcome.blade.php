<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marabunta Swimming Club - Sistem Database Atlet</title>
    <meta name="description" content="Portal monitoring resmi Marabunta Swimming Club. Pantau prestasi atlet renang secara transparan dan real-time.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy-950: #040d1a;
            --navy-900: #060f22;
            --navy-800: #0a1628;
            --navy-700: #0f2040;
            --navy-600: #143058;
            --blue-500: #1a6bb5;
            --blue-400: #2684d4;
            --blue-300: #5ba8e8;
            --silver-300: #c8d8ec;
            --silver-200: #dde8f4;
            --silver-100: #edf4fc;
            --accent-gold: #c9a84c;
            --accent-gold-light: #e8c96d;
            --glass-dark: rgba(10, 22, 40, 0.75);
            --glass-border: rgba(91, 168, 232, 0.12);
            --glass-shine: rgba(200, 216, 236, 0.06);
            --text-primary: #edf4fc;
            --text-secondary: #8eb8d8;
            --text-muted: #5a7a94;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--navy-950);
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(26, 107, 181, 0.18) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 90% 80%, rgba(6, 46, 90, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 10% 90%, rgba(10, 30, 60, 0.3) 0%, transparent 50%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Subtle grid texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(91, 168, 232, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(91, 168, 232, 0.025) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* Reflection shimmer line */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(
                105deg,
                transparent 40%,
                rgba(91, 168, 232, 0.03) 50%,
                transparent 60%
            );
            animation: shimmer 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        /* ─── LAYOUT ─── */
        .page-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 1180px;
            width: 100%;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* ─── HEADER ─── */
        .site-header {
            padding: 1.25rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(4, 13, 26, 0.7);
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
            border-bottom: 1px solid var(--glass-border);
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 0.5rem;
            filter: drop-shadow(0 0 12px rgba(91, 168, 232, 0.4));
            transition: filter 0.3s ease, transform 0.3s ease;
        }

        .brand:hover .brand-logo {
            filter: drop-shadow(0 0 20px rgba(91, 168, 232, 0.7));
            transform: scale(1.05);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--silver-200);
            line-height: 1.1;
            letter-spacing: -0.01em;
        }

        .brand-subtitle {
            font-size: 0.68rem;
            color: var(--accent-gold);
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .nav-actions {
            display: flex;
            gap: 0.65rem;
            align-items: center;
        }

        .btn-outline {
            padding: 0.55rem 1.1rem;
            border-radius: 0.65rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--silver-300);
            border: 1px solid rgba(91, 168, 232, 0.2);
            background: transparent;
            transition: all 0.25s ease;
            letter-spacing: 0.01em;
        }

        .btn-outline:hover {
            background: rgba(91, 168, 232, 0.1);
            border-color: rgba(91, 168, 232, 0.4);
            color: var(--silver-100);
        }

        .btn-primary {
            padding: 0.55rem 1.25rem;
            border-radius: 0.65rem;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--navy-950);
            background: linear-gradient(135deg, var(--blue-300) 0%, var(--blue-400) 100%);
            box-shadow: 0 4px 16px rgba(38, 132, 212, 0.25), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.25s ease;
            letter-spacing: 0.01em;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(38, 132, 212, 0.4), inset 0 1px 0 rgba(255,255,255,0.25);
        }

        /* ─── HERO ─── */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 5rem 0 4rem;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 5rem;
            align-items: center;
            width: 100%;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: 2rem;
            background: rgba(201, 168, 76, 0.1);
            border: 1px solid rgba(201, 168, 76, 0.25);
            color: var(--accent-gold-light);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
        }

        .eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent-gold-light);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            letter-spacing: -0.035em;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--blue-300) 0%, var(--silver-200) 60%, var(--blue-300) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        .hero-desc strong {
            color: var(--silver-200);
            font-weight: 600;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cta-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.9rem 2rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            color: var(--navy-950);
            background: linear-gradient(135deg, var(--blue-300) 0%, var(--blue-400) 100%);
            box-shadow: 0 8px 24px rgba(38, 132, 212, 0.3), inset 0 1px 0 rgba(255,255,255,0.25);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px rgba(38, 132, 212, 0.45), inset 0 1px 0 rgba(255,255,255,0.3);
        }

        .cta-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.9rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            color: var(--silver-200);
            border: 1px solid rgba(91, 168, 232, 0.2);
            background: rgba(10, 22, 40, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cta-secondary:hover {
            transform: translateY(-2px);
            background: rgba(26, 107, 181, 0.15);
            border-color: rgba(91, 168, 232, 0.4);
        }

        /* ─── REFLECTIVE CARD ─── */
        .hero-visual {
            display: flex;
            justify-content: center;
        }

        .reflect-card {
            position: relative;
            width: 100%;
            max-width: 360px;
        }

        .reflect-card-inner {
            background: linear-gradient(160deg, rgba(14, 30, 55, 0.9) 0%, rgba(8, 18, 35, 0.95) 100%);
            border: 1px solid rgba(91, 168, 232, 0.15);
            border-radius: 1.75rem;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 40px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(91, 168, 232, 0.08),
                inset 0 1px 0 rgba(200, 216, 236, 0.1),
                inset 0 -1px 0 rgba(0, 0, 0, 0.3);
        }

        /* Reflection shine effect on top */
        .reflect-card-inner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(200, 216, 236, 0.3) 30%,
                rgba(200, 216, 236, 0.6) 50%,
                rgba(200, 216, 236, 0.3) 70%,
                transparent 100%
            );
        }

        /* Subtle inner glow */
        .reflect-card-inner::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(38, 132, 212, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Card reflection shadow (bottom mirror effect) */
        .reflect-card::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: 10%;
            right: 10%;
            height: 40px;
            background: linear-gradient(to bottom,
                rgba(26, 107, 181, 0.12) 0%,
                transparent 100%
            );
            filter: blur(12px);
            border-radius: 0 0 50% 50%;
        }

        .card-badge {
            position: absolute;
            top: -1px;
            right: 28px;
            background: linear-gradient(135deg, #1a6bb5 0%, #0f2040 100%);
            color: var(--silver-200);
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.3rem 0.85rem;
            border-radius: 0 0 0.75rem 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid rgba(91, 168, 232, 0.2);
            border-top: none;
        }

        .pool-header {
            border-bottom: 1px solid rgba(91, 168, 232, 0.1);
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .pool-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--silver-200);
            margin-bottom: 0.2rem;
            line-height: 1.3;
        }

        .pool-location {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .pool-stats {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .pool-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .stat-value {
            color: var(--silver-200);
            font-weight: 700;
        }

        .stat-value.live {
            color: #4ade80;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 6px #4ade80;
        }

        /* ─── DIVIDER ─── */
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg,
                transparent,
                rgba(91, 168, 232, 0.1) 20%,
                rgba(91, 168, 232, 0.1) 80%,
                transparent
            );
            margin: 1.5rem 0;
        }

        /* ─── FEATURES ─── */
        .features-section {
            padding: 3rem 0 5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .feature-card {
            background: linear-gradient(160deg, rgba(12, 24, 48, 0.8) 0%, rgba(6, 15, 34, 0.9) 100%);
            border: 1px solid rgba(91, 168, 232, 0.1);
            border-radius: 1.25rem;
            padding: 1.75rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Top reflection line */
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent,
                rgba(200, 216, 236, 0.2) 50%,
                transparent
            );
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            border-color: rgba(91, 168, 232, 0.2);
            transform: translateY(-4px);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(200, 216, 236, 0.08);
        }

        .feat-icon {
            width: 44px;
            height: 44px;
            border-radius: 0.85rem;
            background: rgba(26, 107, 181, 0.12);
            border: 1px solid rgba(91, 168, 232, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            color: var(--blue-300);
            transition: all 0.3s ease;
        }

        .feature-card:hover .feat-icon {
            background: rgba(26, 107, 181, 0.25);
            border-color: rgba(91, 168, 232, 0.35);
            box-shadow: 0 0 20px rgba(38, 132, 212, 0.15);
        }

        .feat-icon svg {
            width: 20px;
            height: 20px;
        }

        .feat-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--silver-200);
            margin-bottom: 0.6rem;
            letter-spacing: -0.01em;
        }

        .feat-desc {
            font-size: 0.875rem;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .feat-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--blue-300);
            text-decoration: none;
            transition: gap 0.2s ease;
        }

        .feat-link:hover {
            gap: 0.6rem;
        }

        /* ─── STATS ROW ─── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: linear-gradient(160deg, rgba(12, 24, 48, 0.7) 0%, rgba(6, 15, 34, 0.8) 100%);
            border: 1px solid rgba(91, 168, 232, 0.1);
            border-radius: 1.25rem;
            overflow: hidden;
            position: relative;
        }

        .stats-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg,
                transparent,
                rgba(200, 216, 236, 0.25) 50%,
                transparent
            );
        }

        .stat-item {
            padding: 1.75rem 2rem;
            text-align: center;
            position: relative;
        }

        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            bottom: 20%;
            width: 1px;
            background: rgba(91, 168, 232, 0.1);
        }

        .stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--silver-200);
            margin-bottom: 0.3rem;
            letter-spacing: -0.02em;
        }

        .stat-lbl {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ─── FOOTER ─── */
        .site-footer {
            padding: 2rem 0;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(91, 168, 232, 0.07);
            background: rgba(4, 13, 26, 0.5);
        }

        /* ─── FADE IN ─── */
        .fade-in {
            opacity: 0;
            transform: translateY(18px);
            animation: fadeIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .fade-in-d1 { animation-delay: 0.1s; }
        .fade-in-d2 { animation-delay: 0.2s; }
        .fade-in-d3 { animation-delay: 0.3s; }
        .fade-in-d4 { animation-delay: 0.4s; }

        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .hero-title { font-size: 2.8rem; }
            .hero-grid { gap: 3rem; }
        }

        @media (max-width: 768px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-content {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .hero-visual { order: -1; }

            .hero-cta {
                justify-content: center;
                width: 100%;
            }

            .cta-primary, .cta-secondary {
                width: 100%;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-bar {
                grid-template-columns: 1fr;
            }

            .stat-item:not(:last-child)::after {
                display: none;
            }

            .hero-title { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    <!-- ─── HEADER ─── -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="brand">
                <img src="{{ asset('images/logo-msc.png') }}" alt="Logo Marabunta Swimming Club" class="brand-logo">
                <div class="brand-text">
                    <span class="brand-name">Marabunta</span>
                    <span class="brand-subtitle">Swimming Club</span>
                </div>
            </a>

            <div class="nav-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-outline">Masuk Portal</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- ─── HERO ─── -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <!-- Left: Content -->
                <div class="hero-content">
                    <div class="eyebrow fade-in">Sistem Database &amp; Prestasi Resmi</div>

                    <h1 class="hero-title fade-in fade-in-d1">
                        Pantau Prestasi Atlet<br>
                        <span class="highlight">Renang Secara Transparan</span>
                    </h1>

                    <p class="hero-desc fade-in fade-in-d2">
                        Portal monitoring resmi Marabunta Swimming Club. Platform terpusat untuk mencatat kelas latihan, menyimpan hasil kompetisi, serta menyajikan kalkulasi otomatis <strong>Personal Best (PB)</strong> dengan visualisasi grafik perkembangan atlet bagi pelatih dan orang tua.
                    </p>

                    <div class="hero-cta fade-in fade-in-d3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="cta-primary">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                </svg>
                                Buka Dashboard Anda
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="cta-primary">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                                </svg>
                                Masuk ke Portal
                            </a>
                            <a href="{{ route('register') }}" class="cta-secondary">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                                </svg>
                                Registrasi Orang Tua
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right: Reflective Card -->
                <div class="hero-visual fade-in fade-in-d2">
                    <div class="reflect-card">
                        <div class="reflect-card-inner">
                            <div class="card-badge">Info Kolam</div>

                            <div class="pool-header">
                                <div class="pool-name">Kolam Renang Marabunta Rampal</div>
                                <div class="pool-location">Pusat Latihan Marabunta SC · Malang</div>
                            </div>

                            <div class="pool-stats">
                                <div class="pool-stat-row">
                                    <span class="stat-label">Ukuran Kolam</span>
                                    <span class="stat-value">50 Meter</span>
                                </div>
                                <div class="pool-stat-row">
                                    <span class="stat-label">Kategori</span>
                                    <span class="stat-value">Long Course</span>
                                </div>
                                <div class="pool-stat-row">
                                    <span class="stat-label">Status</span>
                                    <span class="stat-value live">
                                        <span class="live-dot"></span>
                                        Aktif Beroperasi
                                    </span>
                                </div>
                                <div class="section-divider"></div>
                                <div class="pool-stat-row">
                                    <span class="stat-label">Platform</span>
                                    <span class="stat-value">Database &amp; PB Tracking</span>
                                </div>
                                <div class="pool-stat-row">
                                    <span class="stat-label">Akses</span>
                                    <span class="stat-value">Pelatih &amp; Orang Tua</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FEATURES ─── -->
    <section class="features-section">
        <div class="container">
            <div class="section-divider"></div>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card fade-in fade-in-d1">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2"/><path d="M10 2h4"/><path d="M12 2v3"/>
                        </svg>
                    </div>
                    <h3 class="feat-title">Personal Best &amp; Grafik</h3>
                    <p class="feat-desc">Kalkulasi rekap catatan waktu terbaik secara otomatis per nomor lomba dengan grafik analisis perkembangan atlet dari waktu ke waktu.</p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="feat-link">
                            Lihat PB Saya
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    @endauth
                </div>

                <!-- Feature 2 -->
                <div class="feature-card fade-in fade-in-d2">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/>
                        </svg>
                    </div>
                    <h3 class="feat-title">Ekspor Formulir A1–A3</h3>
                    <p class="feat-desc">Ekspor berkas pendaftaran perlombaan ke format Excel resmi Form A1 (Perorangan), Form A2 (Gaya Ganti), dan Form A3 (Estafet).</p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="feat-link">
                            Buka Layanan Ekspor
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    @endauth
                </div>

                <!-- Feature 3 -->
                <div class="feature-card fade-in fade-in-d3">
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 6l-9.5 9.5-5-5L1 18"/><polyline points="17 6 23 6 23 12"/>
                        </svg>
                    </div>
                    <h3 class="feat-title">Rekap Kemajuan Kelas</h3>
                    <p class="feat-desc">Histori kenaikan jenjang pembinaan atlet terpantau penuh, mulai dari Dasar 0, Dasar 1, Pemula, Pra-Prestasi, hingga kelas Prestasi.</p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="feat-link">
                            Lihat Histori Kelas
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="stats-bar fade-in fade-in-d4">
                <div class="stat-item">
                    <div class="stat-num">5 Jenjang</div>
                    <div class="stat-lbl">Kelas Pembinaan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">Otomatis</div>
                    <div class="stat-lbl">Kalkulasi Personal Best</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">Real-Time</div>
                    <div class="stat-lbl">Akses Pelatih &amp; Orang Tua</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FOOTER ─── -->
    <footer class="site-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Marabunta Swimming Club. All Rights Reserved. &nbsp;|&nbsp; Sistem Informasi Database &amp; Manajemen Atlet Renang.</p>
        </div>
    </footer>

</div>
</body>
</html>
