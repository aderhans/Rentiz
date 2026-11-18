@extends('layouts.app')
@section('title', 'Manajemen User')
@section('breadcrumb', 'Manajemen User')

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

    .user-row { display:flex;align-items:center;gap:.85rem;padding:.85rem 1.3rem;border-bottom:1px solid #F1F5F9;transition:background 150ms; }
    .user-row:last-child { border-bottom:none; }
    .user-row:hover { background:#FAFBFF; }
    .u-ava { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.88rem;color:white;flex-shrink:0; }
    .u-info { flex:1;min-width:0; }
    .u-name { font-weight:600;font-size:.86rem;color:var(--text); }
    .u-email { font-size:.73rem;color:var(--text-muted); }
    .u-right { display:flex;align-items:center;gap:.6rem;flex-shrink:0;flex-wrap:wrap; }

    .action-btn { background:none;border:1px solid var(--card-border);border-radius:6px;padding:3px 9px;font-size:.73rem;font-weight:600;cursor:pointer;font-family:var(--font-body);transition:all 150ms; }
    .action-btn.edit { color:var(--color-admin); }
    .action-btn.edit:hover { background:var(--color-admin);color:white;border-color:var(--color-admin); }
    .action-btn.suspend { color:var(--danger); }
    .action-btn.suspend:hover { background:var(--danger);color:white;border-color:var(--danger); }
    .action-btn.activate { color:var(--success); }
    .action-btn.activate:hover { background:var(--success);color:white;border-color:var(--success); }

    .pagination-wrap { display:flex;justify-content:center;gap:.4rem;padding:1.1rem;border-top:1px solid var(--card-border); }
    .pagination-wrap .page-item .page-link { width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--card-border);background:white;font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-family:var(--font-body);transition:all 150ms;text-decoration:none; }
    .pagination-wrap .page-item.active .page-link { background:var(--color-admin);border-color:var(--color-admin);color:white; }
    .pagination-wrap .page-item .page-link:hover { border-color:var(--color-admin);color:var(--color-admin); }
    .pagination-wrap .page-item.disabled .page-link { opacity:.45;pointer-events:none; }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }

    /* Avatar color palette berdasarkan huruf */
    .ava-color-0 { background:#0D9488; }
    .ava-color-1 { background:#2563EB; }
    .ava-color-2 { background:#7C3AED; }
    .ava-color-3 { background:#DB2777; }
    .ava-color-4 { background:#D97706; }
    .ava-color-5 { background:#059669; }
    .ava-color-6 { background:#DC2626; }
    .ava-color-7 { background:#0891B2; }
    .ava-suspended { background:#94A3B8 !important; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 class="page-title">👥 Manajemen User</h1>
        <p class="page-subtitle">Kelola semua pengguna terdaftar di platform Rentiz</p>
    </div>
    <button class="btn btn-primary-purple" onclick="showToast('#7C3AED','➕ Fitur tambah user manual segera hadir!')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Tambah User
    </button>
</div>

{{-- Flash message --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;padding:.75rem 1rem;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:var(--radius-sm);color:#065F46;font-size:.85rem;font-weight:600;">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="stat-row">
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ number_format($totalUser) }}</div><div class="sl">Total User</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">+{{ $userMingguIni }}</div><div class="sl">Minggu Ini</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ $suspended }}</div><div class="sl">Suspended</div></div>
</div>

<div class="toolbar">
    <div class="tbl">
        <div class="swrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="si" id="user-search" placeholder="Cari nama / email..." oninput="filterUsers()" value="{{ request('q') }}">
        </div>
        <select class="fi" id="user-status" onchange="filterUsers()">
            <option value="semua">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
    <button class="btn btn-outline btn-sm" onclick="showToast('#7C3AED','📊 Mengunduh laporan user...')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
    </button>
</div>

<div class="card">
    <div id="user-list">
    @forelse($users as $u)
    @php
        $isSuspended = $u->role === 'suspended';
        $colorIdx    = ord(strtoupper(substr($u->name, 0, 1))) % 8;
        $avaClass    = $isSuspended ? 'ava-suspended' : 'ava-color-'.$colorIdx;
        $statusVal   = $isSuspended ? 'suspended' : 'aktif';
    @endphp
    <div class="user-row"
         data-name="{{ strtolower($u->name.' '.$u->email) }}"
         data-status="{{ $statusVal }}"
         id="urow-{{ $u->id }}">
        <div class="u-ava {{ $avaClass }}">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
        <div class="u-info">
            <div class="u-name">{{ $u->name }}</div>
            <div class="u-email">{{ $u->email }} · Bergabung {{ $u->created_at->translatedFormat('d M Y') }}</div>
        </div>
        <div style="flex-shrink:0;min-width:90px;text-align:center;">
            @if($isSuspended)
                <span class="badge badge-danger" style="font-size:.65rem;">Suspended</span>
            @else
                <span class="badge badge-neutral" style="font-size:.65rem;">User</span>
            @endif
        </div>
        <div class="u-right">
            @if($isSuspended)
                {{-- Tombol Aktifkan --}}
                <form method="POST" action="{{ route('admin.activate-user', $u->id) }}" style="margin:0;" onsubmit="return confirm('Aktifkan akun {{ addslashes($u->name) }}?')">
                    @csrf
                    <button type="submit" class="action-btn activate">Aktifkan</button>
                </form>
            @else
                <button class="action-btn edit" onclick="showToast('#7C3AED','✏️ Mengedit {{ addslashes($u->name) }}...')">Edit</button>
                {{-- Tombol Suspend --}}
                <form method="POST" action="{{ route('admin.suspend-user', $u->id) }}" style="margin:0;" onsubmit="return confirm('Suspend akun {{ addslashes($u->name) }}?')">
                    @csrf
                    <button type="submit" class="action-btn suspend">Suspend</button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div style="padding:2.5rem;text-align:center;color:var(--text-muted);font-size:.88rem;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 1rem;display:block;opacity:.35;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Tidak ada user ditemukan.
    </div>
    @endforelse
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="pagination-wrap">
        {{-- Prev --}}
        <span class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $users->previousPageUrl() }}">‹</a>
        </span>

        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            <span class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </span>
        @endforeach

        {{-- Next --}}
        <span class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $users->nextPageUrl() }}">›</a>
        </span>
    </div>
    @endif
</div>

<div class="toast" id="admin-user-toast"></div>
@endsection

@push('scripts')
<script>
    function filterUsers() {
        var q      = document.getElementById('user-search').value.toLowerCase();
        var status = document.getElementById('user-status').value;
        document.querySelectorAll('#user-list .user-row').forEach(function(row) {
            var nm = row.dataset.name;
            var s  = row.dataset.status;
            row.style.display = (nm.includes(q) && (status === 'semua' || s === status)) ? '' : 'none';
        });
    }

    function showToast(bg, msg) {
        var t = document.getElementById('admin-user-toast');
        t.style.background = bg; t.textContent = msg; t.classList.add('show');
        clearTimeout(window._aut); window._aut = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }

    // Tampilkan flash success sebagai toast juga (opsional)
    @if(session('success'))
        setTimeout(function(){ showToast('#059669', '✅ {{ addslashes(session("success")) }}'); }, 300);
    @endif
</script>
@endpush
