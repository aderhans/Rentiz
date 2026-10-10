<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Email - Rentiz</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAF7;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: #1E293B;
        }
        .notice-card {
            background: white;
            padding: 3.5rem 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            max-width: 450px;
            width: 100%;
            text-align: center;
            border: 1px solid #E2E8F0;
        }
        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: #EFF6FF;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrapper svg { width: 34px; height: 34px; color: #2563EB; }
        h2 { font-family: 'Outfit', sans-serif; margin: 0 0 0.75rem; font-size: 1.75rem; color: #0F172A; }
        p { color: #64748B; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; }
        .btn-submit {
            display: inline-block;
            width: 100%;
            background: #2563EB;
            color: white;
            text-decoration: none;
            border: none;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 200ms;
        }
        .btn-submit:hover { background: #1D4ED8; }
        .footer-note {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #94A3B8;
        }
    </style>
</head>
<body>

    <div class="notice-card">
        <div class="icon-wrapper">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        
        <h2>Cek Email Anda</h2>
        <p>Kami telah mengirimkan sebuah tautan ajaib (Magic Link) ke email Anda. Silakan buka kotak masuk Anda dan klik tautan tersebut untuk memverifikasi akun Anda.</p>

        <a href="https://mail.google.com" target="_blank" class="btn-submit">Buka Gmail Sekarang</a>
        
        <div class="footer-note">
            Tautan tersebut hanya berlaku selama <strong>5 Menit</strong>. Jika lewat dari itu, pendaftaran Anda akan dibatalkan otomatis oleh sistem.
        </div>
    </div>
</body>
</html>
