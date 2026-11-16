@extends('layouts.app')

@section('title', 'Keranjang Sewa')
@section('breadcrumb', 'Keranjang')

@push('styles')
<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .store-group {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .store-header {
        background: #f8fafc;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--card-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .store-name {
        font-family: var(--font-heading);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cart-item {
        display: flex;
        padding: 1.25rem;
        border-bottom: 1px solid var(--card-border);
        gap: 1.25rem;
        align-items: center;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-img {
        width: 100px;
        height: 100px;
        border-radius: var(--radius-md);
        background: #e2e8f0;
        object-fit: cover;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .item-price {
        font-family: var(--font-heading);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--color-penyewa);
    }

    .item-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .btn-delete {
        background: none;
        border: none;
        color: var(--danger);
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: background 150ms;
    }
    
    .btn-delete:hover {
        background: #fef2f2;
    }

    .store-footer {
        padding: 1.25rem;
        background: white;
        border-top: 1px solid var(--card-border);
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .btn-checkout {
        background: var(--color-penyewa);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 150ms;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-checkout:hover {
        background: #0f766e;
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 1rem;
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">🛒 Keranjang Sewa</h1>
    <p class="page-subtitle">Pilih barang yang ingin kamu sewa berdasarkan penyedia</p>
</div>

<div class="cart-layout">
    @if(session('success'))
        <div style="background:var(--success);color:white;padding:1rem;border-radius:var(--radius-sm);margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background:var(--info);color:white;padding:1rem;border-radius:var(--radius-sm);margin-bottom:1rem;">
            {{ session('info') }}
        </div>
    @endif

    @forelse($grouped as $penyediaId => $items)
        @php
            $penyedia = $items->first()->barang->user;
        @endphp
        <div class="store-group">
            <div class="store-header">
                <div class="store-name">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--color-penyewa);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ $penyedia->name ?? 'Penyedia Tidak Diketahui' }}
                </div>
                <span class="badge badge-success">{{ count($items) }} Barang</span>
            </div>

            @foreach($items as $item)
                <div class="cart-item">
                    @php
                        $primaryPhoto = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
                        if (!$primaryPhoto && $item->barang->fotos) $primaryPhoto = $item->barang->fotos->first();
                    @endphp
                    @if($primaryPhoto)
                        <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" class="item-img" alt="{{ $item->barang->nama }}">
                    @else
                        <div class="item-img" style="display:flex;align-items:center;justify-content:center;">
                            <svg style="width: 40px; height: 40px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <div class="item-details">
                        <div class="item-name">{{ $item->barang->nama }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Lokasi: {{ $item->barang->kota }}</div>
                        <div class="item-price">Rp {{ number_format($item->barang->harga_per_hari, 0, ',', '.') }} <span style="font-size:0.8rem;color:var(--text-muted);font-weight:400;">/hari</span></div>
                    </div>

                    <div class="item-actions">
                        <form action="{{ route('penyewa.keranjang.hapus', $item->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini dari keranjang?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" title="Hapus">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="store-footer">
                <button class="btn-checkout" onclick="alert('Checkout untuk {{ $penyedia->name ?? 'Penyedia' }} (Dummy) \nNantinya akan dibawa ke halaman detail tanggal sewa dan pembayaran.')">
                    Lanjut Checkout
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>
    @empty
        <div class="empty-cart">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Keranjang Masih Kosong</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Cari barang yang kamu butuhkan dan tambahkan ke keranjang.</p>
            <a href="{{ route('penyewa.cari-barang') }}" class="btn-checkout" style="text-decoration: none;">
                Mulai Cari Barang
            </a>
        </div>
    @endforelse
</div>

@endsection
