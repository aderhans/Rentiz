<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Akun Rentiz</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #2563eb;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 24px 0;
            text-align: center;
        }
        .warning {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 24px;
            border: 1px solid #fecaca;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang di Rentiz!</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $userData['name'] }}</strong>,</p>
            <p>Terima kasih telah mendaftar di Rentiz. Untuk mulai menyewa atau menyewakan barang, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>
            
            <a href="{{ $verificationUrl }}" class="btn">Verifikasi Akun Saya</a>
            
            <div class="warning">
                <strong>Penting:</strong> Tautan ajaib (Magic Link) ini hanya berlaku selama <strong>5 Menit</strong>.
            </div>
            
            <p style="font-size: 14px; color: #6b7280;">Jika Anda tidak merasa mendaftar di Rentiz, silakan abaikan email ini.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Rentiz. All rights reserved.
        </div>
    </div>
</body>
</html>
