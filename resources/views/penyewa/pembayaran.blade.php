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
                <span class="badge badge-warning">{{ $tagihans->count() }} Tagihan</span>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($tagihans->isEmpty())
                    <div style="text-align:center; padding: 2rem 1rem; color: var(--text-muted);">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎉</div>
                        <p style="font-size: 0.9rem;">Tidak ada tagihan yang menunggu pembayaran.</p>
                    </div>
                @else
                    @foreach($tagihans as $tagihan)
                        <div style="padding: 1.25rem; border-bottom: 1px solid var(--card-border);">
                            <div style="display:flex; justify-content: space-between; margin-bottom: 0.75rem; flex-wrap:wrap; gap:0.5rem; align-items:flex-start;">
                                <div>
                                    <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                        <span style="font-weight:700; font-size:0.95rem; color:var(--text);">Pesanan #{{ substr($tagihan->pesanan_id, 0, 8) }}</span>
                                        <span class="countdown-badge" id="timer-{{ $tagihan->id }}" data-expired="{{ \Carbon\Carbon::parse($tagihan->expired_at)->toISOString() }}" style="display:inline-flex; align-items:center; gap:4px; font-size:0.75rem; font-weight:700; color:#DC2626; background:#FEF2F2; border:1px solid #FECACA; padding:2px 8px; border-radius:50px;">
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="timer-text">Memuat waktu...</span>
                                        </span>
                                    </div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                                        Jatuh Tempo: {{ \Carbon\Carbon::parse($tagihan->expired_at)->format('d M Y, H:i') }} WIB
                                    </div>
                                </div>
                                <div style="font-weight:700; color:var(--color-penyewa); font-size:1.15rem; text-align:right;">
                                    Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="font-size: 0.82rem; color:var(--text-secondary); margin-bottom: 1rem; background:var(--content-bg); padding:0.6rem 0.85rem; border-radius:var(--radius-sm);">
                                @if($tagihan->pesanan && $tagihan->pesanan->items)
                                    @foreach($tagihan->pesanan->items as $item)
                                        <div>📦 {{ $item->barang->nama ?? 'Barang' }} ({{ $item->durasi_hari }} Hari) · {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</div>
                                    @endforeach
                                @endif
                            </div>
                            <div style="display:flex; gap:0.75rem; align-items:center;">
                                <a href="{{ route('penyewa.pembayaran.gateway', $tagihan->id) }}" id="btn-pay-{{ $tagihan->id }}" class="btn btn-primary-teal" style="flex:2; width: 100%; justify-content:center; padding: 0.65rem; font-size:0.85rem; text-decoration:none; display:flex; align-items:center; gap:0.4rem;">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Bayar Sekarang
                                </a>
                                <form action="{{ route('penyewa.pembayaran.batalkan', $tagihan->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="width: 100%; justify-content:center; padding: 0.65rem; font-size:0.85rem; color:var(--danger); border-color:#FECACA; background:#FEF2F2;" onclick="return confirm('Yakin ingin membatalkan pesanan ini? Jadwal booking barang akan langsung dilepas.')">
                                        ✕ Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Riwayat Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Riwayat Pembayaran</span>
                <a href="{{ route('penyewa.riwayat-sewa') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div style="padding: 0;">
                @if($riwayats->isEmpty())
                    <div style="text-align:center; padding: 2rem 1rem; color: var(--text-muted);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">💸</div>
                        <p style="font-size: 0.85rem;">Belum ada riwayat transaksi.</p>
                    </div>
                @else
                    @foreach($riwayats as $riwayat)
                        @php
                            $firstItem = $riwayat->pesanan && $riwayat->pesanan->items ? $riwayat->pesanan->items->first() : null;
                            $namaBarang = $firstItem && $firstItem->barang ? $firstItem->barang->nama : 'Pembayaran Pesanan';
                        @endphp
                        <div class="txn-row">
                            <div class="txn-icon" style="background: {{ $riwayat->status == 'paid' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)' }}; color: {{ $riwayat->status == 'paid' ? '#10B981' : '#EF4444' }};">
                                @if($riwayat->status == 'paid') ✓ @else ✕ @endif
                            </div>
                            <div class="txn-info">
                                <div class="txn-name">{{ $namaBarang }}</div>
                                <div class="txn-date">{{ \Carbon\Carbon::parse($riwayat->updated_at)->format('d M Y H:i') }} · <span class="badge {{ $riwayat->status == 'paid' ? 'badge-success' : 'badge-danger' }}" style="font-size:0.65rem;">{{ ucfirst($riwayat->status) }}</span></div>
                            </div>
                            <div class="txn-amount" style="color: {{ $riwayat->status == 'paid' ? 'var(--text)' : '#EF4444' }}; font-weight:700;">
                                {{ $riwayat->status == 'paid' ? '-' : '' }}Rp {{ number_format($riwayat->jumlah, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- ── Kanan: Ringkasan ── --}}
    <div>

        {{-- Ringkasan Keuangan --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header">
                <span class="card-title">📊 Ringkasan Keuangan</span>
            </div>
            <div class="card-body">
                @php
                $summaryItems = [
                    ['label'=>'Total Pengeluaran Bulan Ini', 'value'=>'Rp ' . number_format($totalBulanIni, 0, ',', '.'), 'color'=>'var(--danger)', 'icon'=>'↓'],
                    ['label'=>'Transaksi Bulan Ini', 'value'=> $riwayats->where('status', 'paid')->where('updated_at', '>=', now()->startOfMonth())->count() . ' transaksi', 'color'=>'var(--info)', 'icon'=>'#'],
                    ['label'=>'Total Refund Diterima', 'value'=>'Rp ' . number_format($riwayats->where('status', 'refunded')->sum('jumlah'), 0, ',', '.'), 'color'=>'var(--text)', 'icon'=>'↑'],
                    ['label'=>'Cashback Terkumpul', 'value'=>'Rp 0', 'color'=>'var(--text)', 'icon'=>'⭐'],
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

        {{-- Jaminan Keamanan Sewa --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">🛡️ Jaminan Keamanan Sewa</span>
            </div>
            <div class="card-body" style="padding:1.15rem;">
                <div style="display:flex;align-items:flex-start;gap:0.7rem;margin-bottom:0.85rem;">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#10B981,#059669);display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;color:white;">✓</div>
                    <div>
                        <div style="font-weight:700;font-size:0.82rem;color:var(--text);margin-bottom:2px;">Dana Ditahan Sistem</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);line-height:1.45;">Uang Anda tidak langsung diteruskan ke penyedia. Dana ditahan aman oleh RentizPay selama proses sewa berjalan.</div>
                    </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.7rem;margin-bottom:0.85rem;">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#3B82F6,#2563EB);display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;color:white;">⚡</div>
                    <div>
                        <div style="font-weight:700;font-size:0.82rem;color:var(--text);margin-bottom:2px;">Verifikasi Otomatis & Instan</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);line-height:1.45;">Pembayaran via Virtual Account, QRIS, atau E-Wallet terverifikasi otomatis dalam hitungan detik.</div>
                    </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.7rem;margin-bottom:0.85rem;">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#F59E0B,#D97706);display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;color:white;">⏱</div>
                    <div>
                        <div style="font-weight:700;font-size:0.82rem;color:var(--text);margin-bottom:2px;">Batas Waktu 10 Menit</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);line-height:1.45;">Setiap tagihan memiliki batas waktu 10 menit. Jika tidak dibayar tepat waktu, pesanan otomatis dibatalkan dan tanggal sewa kembali tersedia.</div>
                    </div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:0.7rem;">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#8B5CF6,#7C3AED);display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;color:white;">🔒</div>
                    <div>
                        <div style="font-weight:700;font-size:0.82rem;color:var(--text);margin-bottom:2px;">Dispute & Perlindungan</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);line-height:1.45;">Jika terjadi masalah dengan barang yang disewa, Anda dapat mengajukan dispute dan dana akan dikembalikan sesuai kebijakan Rentiz.</div>
                    </div>
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

    // Hitung mundur live (Live Countdown Timer 10 Menit)
    function updateCountdowns() {
        var timers = document.querySelectorAll('.countdown-badge');
        timers.forEach(function(badge) {
            var expiredIso = badge.dataset.expired;
            if (!expiredIso) return;

            var expiredTime = new Date(expiredIso).getTime();
            var now = new Date().getTime();
            var distance = expiredTime - now;

            var textEl = badge.querySelector('.timer-text');
            var tagihanId = badge.id.replace('timer-', '');
            var payBtn = document.getElementById('btn-pay-' + tagihanId);

            if (distance <= 0) {
                textEl.textContent = 'Waktu Habis (Kadaluwarsa)';
                badge.style.color = '#64748B';
                badge.style.background = '#F1F5F9';
                badge.style.borderColor = '#CBD5E1';
                if (payBtn) {
                    payBtn.style.opacity = '0.5';
                    payBtn.style.cursor = 'not-allowed';
                    payBtn.style.pointerEvents = 'none';
                    payBtn.textContent = 'Pesanan Kadaluwarsa';
                    payBtn.removeAttribute('href');
                }
            } else {
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                var minStr = minutes < 10 ? '0' + minutes : minutes;
                var secStr = seconds < 10 ? '0' + seconds : seconds;
                textEl.textContent = minStr + ':' + secStr + ' tersisa';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    });
</script>
@endpush
