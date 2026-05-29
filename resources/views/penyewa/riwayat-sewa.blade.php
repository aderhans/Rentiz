@extends('layouts.app')

@section('title', 'Riwayat Sewa')
@section('breadcrumb', 'Riwayat Sewa')

@push('styles')
<style>
    .filter-bar {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .filter-bar-input {
        flex: 1;
        min-width: 180px;
        position: relative;
    }
    .filter-bar-input svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px; height: 16px;
        color: var(--text-muted);
    }
    .filter-bar-input input {
        width: 100%;
        padding: 0.6rem 1rem 0.6rem 2.4rem;
        border: 1.5px solid var(--card-border);
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-family: var(--font-body);
        outline: none;
        color: var(--text);
        transition: border-color 200ms;
    }
    .filter-bar-input input:focus { border-color: var(--color-penyewa); }
    .filter-select-sm {
        padding: 0.6rem 1rem;
        border: 1.5px solid var(--card-border);
        border-radius: var(--radius-sm);
        font-size: 0.83rem;
        font-family: var(--font-body);
        color: var(--text-secondary);
        background: white;
        outline: none;
        cursor: pointer;
    }

    /* Summary stats */
    .riwayat-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .riwayat-stat {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 1rem 1.1rem;
        text-align: center;
    }
    .riwayat-stat .rs-val {
        font-family: var(--font-heading);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text);
    }
    .riwayat-stat .rs-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    /* History row */
    .history-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.4rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 150ms;
    }
    .history-row:last-child { border-bottom: none; }
    .history-row:hover { background: #FAFBFF; }

    .history-emoji {
        width: 46px; height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .history-info { flex: 1; min-width: 0; }
    .history-name { font-weight: 600; font-size: 0.88rem; color: var(--text); }
    .history-meta { font-size: 0.76rem; color: var(--text-muted); margin-top: 2px; }

    .history-right { text-align: right; flex-shrink: 0; }
    .history-price { font-weight: 700; font-size: 0.9rem; color: var(--text); }
    .history-date { font-size: 0.74rem; color: var(--text-muted); margin-top: 2px; }

    .star-rating { color: var(--warning); font-size: 0.8rem; }
    .rate-btn {
        background: none;
        border: 1px dashed var(--card-border);
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.73rem;
        color: var(--text-muted);
        cursor: pointer;
        font-family: var(--font-body);
        transition: all 150ms;
    }
    .rate-btn:hover { border-color: var(--warning); color: var(--warning); }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.4rem;
        padding: 1.25rem;
        border-top: 1px solid var(--card-border);
    }
    .page-btn {
        width: 34px; height: 34px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--card-border);
        background: white;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 150ms;
        font-family: var(--font-body);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
    }
    .page-btn:hover { border-color: var(--color-penyewa); color: var(--color-penyewa); }
    .page-btn.active { background: var(--color-penyewa); border-color: var(--color-penyewa); color: white; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">🕐 Riwayat Sewa</h1>
    <p class="page-subtitle">Semua transaksi sewa yang pernah kamu lakukan</p>
</div>

{{-- Summary Stats --}}
<div class="riwayat-stats">
    <div class="riwayat-stat">
        <div class="rs-val" style="color:var(--color-penyewa);">0</div>
        <div class="rs-label">Total Transaksi</div>
    </div>
    <div class="riwayat-stat">
        <div class="rs-val" style="color:var(--success);">0</div>
        <div class="rs-label">Selesai</div>
    </div>
    <div class="riwayat-stat">
        <div class="rs-val" style="color:var(--danger);">0</div>
        <div class="rs-label">Dibatalkan</div>
    </div>
    <div class="riwayat-stat">
        <div class="rs-val" style="font-size:1.1rem;">Rp 0</div>
        <div class="rs-label">Total Pengeluaran</div>
    </div>
    <div class="riwayat-stat">
        <div class="rs-val" style="color:var(--warning);">⭐ 0.0</div>
        <div class="rs-label">Rating Kamu</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="filter-bar-input">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Cari nama barang..." id="search-riwayat" oninput="filterHistory()">
    </div>
    <select class="filter-select-sm" id="filter-status" onchange="filterHistory()">
        <option value="semua">Semua Status</option>
        <option value="selesai">Selesai</option>
        <option value="batal">Dibatalkan</option>
    </select>
    <select class="filter-select-sm">
        <option>Semua Periode</option>
        <option>Bulan Ini</option>
        <option>3 Bulan Lalu</option>
        <option>6 Bulan Lalu</option>
    </select>
</div>

{{-- History Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📄 Riwayat Transaksi</span>
        <button class="btn btn-outline btn-sm" onclick="alert('Fitur ekspor CSV dalam pengembangan!')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Ekspor CSV
        </button>
    </div>

    <div id="history-list">
        @php
        $history = [];
        @endphp

        @forelse($history as $i => $h)
        <div class="history-row" data-name="{{ strtolower($h['name']) }}" data-status="{{ $h['status'] }}">
            <div class="history-emoji" style="background:{{ $h['bg'] }};">{{ $h['emoji'] }}</div>
            <div class="history-info">
                <div class="history-name">{{ $h['name'] }}</div>
                <div class="history-meta">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $h['seller'] }} · {{ $h['period'] }} · {{ $h['duration'] }}
                </div>
                <div style="margin-top:4px;">
                    @if($h['status'] === 'selesai')
                        @if($h['rated'])
                            <span class="star-rating">{{ str_repeat('⭐', $h['stars']) }}</span>
                        @else
                            <button class="rate-btn" onclick="this.innerHTML='⭐⭐⭐⭐⭐ Terima kasih!';this.style.color='var(--warning)';this.style.borderStyle='solid';this.style.borderColor='var(--warning)';">+ Beri Rating</button>
                        @endif
                    @endif
                </div>
            </div>
            <div class="history-right">
                <div class="history-price">Rp {{ $h['price'] }}</div>
                <div style="margin-top:4px;">
                    @if($h['status'] === 'selesai')
                        <span class="badge badge-success">Selesai</span>
                    @else
                        <span class="badge badge-danger">Dibatalkan</span>
                    @endif
                </div>
                @if($h['status'] === 'selesai')
                <button class="btn btn-outline btn-sm" style="margin-top:4px;font-size:0.73rem;" onclick="alert('Fitur sewa ulang dalam pengembangan!')">Sewa Lagi</button>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center; padding: 3rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada riwayat sewa</h3>
            <p>Anda belum pernah melakukan transaksi penyewaan barang.</p>
        </div>
        @endforelse
    </div>

    <div class="pagination">
        <button class="page-btn">‹</button>
        <button class="page-btn active">1</button>
        <button class="page-btn" onclick="this.parentElement.querySelectorAll('.page-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');">2</button>
        <button class="page-btn" onclick="this.parentElement.querySelectorAll('.page-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active');">3</button>
        <button class="page-btn">›</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterHistory() {
        var q = document.getElementById('search-riwayat').value.toLowerCase();
        var status = document.getElementById('filter-status').value;
        var rows = document.querySelectorAll('#history-list .history-row');

        rows.forEach(function(row) {
            var nameMatch   = row.dataset.name.includes(q);
            var statusMatch = (status === 'semua' || row.dataset.status === status);
            row.style.display = (nameMatch && statusMatch) ? '' : 'none';
        });
    }
</script>
@endpush
