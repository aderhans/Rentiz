<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rentiz — Marketplace Penyewaan Barang</title>
    <meta name="description" content="Rentiz — Platform marketplace penyewaan barang terpercaya. Sewakan atau temukan barang yang Anda butuhkan.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-light: #DBEAFE;
            --teal: #0D9488;
            --teal-dark: #0F766E;
            --teal-light: #CCFBF1;
            --admin-color: #7C3AED;

            --bg-cream: #FAFAF7;
            --bg-card: #FFFFFF;

            --white: #FFFFFF;
            --text: #1E293B;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --border: #E2E8F0;
            --border-focus: rgba(37, 99, 235, 0.3);
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --success: #059669;
            --success-bg: #ECFDF5;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.05);

            --radius: 10px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;

            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', 'Inter', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: var(--font-body);
            background: var(--bg-cream);
            color: var(--text);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Sembunyikan scrollbar bawaan browser untuk UI yang lebih rapi */
        ::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }
        * { -ms-overflow-style: none; scrollbar-width: none; }

        /* ── Split Layout ── */
        .layout-left {
            flex: 0 0 47%;
            position: relative;
            background-image: url('{{ asset('images/bg-rentiz.jpg') }}');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            color: white;
        }

        .layout-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(30,41,59,0.7) 100%);
        }

        .left-content {
            position: relative;
            z-index: 1;
            max-width: 400px;
        }

        .left-content h1 {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #FFFFFF, #E2E8F0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-content p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.6;
        }

        /* Trust badges */
        .trust-badges {
            display: flex;
            gap: 1.5rem;
            margin-top: 2.5rem;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .trust-icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .trust-icon svg { width: 16px; height: 16px; stroke: rgba(255,255,255,0.8); fill: none; }

        .trust-text {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.3;
        }

        .trust-text strong { color: rgba(255,255,255,0.95); display: block; font-size: 0.82rem; }

        .layout-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
            background: var(--bg-cream);
        }

        /* ── Gradient Blobs ── */
        .blob-1 {
            position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
        }

        .blob-2 {
            position: absolute; bottom: -50px; left: -50px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(13,148,136,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; pointer-events: none; z-index: 0;
        }

        /* ── Navbar ── */
        .navbar {
            padding: 1.5rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        .navbar-brand {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
        }

        .navbar-brand-icon {
            width: 36px; height: 36px; background: var(--text); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .navbar-brand-icon svg { width: 20px; height: 20px; fill: white; }

        .navbar-brand-text {
            font-family: var(--font-heading); font-size: 1.35rem; font-weight: 700; color: var(--text);
        }

        .navbar-admin {
            display: flex; align-items: center; gap: 6px; padding: 8px 16px;
            background: white; border: 1px solid var(--border); border-radius: 8px;
            color: var(--text-secondary); font-size: 0.85rem; font-weight: 500;
            text-decoration: none; transition: all 200ms ease;
        }

        .navbar-admin:hover {
            border-color: #7C3AED; color: #7C3AED;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1);
        }

        .navbar-admin svg { width: 16px; height: 16px; }

        /* ── Right Content ── */
        .right-content {
            flex: 1; display: flex; flex-direction: column;
            justify-content: center; padding: 1rem 4rem;
            position: relative; z-index: 10;
            max-width: 520px; margin: 0 auto; width: 100%;
        }

        /* ── Tab Switcher ── */
        .auth-tabs {
            display: flex;
            background: #F1F5F9;
            border-radius: var(--radius);
            padding: 4px;
            margin-bottom: 1.75rem;
            position: relative;
        }

        .auth-tab {
            flex: 1;
            padding: 0.6rem 1rem;
            text-align: center;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 8px;
            transition: all 250ms ease;
            position: relative;
            z-index: 1;
            border: none;
            background: transparent;
            font-family: var(--font-body);
        }

        .auth-tab:hover { color: var(--text); }

        .auth-tab.active {
            background: white;
            color: var(--text);
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        /* ── Auth Panels (form containers) ── */
        .auth-panel {
            display: none;
            animation: panelFade 300ms ease;
        }

        .auth-panel.active { display: block; }

        @keyframes panelFade {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .panel-header {
            margin-bottom: 1.5rem;
        }

        .panel-header h2 {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.3rem;
        }

        .panel-header p {
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        /* ── Form Styles ── */
        .form-group { margin-bottom: 1rem; }

        .form-label {
            display: block; font-size: 0.84rem; font-weight: 500;
            color: var(--text); margin-bottom: 0.4rem;
        }

        .input-wrapper { position: relative; }

        .input-wrapper .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: var(--text-muted);
            pointer-events: none; transition: color 200ms;
        }

        .form-input {
            width: 100%;
            padding: 0.7rem 0.85rem 0.7rem 2.5rem;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: var(--font-body);
            font-size: 0.88rem;
            outline: none;
            transition: border-color 200ms, box-shadow 200ms;
        }

        .form-input::placeholder { color: var(--text-muted); }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--border-focus);
        }

        .input-wrapper:focus-within .input-icon { color: var(--primary); }

        .password-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--text-muted);
            cursor: pointer; padding: 2px;
        }

        .password-toggle:hover { color: var(--text-secondary); }
        .password-toggle svg { width: 18px; height: 18px; }

        /* Password Strength */
        .password-strength { margin-top: 0.4rem; }

        .strength-bar {
            height: 3px; background: var(--border);
            border-radius: 3px; overflow: hidden; margin-bottom: 0.25rem;
        }

        .strength-bar-fill {
            height: 100%; border-radius: 3px;
            transition: width 250ms, background 250ms;
            width: 0;
        }

        .strength-text { font-size: 0.72rem; color: var(--text-muted); }

        /* Checkbox */
        .form-check {
            display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;
        }

        .form-check input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 18px; height: 18px; min-width: 18px; margin-top: 1px;
            border: 2px solid #94A3B8; border-radius: 4px;
            background: var(--white); cursor: pointer;
            transition: all 150ms; position: relative;
        }

        .form-check input[type="checkbox"]:checked {
            background: var(--primary); border-color: var(--primary);
        }

        .form-check input[type="checkbox"]:checked::after {
            content: ''; position: absolute; left: 5px; top: 2px;
            width: 4px; height: 8px; border: solid white;
            border-width: 0 2px 2px 0; transform: rotate(45deg);
        }

        .form-check-label { font-size: 0.84rem; color: var(--text-secondary); line-height: 1.4; }
        .form-check-label a { color: var(--primary); text-decoration: none; }
        .form-check-label a:hover { text-decoration: underline; }

        .form-row-between {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        /* Buttons */
        .btn-primary {
            width: 100%; padding: 0.75rem; border: none;
            border-radius: var(--radius); background: var(--primary);
            color: white; font-family: var(--font-body);
            font-size: 0.9rem; font-weight: 600; cursor: pointer;
            transition: all 200ms; position: relative;
        }

        .btn-primary:hover { filter: brightness(0.92); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .btn-primary span {
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        }

        .btn-primary span svg { width: 18px; height: 18px; }

        /* Loading */
        .btn-primary.loading span { opacity: 0; }
        .btn-primary.loading::after {
            content: ''; position: absolute; top: 50%; left: 50%;
            width: 20px; height: 20px; margin: -10px 0 0 -10px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: white; border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Errors */
        .form-error {
            font-size: 0.78rem; color: var(--danger); margin-top: 0.3rem;
            display: flex; align-items: center; gap: 4px;
        }

        .form-error svg { width: 13px; height: 13px; min-width: 13px; }

        .alert-error {
            background: var(--danger-bg); border: 1px solid #FECACA;
            border-radius: var(--radius); padding: 0.75rem 0.85rem;
            margin-bottom: 1.25rem; display: flex; align-items: center;
            gap: 0.6rem; font-size: 0.84rem; color: var(--danger);
        }

        .alert-error svg { width: 18px; height: 18px; min-width: 18px; }

        .alert-success {
            background: var(--success-bg); border: 1px solid #A7F3D0;
            border-radius: var(--radius); padding: 0.85rem 1.2rem;
            margin-bottom: 1.25rem; display: flex; align-items: center;
            gap: 0.75rem; font-size: 0.88rem; color: var(--success);
            box-shadow: 0 2px 8px rgba(5,150,105,0.1);
            transition: opacity 0.5s ease-out;
        }

        .alert-success svg { width: 18px; height: 18px; min-width: 18px; }

        /* Footer link */
        .auth-footer {
            text-align: center; margin-top: 1.25rem;
            font-size: 0.88rem; color: var(--text-secondary);
        }

        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .auth-footer a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 968px) {
            body { flex-direction: column; height: auto; overflow: auto; }
            .layout-right { overflow-y: visible; }
            .layout-left {
                flex: none; padding: 4rem 2rem; min-height: 280px;
                text-align: center; align-items: center;
            }
            .left-content { max-width: 100%; }
            .left-content h1 { font-size: 2.25rem; }
            .right-content { padding: 2rem; }
            .trust-badges { justify-content: center; }
        }

        @media (max-width: 600px) {
            .navbar { padding: 1rem 1.5rem; }
            .right-content { padding: 1rem 1.5rem; }
            .trust-badges { flex-direction: column; gap: 0.75rem; }
        }
    </style>
</head>
<body>

    {{-- Left Panel --}}
    <div class="layout-left">
        <div class="left-content">
            <h1>Sewa Barang Lebih Mudah.</h1>
            <p>Platform marketplace terpercaya. Temukan barang yang Anda butuhkan atau mulai sewakan barang Anda untuk tambahan penghasilan.</p>

            <div class="trust-badges">
                <div class="trust-item">
                    <div class="trust-icon">
                        <svg viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="trust-text"><strong>Aman</strong>Transaksi terlindungi</div>
                </div>
                <div class="trust-item">
                    <div class="trust-icon">
                        <svg viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="trust-text"><strong>1.200+</strong>Pengguna aktif</div>
                </div>
                <div class="trust-item">
                    <div class="trust-icon">
                        <svg viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div class="trust-text"><strong>4.8 ⭐</strong>Rating platform</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="layout-right">
        <div class="blob-1"></div>
        <div class="blob-2"></div>

        <nav class="navbar">
            <a href="{{ url('/') }}" class="navbar-brand">
                <div class="navbar-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                    </svg>
                </div>
                <span class="navbar-brand-text">Rentiz</span>
            </a>

            <a href="{{ route('login', ['role' => 'admin']) }}" class="navbar-admin">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Admin
            </a>
        </nav>

        <div class="right-content">
            @php
                // Deteksi tab aktif: jika ada error dari form sebelumnya, gunakan _form. Jika tidak, dari controller.
                $activeTab = old('_form', $activeTab ?? 'login');
            @endphp

            {{-- Tab Switcher --}}
            <div class="auth-tabs" id="auth-tabs">
                <button type="button" class="auth-tab {{ $activeTab === 'login' ? 'active' : '' }}" data-tab="login" id="tab-login">Masuk</button>
                <button type="button" class="auth-tab {{ $activeTab === 'register' ? 'active' : '' }}" data-tab="register" id="tab-register">Daftar Akun</button>
            </div>

            {{-- ═════════════════════════════════
                 LOGIN PANEL
            ═════════════════════════════════ --}}
            <div class="auth-panel {{ $activeTab === 'login' ? 'active' : '' }}" id="panel-login">
                <div class="panel-header">
                    <h2>Selamat Datang Kembali 👋</h2>
                    <p>Masukkan email dan password untuk melanjutkan</p>
                </div>

                @if (session('success'))
                <div class="alert-success" id="success-alert">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if ($activeTab === 'login' && $errors->any())
                <div class="alert-error" id="login-alert">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf
                    <input type="hidden" name="_form" value="login">

                    <div class="form-group">
                        <label class="form-label" for="login-email">Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" class="form-input" id="login-email" name="email" placeholder="nama@email.com" value="{{ old('_form') === 'login' ? old('email') : '' }}" required autocomplete="email" {{ $activeTab === 'login' ? 'autofocus' : '' }}>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="login-password">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input type="password" class="form-input" id="login-password" name="password" placeholder="Masukkan password" required autocomplete="current-password" style="padding-right: 2.75rem;">
                            <button type="button" class="password-toggle" onclick="togglePassword('login-password', this)" aria-label="Toggle password">
                                <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-row-between">
                        <label class="form-check" id="remember-check">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="form-check-label">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" id="login-submit">
                        <span>
                            Masuk
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </button>
                </form>

            </div>

            {{-- ═════════════════════════════════
                 REGISTER PANEL
            ═════════════════════════════════ --}}
            <div class="auth-panel {{ $activeTab === 'register' ? 'active' : '' }}" id="panel-register">
                <div class="panel-header">
                    <h2>Buat Akun Baru ✨</h2>
                    <p>Daftar gratis dan mulai sewa atau sewakan barang</p>
                </div>

                @if ($activeTab === 'register' && $errors->any())
                <div class="alert-error" id="register-alert">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf
                    <input type="hidden" name="_form" value="register">

                    {{-- Nama --}}
                    <div class="form-group">
                        <label class="form-label" for="register-name">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <input type="text" class="form-input" id="register-name" name="name" placeholder="Masukkan nama lengkap" value="{{ old('_form') === 'register' ? old('name') : '' }}" required autocomplete="name" {{ $activeTab === 'register' ? 'autofocus' : '' }}>
                        </div>
                        @error('name')
                            <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label class="form-label" for="register-email">Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" class="form-input" id="register-email" name="email" placeholder="nama@email.com" value="{{ old('_form') === 'register' ? old('email') : '' }}" required autocomplete="email">
                        </div>
                        @error('email')
                            <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-group">
                        <label class="form-label" for="register-phone">No. Handphone <span style="color: var(--text-muted); font-weight: 400;">(opsional)</span></label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <input type="tel" class="form-input" id="register-phone" name="phone" placeholder="08xxxxxxxxxx" value="{{ old('_form') === 'register' ? old('phone') : '' }}" autocomplete="tel">
                        </div>
                        @error('phone')
                            <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label class="form-label" for="register-password">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input type="password" class="form-input" id="register-password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password" style="padding-right: 2.75rem;">
                            <button type="button" class="password-toggle" onclick="togglePassword('register-password', this)" aria-label="Toggle password">
                                <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength">
                            <div class="strength-bar"><div class="strength-bar-fill" id="strength-bar-fill"></div></div>
                            <span class="strength-text" id="strength-text"></span>
                        </div>
                        @error('password')
                            <div class="form-error"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-group">
                        <label class="form-label" for="register-password-confirm">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <input type="password" class="form-input" id="register-password-confirm" name="password_confirmation" placeholder="Ulangi password Anda" required autocomplete="new-password" style="padding-right: 2.75rem;">
                            <button type="button" class="password-toggle" onclick="togglePassword('register-password-confirm', this)" aria-label="Toggle password">
                                <svg class="eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="eye-closed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-check" id="terms-check">
                            <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                            <span class="form-check-label">Saya setuju dengan <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a></span>
                        </label>
                        @error('terms')
                            <div class="form-error" style="margin-left: 1.5rem;"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary" id="register-submit">
                        <span>
                            Daftar Sekarang
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </span>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script>
        // ── Tab Switching ──
        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.auth-tab').forEach(function(t) { t.classList.remove('active'); });
            document.getElementById('tab-' + tab).classList.add('active');

            // Update panels
            document.querySelectorAll('.auth-panel').forEach(function(p) { p.classList.remove('active'); });
            document.getElementById('panel-' + tab).classList.add('active');

            // Focus first input
            var panel = document.getElementById('panel-' + tab);
            var firstInput = panel.querySelector('input:not([type="hidden"])');
            if (firstInput) firstInput.focus();
        }

        document.querySelectorAll('.auth-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                switchTab(this.getAttribute('data-tab'));
            });
        });

        // ── Password Toggle ──
        function togglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            var eyeOpen = btn.querySelector('.eye-open');
            var eyeClosed = btn.querySelector('.eye-closed');
            if (eyeOpen && eyeClosed) {
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClosed.style.display = isPassword ? 'block' : 'none';
            }
        }

        // ── Password Strength ──
        var passwordInput = document.getElementById('register-password');
        var strengthFill = document.getElementById('strength-bar-fill');
        var strengthText = document.getElementById('strength-text');

        if (passwordInput) {
            passwordInput.addEventListener('input', function () {
                var val = this.value;
                var score = 0;

                if (val.length === 0) {
                    strengthFill.style.width = '0';
                    strengthText.textContent = '';
                    return;
                }

                if (val.length >= 8) score++;
                if (val.length >= 12) score++;
                if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
                if (/\d/.test(val)) score++;
                if (/[^a-zA-Z0-9]/.test(val)) score++;

                if (score <= 1) {
                    strengthFill.style.width = '20%';
                    strengthFill.style.background = '#DC2626';
                    strengthText.style.color = '#DC2626';
                    strengthText.textContent = 'Lemah';
                } else if (score <= 2) {
                    strengthFill.style.width = '40%';
                    strengthFill.style.background = '#D97706';
                    strengthText.style.color = '#D97706';
                    strengthText.textContent = 'Cukup';
                } else if (score <= 3) {
                    strengthFill.style.width = '65%';
                    strengthFill.style.background = '#2563EB';
                    strengthText.style.color = '#2563EB';
                    strengthText.textContent = 'Bagus';
                } else {
                    strengthFill.style.width = '100%';
                    strengthFill.style.background = '#059669';
                    strengthText.style.color = '#059669';
                    strengthText.textContent = 'Kuat';
                }
            });
        }

        // ── Form Loading ──
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = this.querySelector('.btn-primary');
                if (btn) btn.classList.add('loading');
            });
        });
        // Auto-hide success alert
        var successAlert = document.getElementById('success-alert');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.opacity = '0';
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 500); // Wait for transition
            }, 4000); // Hide after 4 seconds
        }
    </script>
</body>
</html>
