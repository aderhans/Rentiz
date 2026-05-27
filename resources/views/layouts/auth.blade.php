<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rentiz') — Marketplace Penyewaan Barang</title>
    <meta name="description" content="Rentiz — Platform marketplace penyewaan barang terpercaya.">
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
            --admin-light: #EDE9FE;
            --bg: #F8FAFC;
            --white: #FFFFFF;
            --text: #1E293B;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --border: #E2E8F0;
            --border-focus: rgba(37, 99, 235, 0.3);
            --success: #059669;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.08);
            --radius: 10px;
            --radius-lg: 14px;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', 'Inter', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .navbar-brand-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-brand-icon svg { width: 18px; height: 18px; fill: white; }

        .navbar-brand-text {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
        }

        .navbar-back {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 0.83rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 200ms ease;
        }

        .navbar-back:hover {
            background: var(--bg);
            color: var(--text);
        }

        .navbar-back svg { width: 16px; height: 16px; }

        /* ── Main ── */
        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            box-shadow: var(--shadow-md);
        }

        /* ── Role Badge (header) ── */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .role-badge svg { width: 14px; height: 14px; }

        .role-badge.penyedia { background: var(--primary-light); color: var(--primary); }
        .role-badge.penyewa  { background: var(--teal-light); color: var(--teal); }
        .role-badge.admin    { background: var(--admin-light); color: var(--admin-color); }

        .auth-card-header {
            margin-bottom: 1.75rem;
        }

        .auth-card-header h1 {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.35rem;
        }

        .auth-card-header p {
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        /* ── Form ── */
        .form-group { margin-bottom: 1.1rem; }

        .form-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .input-wrapper { position: relative; }

        .input-wrapper .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted);
            pointer-events: none;
            transition: color 200ms;
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
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 2px;
        }

        .password-toggle:hover { color: var(--text-secondary); }
        .password-toggle svg { width: 18px; height: 18px; }

        /* ── Password Strength ── */
        .password-strength { margin-top: 0.4rem; }

        .strength-bar {
            height: 3px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .strength-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 250ms, background 250ms;
            width: 0;
        }

        .strength-text {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        /* ── Checkbox ── */
        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            cursor: pointer;
        }

        .form-check input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 17px;
            height: 17px;
            min-width: 17px;
            margin-top: 1px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: var(--white);
            cursor: pointer;
            transition: all 150ms;
            position: relative;
        }

        .form-check input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .form-check input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .form-check-label {
            font-size: 0.84rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .form-check-label a {
            color: var(--primary);
            text-decoration: none;
        }

        .form-check-label a:hover { text-decoration: underline; }

        /* ── Row between ── */
        .form-row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        /* ── Buttons ── */
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius);
            color: white;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 200ms;
            position: relative;
        }

        .btn-primary:hover { filter: brightness(0.92); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .btn-primary span {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-primary span svg { width: 18px; height: 18px; }

        /* Role-specific button colors */
        .btn-primary.penyedia { background: var(--primary); }
        .btn-primary.penyewa  { background: var(--teal); }
        .btn-primary.admin    { background: var(--admin-color); }

        /* Loading */
        .btn-primary.loading span { opacity: 0; }
        .btn-primary.loading::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 20px; height: 20px;
            margin: -10px 0 0 -10px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Errors ── */
        .form-error {
            font-size: 0.78rem;
            color: var(--danger);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-error svg { width: 13px; height: 13px; min-width: 13px; }

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid #FECACA;
            border-radius: var(--radius);
            padding: 0.75rem 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.84rem;
            color: var(--danger);
        }

        .alert-error svg { width: 18px; height: 18px; min-width: 18px; }

        /* ── Footer ── */
        .auth-footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .auth-main { padding: 1.25rem; }
            .auth-card { padding: 1.5rem; }
            .navbar { padding: 0 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar">
        <a href="{{ url('/') }}" class="navbar-brand">
            <div class="navbar-brand-icon" style="background: {{ $role === 'penyedia' ? 'var(--primary)' : ($role === 'admin' ? 'var(--admin-color)' : 'var(--teal)') }};">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                </svg>
            </div>
            <span class="navbar-brand-text">Rentiz</span>
        </a>

        <a href="{{ url('/') }}" class="navbar-back">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </nav>

    <main class="auth-main">
        @yield('content')
    </main>

    <script>
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

        var forms = document.querySelectorAll('form');
        for (var i = 0; i < forms.length; i++) {
            forms[i].addEventListener('submit', function() {
                var btn = this.querySelector('.btn-primary');
                if (btn) btn.classList.add('loading');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
