@extends('layouts.app')
@section('title', 'Semua Transaksi')
@section('breadcrumb', 'Semua Transaksi')

@push('styles')
<style>
    .stat-row { display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1rem;margin-bottom:1.5rem; }
    .scard { background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:.9rem 1rem;text-align:center; }
    .scard .sv { font-family:var(--font-heading);font-size:1.3rem;font-weight:700; }
    .scard .sl { font-size:.72rem;color:var(--text-muted);margin-top:2px; }

    .filter-row { display:flex;gap:.65rem;flex-wrap:wrap;align-items:center;margin-bottom:1.25rem; }
    .swrap { position:relative; }
    .swrap svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted); }
    .si { padding:.55rem 1rem .55rem 2.2rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.83rem;font-family:var(--font-body);outline:none;color:var(--text);width:200px;transition:border-color 200ms; }
    .si:focus { border-color:var(--color-admin); }
    .fi { padding:.55rem .85rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-family:var(--font-body);color:var(--text-secondary);background:white;outline:none;cursor:pointer; }

    .trx-row { display:flex;align-items:center;gap:.85rem;padding:.85rem 1.25rem;border-bottom:1px solid #F1F5F9;transition:background 150ms; }
    .trx-row:last-child { border-bottom:none; }
    .trx-row:hover { background:#FAFBFF; }
    .trx-info { flex:1;min-width:0; }
    .trx-name { font-weight:600;font-size:.85rem;color:var(--text); }
    .trx-sub { font-size:.73rem;color:var(--text-muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .trx-id { font-family:monospace;font-size:.7rem;color:var(--text-muted);background:var(--content-bg);padding:2px 6px;border-radius:4px; }

    .pagination { display:flex;justify-content:center;gap:.4rem;padding:1.1rem;border-top:1px solid var(--card-border); }
    .pg { width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--card-border);background:white;font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-family:var(--font-body);transition:all 150ms; }
    .pg:hover { border-color:var(--color-admin);color:var(--color-admin); }
    .pg.active { background:var(--color-admin);border-color:var(--color-admin);color:white; }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;background:var(--color-admin);color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">💰 Semua Transaksi</h1>
    <p class="page-subtitle">Pantau seluruh transaksi yang terjadi di platform Rentiz</p>
</div>

<div class="stat-row">
    <div class="scard"><div class="sv" style="color:var(--color-admin);">{{ number_format($totalTrx) }}</div><div class="sl">Total Transaksi</div></div>
    <div class="scard"><div class="sv" style="color:var(--success);font-size:1rem;">Rp {{ number_format($totalGMV, 0, ',', '.') }}</div><div class="sl">Total GMV</div></div>
    <div class="scard"><div class="sv" style="color:var(--color-penyedia);font-size:1rem;">Rp {{ number_format($platformFee, 0, ',', '.') }}</div><div class="sl">Platform Fee (10%)</div></div>
    <div class="scard"><div class="sv" style="color:var(--warning);">{{ number_format($trxBulanIni) }}</div><div class="sl">Bulan Ini</div></div>
    <div class="scard"><div class="sv" style="color:var(--danger);">{{ number_format($trxDispute) }}</div><div class="sl">Dispute</div></div>
</div>

<div class="filter-row">
    <div class="swrap">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" class="si" id="trx-search" placeholder="Cari ID / nama..." oninput="filterTrx()">
    </div>
    <select class="fi" id="trx-status" onchange="filterTrx()">
        <option value="semua">Semua Status</option>
        <option value="selesai">Selesai</option>
        <option value="aktif">Aktif</option>
        <option value="batal">Dibatalkan</option>
        <option value="dispute">Dispute</option>
    </select>
    <select class="fi">
        <option>Bulan Ini</option>
        <option>3 Bulan</option>
        <option>6 Bulan</option>
        <option>Tahun Ini</option>
    </select>
    <button class="btn btn-outline btn-sm" onclick="showToast('📊 Mengunduh laporan transaksi...')">Export</button>
</div>

<div class="card">
    <div id="trx-list">
    @forelse($transactions as $t)
    @php
        $itemName = $t->items->first()->barang->nama ?? 'Unknown Item';
        $itemTotal = $t->total_biaya;
        $fee = $itemTotal * 0.1;
    @endphp
    <div class="trx-row" data-name="{{ strtolower(($t->pemesan->name ?? '') . ' ' . $t->id) }}" data-status="{{ $t->status }}">
        <div style="flex-shrink:0;"><span class="trx-id">{{ substr($t->id, 0, 8) }}</span></div>
        <div class="trx-info">
            <div class="trx-name">{{ $t->pemesan->name ?? 'Unknown' }} → {{ $t->pemilik->name ?? 'Unknown' }}</div>
            <div class="trx-sub">{{ $itemName }} · {{ $t->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-weight:700;font-size:.88rem;color:var(--text);">Rp {{ number_format($itemTotal, 0, ',', '.') }}</div>
            <div style="font-size:.7rem;color:var(--success);">Fee: +Rp {{ number_format($fee, 0, ',', '.') }}</div>
        </div>
        <div style="flex-shrink:0;min-width:70px;text-align:right;">
            @if(in_array($t->status, ['completed', 'returned'])) <span class="badge badge-success" style="font-size:.65rem;">Selesai</span>
            @elseif(in_array($t->status, ['active', 'confirmed', 'paid'])) <span class="badge badge-warning" style="font-size:.65rem;">Aktif</span>
            @elseif($t->status === 'disputed') <span class="badge badge-danger" style="font-size:.65rem;">⚠ Dispute</span>
            @else <span class="badge badge-neutral" style="font-size:.65rem;">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span>
            @endif
        </div>
        <button style="background:none;border:1px solid var(--card-border);border-radius:6px;padding:3px 9px;font-size:.72rem;font-weight:600;cursor:pointer;color:var(--color-admin);font-family:var(--font-body);transition:all 150ms;flex-shrink:0;" onmouseover="this.style.background='var(--color-admin)';this.style.color='white'" onmouseout="this.style.background='none';this.style.color='var(--color-admin)'" onclick="showToast('🔍 Membuka detail transaksi {{ substr($t->id, 0, 8) }}')">Detail</button>
    </div>
    @empty
    <div id="trx-empty" style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Belum ada transaksi</h3>
        <p>Belum ada transaksi penyewaan yang masuk ke sistem.</p>
    </div>
    @endforelse
    </div>

    @if($transactions->hasPages())
    <div class="pagination">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

<div class="toast" id="trx-toast"></div>
@endsection

@push('scripts')
<script>
    function filterTrx() {
        var q = document.getElementById('trx-search').value.toLowerCase();
        var status = document.getElementById('trx-status').value;
        document.querySelectorAll('#trx-list .trx-row').forEach(function(row){
            var nm=row.dataset.name; var s=row.dataset.status;
            row.style.display=(nm.includes(q)&&(status==='semua'||s===status))?'':'none';
        });
    }
    function showToast(msg) {
        var t=document.getElementById('trx-toast');
        t.textContent=msg; t.classList.add('show');
        clearTimeout(window._trt); window._trt=setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
