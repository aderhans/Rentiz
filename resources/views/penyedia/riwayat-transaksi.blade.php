@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('breadcrumb', 'Riwayat Transaksi')

@push('styles')
<style>
    .trx-summary { display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem; }
    .trx-sum { background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:1rem 1.1rem;text-align:center; }
    .trx-sum .val { font-family:var(--font-heading);font-size:1.3rem;font-weight:700; }
    .trx-sum .lbl { font-size:.72rem;color:var(--text-muted);margin-top:2px; }

    .filter-row { display:flex;gap:.65rem;flex-wrap:wrap;align-items:center;margin-bottom:1.25rem; }
    .fi { padding:.55rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font-body);color:var(--text-secondary);background:white;outline:none;cursor:pointer; }
    .si { padding:.55rem 1rem .55rem 2.2rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.84rem;font-family:var(--font-body);outline:none;color:var(--text);width:200px;transition:border-color 200ms; }
    .si:focus { border-color:var(--color-penyedia); }
    .swrap { position:relative; }
    .swrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted); }

    .trx-row { display:flex;align-items:center;gap:1rem;padding:.85rem 1.3rem;border-bottom:1px solid #F1F5F9;transition:background 150ms; }
    .trx-row:last-child { border-bottom:none; }
    .trx-row:hover { background:#FAFBFF; }
    .trx-ava { width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.88rem;flex-shrink:0; }
    .trx-detail { flex:1;min-width:0; }
    .trx-name { font-weight:600;font-size:.86rem;color:var(--text); }
    .trx-sub { font-size:.74rem;color:var(--text-muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .trx-right { text-align:right;flex-shrink:0; }
    .trx-amount { font-weight:700;font-size:.9rem;color:var(--success); }
    .trx-date { font-size:.72rem;color:var(--text-muted);margin-top:2px; }

    .pagination { display:flex;justify-content:center;gap:.4rem;padding:1.1rem;border-top:1px solid var(--card-border); }
    .pg-btn { width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--card-border);background:white;font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-family:var(--font-body);transition:all 150ms; }
    .pg-btn:hover { border-color:var(--color-penyedia);color:var(--color-penyedia); }
    .pg-btn.active { background:var(--color-penyedia);border-color:var(--color-penyedia);color:white; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">📊 Riwayat Transaksi</h1>
    <p class="page-subtitle">Semua transaksi sewa yang pernah terjadi di toko kamu</p>
</div>

<div class="trx-summary">
    <div class="trx-sum"><div class="val" style="color:var(--color-penyedia);">0</div><div class="lbl">Total Transaksi</div></div>
    <div class="trx-sum"><div class="val" style="color:var(--success);font-size:1rem;">Rp 0</div><div class="lbl">Bulan Ini</div></div>
    <div class="trx-sum"><div class="val" style="color:var(--text);">Rp 0</div><div class="lbl">Total Pendapatan</div></div>
    <div class="trx-sum"><div class="val" style="color:var(--warning);">⭐ 0.0</div><div class="lbl">Avg. Rating</div></div>
</div>

<div class="filter-row">
    <div class="swrap">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" class="si" id="trx-search" placeholder="Cari nama / ID..." oninput="filterTrx()">
    </div>
    <select class="fi" id="trx-status" onchange="filterTrx()">
        <option value="semua">Semua Status</option>
        <option value="selesai">Selesai</option>
        <option value="aktif">Aktif</option>
        <option value="batal">Dibatalkan</option>
    </select>
    <select class="fi">
        <option>Bulan Ini</option>
        <option>3 Bulan Lalu</option>
        <option>6 Bulan Lalu</option>
        <option>Tahun Ini</option>
    </select>
    <button class="btn btn-outline btn-sm" onclick="alert('Fitur export sedang dikembangkan!')">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Ekspor
    </button>
</div>

<div class="card">
    <div id="trx-list">
    @php
    $transactions = [];
    @endphp

    @forelse($transactions as $t)
    <div class="trx-row" data-name="{{ strtolower($t['name'].' '.$t['id']) }}" data-status="{{ $t['status'] }}">
        <div class="trx-ava">{{ $t['init'] }}</div>
        <div class="trx-detail">
            <div class="trx-name">{{ $t['name'] }} <span style="font-size:.72rem;color:var(--text-muted);font-weight:400;">{{ $t['id'] }}</span></div>
            <div class="trx-sub">{{ $t['item'] }}</div>
        </div>
        <div style="flex-shrink:0;">
            @if($t['status']==='selesai') <span class="badge badge-success" style="font-size:.68rem;">{{ $t['label'] }}</span>
            @elseif($t['status']==='aktif') <span class="badge badge-warning" style="font-size:.68rem;">{{ $t['label'] }}</span>
            @else <span class="badge badge-danger" style="font-size:.68rem;">{{ $t['label'] }}</span>
            @endif
        </div>
        <div class="trx-right">
            <div class="trx-amount" style="{{ $t['status']==='batal'?'color:var(--text-muted);text-decoration:line-through;':'' }}">+ Rp {{ $t['amount'] }}</div>
            <div class="trx-date">{{ $t['date'] }}</div>
        </div>
    </div>
    @empty
    <div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Belum ada transaksi</h3>
        <p>Anda belum memiliki riwayat transaksi penyewaan.</p>
    </div>
    @endforelse
    </div>
    <div class="pagination">
        <button class="pg-btn">‹</button>
        <button class="pg-btn active">1</button>
        <button class="pg-btn" onclick="this.parentElement.querySelectorAll('.pg-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');">2</button>
        <button class="pg-btn" onclick="this.parentElement.querySelectorAll('.pg-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');">3</button>
        <button class="pg-btn">›</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterTrx() {
        var q = document.getElementById('trx-search').value.toLowerCase();
        var st = document.getElementById('trx-status').value;
        document.querySelectorAll('#trx-list .trx-row').forEach(function(row){
            var nm = row.dataset.name;
            var s  = row.dataset.status;
            row.style.display = (nm.includes(q) && (st==='semua'||s===st)) ? '' : 'none';
        });
    }
</script>
@endpush
