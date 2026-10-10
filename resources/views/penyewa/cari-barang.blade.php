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
        padding: 3px 8px;
        border-radius: 50px;
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
        background: var(--color-penyewa);
        color: white;
        font-size: 0.82rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 180ms;
        font-family: var(--font-body);
    }
    .btn-sewa-mini:hover { background: #0F766E; }

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
    <div class="search-bar-wrap">
        <div class="search-input-wrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="main-search" class="search-input" placeholder="Cari nama barang..." value="{{ $query }}" oninput="filterProducts()">
        </div>
        <div class="search-input-wrap" style="flex: 0.5; min-width: 150px;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <input type="text" id="city-search" class="search-input" placeholder="Kota / Lokasi" value="{{ $kota ?? '' }}" oninput="filterProducts()">
        </div>
        <select class="filter-select" id="sort-hero" onchange="filterProducts()">
            <option value="populer">Terpopuler</option>
            <option value="murah">Harga Terendah</option>
            <option value="mahal">Harga Tertinggi</option>
            <option value="rating">Rating Tertinggi</option>
        </select>
        <button class="btn btn-sm" style="background:white;color:var(--color-penyewa);font-weight:700;border:none;" onclick="filterProducts()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Cari
        </button>
    </div>
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
    <span class="result-count" id="result-count">Menampilkan <strong>16</strong> barang</span>
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
        $prodData = [
            'id' => $p->id,
            'nama' => $p->nama,
            'deskripsi' => $p->deskripsi,
            'harga' => $p->harga_per_hari,
            'kondisi' => $p->kondisi,
            'kota' => $p->kota,
            'seller' => $p->user->name ?? 'Unknown',
            'min_durasi' => $p->min_durasi_sewa,
            'max_durasi' => $p->max_durasi_sewa,
            'foto' => ($p->fotos && $p->fotos->first()) ? asset('storage/' . $p->fotos->first()->path_foto) : null
        ];
    @endphp
    <div class="product-card" data-cat="{{ strtolower($p->kategori_id ?? '') }}" data-name="{{ strtolower($p->nama) }}" data-city="{{ strtolower($p->kota) }}" data-price="{{ $p->harga_per_hari }}" onclick="openDetail(this)" style="cursor: pointer;">
        <textarea class="product-json-data" style="display:none;">{{ json_encode($prodData) }}</textarea>
        <div class="product-img" style="background: #F1F5F9; padding:0; overflow:hidden;">
            @if($p->fotos && $p->fotos->first())
                <img src="{{ asset('storage/' . $p->fotos->first()->path_foto) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                📷
            @endif
            @php $isWished = in_array($p->id, $user_wishlist); @endphp
            <button class="wishlist-btn {{ $isWished ? 'liked' : '' }}" onclick="toggleWishlist(this, '{{ $p->id }}', '{{ addslashes($p->nama) }}')" title="Tambah ke wishlist">
                ♥
            </button>
        </div>
        <div class="product-body">
            <div class="product-name">{{ $p->nama }}</div>
            <div class="product-seller">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $p->user->name ?? 'Unknown' }} · {{ $p->kota }}
            </div>
            <div class="product-footer">
                <div class="product-price">Rp {{ number_format($p->harga_per_hari, 0, ',', '.') }}<span>/hari</span></div>
                <div class="product-rating">⭐ 0.0 <span style="color:var(--text-muted);font-weight:400;">(0x)</span></div>
            </div>
            <button class="btn-sewa-mini" onclick="event.stopPropagation(); openDetail(this.closest('.product-card'))">
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

{{-- Modal Detail Barang --}}
<div class="modal" id="modal-detail" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:white;width:90%;max-width:600px;border-radius:var(--radius-lg);overflow:hidden;animation:slideUp 0.3s ease;">
        <div style="position:relative;height:250px;background:#F1F5F9;display:flex;align-items:center;justify-content:center;" id="md-img-wrapper">
            <img id="md-img" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
            <div id="md-img-fallback" style="font-size:4rem;">📷</div>
            <button onclick="closeDetail()" style="position:absolute;top:15px;right:15px;background:white;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.2);">✕</button>
        </div>
        <div style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                <div>
                    <h2 id="md-nama" style="font-size:1.25rem;font-weight:700;color:var(--text);margin-bottom:0.25rem;">Nama Barang</h2>
                    <div style="font-size:0.85rem;color:var(--text-muted);">Dari: <span id="md-seller" style="font-weight:600;">Seller</span> • <span id="md-kota">Kota</span></div>
                </div>
                <div style="text-align:right;">
                    <div id="md-harga" style="font-family:var(--font-heading);font-size:1.4rem;font-weight:700;color:var(--color-penyewa);">Rp 0</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">/hari</div>
                </div>
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;padding:1rem;background:#F8FAFC;border-radius:var(--radius-sm);border:1px solid #E2E8F0;">
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">Kondisi</div>
                    <div id="md-kondisi" style="font-size:0.85rem;font-weight:600;">-</div>
                </div>
                <div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">Batas Durasi Sewa</div>
                    <div id="md-durasi" style="font-size:0.85rem;font-weight:600;">-</div>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <h3 style="font-size:0.9rem;font-weight:700;margin-bottom:0.5rem;">Deskripsi Barang</h3>
                <p id="md-deskripsi" style="font-size:0.85rem;color:var(--text-secondary);line-height:1.5;max-height:100px;overflow-y:auto;"></p>
            </div>

            <form action="{{ route('penyewa.proses-sewa') }}" method="POST" id="form-sewa">
                @csrf
                <input type="hidden" name="barang_id" id="md-barang-id">
                
                <div style="display:flex;gap:1rem;margin-bottom:1.5rem;">
                    <div style="flex:1;">
                        <label style="display:block;font-size:0.8rem;font-weight:600;margin-bottom:0.3rem;">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required style="width:100%;padding:0.6rem;border:1px solid var(--input-border);border-radius:var(--radius-sm);outline:none;font-family:var(--font-body);">
                    </div>
                    <div style="flex:1;">
                        <label style="display:block;font-size:0.8rem;font-weight:600;margin-bottom:0.3rem;">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required style="width:100%;padding:0.6rem;border:1px solid var(--input-border);border-radius:var(--radius-sm);outline:none;font-family:var(--font-body);">
                    </div>
                </div>

                <button type="submit" style="width:100%;padding:0.8rem;background:var(--color-penyewa);color:white;border:none;border-radius:var(--radius-md);font-weight:600;font-size:1rem;cursor:pointer;transition:background 0.2s;">
                    Lanjut ke Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

{{-- Toast --}}
<div class="toast-notify" id="toast" role="alert"></div>

@endsection

@push('scripts')
<script>
    var currentCat = 'semua';

    function setCat(btn, cat) {
        document.querySelectorAll('.cat-pill').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        currentCat = cat;
        filterProducts();
    }

    function filterProducts() {
        var q = document.getElementById('main-search').value.toLowerCase();
        var c = document.getElementById('city-search').value.toLowerCase();
        var cards = document.querySelectorAll('#product-grid .product-card');
        var count = 0;

        cards.forEach(function(card) {
            var cat  = card.dataset.cat;
            var name = card.dataset.name;
            var city = card.dataset.city;
            var catMatch  = (currentCat === 'semua' || cat === currentCat);
            var nameMatch = (q === '' || name.includes(q));
            var cityMatch = (c === '' || city.includes(c));

            if (catMatch && nameMatch && cityMatch) {
                card.style.display = '';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('result-count').innerHTML =
            'Menampilkan <strong>' + count + '</strong> barang';
    }

    function toggleWishlist(btn, id, name) {
        event.stopPropagation();
        
        fetch("{{ route('penyewa.wishlist.toggle') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ barang_id: id })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'added') {
                btn.classList.add('liked');
                showToast('❤️ <strong>' + name + '</strong> ditambahkan ke Wishlist!');
            } else if(data.status === 'removed') {
                btn.classList.remove('liked');
                showToast('💔 <strong>' + name + '</strong> dihapus dari Wishlist.');
            }
        })
        .catch(err => {
            showToast('⚠️ Gagal menambahkan ke wishlist, silakan login dulu.');
            window.location.href = "{{ route('login') }}";
        });
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
    function openDetail(card) {
        var jsonText = card.querySelector('.product-json-data').value;
        var data = JSON.parse(jsonText);
        openDetailModalByData(data);
    }

    function openDetailModalByData(data) {
        document.getElementById('md-barang-id').value = data.id;
        document.getElementById('md-nama').textContent = data.nama;
        document.getElementById('md-seller').textContent = data.seller;
        document.getElementById('md-kota').textContent = data.kota;
        document.getElementById('md-harga').textContent = 'Rp ' + parseInt(data.harga).toLocaleString('id-ID');
        document.getElementById('md-kondisi').textContent = data.kondisi ? (data.kondisi.charAt(0).toUpperCase() + data.kondisi.slice(1)) : '-';
        document.getElementById('md-durasi').textContent = data.min_durasi + ' - ' + data.max_durasi + ' hari';
        document.getElementById('md-deskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi.';
        
        var img = document.getElementById('md-img');
        var fallback = document.getElementById('md-img-fallback');
        if (data.foto) {
            img.src = data.foto;
            img.style.display = 'block';
            fallback.style.display = 'none';
        } else {
            img.src = '';
            img.style.display = 'none';
            fallback.style.display = 'block';
        }

        var modal = document.getElementById('modal-detail');
        modal.style.display = 'flex';
    }

    function closeDetail() {
        document.getElementById('modal-detail').style.display = 'none';
    }

    // Close modal on click outside
    window.onclick = function(event) {
        var modal = document.getElementById('modal-detail');
        if (event.target == modal) {
            closeDetail();
        }
    }
</script>
@endpush
