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
        ♥ <span id="wl-count">0</span> barang tersimpan
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
        <select style="padding:0.5rem 0.85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:0.82rem;font-family:var(--font-body);color:var(--text-secondary);background:white;outline:none;">
            <option>Semua Kategori</option>
            <option>Fotografi</option>
            <option>Elektronik</option>
            <option>Gaming</option>
            <option>Outdoor</option>
        </select>
        <button class="btn btn-outline btn-sm" onclick="clearAll()">Hapus Semua</button>
        <a href="{{ route('penyewa.cari-barang') }}" class="btn btn-primary-teal btn-sm">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari Barang Lain
        </a>
    </div>
</div>

<div class="wishlist-grid" id="wl-grid">
    @php
    $wishlist = [];
    @endphp

    @forelse($wishlist as $w)
    <div class="wl-card" id="card-{{ $w['id'] }}">
        <div class="wl-img" style="background:{{ $w['bg'] }};">
            <button class="remove-wl" onclick="removeWishlist('{{ $w['id'] }}', '{{ $w['name'] }}')" title="Hapus dari wishlist">♥</button>
            <span>{{ $w['emoji'] }}</span>
            <div class="wl-avail-dot {{ $w['available'] ? 'dot-green' : 'dot-red' }}">
                <div class="avail-indicator" style="background:{{ $w['available'] ? 'var(--success)' : 'var(--danger)' }};"></div>
                {{ $w['available'] ? 'Tersedia' : 'Sedang Disewa' }}
            </div>
        </div>
        <div class="wl-body">
            <div class="wl-name">{{ $w['name'] }}</div>
            <div class="wl-seller">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $w['seller'] }}
            </div>
            <div class="wl-footer">
                <div class="wl-price">Rp {{ $w['price'] }}<span>/hari</span></div>
                <div class="wl-rating">⭐ {{ $w['rating'] }}</div>
            </div>
            <button class="btn-sewa-wl {{ $w['available'] ? 'available' : 'unavailable' }}"
                @if($w['available'])
                    onclick="showToast('green', '🛒 Permintaan sewa untuk <strong>{{ $w['name'] }}</strong> dikirim!')"
                @else
                    onclick="showToast('red', '⚠️ Barang sedang tidak tersedia. Coba nanti!')"
                @endif>
                {{ $w['available'] ? 'Sewa Sekarang' : 'Tidak Tersedia' }}
            </button>
        </div>
    </div>
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
    var wishlistCount = 7;

    function removeWishlist(id, name) {
        var card = document.getElementById('card-' + id);
        card.style.transform = 'scale(0.85)';
        card.style.opacity = '0';
        card.style.transition = 'all 300ms';
        setTimeout(function() {
            card.remove();
            wishlistCount--;
            document.getElementById('wl-count').textContent = wishlistCount;
        }, 300);
        showToast('red', '💔 <strong>' + name + '</strong> dihapus dari wishlist');
    }

    function clearAll() {
        if (!confirm('Hapus semua barang dari wishlist?')) return;
        var cards = document.querySelectorAll('#wl-grid .wl-card');
        cards.forEach(function(card, i) {
            setTimeout(function() {
                card.style.transform = 'scale(0.85)';
                card.style.opacity = '0';
                card.style.transition = 'all 300ms';
                setTimeout(function() { card.remove(); }, 300);
            }, i * 80);
        });
        wishlistCount = 0;
        document.getElementById('wl-count').textContent = '0';
        showToast('red', '🗑️ Semua item dihapus dari wishlist');
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
