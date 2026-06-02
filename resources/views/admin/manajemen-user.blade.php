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

    .pagination { display:flex;justify-content:center;gap:.4rem;padding:1.1rem;border-top:1px solid var(--card-border); }
    .pg { width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--card-border);background:white;font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-family:var(--font-body);transition:all 150ms; }
    .pg:hover { border-color:var(--color-admin);color:var(--color-admin); }
    .pg.active { background:var(--color-admin);border-color:var(--color-admin);color:white; }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }

    /* Custom Confirm Modal */
    .c-modal-overlay { position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;align-items:center;justify-content:center;opacity:0;transition:opacity 200ms;backdrop-filter:blur(2px); }
    .c-modal-overlay.show { opacity:1; }
    .c-modal { background:white;border-radius:var(--radius-md);padding:1.5rem;width:90%;max-width:320px;box-shadow:0 10px 25px rgba(0,0,0,0.15);text-align:center;transform:scale(0.95);transition:transform 200ms; }
    .c-modal-overlay.show .c-modal { transform:scale(1); }
    .c-modal-icon { width:45px;height:45px;border-radius:50%;background:#FEE2E2;color:var(--danger);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem; }
    .c-modal-title { font-family:var(--font-heading);font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:.4rem; }
    .c-modal-msg { font-size:.82rem;color:var(--text-muted);margin-bottom:1.5rem;line-height:1.4; }
    .c-modal-actions { display:flex;gap:.6rem;justify-content:center; }
    .c-modal-btn { padding:.45rem 1.1rem;border-radius:var(--radius-sm);font-weight:600;font-size:.82rem;cursor:pointer;border:none;transition:all 150ms;font-family:var(--font-body); }
    .c-btn-cancel { background:#F1F5F9;color:var(--text-secondary); }
    .c-btn-cancel:hover { background:#E2E8F0; }
    .c-btn-confirm { background:var(--color-admin);color:white; }
    .c-btn-confirm:hover { filter:brightness(1.1); }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 class="page-title">👥 Manajemen User</h1>
        <p class="page-subtitle">Kelola semua pengguna terdaftar di platform Rentiz</p>
    </div>
</div>

<div class="stat-row">
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ $totalUser }}</div><div class="sl">Total User</div></div>
    <div class="scard"><div class="sv" style="color:var(--color-penyewa);">{{ $totalAktif }}</div><div class="sl">Aktif</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ $totalSuspended }}</div><div class="sl">Suspended</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">+{{ $mingguIni }}</div><div class="sl">Baru Minggu Ini</div></div>
</div>

<div class="toolbar">
    <div class="tbl">
        <div class="swrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="si" id="user-search" placeholder="Cari nama / email..." oninput="filterUsers()">
        </div>
        <select class="fi" id="user-status" onchange="filterUsers()">
            <option value="semua">Semua Status</option>
            <option value="active">Aktif</option>
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
    @if($users->isEmpty())
        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
            Tidak ada data pengguna.
        </div>
    @else
        @foreach($users as $u)
        @php
            $roleLabel = 'Pengguna';
            $badgeClass = 'badge-info';
            $statusBadge = $u->status === 'active' ? 'badge-success' : 'badge-danger';
            $statusText = $u->status === 'active' ? 'Aktif' : 'Suspended';
            $initial = strtoupper(substr($u->name, 0, 1));
            $color = $u->status === 'active' ? '#0D9488' : '#94A3B8';
        @endphp
        <div class="user-row" data-name="{{ strtolower($u->name.' '.$u->email) }}" data-status="{{ $u->status }}" id="urow-{{ $u->id }}">
            <div class="u-ava" style="background:{{ $color }};">{{ $initial }}</div>
            <div class="u-info">
                <div class="u-name">{{ $u->name }}</div>
                <div class="u-email">{{ $u->email }} · Bergabung {{ $u->created_at->translatedFormat('d M Y') }}</div>
            </div>
            <div style="flex-shrink:0;min-width:70px;text-align:center;">
                <span class="badge {{ $badgeClass }}" style="font-size:.65rem;">{{ $roleLabel }}</span>
            </div>
            <div style="font-size:.82rem;font-weight:600;color:var(--text-secondary);min-width:40px;text-align:center;">{{ $u->trx }}x</div>
            <div class="u-right">
                <span class="badge {{ $statusBadge }}" style="font-size:.65rem;">{{ $statusText }}</span>
                @if($u->status === 'active')
                    <form action="{{ route('admin.suspend-user', $u->id) }}" method="POST" style="display:inline;" onsubmit="event.preventDefault(); openConfirm(this, 'Yakin ingin men-suspend {{ $u->name }}?');">
                        @csrf
                        <button type="submit" class="action-btn suspend">Suspend</button>
                    </form>
                @else
                    <form action="{{ route('admin.activate-user', $u->id) }}" method="POST" style="display:inline;" onsubmit="event.preventDefault(); openConfirm(this, 'Yakin ingin mengaktifkan akun {{ $u->name }}?');">
                        @csrf
                        <button type="submit" class="action-btn activate">Aktifkan</button>
                    </form>
                @endif
                <form action="{{ route('admin.delete-user', $u->id) }}" method="POST" style="display:inline;" onsubmit="event.preventDefault(); openConfirm(this, 'Yakin ingin menghapus permanen akun {{ $u->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn suspend">Hapus</button>
                </form>
            </div>
        </div>
        @endforeach
    @endif
    </div>
    @if ($users->hasPages())
    <div class="pagination" style="padding-top: 1rem;">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Custom Confirm Modal -->
<div class="c-modal-overlay" id="confirm-modal">
    <div class="c-modal">
        <div class="c-modal-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:22px;height:22px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="c-modal-title">Konfirmasi Tindakan</div>
        <div class="c-modal-msg" id="confirm-modal-msg">Pesan konfirmasi di sini.</div>
        <div class="c-modal-actions">
            <button class="c-modal-btn c-btn-cancel" onclick="closeConfirm()">Batal</button>
            <button class="c-modal-btn c-btn-confirm" onclick="doConfirm()">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<div class="toast" id="admin-user-toast"></div>
@endsection

@push('scripts')
<script>
    function filterUsers() {
        var q = document.getElementById('user-search').value.toLowerCase();
        var status = document.getElementById('user-status').value;
        document.querySelectorAll('#user-list .user-row').forEach(function(row) {
            var nm = row.dataset.name; var s = row.dataset.status;
            row.style.display = (nm.includes(q) && (status==='semua'||s===status)) ? '' : 'none';
        });
    }
    function showToast(bg, msg) {
        var t = document.getElementById('admin-user-toast');
        t.style.background = bg; t.textContent = msg; t.classList.add('show');
        clearTimeout(window._aut); window._aut = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
    
    // Custom Confirm Script
    window._confirmForm = null;
    function openConfirm(form, msg) {
        document.getElementById('confirm-modal-msg').textContent = msg;
        var m = document.getElementById('confirm-modal');
        m.style.display = 'flex';
        // force reflow
        void m.offsetWidth;
        m.classList.add('show');
        window._confirmForm = form;
    }
    function closeConfirm() {
        var m = document.getElementById('confirm-modal');
        m.classList.remove('show');
        setTimeout(() => m.style.display = 'none', 200);
        window._confirmForm = null;
    }
    function doConfirm() {
        if(window._confirmForm) window._confirmForm.submit();
    }
</script>
@endpush
