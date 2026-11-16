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
                <span class="badge badge-neutral">0 Tagihan</span>
            </div>
            <div class="card-body">
                <div style="text-align:center; padding: 2rem 1rem; color: var(--text-muted);">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
                    <p style="font-size: 0.9rem;">Tidak ada tagihan yang menunggu pembayaran.</p>
                </div>
            </div>
        </div>

        {{-- Riwayat Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Riwayat Pembayaran</span>
                <a href="{{ route('penyewa.riwayat-sewa') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div style="text-align:center; padding: 2rem 1rem; color: var(--text-muted);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💸</div>
                <p style="font-size: 0.85rem;">Belum ada riwayat transaksi.</p>
            </div>
        </div>
    </div>

    {{-- ── Kanan: Saldo & Ringkasan ── --}}
    <div>
        {{-- Saldo RentizPay --}}
        <div class="saldo-card">
            <div style="font-size:0.82rem;font-weight:600;color:rgba(255,255,255,0.75);margin-bottom:2px;">RentizPay</div>
            <div class="saldo-label">Saldo Kamu</div>
            <div class="saldo-amount">Rp 0</div>
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
                    ['label'=>'Total Pengeluaran Bulan Ini', 'value'=>'Rp 0', 'color'=>'var(--text)', 'icon'=>'↓'],
                    ['label'=>'Total Refund Diterima', 'value'=>'Rp 0', 'color'=>'var(--text)', 'icon'=>'↑'],
                    ['label'=>'Cashback Terkumpul', 'value'=>'Rp 0', 'color'=>'var(--text)', 'icon'=>'⭐'],
                    ['label'=>'Transaksi Bulan Ini', 'value'=>'0 transaksi', 'color'=>'var(--text)', 'icon'=>'#'],
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
                <div style="text-align:center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                    Belum ada metode yang tersimpan.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pay-toast" id="pay-toast"></div>

@endsection

@push('scripts')
<script>
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
