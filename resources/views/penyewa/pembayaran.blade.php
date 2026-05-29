@extends('layouts.app')

@section('title', 'Pembayaran')
@section('breadcrumb', 'Pembayaran')

@push('styles')
<style>
    .payment-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .payment-layout { grid-template-columns: 1fr; }
    }

    /* Method cards */
    .method-card {
        border: 2px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 1rem 1.1rem;
        cursor: pointer;
        transition: border-color 200ms, background 200ms;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 0.75rem;
    }
    .method-card:hover { border-color: var(--color-penyewa); }
    .method-card.selected { border-color: var(--color-penyewa); background: rgba(13,148,136,0.04); }
    .method-icon {
        width: 44px; height: 44px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .method-info { flex: 1; }
    .method-name { font-weight: 700; font-size: 0.88rem; color: var(--text); }
    .method-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }
    .method-radio {
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: border-color 200ms;
    }
    .method-card.selected .method-radio {
        border-color: var(--color-penyewa);
        background: var(--color-penyewa);
    }
    .method-card.selected .method-radio::after {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: white;
    }

    /* Virtual account detail */
    .va-detail {
        background: var(--content-bg);
        border-radius: var(--radius-sm);
        padding: 0.85rem 1rem;
        margin-top: 0.75rem;
        display: none;
    }
    .va-detail.show { display: block; }
    .va-number {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text);
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .copy-btn {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.76rem;
        font-weight: 600;
        cursor: pointer;
        color: var(--color-penyewa);
        font-family: var(--font-body);
        transition: all 150ms;
    }
    .copy-btn:hover { background: var(--color-penyewa); color: white; border-color: var(--color-penyewa); }

    /* Saldo card */
    .saldo-card {
        background: linear-gradient(135deg, #0D9488 0%, #065f52 100%);
        border-radius: var(--radius-xl);
        padding: 1.5rem 1.4rem;
        color: white;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .saldo-card::before {
        content:'';
        position:absolute;
        top:-30px; right:-30px;
        width:130px; height:130px;
        border-radius:50%;
        background:rgba(255,255,255,0.06);
        pointer-events:none;
    }
    .saldo-label { font-size:0.8rem; color:rgba(255,255,255,0.7); margin-bottom:4px; }
    .saldo-amount {
        font-family: var(--font-heading);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.85rem;
    }
    .saldo-actions { display: flex; gap: 0.6rem; }
    .saldo-btn {
        flex: 1;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        border: 1.5px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font-body);
        transition: background 150ms;
    }
    .saldo-btn:hover { background: rgba(255,255,255,0.2); }

    /* Transaction list */
    .txn-row {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 150ms;
    }
    .txn-row:last-child { border-bottom: none; }
    .txn-row:hover { background: #FAFBFF; }
    .txn-icon {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .txn-info { flex: 1; min-width: 0; }
    .txn-name { font-weight: 600; font-size: 0.85rem; color: var(--text); }
    .txn-date { font-size: 0.74rem; color: var(--text-muted); margin-top: 1px; }
    .txn-amount { font-weight: 700; font-size: 0.9rem; flex-shrink: 0; }

    /* Countdown timer */
    .countdown {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 0.75rem;
    }
    .countdown-item {
        text-align: center;
        background: var(--content-bg);
        border-radius: var(--radius-sm);
        padding: 0.5rem 0.75rem;
        min-width: 55px;
    }
    .countdown-val {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--danger);
    }
    .countdown-label { font-size: 0.65rem; color: var(--text-muted); }

    /* Toast */
    .pay-toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem;
        background: var(--color-penyewa); color: white;
        padding: 0.75rem 1.25rem; border-radius: var(--radius-sm);
        font-size: 0.86rem; font-weight: 600;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        z-index: 9999;
        transform: translateY(100px); opacity: 0;
        transition: all 300ms;
    }
    .pay-toast.show { transform: translateY(0); opacity: 1; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">💳 Pembayaran</h1>
    <p class="page-subtitle">Kelola metode pembayaran dan riwayat transaksi keuangan kamu</p>
</div>

<div class="payment-layout">

    {{-- ── Kiri: Metode & Tagihan ── --}}
    <div>

        {{-- Tagihan Pending --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header">
                <span class="card-title">⏳ Menunggu Pembayaran</span>
                <span class="badge badge-warning">1 Tagihan</span>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:flex-start;gap:1rem;padding:1rem;background:var(--warning-bg);border-radius:var(--radius-sm);border:1px dashed var(--warning);">
                    <div style="font-size:2rem;">🚁</div>
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:0.92rem;color:var(--text);">DJI Mini 3 Pro Combo</div>
                        <div style="font-size:0.77rem;color:var(--text-muted);margin-top:2px;">Dari: Drone Indo · 25–26 Mei 2026 · 1 hari</div>
                        <div style="font-family:var(--font-heading);font-size:1.2rem;font-weight:700;color:var(--warning);margin-top:6px;">Rp 280.000</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.74rem;color:var(--text-muted);margin-bottom:6px;">Bayar sebelum:</div>
                        <div class="countdown" id="countdown" style="justify-content:flex-end;">
                            <div class="countdown-item"><div class="countdown-val" id="cd-h">01</div><div class="countdown-label">Jam</div></div>
                            <div class="countdown-item"><div class="countdown-val" id="cd-m">47</div><div class="countdown-label">Menit</div></div>
                            <div class="countdown-item"><div class="countdown-val" id="cd-s">22</div><div class="countdown-label">Detik</div></div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <p style="font-size:0.82rem;font-weight:600;color:var(--text);margin-bottom:0.75rem;">Pilih Metode Pembayaran:</p>

                    <div class="method-card selected" id="m-transfer" onclick="selectMethod('transfer', this)">
                        <div class="method-icon" style="background:#EFF6FF;">🏦</div>
                        <div class="method-info">
                            <div class="method-name">Transfer Bank</div>
                            <div class="method-sub">BCA, Mandiri, BNI, BRI</div>
                        </div>
                        <div class="method-radio"></div>
                    </div>
                    <div class="va-detail show" id="va-bca">
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:5px;">Virtual Account BCA</div>
                        <div class="va-number">
                            1234 5678 9012 3456
                            <button class="copy-btn" onclick="copyVA()">Salin</button>
                        </div>
                        <div style="font-size:0.73rem;color:var(--text-muted);margin-top:5px;">Berlaku hingga 25 Mei 2026, 20:30 WIB</div>
                    </div>

                    <div class="method-card" id="m-ewallet" onclick="selectMethod('ewallet', this)">
                        <div class="method-icon" style="background:#F0FDF4;">📱</div>
                        <div class="method-info">
                            <div class="method-name">E-Wallet</div>
                            <div class="method-sub">GoPay, OVO, Dana, ShopeePay</div>
                        </div>
                        <div class="method-radio"></div>
                    </div>

                    <div class="method-card" id="m-qris" onclick="selectMethod('qris', this)">
                        <div class="method-icon" style="background:#FFF7ED;">⬛</div>
                        <div class="method-info">
                            <div class="method-name">QRIS</div>
                            <div class="method-sub">Scan QR dengan semua e-wallet</div>
                        </div>
                        <div class="method-radio"></div>
                    </div>

                    <button class="btn btn-primary-teal" style="width:100%;margin-top:0.5rem;justify-content:center;" onclick="showToast('✅ Konfirmasi pembayaran berhasil dikirim! Tim kami akan memverifikasi dalam 1x24 jam.')">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Konfirmasi Sudah Bayar
                    </button>
                </div>
            </div>
        </div>

        {{-- Riwayat Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Riwayat Pembayaran</span>
                <a href="{{ route('penyewa.riwayat-sewa') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            @php
            $payments = [
                ['icon'=>'📷','bg'=>'#EEF2FF','name'=>'Sony A7III + Lensa Kit','date'=>'21 Mei 2026','amount'=>'1.050.000','type'=>'debit','status'=>'success','label'=>'Sukses'],
                ['icon'=>'🎮','bg'=>'#F0F9FF','name'=>'PS5 + 2 Controller','date'=>'18 Mei 2026','amount'=>'240.000','type'=>'debit','status'=>'success','label'=>'Sukses'],
                ['icon'=>'⭐','bg'=>'#FFFBEB','name'=>'Cashback Promo Mei','date'=>'15 Mei 2026','amount'=>'25.000','type'=>'credit','status'=>'success','label'=>'Cashback'],
                ['icon'=>'📽️','bg'=>'#FFF7ED','name'=>'Proyektor 4K Epson','date'=>'10 Mei 2026','amount'=>'200.000','type'=>'debit','status'=>'success','label'=>'Sukses'],
                ['icon'=>'🚵','bg'=>'#F0FDF4','name'=>'Refund - Sepeda MTB','date'=>'6 Mei 2026','amount'=>'160.000','type'=>'credit','status'=>'success','label'=>'Refund'],
            ];
            @endphp
            @foreach($payments as $p)
            <div class="txn-row">
                <div class="txn-icon" style="background:{{ $p['bg'] }};">{{ $p['icon'] }}</div>
                <div class="txn-info">
                    <div class="txn-name">{{ $p['name'] }}</div>
                    <div class="txn-date">{{ $p['date'] }} · <span class="badge badge-{{ $p['status'] }}" style="font-size:0.65rem;">{{ $p['label'] }}</span></div>
                </div>
                <div class="txn-amount" style="color:{{ $p['type'] === 'credit' ? 'var(--success)' : 'var(--text)' }};">
                    {{ $p['type'] === 'credit' ? '+' : '-' }} Rp {{ $p['amount'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Kanan: Saldo & Ringkasan ── --}}
    <div>
        {{-- Saldo RentizPay --}}
        <div class="saldo-card">
            <div style="font-size:0.82rem;font-weight:600;color:rgba(255,255,255,0.75);margin-bottom:2px;">RentizPay</div>
            <div class="saldo-label">Saldo Kamu</div>
            <div class="saldo-amount">Rp 185.000</div>
            <div class="saldo-actions">
                <button class="saldo-btn" onclick="showToast('🏧 Fitur top-up sedang dalam pengembangan!')">
                    + Top Up
                </button>
                <button class="saldo-btn" onclick="showToast('↗️ Fitur tarik saldo segera hadir!')">
                    ↗ Tarik
                </button>
            </div>
        </div>

        {{-- Ringkasan Keuangan --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header">
                <span class="card-title">📊 Ringkasan Keuangan</span>
            </div>
            <div class="card-body">
                @php
                $summaryItems = [
                    ['label'=>'Total Pengeluaran Bulan Ini', 'value'=>'Rp 1.290.000', 'color'=>'var(--danger)', 'icon'=>'↓'],
                    ['label'=>'Total Refund Diterima', 'value'=>'Rp 185.000', 'color'=>'var(--success)', 'icon'=>'↑'],
                    ['label'=>'Cashback Terkumpul', 'value'=>'Rp 45.000', 'color'=>'var(--warning)', 'icon'=>'⭐'],
                    ['label'=>'Transaksi Bulan Ini', 'value'=>'4 transaksi', 'color'=>'var(--info)', 'icon'=>'#'],
                ];
                @endphp
                @foreach($summaryItems as $s)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:0.7rem 0;border-bottom:1px solid #F1F5F9;">
                    <div style="font-size:0.82rem;color:var(--text-secondary);">{{ $s['label'] }}</div>
                    <div style="font-weight:700;font-size:0.88rem;color:{{ $s['color'] }};">{{ $s['value'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Metode Tersimpan --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">💾 Metode Tersimpan</span>
                <button class="btn btn-outline btn-sm" onclick="showToast('➕ Fitur tambah metode pembayaran segera hadir!')">+ Tambah</button>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:0.85rem;padding:0.75rem;background:var(--content-bg);border-radius:var(--radius-sm);margin-bottom:0.75rem;">
                    <div style="width:36px;height:36px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">🏦</div>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:0.85rem;">BCA Virtual Account</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);">****3456 · Default</div>
                    </div>
                    <span class="badge badge-success" style="font-size:0.65rem;">Utama</span>
                </div>
                <div style="display:flex;align-items:center;gap:0.85rem;padding:0.75rem;background:var(--content-bg);border-radius:var(--radius-sm);">
                    <div style="width:36px;height:36px;background:#F0FDF4;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">📱</div>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:0.85rem;">GoPay</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);">+62 812-3456-7890</div>
                    </div>
                    <span class="badge badge-neutral" style="font-size:0.65rem;">Tersimpan</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pay-toast" id="pay-toast"></div>

@endsection

@push('scripts')
<script>
    // Countdown timer
    var totalSecs = 6442; // ~1 jam 47 menit
    function updateCountdown() {
        if (totalSecs <= 0) {
            document.getElementById('cd-h').textContent = '00';
            document.getElementById('cd-m').textContent = '00';
            document.getElementById('cd-s').textContent = '00';
            return;
        }
        var h = Math.floor(totalSecs / 3600);
        var m = Math.floor((totalSecs % 3600) / 60);
        var s = totalSecs % 60;
        document.getElementById('cd-h').textContent = String(h).padStart(2, '0');
        document.getElementById('cd-m').textContent = String(m).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(s).padStart(2, '0');
        totalSecs--;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Payment method selection
    function selectMethod(method, el) {
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        // Toggle VA detail
        var va = document.getElementById('va-bca');
        va.classList.toggle('show', method === 'transfer');
    }

    function copyVA() {
        navigator.clipboard.writeText('1234567890123456').catch(function() {});
        showToast('📋 Nomor VA berhasil disalin!');
    }

    function showToast(msg) {
        var toast = document.getElementById('pay-toast');
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(window._payToastTimer);
        window._payToastTimer = setTimeout(function() {
            toast.classList.remove('show');
        }, 3500);
    }
</script>
@endpush
