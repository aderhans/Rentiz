@extends('layouts.app')
@section('title', 'Request Sewa')
@section('breadcrumb', 'Request Sewa')

@push('styles')
<style>
    .req-tabs { display:flex;gap:0;background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:4px;margin-bottom:1.5rem;overflow-x:auto; }
    .req-tab { flex:1;min-width:90px;padding:.5rem .85rem;border-radius:calc(var(--radius-md) - 4px);border:none;background:transparent;font-size:.81rem;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all 200ms;white-space:nowrap;font-family:var(--font-body);display:flex;align-items:center;justify-content:center;gap:5px; }
    .req-tab .tb { background:var(--card-border);color:var(--text-muted);font-size:.66rem;padding:1px 5px;border-radius:50px; }
    .req-tab.active { background:var(--color-penyedia);color:white; }
    .req-tab.active .tb { background:rgba(255,255,255,.25);color:white; }

    .req-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.1rem 1.3rem;margin-bottom:.85rem;display:flex;align-items:flex-start;gap:1rem;transition:box-shadow 200ms; }
    .req-card:hover { box-shadow:var(--shadow-sm); }
    .req-card.border-l-pending { border-left:4px solid var(--warning); }
    .req-card.border-l-new    { border-left:4px solid var(--info); }
    .req-card.border-l-done   { border-left:4px solid var(--success); }
    .req-card.border-l-reject { border-left:4px solid var(--danger); }

    .req-avatar { width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.95rem;flex-shrink:0; }
    .req-info { flex:1;min-width:0; }
    .req-name { font-weight:700;font-size:.9rem;color:var(--text); }
    .req-item { font-size:.79rem;color:var(--text-secondary);margin-top:2px; }
    .req-meta { display:flex;gap:1rem;margin-top:.5rem;flex-wrap:wrap; }
    .req-meta-item { font-size:.76rem;color:var(--text-muted);display:flex;align-items:center;gap:3px; }
    .req-meta-item svg { width:12px;height:12px; }

    .req-actions { display:flex;flex-direction:column;gap:.4rem;align-items:flex-end;flex-shrink:0; }
    .btn-approve { background:#D1FAE5;color:var(--success);border:none;padding:.4rem .85rem;border-radius:var(--radius-sm);font-size:.78rem;font-weight:700;cursor:pointer;font-family:var(--font-body);transition:all 150ms; }
    .btn-approve:hover { background:var(--success);color:white; }
    .btn-reject { background:#FEE2E2;color:var(--danger);border:none;padding:.4rem .85rem;border-radius:var(--radius-sm);font-size:.78rem;font-weight:700;cursor:pointer;font-family:var(--font-body);transition:all 150ms; }
    .btn-reject:hover { background:var(--danger);color:white; }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
    <div>
        <h1 class="page-title">🔔 Request Sewa Masuk</h1>
        <p class="page-subtitle">Tindaklanjuti permintaan sewa dari calon penyewa</p>
    </div>
    <span class="badge badge-warning" style="font-size:.82rem;padding:6px 14px;">0 Menunggu</span>
</div>

<div class="req-tabs">
    <button class="req-tab active" onclick="switchReqTab('all',this)">Semua <span class="tb">0</span></button>
    <button class="req-tab" onclick="switchReqTab('pending',this)">Menunggu <span class="tb">0</span></button>
    <button class="req-tab" onclick="switchReqTab('new',this)">Baru <span class="tb">0</span></button>
    <button class="req-tab" onclick="switchReqTab('done',this)">Disetujui <span class="tb">0</span></button>
    <button class="req-tab" onclick="switchReqTab('reject',this)">Ditolak <span class="tb">0</span></button>
</div>

<div id="req-list">
@php
$requests = [];
$borderMap = ['pending'=>'border-l-pending','new'=>'border-l-new','done'=>'border-l-done','reject'=>'border-l-reject'];
@endphp

@forelse($requests as $req)
<div class="req-card {{ $borderMap[$req['type']] }}" data-type="{{ $req['type'] }}" id="reqcard-{{ $loop->index }}">
    <div class="req-avatar">{{ $req['init'] }}</div>
    <div class="req-info">
        <div class="req-name">{{ $req['name'] }}</div>
        <div class="req-item">{{ $req['item'] }} · <strong style="color:var(--color-penyedia);">Rp {{ $req['total'] }}</strong></div>
        <div class="req-meta">
            <span class="req-meta-item">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $req['period'] }}
            </span>
            <span class="req-meta-item">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $req['dur'] }}
            </span>
            <span class="req-meta-item" style="font-style:italic;color:var(--text-muted);">"{{ $req['msg'] }}"</span>
        </div>
    </div>
    <div class="req-actions">
        @if($req['type'] === 'done')
            <span class="badge badge-success">✓ Disetujui</span>
        @elseif($req['type'] === 'reject')
            <span class="badge badge-danger">✕ Ditolak</span>
        @else
            <span class="badge badge-{{ $req['type']==='new'?'info':'warning' }}">{{ $req['label'] }}</span>
            <button class="btn-approve" onclick="approveReq({{ $loop->index }}, '{{ $req['name'] }}')">✓ Setujui</button>
            <button class="btn-reject" onclick="rejectReq({{ $loop->index }}, '{{ $req['name'] }}')">✕ Tolak</button>
        @endif
    </div>
</div>
@empty
<div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);">
    <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada request masuk</h3>
    <p>Belum ada permintaan sewa baru dari pengguna.</p>
</div>
@endforelse
</div>

<div class="toast" id="req-toast"></div>
@endsection

@push('scripts')
<script>
    function switchReqTab(type, btn) {
        document.querySelectorAll('.req-tab').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#req-list .req-card').forEach(function(c) {
            c.style.display = (type==='all'||c.dataset.type===type) ? '' : 'none';
        });
    }
    function approveReq(idx, name) {
        var card = document.getElementById('reqcard-'+idx);
        card.querySelector('.req-actions').innerHTML = '<span class="badge badge-success">✓ Disetujui</span>';
        card.className = card.className.replace(/border-l-\w+/,'border-l-done');
        card.dataset.type = 'done';
        showToast('#059669','✅ Request dari '+name+' disetujui!');
    }
    function rejectReq(idx, name) {
        if (!confirm('Tolak request dari '+name+'?')) return;
        var card = document.getElementById('reqcard-'+idx);
        card.querySelector('.req-actions').innerHTML = '<span class="badge badge-danger">✕ Ditolak</span>';
        card.className = card.className.replace(/border-l-\w+/,'border-l-reject');
        card.dataset.type = 'reject';
        showToast('#DC2626','❌ Request dari '+name+' ditolak.');
    }
    function showToast(bg, msg) {
        var t = document.getElementById('req-toast');
        t.style.background = bg; t.textContent = msg; t.classList.add('show');
        clearTimeout(window._rt); window._rt = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
