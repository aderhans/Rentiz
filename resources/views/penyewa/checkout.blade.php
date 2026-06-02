@extends('layouts.app')

@section('title', 'Checkout Pesanan')
@section('breadcrumb', 'Checkout')

@push('styles')
<style>
    .checkout-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 900px) {
        .checkout-layout { grid-template-columns: 1fr; }
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Left Panel */
    .checkout-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
    }

    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid var(--card-border);
        border-radius: var(--radius-sm);
        font-family: var(--font-body);
        font-size: 0.9rem;
        color: var(--text);
        outline: none;
        transition: border-color 200ms;
    }

    .form-input:focus {
        border-color: var(--color-penyewa);
    }

    .item-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .item-card {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        background: #f8fafc;
    }

    .item-img {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        background: #e2e8f0;
    }

    .item-info { flex: 1; }
    
    .item-name {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .item-price {
        font-family: var(--font-heading);
        font-weight: 700;
        color: var(--color-penyewa);
    }

    /* Right Panel (Summary) */
    .summary-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: calc(var(--header-h) + 1.5rem);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    .summary-row.total {
        border-top: 1px dashed var(--card-border);
        padding-top: 1rem;
        margin-top: 0.5rem;
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text);
    }

    .btn-pay {
        width: 100%;
        padding: 0.85rem;
        background: var(--color-penyewa);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-family: var(--font-body);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 150ms;
        margin-top: 1.5rem;
    }

    .btn-pay:hover {
        background: #0f766e;
    }
    
    .btn-pay:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <a href="{{ route('penyewa.keranjang') }}" style="display:inline-flex;align-items:center;gap:5px;font-size:0.85rem;color:var(--text-secondary);text-decoration:none;margin-bottom:0.5rem;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Keranjang
    </a>
    <h1 class="page-title">💳 Checkout Pesanan</h1>
    <p class="page-subtitle">Lengkapi detail penyewaan kamu</p>
</div>

<div class="checkout-layout">
    
    {{-- Left Form --}}
    <div>
        @if ($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                <strong style="font-weight: 700;">Gagal membuat pesanan:</strong>
                <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                <strong style="font-weight: 700;">Gagal:</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Removed global date selection since it's now handled in the cart --}}

        <div class="checkout-card">
            <h2 class="section-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--color-penyewa);"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Barang dari: {{ $penyedia->name }}
            </h2>
            <div class="item-list">
                @php 
                    $totalHargaDasar = 0; 
                    $totalDurasiSummary = 0;
                    $totalSubtotal = 0;
                @endphp
                @foreach($items as $item)
                    @php 
                        $primaryPhoto = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
                        if (!$primaryPhoto && $item->barang->fotos) $primaryPhoto = $item->barang->fotos->first();
                        
                        $mulai = \Carbon\Carbon::parse($item->tanggal_mulai)->startOfDay();
                        $selesai = \Carbon\Carbon::parse($item->tanggal_selesai)->startOfDay();
                        $durasi = $mulai->diffInDays($selesai);
                        if($durasi == 0) $durasi = 1;
                        
                        $subtotal = $item->barang->harga_per_hari * $durasi;
                        $totalSubtotal += $subtotal;
                        $totalDurasiSummary += $durasi;
                    @endphp
                    <div class="item-card">
                        @if($primaryPhoto)
                            <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" class="item-img" alt="{{ $item->barang->nama }}">
                        @else
                            <div class="item-img" style="display:flex;align-items:center;justify-content:center;">
                                <svg style="width: 30px; height: 30px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="item-info">
                            <div class="item-name">{{ $item->barang->nama }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Lokasi: {{ $item->barang->kota }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem; display:flex; gap:10px;">
                                <span>Dari: <strong>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</strong></span>
                                <span>Sampai: <strong>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</strong></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                                <div class="item-price">Rp {{ number_format($item->barang->harga_per_hari, 0, ',', '.') }} <span style="font-size:0.75rem;font-weight:400;color:var(--text-muted);">/hari x {{ $durasi }} hr</span></div>
                                <div style="font-weight:700; color:var(--text);">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Summary --}}
    <div>
        <div class="summary-card">
            <h2 class="section-title" style="font-size:1.05rem;">Ringkasan Pembayaran</h2>
            
            <div class="summary-row">
                <span>Total Harga Sewa</span>
                <span style="font-weight:600;color:var(--text);">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Total Durasi Sewa</span>
                <span style="font-weight:600;color:var(--text);">{{ $totalDurasiSummary }} Hari</span>
            </div>
            <div class="summary-row">
                <span>Biaya Layanan</span>
                <span style="font-weight:600;color:var(--text);">Rp 2.000</span>
            </div>

            <div class="summary-row total">
                <span>Total Pembayaran</span>
                <span style="color:var(--color-penyewa);">Rp {{ number_format($totalSubtotal + 2000, 0, ',', '.') }}</span>
            </div>

            <form action="{{ route('penyewa.keranjang.checkout.proses') }}" method="POST" style="position: relative; z-index: 10;">
                @csrf
                @foreach($items as $item)
                    <input type="hidden" name="keranjang_ids[]" value="{{ $item->id }}">
                @endforeach
                <button type="submit" class="btn-pay" style="position: relative; z-index: 20; cursor: pointer;">
                    Buat Pesanan
                </button>
            </form>
            <p style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:1rem;position: relative;z-index: 10;">
                Dengan menekan tombol, Anda menyetujui Syarat & Ketentuan.
            </p>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Tanggal dan durasi sudah ditangani di server-side
</script>
@endpush
