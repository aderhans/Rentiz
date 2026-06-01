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
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ $allItems->total ?? 0 }}</div><div class="sl">Total Listing</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">{{ $allItems->aktif ?? 0 }}</div><div class="sl">Aktif</div></div>
    <div class="scard"><div class="sv" style="color:var(--warning);">{{ $allItems->pending ?? 0 }}</div><div class="sl">Menunggu Persetujuan</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ $allItems->lainnya ?? 0 }}</div><div class="sl">Ditolak / Nonaktif</div></div>
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
            <option value="nonaktif">Nonaktif</option>
        </select>
        <select class="fi" name="kategori" id="list-kategori" onchange="document.getElementById('filter-form').submit()">
            <option value="semua" {{ request('kategori', 'semua') === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
            @foreach($kategoris as $k)
                <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
    <button type="button" class="btn btn-outline btn-sm" onclick="showToast('#7C3AED','📊 Mengunduh data listing...')">Export CSV</button>
</div>
</form>

<div class="card">
    <div id="listing-list">

    @forelse($items as $l)
    @php $rowStatus = in_array($l->status, ['rejected','suspended','unavailable']) ? 'nonaktif' : $l->status; @endphp
    <div class="listing-row" data-name="{{ strtolower($l->nama) }}" data-status="{{ $rowStatus }}" data-kategori="{{ strtolower($l->kategori->nama ?? '') }}" id="lrow-{{ $l->id }}">
        <div class="l-thumb" style="background:#F1F5F9;">
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
            @elseif($l->status==='active')
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

    @if($items->hasPages())
    <div style="padding: 1rem; border-top: 1px solid var(--card-border);">
        {{ $items->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<div class="toast" id="listing-toast"></div>
@endsection

@push('scripts')
<script>
    // Search + Status: client-side instant filter (tanpa reload halaman)
    function filterListings() {
        var q      = document.getElementById('list-search').value.toLowerCase();
        var status = document.getElementById('list-status').value;
        var shown  = 0;
        document.querySelectorAll('#listing-list .listing-row').forEach(function(row) {
            var nm = row.dataset.name;
            var st = row.dataset.status;  // 'active' | 'pending' | 'nonaktif'
            var matchQ = nm.includes(q);
            var matchS = (status === 'semua') || (st === status);
            var show   = matchQ && matchS;
            row.style.display = show ? '' : 'none';
            if (show) shown++;
        });
        // Tampilkan pesan kosong jika tidak ada hasil
        var emptyEl = document.getElementById('no-result');
        if (emptyEl) emptyEl.style.display = shown === 0 ? '' : 'none';
    }

    function showToast(bg, msg) {
        var t = document.getElementById('listing-toast');
        t.style.background=bg; t.textContent=msg; t.classList.add('show');
        clearTimeout(window._lt); window._lt=setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
