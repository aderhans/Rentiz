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

<div class="stat-row">
    <div class="scard"><div class="sv" style="color:var(--color-admin);">1.284</div><div class="sl">Total User</div></div>
    <div class="scard"><div class="sv" style="color:var(--color-penyewa);">937</div><div class="sl">Penyewa</div></div>
    <div class="scard"><div class="sv" style="color:var(--color-penyedia);">347</div><div class="sl">Penyedia</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);">+42</div><div class="sl">Minggu Ini</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">12</div><div class="sl">Suspended</div></div>
</div>

<div class="toolbar">
    <div class="tbl">
        <div class="swrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="si" id="user-search" placeholder="Cari nama / email..." oninput="filterUsers()">
        </div>
        <select class="fi" id="user-role" onchange="filterUsers()">
            <option value="semua">Semua Role</option>
            <option value="penyewa">Penyewa</option>
            <option value="penyedia">Penyedia</option>
        </select>
        <select class="fi" id="user-status" onchange="filterUsers()">
            <option value="semua">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="suspend">Suspended</option>
        </select>
    </div>
    <button class="btn btn-outline btn-sm" onclick="showToast('#7C3AED','📊 Mengunduh laporan user...')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export
    </button>
</div>

<div class="card">
    <div id="user-list">
    @php
    $users=[
        ['init'=>'A','name'=>'Ahmad Fauzi','email'=>'ahmad@example.com','role'=>'penyewa','join'=>'25 Mei 2026','trx'=>12,'status'=>'aktif','color'=>'#0D9488'],
        ['init'=>'B','name'=>'Budi Santoso','email'=>'budi@example.com','role'=>'penyedia','join'=>'24 Mei 2026','trx'=>45,'status'=>'aktif','color'=>'#2563EB'],
        ['init'=>'S','name'=>'Siti Rahma','email'=>'siti@example.com','role'=>'penyewa','join'=>'23 Mei 2026','trx'=>3,'status'=>'aktif','color'=>'#0D9488'],
        ['init'=>'D','name'=>'Dewi Rahayu','email'=>'dewi@example.com','role'=>'penyedia','join'=>'22 Mei 2026','trx'=>22,'status'=>'aktif','color'=>'#2563EB'],
        ['init'=>'E','name'=>'Eko Prasetyo','email'=>'eko@example.com','role'=>'penyewa','join'=>'21 Mei 2026','trx'=>0,'status'=>'suspend','color'=>'#94A3B8'],
        ['init'=>'F','name'=>'Fajar Nugraha','email'=>'fajar@example.com','role'=>'penyewa','join'=>'20 Mei 2026','trx'=>7,'status'=>'aktif','color'=>'#0D9488'],
        ['init'=>'G','name'=>'Gita Maharani','email'=>'gita@example.com','role'=>'penyedia','join'=>'18 Mei 2026','trx'=>31,'status'=>'aktif','color'=>'#2563EB'],
        ['init'=>'H','name'=>'Hadi Kurniawan','email'=>'hadi@example.com','role'=>'penyewa','join'=>'15 Mei 2026','trx'=>5,'status'=>'aktif','color'=>'#0D9488'],
        ['init'=>'I','name'=>'Inda Pratiwi','email'=>'inda@example.com','role'=>'penyedia','join'=>'12 Mei 2026','trx'=>18,'status'=>'suspend','color'=>'#94A3B8'],
        ['init'=>'J','name'=>'Joko Widodo','email'=>'joko@example.com','role'=>'penyewa','join'=>'10 Mei 2026','trx'=>2,'status'=>'aktif','color'=>'#0D9488'],
    ];
    @endphp

    @foreach($users as $u)
    <div class="user-row" data-name="{{ strtolower($u['name'].' '.$u['email']) }}" data-role="{{ $u['role'] }}" data-status="{{ $u['status'] }}" id="urow-{{ $loop->index }}">
        <div class="u-ava" style="background:{{ $u['color'] }};">{{ $u['init'] }}</div>
        <div class="u-info">
            <div class="u-name">{{ $u['name'] }}</div>
            <div class="u-email">{{ $u['email'] }} · Bergabung {{ $u['join'] }}</div>
        </div>
        <div style="flex-shrink:0;min-width:70px;text-align:center;">
            <span class="badge {{ $u['role']==='penyedia'?'badge-info':'badge-neutral' }}" style="font-size:.65rem;">{{ ucfirst($u['role']) }}</span>
        </div>
        <div style="font-size:.82rem;font-weight:600;color:var(--text-secondary);min-width:40px;text-align:center;">{{ $u['trx'] }}x</div>
        <div class="u-right">
            @if($u['status']==='aktif')
                <span class="badge badge-success" style="font-size:.65rem;">Aktif</span>
                <button class="action-btn edit" onclick="showToast('#7C3AED','✏️ Mengedit {{ $u['name'] }}...')">Edit</button>
                <button class="action-btn suspend" onclick="suspendUser({{ $loop->index }},'{{ $u['name'] }}')">Suspend</button>
            @else
                <span class="badge badge-danger" style="font-size:.65rem;">Suspended</span>
                <button class="action-btn activate" onclick="activateUser({{ $loop->index }},'{{ $u['name'] }}')">Aktifkan</button>
            @endif
        </div>
    </div>
    @endforeach
    </div>
    <div class="pagination">
        <button class="pg">‹</button>
        <button class="pg active">1</button>
        <button class="pg" onclick="this.parentElement.querySelectorAll('.pg').forEach(b=>b.classList.remove('active'));this.classList.add('active');">2</button>
        <button class="pg" onclick="this.parentElement.querySelectorAll('.pg').forEach(b=>b.classList.remove('active'));this.classList.add('active');">3</button>
        <button class="pg">›</button>
    </div>
</div>

<div class="toast" id="admin-user-toast"></div>
@endsection

@push('scripts')
<script>
    function filterUsers() {
        var q = document.getElementById('user-search').value.toLowerCase();
        var role = document.getElementById('user-role').value;
        var status = document.getElementById('user-status').value;
        document.querySelectorAll('#user-list .user-row').forEach(function(row) {
            var nm = row.dataset.name; var r = row.dataset.role; var s = row.dataset.status;
            row.style.display = (nm.includes(q) && (role==='semua'||r===role) && (status==='semua'||s===status)) ? '' : 'none';
        });
    }
    function suspendUser(idx, name) {
        if (!confirm('Suspend akun '+name+'?')) return;
        var row = document.getElementById('urow-'+idx);
        var right = row.querySelector('.u-right');
        row.querySelector('.u-ava').style.background='#94A3B8';
        row.dataset.status = 'suspend';
        right.innerHTML = '<span class="badge badge-danger" style="font-size:.65rem;">Suspended</span><button class="action-btn activate" onclick="activateUser('+idx+',\''+name+'\')">Aktifkan</button>';
        showToast('#DC2626','🚫 Akun '+name+' berhasil di-suspend!');
    }
    function activateUser(idx, name) {
        var row = document.getElementById('urow-'+idx);
        var right = row.querySelector('.u-right');
        row.querySelector('.u-ava').style.background='#0D9488';
        row.dataset.status = 'aktif';
        right.innerHTML = '<span class="badge badge-success" style="font-size:.65rem;">Aktif</span><button class="action-btn edit" onclick="showToast(\'#7C3AED\',\'✏️ Mengedit '+name+'...\')">Edit</button><button class="action-btn suspend" onclick="suspendUser('+idx+',\''+name+'\')">Suspend</button>';
        showToast('#059669','✅ Akun '+name+' berhasil diaktifkan!');
    }
    function showToast(bg, msg) {
        var t = document.getElementById('admin-user-toast');
        t.style.background = bg; t.textContent = msg; t.classList.add('show');
        clearTimeout(window._aut); window._aut = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
