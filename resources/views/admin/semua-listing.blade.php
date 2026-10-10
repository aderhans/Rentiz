@extends('layouts.app')
@section('title', 'Semua Listing')
@section('breadcrumb', 'Semua Listing')

@push('styles')
<style>
    .toolbar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem; }
    .tbl { display:flex;gap:.6rem;flex-wrap:wrap;align-items:center; }
    .swrap { position:relative; }
    .swrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted); }
    .si { padding:.55rem 1rem .55rem 2.2rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.83rem;font-family:var(--font-body);outline:none;color:var(--text);width:200px;transition:border-color 200ms; }
    .si:focus { border-color:var(--color-admin); }
    .fi { padding:.55rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font-body);color:var(--text-secondary);background:white;outline:none;cursor:pointer; }

    .stat-row { display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1rem;margin-bottom:1.5rem; }
    .scard { background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:.9rem 1rem;text-align:center; }
    .scard .sv { font-family:var(--font-heading);font-size:1.3rem;font-weight:700; }
    .scard .sl { font-size:.72rem;color:var(--text-muted);margin-top:2px; }

    .listing-row { display:flex;align-items:center;gap:.85rem;padding:.85rem 1.25rem;border-bottom:1px solid #F1F5F9;transition:background 150ms; }
    .listing-row:last-child { border-bottom:none; }
    .listing-row:hover { background:#FAFBFF; }
    .l-thumb { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;overflow:hidden; }
    .l-info { flex:1;min-width:0; }
    .l-name { font-weight:600;font-size:.86rem;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .l-sub { font-size:.73rem;color:var(--text-muted);margin-top:1px; }
    .l-right { display:flex;align-items:center;gap:.6rem;flex-shrink:0;flex-wrap:wrap; }
    .action-btn { background:none;border:1px solid var(--card-border);border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:600;cursor:pointer;font-family:var(--font-body);transition:all 150ms;text-decoration:none;display:inline-flex;align-items:center;gap:3px; }
    .action-btn.view { color:var(--color-admin); }
    .action-btn.view:hover { background:var(--color-admin);color:white;border-color:var(--color-admin); }
    .action-btn.takedown { color:var(--danger); }
    .action-btn.takedown:hover { background:var(--danger);color:white;border-color:var(--danger); }
    .action-btn.approve { color:var(--success); }
    .action-btn.approve:hover { background:var(--success);color:white;border-color:var(--success); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">📦 Semua Listing</h1>
    <p class="page-subtitle">Pantau dan moderasi seluruh listing barang di platform</p>
</div>

<div class="stat-row">
<<<<<<< HEAD
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ $items->count() }}</div><div class="sl">Total Listing</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">{{ $items->where('status', 'active')->count() }}</div><div class="sl">Aktif</div></div>
    <div class="scard"><div class="sv" style="color:var(--warning);">{{ $items->where('status', 'pending')->count() }}</div><div class="sl">Pending Review</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ $items->where('status', 'rejected')->count() }}</div><div class="sl">Ditolak</div></div>
=======
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ $allItems->total ?? 0 }}</div><div class="sl">Total Listing</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">{{ $allItems->aktif ?? 0 }}</div><div class="sl">Aktif</div></div>
    <div class="scard"><div class="sv" style="color:var(--warning);">{{ $allItems->pending ?? 0 }}</div><div class="sl">Menunggu Persetujuan</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ $allItems->lainnya ?? 0 }}</div><div class="sl">Ditolak / Nonaktif</div></div>
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
</div>

{{-- Filter Form — GET untuk dropdown, search dilakukan di sisi client --}}
<form method="GET" action="{{ route('admin.semua-listing') }}" id="filter-form">
<div class="toolbar">
    <div class="tbl">
        <div class="swrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="si" id="list-search" placeholder="Cari nama barang..." oninput="filterListings()">
        </div>
        <select class="fi" id="list-status" onchange="filterListings()">
            <option value="semua">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="pending">Pending</option>
            <option value="rejected">Nonaktif/Ditolak</option>
        </select>
<<<<<<< HEAD
        <select class="fi" id="list-kategori" onchange="filterListings()">
            <option value="semua">Semua Kategori</option>
            @php
                $kategoris = \App\Models\Barang::select('kategori_id')->distinct()->pluck('kategori_id');
            @endphp
            @foreach($kategoris as $k)
                @if($k) <option value="{{ strtolower($k) }}">{{ ucwords(str_replace('_', ' ', $k)) }}</option> @endif
=======
        <select class="fi" name="kategori" id="list-kategori" onchange="document.getElementById('filter-form').submit()">
            <option value="semua" {{ request('kategori', 'semua') === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
            @foreach($kategoris as $k)
                <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
            @endforeach
        </select>
    </div>
    <button type="button" class="btn btn-outline btn-sm" onclick="showToast('#7C3AED','📊 Mengunduh data listing...')">Export CSV</button>
</div>
</form>

<div class="card">
    <div id="listing-list">

<<<<<<< HEAD

    @forelse($items as $i=>$l)
    @php
        $prodData = [
            'id' => $l->id,
            'nama' => $l->nama,
            'deskripsi' => $l->deskripsi,
            'harga' => $l->harga_per_hari,
            'kondisi' => $l->kondisi,
            'kota' => $l->kota,
            'seller' => $l->user->name ?? 'Unknown',
            'min_durasi' => $l->min_durasi_sewa,
            'max_durasi' => $l->max_durasi_sewa,
            'foto' => ($l->fotos && $l->fotos->first()) ? asset('storage/' . $l->fotos->first()->path_foto) : null
        ];
    @endphp
    <div class="listing-row" data-name="{{ strtolower($l->nama) }}" data-status="{{ $l->status }}" data-kategori="{{ strtolower($l->kategori_id ?? '') }}" id="lrow-{{ $i }}">
        <textarea class="product-json-data" style="display:none;">{{ json_encode($prodData) }}</textarea>
        <div class="l-thumb" style="background:#F1F5F9; padding:0; overflow:hidden;">
=======
    @forelse($items as $l)
    @php $rowStatus = in_array($l->status, ['rejected','suspended','unavailable']) ? 'nonaktif' : $l->status; @endphp
    <div class="listing-row" data-name="{{ strtolower($l->nama) }}" data-status="{{ $rowStatus }}" data-kategori="{{ strtolower($l->kategori->nama ?? '') }}" id="lrow-{{ $l->id }}">
        <div class="l-thumb" style="background:#F1F5F9;">
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
            @if($l->fotos && $l->fotos->first())
                <img src="{{ asset('storage/' . $l->fotos->first()->path_foto) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                📷
            @endif
        </div>
        <div class="l-info">
            <div class="l-name">{{ $l->nama }}</div>
            <div class="l-sub">
                {{ $l->user->name ?? 'Unknown' }}
                · {{ $l->kota }}
                @if($l->kategori) · {{ $l->kategori->nama }} @endif
            </div>
        </div>
        <div style="font-weight:700;font-size:.86rem;color:var(--color-penyedia);flex-shrink:0;">Rp {{ number_format($l->harga_per_hari, 0, ',', '.') }}/hari</div>
        <div style="flex-shrink:0;">
            @if($l->status==='active') <span class="badge badge-success" style="font-size:.65rem;">Aktif</span>
            @elseif($l->status==='pending') <span class="badge badge-warning" style="font-size:.65rem;">Pending</span>
            @else <span class="badge badge-neutral" style="font-size:.65rem;">Ditolak / Nonaktif</span>
            @endif
        </div>
        <div class="l-right">
            {{-- Tombol Detail selalu ada, baik pending maupun aktif --}}
            <a href="{{ route('admin.detail-barang', $l->id) }}" class="action-btn view">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Detail
            </a>
            @if($l->status==='pending')
                <form action="{{ route('admin.approve-barang', $l->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="action-btn approve">✓ Setujui</button>
                </form>
                <form action="{{ route('admin.reject-barang', $l->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tolak listing ini?');">
                    @csrf
                    <button type="submit" class="action-btn takedown">✕ Tolak</button>
                </form>
<<<<<<< HEAD
            @else
                <button class="action-btn view" onclick="openDetail(this.closest('.listing-row'))">Detail</button>
=======
            @elseif($l->status==='active')
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
                <form action="{{ route('admin.reject-barang', $l->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Takedown listing ini?');">
                    @csrf
                    <button type="submit" class="action-btn takedown">Takedown</button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding: 3rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada listing ditemukan</h3>
        <p>Coba ubah filter atau kata kunci pencarian.</p>
    </div>
    @endforelse

    </div>
<<<<<<< HEAD
    </div>
    @if($items->count() > 10)
    <div class="pagination">
        <button class="pg">‹</button>
        <button class="pg active">1</button>
        <button class="pg" onclick="this.parentElement.querySelectorAll('.pg').forEach(b=>b.classList.remove('active'));this.classList.add('active');">2</button>
        <button class="pg" onclick="this.parentElement.querySelectorAll('.pg').forEach(b=>b.classList.remove('active'));this.classList.add('active');">3</button>
        <button class="pg">›</button>
    </div>
    @endif
</div>

{{-- Modal Detail --}}
<div class="modal" id="modal-detail" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:white;width:90%;max-width:600px;border-radius:var(--radius-lg);overflow:hidden;">
        <div style="position:relative;height:250px;background:#F1F5F9;display:flex;align-items:center;justify-content:center;">
            <img id="md-img" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
            <div id="md-img-fallback" style="font-size:4rem;">📷</div>
            <button onclick="document.getElementById('modal-detail').style.display='none'" style="position:absolute;top:15px;right:15px;background:white;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.2);">✕</button>
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
        </div>
    </div>
=======

    @if($items->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--card-border);">
        {{ $items->appends(request()->query())->links() }}
    </div>
    @endif
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
</div>

<div class="toast" id="listing-toast"></div>
@endsection

@push('scripts')
<script>
    // Search + Status: client-side instant filter (tanpa reload halaman)
    function filterListings() {
        var q      = document.getElementById('list-search').value.toLowerCase();
        var status = document.getElementById('list-status').value;
<<<<<<< HEAD
        var kat = document.getElementById('list-kategori').value;
        document.querySelectorAll('#listing-list .listing-row').forEach(function(row) {
            var nm = row.dataset.name; var s = row.dataset.status; var k = row.dataset.kategori;
            row.style.display = (nm.includes(q) && (status==='semua'||s===status) && (kat==='semua'||k===kat)) ? '' : 'none';
=======
        var shown  = 0;
        document.querySelectorAll('#listing-list .listing-row').forEach(function(row) {
            var nm = row.dataset.name;
            var st = row.dataset.status;  // 'active' | 'pending' | 'nonaktif'
            var matchQ = nm.includes(q);
            var matchS = (status === 'semua') || (st === status);
            var show   = matchQ && matchS;
            row.style.display = show ? '' : 'none';
            if (show) shown++;
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
        });
        // Tampilkan pesan kosong jika tidak ada hasil
        var emptyEl = document.getElementById('no-result');
        if (emptyEl) emptyEl.style.display = shown === 0 ? '' : 'none';
    }

    function openDetail(row) {
        var jsonText = row.querySelector('.product-json-data').value;
        var data = JSON.parse(jsonText);
        
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

        document.getElementById('modal-detail').style.display = 'flex';
    }

    function showToast(bg, msg) {
        var t = document.getElementById('listing-toast');
        t.style.background=bg; t.textContent=msg; t.classList.add('show');
        clearTimeout(window._lt); window._lt=setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
