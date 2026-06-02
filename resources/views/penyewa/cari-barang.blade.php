@extends('layouts.app')

@section('title', 'Cari Barang')
@section('breadcrumb', 'Cari Barang')

@push('styles')
<style>
    .search-hero {
        background: linear-gradient(135deg, #0D9488 0%, #065f52 100%);
        border-radius: var(--radius-xl);
        padding: 2rem 1.75rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .search-hero::before {
        content:'';
        position:absolute;
        top:-40px; right:-40px;
        width:200px; height:200px;
        background:rgba(255,255,255,0.06);
        border-radius:50%;
        pointer-events:none;
    }
    .search-hero-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.3rem;
    }
    .search-hero-sub { color: rgba(255,255,255,0.7); font-size: 0.88rem; margin-bottom: 1.25rem; }

    .search-bar-wrap {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .search-input-wrap {
        flex: 1;
        min-width: 220px;
        position: relative;
    }
    .search-input-wrap svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px; height: 18px;
        color: rgba(255,255,255,0.5);
    }
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.7rem;
        background: rgba(255,255,255,0.15);
        border: 1.5px solid rgba(255,255,255,0.25);
        border-radius: var(--radius-sm);
        color: white;
        font-size: 0.9rem;
        font-family: var(--font-body);
        outline: none;
        transition: border-color 200ms, background 200ms;
    }
    .search-input::placeholder { color: rgba(255,255,255,0.5); }
    .search-input:focus { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5); }

    .filter-select {
        padding: 0.75rem 1rem;
        background: rgba(255,255,255,0.15);
        border: 1.5px solid rgba(255,255,255,0.25);
        border-radius: var(--radius-sm);
        color: white;
        font-size: 0.86rem;
        font-family: var(--font-body);
        outline: none;
        cursor: pointer;
    }
    .filter-select option { background: #0F2044; color: white; }

    /* Category pills */
    .category-pills {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .cat-pill {
        padding: 0.45rem 1rem;
        border-radius: 50px;
        border: 1.5px solid var(--card-border);
        background: white;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 160ms;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .cat-pill:hover { border-color: var(--color-penyewa); color: var(--color-penyewa); }
    .cat-pill.active { background: var(--color-penyewa); border-color: var(--color-penyewa); color: white; }

    /* Product grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.1rem;
        margin-bottom: 1.5rem;
    }
    .product-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: box-shadow 200ms, transform 200ms;
        cursor: pointer;
        position: relative;
    }
    .product-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }
    .product-img {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        position: relative;
    }
    .product-badge-corner {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        background: rgba(0,0,0,0.6);
        color: white;
        backdrop-filter: blur(4px);
    }
    .wishlist-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px; height: 32px;
        border-radius: 50%;
        background: white;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 160ms;
        color: #CBD5E1;
        font-size: 1rem;
    }
    .wishlist-btn:hover { color: #EF4444; transform: scale(1.1); }
    .wishlist-btn.liked { color: #EF4444; }

    .product-body { padding: 1rem 1.1rem; }
    .product-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text);
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-seller {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin-bottom: 0.7rem;
    }
    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .product-price {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: 1rem;
        color: var(--color-penyewa);
    }
    .product-price span { font-size: 0.72rem; font-weight: 400; color: var(--text-muted); }
    .product-rating { font-size: 0.78rem; color: var(--warning); font-weight: 600; }

    .btn-sewa-mini {
        width: 100%;
        margin-top: 0.85rem;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        background: #0264c8; /* Traveloka blue style */
        color: white;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 180ms;
        font-family: var(--font-body);
    }
    .btn-sewa-mini:hover { background: #014f9c; }

    /* Sort bar */
    .result-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .result-count { font-size: 0.85rem; color: var(--text-secondary); }
    .sort-select {
        padding: 0.45rem 0.85rem;
        border: 1.5px solid var(--card-border);
        border-radius: var(--radius-sm);
        font-size: 0.82rem;
        font-family: var(--font-body);
        color: var(--text-secondary);
        background: white;
        outline: none;
        cursor: pointer;
    }

    /* Toast */
    .toast-notify {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: #0D9488;
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

    @media (max-width: 600px) {
        .product-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Search Hero --}}
<div class="search-hero">
    <p class="search-hero-sub">🔍 Temukan barang yang kamu butuhkan</p>
    <h1 class="search-hero-title">Cari Barang untuk Disewa</h1>
    <form action="{{ route('penyewa.cari-barang') }}" method="GET" id="search-form">
        <div class="search-bar-wrap">
            <div class="search-input-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" id="main-search" class="search-input" placeholder="Cari nama barang..." value="{{ $query }}">
            </div>
            <div class="search-input-wrap" style="flex: 0.5; min-width: 150px;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <select name="kota" id="city-search" class="filter-select" style="width: 100%; padding-left: 2.7rem;">
                    <option value="">Semua Kota</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ $kota == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <select class="filter-select" id="sort-hero">
                <option value="populer">Terpopuler</option>
                <option value="murah">Harga Terendah</option>
                <option value="mahal">Harga Tertinggi</option>
                <option value="rating">Rating Tertinggi</option>
            </select>
            <button type="submit" class="btn btn-sm" style="background:white;color:var(--color-penyewa);font-weight:700;border:none;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </div>
    </form>
</div>

{{-- Category Filters --}}
<div class="category-pills">
    <button class="cat-pill active" onclick="setCat(this,'semua')">🏷️ Semua</button>
    <button class="cat-pill" onclick="setCat(this,'elektronik')">⚡ Elektronik</button>
    <button class="cat-pill" onclick="setCat(this,'fotografi')">📷 Fotografi</button>
    <button class="cat-pill" onclick="setCat(this,'drone')">🚁 Drone</button>
    <button class="cat-pill" onclick="setCat(this,'olahraga')">⛺ Outdoor</button>
    <button class="cat-pill" onclick="setCat(this,'gaming')">🎮 Gaming</button>
    <button class="cat-pill" onclick="setCat(this,'audio')">🔊 Audio</button>
    <button class="cat-pill" onclick="setCat(this,'kendaraan')">🚗 Kendaraan</button>
</div>

{{-- Result Bar --}}
<div class="result-bar">
    <span class="result-count" id="result-count">Menampilkan <strong>{{ count($items) }}</strong> barang</span>
    <select class="sort-select">
        <option>Urutkan: Terpopuler</option>
        <option>Harga Terendah</option>
        <option>Harga Tertinggi</option>
        <option>Rating Tertinggi</option>
        <option>Terbaru</option>
    </select>
</div>

{{-- Product Grid --}}
<div class="product-grid" id="product-grid">



    @forelse($items as $p)
    @php
        $primaryPhoto = $p->fotos ? $p->fotos->where('is_primary', true)->first() : null;
        if (!$primaryPhoto && $p->fotos) $primaryPhoto = $p->fotos->first();
    @endphp
    <div class="product-card" data-cat="{{ strtolower($p->kategori_id ?? '') }}" data-name="{{ strtolower($p->nama) }}" data-city="{{ strtolower($p->kota) }}" data-price="{{ $p->harga_per_hari }}">
        <div class="product-img" style="background: #E2E8F0; padding:0; overflow:hidden;">
            @if($primaryPhoto)
                <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <svg style="width: 40px; height: 40px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
            
            <div class="product-badge-corner" style="background: {{ $p->status == 'active' ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)' }}">
                {{ $p->status == 'active' ? 'Tersedia' : 'Disewa' }}
            </div>
            <button class="wishlist-btn" onclick="toggleWishlist(this, '{{ $p->nama }}')" title="Tambah ke wishlist">
                ♥
            </button>
        </div>
        <div class="product-body" style="padding: 1.2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <div class="product-name" style="font-size: 1.05rem; line-height: 1.3; white-space: normal; height: 2.6em; overflow: hidden; color: #1e293b;">
                    {{ $p->nama }}
                </div>
            </div>
            
            <div class="product-rating" style="margin-bottom: 0.5rem;">
                ⭐ 4.5 <span style="color:var(--text-muted);font-weight:400;">(24 Review)</span>
            </div>

            <div class="product-seller" style="font-size: 0.8rem; display: flex; align-items: center; color: #64748b; margin-bottom: 1rem;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $p->kota }}
            </div>
            
            <div class="product-footer" style="flex-direction: column; align-items: flex-start; border-top: 1px solid #f1f5f9; padding-top: 0.8rem;">
                <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.2rem;">Mulai dari</div>
                <div class="product-price" style="font-size: 1.15rem; color: #0f172a;">Rp {{ number_format($p->harga_per_hari, 0, ',', '.') }}<span style="font-size:0.8rem; color:#64748b;"> /hari</span></div>
            </div>
            <button class="btn-sewa-mini" onclick="showToast('🛒 Permintaan sewa dikirim untuk <strong>{{ $p->nama }}</strong>!')">
                Sewa Sekarang
            </button>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Belum ada barang</h3>
        <p>Belum ada barang yang tersedia saat ini.</p>
    </div>
    @endforelse
</div>

{{-- Toast --}}
<div class="toast-notify" id="toast" role="alert"></div>

@endsection

@push('scripts')
<script>
    var currentCat = 'semua';

    function setCat(btn, cat) {
        // Option to submit form with category if needed in future
        document.querySelectorAll('.cat-pill').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        currentCat = cat;
        // Basic frontend filtering for category pills if still desired
        var cards = document.querySelectorAll('#product-grid .product-card');
        var count = 0;
        cards.forEach(function(card) {
            var cardCat = card.dataset.cat;
            if (currentCat === 'semua' || cardCat === currentCat) {
                card.style.display = '';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
        document.getElementById('result-count').innerHTML = 'Menampilkan <strong>' + count + '</strong> barang';
    }

    function toggleWishlist(btn, name) {
        event.stopPropagation();
        btn.classList.toggle('liked');
        if (btn.classList.contains('liked')) {
            showToast('❤️ <strong>' + name + '</strong> ditambahkan ke Wishlist!');
        } else {
            showToast('💔 <strong>' + name + '</strong> dihapus dari Wishlist.');
        }
    }

    function showToast(msg) {
        var toast = document.getElementById('toast');
        toast.innerHTML = msg;
        toast.classList.add('show');
        clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endpush
