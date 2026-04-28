<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashOrbit - Play Video Call Game & Earn Money</title>
    <meta name="description" content="Play video call games and earn real money. Get ₹5 per referral. Instant UPI withdrawal. Download CashOrbit now!">
    <meta name="keywords" content="CashOrbit, earn money, video call game, referral earning, UPI withdrawal, online earning app">
    <meta name="theme-color" content="#050a18">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🚀</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #050a18;
            --bg-card: #0c1427;
            --bg-glass: rgba(12, 20, 39, 0.7);
            --border-glow: rgba(99, 102, 241, 0.2);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #475569;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --cyan: #06b6d4;
            --green: #22c55e;
            --purple: #a855f7;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* ========== ANIMATED BACKGROUND ========== */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
            pointer-events: none;
        }

        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.12;
            z-index: 0;
            pointer-events: none;
        }

        .bg-glow-1 {
            top: -200px;
            left: -200px;
            width: 600px;
            height: 600px;
            background: var(--accent);
            animation: floatGlow 15s ease-in-out infinite;
        }

        .bg-glow-2 {
            bottom: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: var(--cyan);
            animation: floatGlow 18s ease-in-out infinite reverse;
        }

        .bg-glow-3 {
            top: 40%;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: var(--purple);
            opacity: 0.06;
            animation: floatGlow 20s ease-in-out infinite;
        }

        @keyframes floatGlow {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(30px, -20px); }
            66% { transform: translate(-20px, 30px); }
        }

        /* ========== FLOATING PARTICLES ========== */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--accent-light);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat linear infinite;
        }

        @keyframes particleFloat {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        /* ========== NAVBAR ========== */
        .navbar-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            padding: 18px 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-custom.scrolled {
            background: rgba(5, 10, 24, 0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border-glow);
            padding: 12px 0;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            box-shadow: 0 4px 16px rgba(99,102,241,0.35);
            transition: transform 0.3s;
        }

        .nav-brand:hover .nav-brand-icon {
            transform: rotate(-8deg) scale(1.05);
        }

        .nav-brand-text {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .nav-link-custom {
            padding: 8px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 9px;
            transition: all 0.25s;
            position: relative;
        }

        .nav-link-custom:hover {
            color: var(--text-primary);
            background: rgba(99,102,241,0.08);
        }

        .nav-btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: white !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(99,102,241,0.3);
        }

        .nav-btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(99,102,241,0.45);
        }

        .mobile-menu-btn {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border-glow);
            color: var(--text-primary);
            width: 42px;
            height: 42px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 16px;
            right: 16px;
            background: rgba(12, 20, 39, 0.97);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border-glow);
            border-radius: 16px;
            padding: 16px;
            z-index: 49;
            flex-direction: column;
            gap: 4px;
        }

        .mobile-menu.show {
            display: flex;
        }

        .mobile-menu a {
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .mobile-menu a:hover {
            background: rgba(99,102,241,0.08);
            color: var(--text-primary);
        }

        /* ========== HERO ========== */
        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 130px 24px 80px;
        }

        .hero-content {
            max-width: 740px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: var(--accent-light);
            margin-bottom: 28px;
            animation: fadeInUp 0.7s ease;
        }

        .hero-badge .dot {
            width: 8px;
            height: 8px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 8px rgba(34,197,94,0.5);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .hero h1 {
            font-size: clamp(38px, 7.5vw, 68px);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -2px;
            margin-bottom: 22px;
            animation: fadeInUp 0.7s ease 0.1s both;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--cyan) 50%, var(--green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: clamp(16px, 2.5vw, 20px);
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.7s ease 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.7s ease 0.3s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-primary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 17px 38px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 28px rgba(99,102,241,0.35);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }

        .btn-primary-custom:hover::before {
            transform: translateX(100%);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px rgba(99,102,241,0.5);
        }

        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 17px 38px;
            background: transparent;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 14px;
            border: 1px solid rgba(99,102,241,0.3);
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background: rgba(99,102,241,0.08);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .hero-scroll-indicator {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s ease-in-out infinite;
            color: var(--text-muted);
            font-size: 20px;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(8px); }
        }

        /* ========== FEATURES ========== */
        .features {
            position: relative;
            z-index: 1;
            padding: 80px 24px 100px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-label {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-light);
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin-bottom: 12px;
        }

        .section-title {
            text-align: center;
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 56px;
            line-height: 1.2;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 32px 28px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99,102,241,0.35);
            box-shadow: 0 20px 48px rgba(0,0,0,0.35), 0 0 40px rgba(99,102,241,0.05);
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-4deg);
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.65;
        }

        /* ========== HOW IT WORKS ========== */
        .how-it-works {
            position: relative;
            z-index: 1;
            padding: 80px 24px 100px;
            max-width: 860px;
            margin: 0 auto;
        }

        .steps {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            left: 43px;
            top: 44px;
            bottom: 44px;
            width: 2px;
            background: linear-gradient(to bottom, var(--accent), var(--cyan), var(--green));
            opacity: 0.15;
        }

        .step {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glow);
            border-radius: 18px;
            padding: 28px;
            transition: all 0.35s;
            position: relative;
        }

        .step:hover {
            border-color: rgba(99,102,241,0.35);
            transform: translateX(6px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.25);
        }

        .step-number {
            width: 46px;
            height: 46px;
            min-width: 46px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: white;
            box-shadow: 0 4px 16px rgba(99,102,241,0.3);
            position: relative;
            z-index: 2;
        }

        .step:nth-child(2) .step-number {
            background: linear-gradient(135deg, var(--cyan), var(--green));
            box-shadow: 0 4px 16px rgba(6,182,212,0.3);
        }

        .step:nth-child(3) .step-number {
            background: linear-gradient(135deg, var(--green), #16a34a);
            box-shadow: 0 4px 16px rgba(34,197,94,0.3);
        }

        .step h4 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.2px;
        }

        .step p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ========== STATS ========== */
        .stats-bar {
            position: relative;
            z-index: 1;
            padding: 40px 24px 80px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .stat-item {
            text-align: center;
            padding: 32px 16px;
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glow);
            border-radius: 18px;
            transition: all 0.3s;
        }

        .stat-item:hover {
            transform: translateY(-4px);
            border-color: rgba(99,102,241,0.35);
        }

        .stat-number {
            font-size: clamp(28px, 4vw, 38px);
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-light), var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========== REFERRAL HIGHLIGHT ========== */
        .referral-section {
            position: relative;
            z-index: 1;
            padding: 0 24px 100px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .referral-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(34,197,94,0.2);
            border-radius: 24px;
            padding: 48px;
            overflow: hidden;
            position: relative;
        }

        .referral-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(34,197,94,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .referral-left h2 {
            font-size: clamp(26px, 4vw, 36px);
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.8px;
            line-height: 1.2;
        }

        .referral-left p {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .referral-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 28px;
        }

        .referral-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .referral-features li i {
            color: var(--green);
            font-size: 14px;
            width: 20px;
            text-align: center;
        }

        .referral-right {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .referral-demo-card {
            background: rgba(5, 10, 24, 0.6);
            border: 1px solid var(--border-glow);
            border-radius: 16px;
            padding: 24px;
        }

        .referral-demo-card .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .referral-demo-card .code {
            font-family: 'Courier New', monospace;
            font-size: 22px;
            font-weight: 800;
            color: var(--green);
            letter-spacing: 3px;
        }

        .referral-demo-card .earn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border-glow);
        }

        .referral-demo-card .earn-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        .referral-demo-card .earn-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--green);
        }

        .referral-demo-card .earn-value.small {
            font-size: 15px;
            color: var(--accent-light);
        }

        /* ========== FAQ ========== */
        .faq-section {
            position: relative;
            z-index: 1;
            padding: 0 24px 100px;
            max-width: 720px;
            margin: 0 auto;
        }

        .faq-item {
            border: 1px solid var(--border-glow);
            border-radius: 14px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            border-color: rgba(99,102,241,0.3);
        }

        .faq-question {
            width: 100%;
            background: var(--bg-glass);
            border: none;
            color: var(--text-primary);
            padding: 20px 24px;
            font-size: 15px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: inherit;
            transition: background 0.2s;
        }

        .faq-question:hover {
            background: rgba(99,102,241,0.05);
        }

        .faq-question i {
            color: var(--accent-light);
            font-size: 14px;
            transition: transform 0.3s;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding 0.35s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 200px;
        }

        .faq-answer p {
            padding: 0 24px 20px;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.7;
        }

        /* ========== CTA ========== */
        .cta {
            position: relative;
            z-index: 1;
            padding: 0 24px 100px;
            text-align: center;
        }

        .cta-box {
            max-width: 720px;
            margin: 0 auto;
            padding: 64px 40px;
            background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(6,182,212,0.04));
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 28px;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 60%);
            animation: rotateSlow 30s linear infinite;
            pointer-events: none;
        }

        @keyframes rotateSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .cta-box h2 {
            position: relative;
            font-size: clamp(24px, 4vw, 38px);
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.8px;
        }

        .cta-box p {
            position: relative;
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 36px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ========== FOOTER ========== */
        .footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid var(--border-glow);
            padding: 32px 24px;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .footer-brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
        }

        .footer-brand span {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .footer-copyright {
            color: var(--text-muted);
            font-size: 13px;
        }

        .footer-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .footer-link:hover {
            color: var(--accent-light);
        }

        .footer-admin {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 8px;
            color: var(--accent-light);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .footer-admin:hover {
            background: rgba(99,102,241,0.15);
            border-color: rgba(99,102,241,0.3);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .referral-box {
                grid-template-columns: 1fr;
                padding: 32px;
            }
        }

        @media (max-width: 767px) {
            .nav-links .nav-link-custom {
                display: none;
            }
            .nav-links .nav-btn-download {
                display: none;
            }
            .mobile-menu-btn {
                display: flex;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .steps::before {
                display: none;
            }
            .hero h1 {
                letter-spacing: -1px;
            }
            .cta-box {
                padding: 40px 24px;
            }
            .footer-inner {
                flex-direction: column;
                text-align: center;
            }
            .footer-left {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
                justify-content: center;
                padding: 15px 28px;
            }
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }
            .stat-item {
                padding: 24px 12px;
            }
        }
    </style>
</head>
<body>

    <!-- Background effects -->
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>
    <div class="particles" id="particles"></div>

    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar">
        <div class="navbar-inner">
            <a href="#" class="nav-brand">
                <div class="nav-brand-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <span class="nav-brand-text">CashOrbit</span>
            </a>
            <div class="nav-links">
                <a href="#features" class="nav-link-custom">Features</a>
                <a href="#how" class="nav-link-custom">How It Works</a>
                <a href="#faq" class="nav-link-custom">FAQ</a>
                <a href="app.apk" class="nav-btn-download">
                    <i class="fas fa-download"></i> Download
                </a>
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="#features" onclick="closeMobileMenu()">Features</a>
        <a href="#how" onclick="closeMobileMenu()">How It Works</a>
        <a href="#faq" onclick="closeMobileMenu()">FAQ</a>
        <a href="app.apk" onclick="closeMobileMenu()">
            <i class="fas fa-download me-2"></i>Download App
        </a>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                Now Live — Start Earning Today
            </div>
            <h1>
                Play Video Call Game<br>
                &amp; <span class="gradient-text">Earn Real Money</span>
            </h1>
            <p>
                Join thousands of users earning ₹5 per referral and real cash through
                video call games. Instant withdrawal to your UPI.
            </p>
            <div class="hero-buttons">
                <a href="app.apk" class="btn-primary-custom">
                    <i class="fas fa-download"></i> Download App
                </a>
                <a href="#how" class="btn-secondary-custom">
                    <i class="fas fa-play-circle"></i> How It Works
                </a>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-label">Features</div>
        <h2 class="section-title">Why Choose CashOrbit?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(99,102,241,0.15);color:var(--accent-light);">
                    <i class="fas fa-video"></i>
                </div>
                <h3>Video Call Games</h3>
                <p>Engage in fun video call games with other players. The more you play, the more you earn.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(34,197,94,0.15);color:var(--green);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>₹5 Per Referral</h3>
                <p>Share your unique referral code and earn ₹5 for every friend who joins. They get ₹5 too!</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(6,182,212,0.15);color:var(--cyan);">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Instant UPI Withdraw</h3>
                <p>Withdraw your earnings directly to any UPI ID. No complicated processes, just real cash.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(168,85,247,0.15);color:var(--purple);">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3>Secure Payments</h3>
                <p>All transactions are encrypted and processed through trusted gateways like Cashfree.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(245,158,11,0.15);color:var(--warning);">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Add Money Easily</h3>
                <p>Top up your wallet via UPI, cards, or net banking through our secure payment system.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(239,68,68,0.15);color:var(--danger);">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Got issues? Our support team is available round the clock to help you with any problems.</p>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how">
        <div class="section-label">How It Works</div>
        <h2 class="section-title">Start Earning in 3 Steps</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <div>
                    <h4>Download & Register</h4>
                    <p>Download the CashOrbit APK and create your free account in seconds. You'll get a unique referral code instantly.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div>
                    <h4>Share & Earn Referral Bonus</h4>
                    <p>Share your referral code with friends. When they sign up using your code, both of you get ₹5 bonus added to wallet instantly.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div>
                    <h4>Withdraw to UPI</h4>
                    <p>Once you have ₹50 or more in your wallet, request a withdrawal to any UPI ID. Money arrives after admin approval.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">₹5L+</div>
                <div class="stat-label">Total Paid Out</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">₹5</div>
                <div class="stat-label">Per Referral</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Available</div>
            </div>
        </div>
    </section>

    <!-- Referral Highlight Section -->
    <section class="referral-section">
        <div class="referral-box">
            <div class="referral-left">
                <h2>Refer Friends,<br><span class="gradient-text">Earn ₹5 Each</span></h2>
                <p>Our referral system rewards both the referrer and the new user. The more you share, the more you earn — it's that simple.</p>
                <ul class="referral-features">
                    <li><i class="fas fa-check-circle"></i> You earn ₹5 for every friend who joins</li>
                    <li><i class="fas fa-check-circle"></i> Your friend also gets ₹5 welcome bonus</li>
                    <li><i class="fas fa-check-circle"></i> No limit on number of referrals</li>
                    <li><i class="fas fa-check-circle"></i> Bonus added instantly to wallet</li>
                    <li><i class="fas fa-check-circle"></i> Track earnings in real-time</li>
                </ul>
                <a href="app.apk" class="btn-primary-custom" style="padding:14px 30px;font-size:15px;">
                    <i class="fas fa-rocket"></i> Start Referring
                </a>
            </div>
            <div class="referral-right">
                <div class="referral-demo-card">
                    <div class="label">Your Referral Code</div>
                    <div class="code">COAB3K7X</div>
                    <div class="earn">
                        <span class="earn-label">You Earn</span>
                        <span class="earn-value">+₹5.00</span>
                    </div>
                </div>
                <div class="referral-demo-card">
                    <div class="label">Friend Gets</div>
                    <div class="code" style="color:var(--accent-light);font-size:16px;letter-spacing:1px;">Welcome Bonus</div>
                    <div class="earn">
                        <span class="earn-label">Friend Earns</span>
                        <span class="earn-value small">+₹5.00</span>
                    </div>
                </div>
                <div class="referral-demo-card" style="border-color:rgba(34,197,94,0.2);">
                    <div class="label">If You Refer 100 Friends</div>
                    <div class="earn" style="border:none;margin:0;padding:0;">
                        <span class="earn-label">Total Earnings</span>
                        <span class="earn-value" style="font-size:24px;">₹500.00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="section-label">FAQ</div>
        <h2 class="section-title">Frequently Asked Questions</h2>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                How do I earn money on CashOrbit?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>You earn money through referral bonuses (₹5 per friend who joins using your code) and by playing video call games. Both you and your referred friend receive the bonus instantly.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                What is the minimum withdrawal amount?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>The minimum withdrawal amount is ₹50. Once your wallet balance reaches ₹50, you can request a withdrawal to any UPI ID.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                How long does withdrawal take?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>Withdrawal requests are reviewed by our admin team. Once approved, the money is sent to your UPI ID. Typically this takes a few hours during business hours.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                Is CashOrbit safe to use?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>Absolutely. All payments are processed through Cashfree, a RBI-compliant payment gateway. Your data is encrypted and we never share your information with third parties.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                How do I add money to my wallet?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>You can add money through UPI, debit cards, credit cards, or net banking. The amount is added to your wallet instantly after successful payment.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                Is there a limit on referrals?
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <p>No, there is no limit! You can refer as many friends as you want and earn ₹5 for each successful referral. The more you share, the more you earn.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-box">
            <h2>Ready to Start <span class="gradient-text">Earning</span>?</h2>
            <p>Download CashOrbit now and turn your free time into real money. It only takes 30 seconds to get started.</p>
            <a href="app.apk" class="btn-primary-custom" style="position:relative;">
                <i class="fas fa-download"></i> Download CashOrbit APK
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-left">
                <a href="#" class="footer-brand">
                    <div class="footer-brand-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <span>CashOrbit</span>
                </a>
                <span class="footer-copyright">
                    &copy; <?= date('Y') ?> CashOrbit. All rights reserved.
                </span>
            </div>
            <div class="footer-right">
                <a href="#features" class="footer-link">Features</a>
                <a href="#faq" class="footer-link">FAQ</a>
                <a href="admin/login.php" class="footer-admin">
                    <i class="fas fa-lock"></i> Admin
                </a>
            </div>
        </div>
    </footer>

    <script>
        // ========== NAVBAR SCROLL EFFECT ==========
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ========== MOBILE MENU ==========
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        let menuOpen = false;

        mobileMenuBtn.addEventListener('click', function() {
            menuOpen = !menuOpen;
            mobileMenu.classList.toggle('show', menuOpen);
            mobileMenuBtn.innerHTML = menuOpen
                ? '<i class="fas fa-times"></i>'
                : '<i class="fas fa-bars"></i>';
        });

        function closeMobileMenu() {
            menuOpen = false;
            mobileMenu.classList.remove('show');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }

        // ========== FAQ TOGGLE ==========
        function toggleFaq(button) {
            const item = button.parentElement;
            const isActive = item.classList.contains('active');

            // Close all
            document.querySelectorAll('.faq-item').forEach(function(el) {
                el.classList.remove('active');
            });

            // Open clicked if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        }

        // ========== FLOATING PARTICLES ==========
        (function createParticles() {
            const container = document.getElementById('particles');
            var particleCount = 25;

            // Reduce particles on mobile
            if (window.innerWidth < 768) {
                particleCount = 10;
            }

            for (var i = 0; i < particleCount; i++) {
                var particle = document.createElement('div');
                particle.classList.add('particle');
                particle.style.left = Math.random() * 100 + '%';
                particle.style.width = (Math.random() * 3 + 1) + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDuration = (Math.random() * 15 + 10) + 's';
                particle.style.animationDelay = (Math.random() * 15) + 's';

                var colors = ['#818cf8', '#06b6d4', '#22c55e', '#a855f7'];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];

                container.appendChild(particle);
            }
        })();

        // ========== SMOOTH SCROLL FOR ANCHOR LINKS ==========
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var targetId = this.getAttribute('href');
                if (targetId === '#') return;

                var target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    var offset = 80;
                    var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        });

        // ========== INTERSECTION OBSERVER FOR FADE-IN ==========
        (function initScrollAnimations() {
            var animElements = document.querySelectorAll(
                '.feature-card, .step, .stat-item, .referral-box, .faq-item, .cta-box'
            );

            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -40px 0px'
                });

                animElements.forEach(function(el, index) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(30px)';
                    el.style.transition = 'opacity 0.6s ease ' + (index % 6) * 0.08 + 's, transform 0.6s ease ' + (index % 6) * 0.08 + 's';
                    observer.observe(el);
                });
            } else {
                // Fallback for old browsers
                animElements.forEach(function(el) {
                    el.style.opacity = '1';
                    el.style.transform = 'none';
                });
            }
        })();
    </script>

</body>
</html>
