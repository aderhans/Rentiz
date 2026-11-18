<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — Rentiz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
            padding: 1.5rem;
        }

        /* Decorative background blobs */
        body::before {
            content: '';
            position: fixed;
            top: -10rem;
            right: -10rem;
            width: 36rem;
            height: 36rem;
            background: radial-gradient(circle, rgba(96,165,250,0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -10rem;
            left: -10rem;
            width: 36rem;
            height: 36rem;
            background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 460px;
            text-align: center;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }

        /* ---- Logo / Brand ---- */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(59,130,246,0.4);
        }
        .brand-icon svg { width: 1.3rem; height: 1.3rem; fill: white; }
        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }
        .brand-name span { color: #60a5fa; }

        /* ---- Email Icon ---- */
        .envelope-wrap {
            width: 5rem;
            height: 5rem;
            background: linear-gradient(135deg, rgba(59,130,246,0.25), rgba(29,78,216,0.25));
            border: 1px solid rgba(96,165,250,0.35);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse-ring 2.5s ease-in-out infinite;
        }
        .envelope-wrap svg { width: 2.2rem; height: 2.2rem; color: #60a5fa; }

        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(96,165,250,0.4); }
            50%       { box-shadow: 0 0 0 12px rgba(96,165,250,0); }
        }

        /* ---- Typography ---- */
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
        }
        .desc {
            font-size: 0.925rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            margin-bottom: 0.4rem;
        }
        .email-highlight {
            display: inline-block;
            background: rgba(96,165,250,0.15);
            border: 1px solid rgba(96,165,250,0.3);
            color: #93c5fd;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.2rem 0.75rem;
            border-radius: 999px;
            margin: 0.5rem 0 1.25rem;
            word-break: break-all;
        }

        /* ---- Alert Flash Messages ---- */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.6rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            text-align: left;
        }
        .alert-success { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #86efac; }
        .alert-info    { background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #93c5fd; }

        /* ---- Divider ---- */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 1.5rem 0;
        }

        /* ---- Buttons ---- */
        .btn {
            display: block;
            width: 100%;
            padding: 0.8rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            box-shadow: 0 4px 14px rgba(59,130,246,0.35);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,0.5); }
        .btn-primary:active { transform: translateY(0); }

        .btn-ghost {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.65);
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 0.75rem;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); color: white; }

        /* ---- Spam note ---- */
        .spam-note {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-top: 1rem;
        }
        .spam-note span { color: #fbbf24; }
    </style>
</head>
<body>

<div class="card">

    {{-- Brand --}}
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
            </svg>
        </div>
        <span class="brand-name">Rent<span>iz</span></span>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">ℹ {{ session('info') }}</div>
    @endif

    {{-- Envelope Icon --}}
    <div class="envelope-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-9.75 7.5-9.75-7.5"/>
        </svg>
    </div>

    <h1>Verifikasi Email Anda</h1>

    <p class="desc">Kami telah mengirimkan link verifikasi ke email:</p>
    <span class="email-highlight">{{ Auth::user()->email }}</span>

    <p class="desc">Silakan buka inbox atau folder <strong style="color:rgba(255,255,255,0.75)">Spam/Junk</strong> dan klik link verifikasi yang kami kirimkan.</p>

    <hr class="divider">

    {{-- Resend Button --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            🔁 &nbsp;Kirim Ulang Email Verifikasi
        </button>
    </form>

    {{-- Logout Button --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-ghost">
            ← Kembali &amp; Logout
        </button>
    </form>

    <p class="spam-note">
        <span>Tips:</span> Cek folder Spam/Junk jika email tidak muncul dalam 2 menit.
    </p>

</div>

</body>
</html>
