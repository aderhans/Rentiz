@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('breadcrumb', 'Daftar Barang')

@push('styles')
<style>
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.25rem; }
    .toolbar-left { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
    .search-wrap { position:relative; }
    .search-wrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--text-muted); }
    .search-wrap input { padding:.55rem 1rem .55rem 2.2rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.84rem;font-family:var(--font-body);outline:none;color:var(--text);width:220px;transition:border-color 200ms; }
    .search-wrap input:focus { border-color:var(--color-penyedia); }
    .flt { padding:.55rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font-body);color:var(--text-secondary);background:white;outline:none;cursor:pointer; }

    .item-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;margin-bottom:1.5rem; }
    .item-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);overflow:hidden;transition:box-shadow 200ms,transform 200ms;position:relative; }
    .item-card:hover { box-shadow:var(--shadow-md);transform:translateY(-2px); }
    .item-img { height:140px;display:flex;align-items:center;justify-content:center;font-size:3.2rem;position:relative; }
    .item-status { position:absolute;top:8px;left:8px; }
    .item-actions-menu { position:absolute;top:8px;right:8px;display:flex;gap:5px; }
    .icon-btn { width:28px;height:28px;border-radius:7px;background:white;border:1px solid var(--card-border);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.8rem;transition:all 150ms;box-shadow:0 1px 4px rgba(0,0,0,.08); }
    .icon-btn:hover { background:var(--content-bg);transform:scale(1.08); }
    .item-body { padding:.9rem 1rem; }
    .item-name { font-weight:700;font-size:.88rem;color:var(--text);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .item-cat { font-size:.74rem;color:var(--text-muted);margin-bottom:.55rem; }
    .item-footer { display:flex;align-items:center;justify-content:space-between; }
    .item-price { font-family:var(--font-heading);font-weight:700;color:var(--color-penyedia);font-size:.95rem; }
    .item-price span { font-size:.68rem;font-weight:400;color:var(--text-muted); }
    .item-stats { display:flex;gap:.85rem;margin-top:.6rem;padding-top:.6rem;border-top:1px solid #F1F5F9; }
    .item-stat { font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:3px; }

    .summary-bar { display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1rem;margin-bottom:1.25rem; }
    .sum-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:.9rem 1rem;text-align:center; }
    .sum-val { font-family:var(--font-heading);font-size:1.3rem;font-weight:700;color:var(--text); }
    .sum-lbl { font-size:.72rem;color:var(--text-muted);margin-top:2px; }

    .modal-overlay { position:fixed;inset:0;background:rgba(15,32,68,.45);z-index:1000;display:flex;align-items:center;justify-content:center;padding:1rem;opacity:0;pointer-events:none;transition:opacity 250ms;backdrop-filter:blur(3px); }
    .modal-overlay.open { opacity:1;pointer-events:auto; }
    .modal-box { background:white;border-radius:var(--radius-xl);width:100%;max-width:460px;box-shadow:var(--shadow-lg);transform:translateY(20px);transition:transform 250ms;overflow:hidden; }
    .modal-overlay.open .modal-box { transform:translateY(0); }
    .modal-header { padding:1.1rem 1.3rem;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between; }
    .modal-title { font-family:var(--font-heading);font-weight:700;font-size:.95rem;color:var(--text); }
    .modal-close { background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.2rem;padding:4px;border-radius:6px; }
    .modal-close:hover { background:var(--content-bg); }
    .modal-body { padding:1.25rem; }
    .image-slider { position:relative; height:180px; border-radius:14px; overflow:hidden; background:#F3F4F6; margin-bottom:1rem; }
    .slide-track { display:flex; height:100%; transition:transform 300ms ease; }
    .slide { min-width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#F3F4F6; }
    .slide img { max-width:100%; max-height:100%; width:auto; height:100%; object-fit:contain; }
    .slider-arrow { position:absolute; top:50%; transform:translateY(-50%); width:36px; height:36px; border-radius:12px; background:rgba(255,255,255,.85); border:1px solid rgba(148,163,184,.3); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background 150ms; }
    .slider-arrow:hover { background:white; }
    .slider-arrow.left { left:10px; }
    .slider-arrow.right { right:10px; }
    .slider-dots { position:absolute; bottom:12px; left:50%; transform:translateX(-50%); display:flex; gap:6px; }
    .slider-dot { width:9px; height:9px; border-radius:50%; background:rgba(148,163,184,.55); cursor:pointer; }
    .slider-dot.active { background:var(--color-penyedia); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;background:var(--color-penyedia);color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 class="page-title">📦 Daftar Barang</h1>
        <p class="page-subtitle">Kelola semua listing barang sewaan kamu</p>
    </div>
    <a href="{{ route('penyedia.tambah-barang') }}" class="btn btn-primary-blue">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Barang
    </a>
</div>

{{-- Summary Bar --}}
@php
    $totalListing = $totalListing ?? 0;
    $activeListing = $activeListing ?? 0;
    $rentedListing = $rentedListing ?? 0;
    $inactiveListing = $inactiveListing ?? 0;
    $pendapatanBulanIni = $pendapatanBulanIni ?? 0;
@endphp
<div class="summary-bar">
    <div class="sum-card"><div class="sum-val" style="color:var(--color-penyedia);">{{ $totalListing }}</div><div class="sum-lbl">Total Listing</div></div>
    <div class="sum-card"><div class="sum-val" style="color:var(--success);">{{ $activeListing }}</div><div class="sum-lbl">Aktif</div></div>
    <div class="sum-card"><div class="sum-val" style="color:var(--warning);">{{ $rentedListing }}</div><div class="sum-lbl">Sedang Disewa</div></div>
    <div class="sum-card"><div class="sum-val" style="color:var(--text-muted);">{{ $inactiveListing }}</div><div class="sum-lbl">Nonaktif</div></div>
    <div class="sum-card"><div class="sum-val" style="font-size:1rem;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div><div class="sum-lbl">Pendapatan Bulan Ini</div></div>
</div>

{{-- Toolbar --}}
<div class="toolbar">
    <div class="toolbar-left">
        <div class="search-wrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="item-search" placeholder="Cari nama barang..." oninput="filterItems()">
        </div>
        <select class="flt" id="item-filter-status" onchange="filterItems()">
            <option value="semua">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="disewa">Sedang Disewa</option>
            <option value="nonaktif">Nonaktif</option>
        </select>
        <select class="flt">
            <option>Semua Kategori</option>
            <option>Fotografi</option>
            <option>Drone</option>
            <option>Elektronik</option>
            <option>Outdoor</option>
            <option>Gaming</option>
            <option>Audio</option>
        </select>
    </div>
    <div style="display:flex;gap:.5rem;">
        <button class="btn btn-outline btn-sm" onclick="alert('Fitur export dalam pengembangan!')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </button>
    </div>
</div>

{{-- Items Grid --}}
<div class="item-grid" id="item-grid">


@forelse($items as $item)
@php
    if (in_array($item->id, $rentedItemIds ?? [], true)) {
        $displayStatus = 'disewa';
    } elseif ($item->status === 'active') {
        $displayStatus = 'aktif';
    } else {
        $displayStatus = 'nonaktif';
    }
@endphp
<div class="item-card" data-name="{{ strtolower($item->nama) }}" data-status="{{ $displayStatus }}"
     data-images='@json($item->fotos->pluck('path_foto')->map(fn($p) => asset('storage/' . ltrim($p, '/')))->all())'
     data-category="{{ $item->kategori_id ?? 'Lainnya' }}"
     data-desc="{{ e($item->deskripsi) }}"
     data-city="{{ $item->kota }}"
     data-condition="{{ $item->kondisi }}"
     data-price="{{ $item->harga_per_hari }}"
     onclick="openEditModal(this)">
    <div class="item-img" style="background:#F1F5F9; padding: 0;">
        @php $firstImage = $item->fotos && $item->fotos->first() ? $item->fotos->first()->path_foto : null; @endphp
        @if($firstImage)
            <img src="{{ asset('storage/' . ltrim($firstImage, '/')) }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'">
        @else
            <span>📷</span>
        @endif
        <div class="item-status">
            @if($displayStatus==='disewa') <span class="badge badge-warning" style="font-size:.64rem;">● Sedang Disewa</span>
            @elseif($displayStatus==='aktif') <span class="badge badge-success" style="font-size:.64rem;">● Aktif</span>
            @elseif($item->status==='pending') <span class="badge badge-warning" style="font-size:.64rem;">● Menunggu Verifikasi</span>
            @else <span class="badge badge-danger" style="font-size:.64rem;">● Nonaktif</span>
            @endif
        </div>
    </div>
    <div class="item-body">
        <div class="item-name">{{ $item->nama }}</div>
        <div class="item-cat">{{ $item->kategori_id ?? '-' }}</div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.55rem;">Klik kartu untuk melihat detail & edit</div>
        <div class="item-footer">
            <div class="item-price">Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}<span>/hari</span></div>
            <div style="font-size:.76rem;color:var(--warning);">⭐ 0.0</div>
        </div>
        <div class="item-stats">
            <span class="item-stat">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                0x disewa
            </span>
            <span class="item-stat">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                0 tayangan
            </span>
        </div>
    </div>
</div>
@empty
<div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: var(--text-muted); background: white; border: 2px dashed var(--card-border); border-radius: var(--radius-lg);">
    <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Belum ada barang di toko Anda</h3>
    <p style="margin-bottom: 1rem; font-size: 0.85rem;">Mulai tambahkan barang pertama Anda untuk disewakan.</p>
    <a href="{{ route('penyedia.tambah-barang') }}" class="btn btn-primary-blue" style="display:inline-flex; align-items:center; gap:0.5rem;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Barang Baru
    </a>
</div>
@endforelse
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="edit-modal" onclick="closeModal(event)">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="edit-modal-title">Edit Barang</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="image-slider" id="edit-slider">
                <div class="slide-track" id="edit-slide-track"></div>
                <button class="slider-arrow left" onclick="prevSlide(event)">&#8249;</button>
                <button class="slider-arrow right" onclick="nextSlide(event)">&#8250;</button>
                <div class="slider-dots" id="edit-slider-dots"></div>
            </div>
            <div style="margin-bottom:.85rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Nama Barang</label>
                <input type="text" id="edit-name" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.86rem;font-family:var(--font-body);outline:none;" onfocus="this.style.borderColor='var(--color-penyedia)'" onblur="this.style.borderColor='var(--card-border)'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.85rem;">
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Harga/Hari (Rp)</label>
                    <input type="number" id="edit-price" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.86rem;font-family:var(--font-body);outline:none;" value="350000" onfocus="this.style.borderColor='var(--color-penyedia)'" onblur="this.style.borderColor='var(--card-border)'">
                </div>
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Status</label>
                    <select id="edit-status" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.84rem;font-family:var(--font-body);outline:none;background:white;">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="disewa">Sedang Disewa</option>
                        <option value="pending">Menunggu Verifikasi</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:.85rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Kategori</label>
                <input type="text" id="edit-category" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.86rem;font-family:var(--font-body);outline:none;" readonly>
            </div>
            <div style="margin-bottom:.85rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Kota / Lokasi</label>
                <input type="text" id="edit-city" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.86rem;font-family:var(--font-body);outline:none;" readonly>
            </div>
            <div style="margin-bottom:.85rem;">
                <label style="display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.3rem;">Deskripsi</label>
                <textarea id="edit-desc" style="width:100%;padding:.6rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.86rem;font-family:var(--font-body);outline:none;" rows="4" readonly></textarea>
            </div>
            <button class="btn btn-primary-blue" style="width:100%;justify-content:center;" onclick="closeModal();showToast('✅ Perubahan berhasil disimpan!')">Simpan Perubahan</button>
        </div>
    </div>
</div>

<div class="toast" id="item-toast"></div>
@endsection

@push('scripts')
<script>
    function filterItems() {
        var q      = document.getElementById('item-search').value.toLowerCase();
        var status = document.getElementById('item-filter-status').value;
        document.querySelectorAll('#item-grid .item-card').forEach(function(c) {
            var nm = c.dataset.name;
            var st = c.dataset.status;
            c.style.display = (nm.includes(q) && (status==='semua'||st===status)) ? '' : 'none';
        });
    }

    var sliderImages = [];
    var currentSlideIndex = 0;

    function openEditModal(card) {
        var imagesJson = card.dataset.images || '[]';
        sliderImages = [];
        try {
            sliderImages = JSON.parse(imagesJson);
        } catch (e) {
            sliderImages = [];
        }
        currentSlideIndex = 0;

        var name = card.dataset.name || '';
        var category = card.dataset.category || '';
        var desc = card.dataset.desc || '';
        var city = card.dataset.city || '';
        var condition = card.dataset.condition || '';
        var price = card.dataset.price || '';
        var status = card.dataset.status || 'aktif';

        document.getElementById('edit-name').value = name;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-status').value = status;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-city').value = city;
        document.getElementById('edit-desc').value = desc;
        document.getElementById('edit-modal-title').textContent = '✏️ Detail: ' + name;
        renderSlider();
        document.getElementById('edit-modal').classList.add('open');
    }

    function renderSlider() {
        var track = document.getElementById('edit-slide-track');
        var dots = document.getElementById('edit-slider-dots');
        track.innerHTML = '';
        dots.innerHTML = '';
        if (!sliderImages.length) {
            track.innerHTML = '<div class="slide"><span style="font-size:3rem;">📷</span></div>';
            return;
        }

        sliderImages.forEach(function(url, index) {
            var slide = document.createElement('div');
            slide.className = 'slide';
            var img = document.createElement('img');
            img.src = url;
            img.onerror = function() {
                var fallback = document.createElement('span');
                fallback.style.fontSize = '3rem';
                fallback.textContent = '📷';
                slide.innerHTML = '';
                slide.appendChild(fallback);
            };
            slide.appendChild(img);
            track.appendChild(slide);

            var dot = document.createElement('div');
            dot.className = 'slider-dot' + (index === currentSlideIndex ? ' active' : '');
            dot.addEventListener('click', function() { goToSlide(index); });
            dots.appendChild(dot);
        });

        updateSliderPosition();
    }

    function updateSliderPosition() {
        var track = document.getElementById('edit-slide-track');
        var dots = document.querySelectorAll('#edit-slider-dots .slider-dot');
        track.style.transform = 'translateX(-' + (currentSlideIndex * 100) + '%)';
        dots.forEach(function(dot, index) {
            dot.classList.toggle('active', index === currentSlideIndex);
        });
    }

    function prevSlide(event) {
        event.stopPropagation();
        if (!sliderImages.length) return;
        currentSlideIndex = (currentSlideIndex - 1 + sliderImages.length) % sliderImages.length;
        updateSliderPosition();
    }

    function nextSlide(event) {
        event.stopPropagation();
        if (!sliderImages.length) return;
        currentSlideIndex = (currentSlideIndex + 1) % sliderImages.length;
        updateSliderPosition();
    }

    function goToSlide(index) {
        currentSlideIndex = index;
        updateSliderPosition();
    }
    function closeModal(e) {
        if (!e || e.target === document.getElementById('edit-modal')) {
            document.getElementById('edit-modal').classList.remove('open');
        }
    }
    function deleteItem(btn, name) {
        if (!confirm('Hapus "' + name + '" dari listing?')) return;
        var card = btn.closest('.item-card');
        card.style.transform='scale(0.85)'; card.style.opacity='0'; card.style.transition='all 300ms';
        setTimeout(function(){ card.remove(); showToast('🗑️ "'+name+'" berhasil dihapus'); }, 300);
    }
    function showToast(msg) {
        var t = document.getElementById('item-toast');
        t.textContent = msg; t.classList.add('show');
        clearTimeout(window._it); window._it = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
