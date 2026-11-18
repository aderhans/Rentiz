@extends('layouts.app')

@section('title', 'Wishlist Saya')
@section('breadcrumb', 'Wishlist')

@push('styles')
<style>
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.1rem;
    }
    .wl-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: box-shadow 220ms, transform 220ms;
        position: relative;
    }
    .wl-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }

    .wl-img {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        position: relative;
    }
    .remove-wl {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px; height: 30px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        color: #EF4444;
        font-size: 0.9rem;
        transition: all 160ms;
    }
    .remove-wl:hover { background: #FEE2E2; transform: scale(1.1); }

    .wl-avail-dot {
        position: absolute;
        bottom: 10px;
        left: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 50px;
        backdrop-filter: blur(8px);
    }
    .dot-green { background: rgba(5,150,105,0.12); color: var(--success); }
    .dot-red   { background: rgba(220,38,38,0.12);  color: var(--danger); }
    .avail-indicator {
        width: 7px; height: 7px;
        border-radius: 50%;
    }

    .wl-body { padding: 1rem 1.1rem; }
    .wl-name { font-weight: 700; font-size: 0.9rem; color: var(--text); margin-bottom: 3px; }
    .wl-seller { font-size: 0.76rem; color: var(--text-muted); margin-bottom: 0.7rem; }
    .wl-footer { display: flex; align-items: center; justify-content: space-between; }
    .wl-price { font-family: var(--font-heading); font-weight: 700; color: var(--color-penyewa); }
    .wl-price span { font-size: 0.72rem; font-weight: 400; color: var(--text-muted); }
    .wl-rating { font-size: 0.78rem; color: var(--warning); font-weight: 600; }
    .btn-sewa-wl {
        width: 100%;
        margin-top: 0.85rem;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        border: none;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font-body);
        transition: all 180ms;
    }
    .btn-sewa-wl.available { background: var(--color-penyewa); color: white; }
    .btn-sewa-wl.available:hover { background: #0F766E; }
    .btn-sewa-wl.unavailable { background: #F1F5F9; color: var(--text-muted); cursor: not-allowed; }

    /* Top bar */
    .wl-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .wl-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0.45rem 0.9rem;
        background: #FEE2E2;
        color: #EF4444;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .toast-notify {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: #EF4444;
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: var(--radius-sm);
        font-size: 0.86rem;
        font-weight: 600;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        z-index: 9999;
        transform: translateY(100px);
        opacity: 0;
        transition: all 300ms cubic-bezier(0.4,0,0.2,1);
    }
    .toast-notify.show { transform: translateY(0); opacity: 1; }
    .toast-notify.green { background: var(--color-penyewa); }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">❤️ Wishlist Saya</h1>
    <p class="page-subtitle">Barang-barang yang kamu simpan untuk disewa nanti</p>
</div>

<div class="wl-topbar">
    <div class="wl-count-chip">
        ♥ <span id="wl-count">{{ count($wishlists ?? []) }}</span> barang tersimpan
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('penyewa.cari-barang') }}" class="btn btn-primary-teal btn-sm">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari Barang Lain
        </a>
    </div>
</div>

<div class="wishlist-grid" id="wl-grid">
    @forelse($wishlists ?? [] as $w)
    @php
        $barang = $w->barang;
        $primaryPhoto = $barang && $barang->fotos ? $barang->fotos->where('is_primary', true)->first() : null;
        if (!$primaryPhoto && $barang && $barang->fotos) $primaryPhoto = $barang->fotos->first();
    @endphp
    @if($barang)
    <div class="wl-card" id="card-{{ $w->id }}">
        <div class="wl-img" style="background:#f1f5f9; overflow:hidden;">
            @if($primaryPhoto)
                <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <div style="font-size:2.5rem;">📦</div>
            @endif
            <button class="remove-wl" onclick="removeWishlist('{{ $barang->id }}', '{{ addslashes($barang->nama) }}', 'card-{{ $w->id }}')" title="Hapus dari wishlist">♥</button>
            <div class="wl-avail-dot {{ $barang->status == 'active' ? 'dot-green' : 'dot-red' }}">
                <div class="avail-indicator" style="background:{{ $barang->status == 'active' ? 'var(--success)' : 'var(--danger)' }};"></div>
                {{ $barang->status == 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
            </div>
        </div>
        <div class="wl-body">
            <div class="wl-name">{{ $barang->nama }}</div>
            <div class="wl-seller">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $barang->user->name ?? 'Penyedia' }} · {{ $barang->kota }}
            </div>
            <div class="wl-footer">
                <div class="wl-price">Rp {{ number_format($barang->harga_per_hari, 0, ',', '.') }}<span>/hari</span></div>
            </div>
            <a href="{{ route('penyewa.barang.detail', $barang->id) }}" class="btn-sewa-wl available" style="text-decoration:none; display:block; text-align:center;">
                Lihat Detail & Sewa
            </a>
        </div>
    </div>
    @endif
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Wishlist kosong</h3>
        <p>Anda belum menyimpan barang apapun ke dalam wishlist.</p>
    </div>
    @endforelse
</div>

<div class="toast-notify" id="wl-toast" role="alert"></div>

@endsection

@push('scripts')
<script>
    function removeWishlist(barangId, name, cardId) {
        fetch("{{ route('penyewa.wishlist.toggle') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barang_id: barangId })
        })
        .then(res => res.json())
        .then(data => {
            var card = document.getElementById(cardId);
            if (card) {
                card.style.transform = 'scale(0.85)';
                card.style.opacity = '0';
                card.style.transition = 'all 300ms';
                setTimeout(function() {
                    card.remove();
                    var countEl = document.getElementById('wl-count');
                    var current = parseInt(countEl.textContent) || 0;
                    if (current > 0) countEl.textContent = current - 1;
                }, 300);
            }
            showToast('red', '💔 <strong>' + name + '</strong> dihapus dari wishlist');
        })
        .catch(err => {
            showToast('red', 'Gagal memperbarui wishlist');
        });
    }

    function showToast(type, msg) {
        var toast = document.getElementById('wl-toast');
        toast.innerHTML = msg;
        toast.className = 'toast-notify' + (type === 'green' ? ' green' : '') + ' show';
        clearTimeout(window._wlToastTimer);
        window._wlToastTimer = setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endpush
