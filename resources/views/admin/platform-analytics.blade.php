@extends('layouts.app')
@section('title', 'Platform Analytics')
@section('breadcrumb', 'Platform Analytics')

@push('styles')
<style>
    .grid-4 { display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:1.5rem; }
    .kpi-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.25rem;display:flex;flex-direction:column;gap:.5rem;position:relative;overflow:hidden; }
    .kpi-lbl { font-size:.8rem;font-weight:600;color:var(--text-secondary); }
    .kpi-val { font-family:var(--font-heading);font-size:1.8rem;font-weight:700;color:var(--text); }
    .kpi-sub { font-size:.72rem;display:flex;align-items:center;gap:4px; }
    .kpi-sub.up { color:var(--success); }
    .kpi-sub.down { color:var(--danger); }
    .kpi-bg { position:absolute;right:-10px;bottom:-10px;font-size:4rem;opacity:.04;line-height:1; }

    .chart-box { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.25rem; }
    .cb-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; }
    .cb-title { font-family:var(--font-heading);font-weight:700;font-size:.95rem;color:var(--text); }
    
    /* CSS Line Chart simulation */
    .line-chart { display:flex;align-items:flex-end;height:180px;gap:0;padding-top:20px;position:relative; }
    .lc-grid { position:absolute;inset:0;display:flex;flex-direction:column;justify-content:space-between;z-index:0;padding-bottom:20px;pointer-events:none; }
    .lc-line { width:100%;height:1px;background:#F1F5F9; }
    .lc-col { flex:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;z-index:1;position:relative;cursor:crosshair; }
    .lc-dot { width:10px;height:10px;background:white;border:2.5px solid var(--color-admin);border-radius:50%;position:relative;z-index:2;transition:transform 200ms; }
    .lc-col:hover .lc-dot { transform:scale(1.5); }
    .lc-tooltip { position:absolute;top:-25px;left:50%;transform:translateX(-50%);background:var(--text);color:white;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:4px;opacity:0;transition:opacity 150ms;pointer-events:none;white-space:nowrap; }
    .lc-col:hover .lc-tooltip { opacity:1; }
    .lc-lbl { font-size:.65rem;color:var(--text-muted);margin-top:8px; }

    /* Progress bars */
    .prog-row { margin-bottom:.85rem; }
    .prog-top { display:flex;justify-content:space-between;font-size:.78rem;font-weight:600;margin-bottom:4px; }
    .prog-bar { width:100%;height:8px;background:var(--content-bg);border-radius:50px;overflow:hidden; }
    .prog-fill { height:100%;border-radius:50px; }

    /* Map Box */
    .map-box { height:240px;background:#E2E8F0;border-radius:var(--radius-md);position:relative;overflow:hidden;background-image:radial-gradient(#CBD5E1 1px, transparent 1px);background-size:20px 20px;display:flex;align-items:center;justify-content:center; }
    .map-pin { position:absolute;display:flex;flex-direction:column;align-items:center;cursor:pointer; }
    .pin-dot { width:12px;height:12px;background:var(--color-admin);border-radius:50%;border:2px solid white;box-shadow:0 2px 4px rgba(0,0,0,.2); }
    .pin-pulse { position:absolute;width:12px;height:12px;background:var(--color-admin);border-radius:50%;animation:pulse 2s infinite; }
    .pin-lbl { background:white;font-size:.65rem;font-weight:700;padding:2px 6px;border-radius:4px;margin-top:4px;box-shadow:0 2px 4px rgba(0,0,0,.1); }
    @keyframes pulse { 0%{transform:scale(1);opacity:.8;} 100%{transform:scale(3);opacity:0;} }
</style>
@endpush

@section('content')
<div id="analytics-data" style="position: relative;">
    {{-- Indikator loading HTMX (opsional, tapi bagus untuk UX) --}}
    <style>
        .htmx-indicator { opacity: 0; transition: opacity 200ms ease-in; position: absolute; inset: 0; background: rgba(240,244,251,0.6); z-index: 50; display: flex; align-items: center; justify-content: center; pointer-events: none; backdrop-filter: blur(2px); }
        .htmx-request .htmx-indicator { opacity: 1; pointer-events: all; }
        .spinner { width: 30px; height: 30px; border: 3px solid var(--card-border); border-top-color: var(--color-admin); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <div class="htmx-indicator"><div class="spinner"></div></div>

    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
    <div>
        <h1 class="page-title">📈 Platform Analytics</h1>
        <p class="page-subtitle">Visualisasi performa keseluruhan platform Rentiz</p>
    </div>
        <select 
            name="filter"
            hx-get="{{ route('admin.platform-analytics') }}" 
            hx-target="#analytics-data" 
            hx-select="#analytics-data" 
            hx-swap="outerHTML" 
            hx-indicator="#analytics-data"
            style="padding:.5rem 1rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-weight:600;color:var(--text);outline:none;background:white;font-family:var(--font-body);cursor:pointer;"
        >
            <option value="this_month" {{ $filter === 'this_month' ? 'selected' : '' }}>Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('M Y') }})</option>
            <option value="last_month" {{ $filter === 'last_month' ? 'selected' : '' }}>Bulan Lalu ({{ \Carbon\Carbon::now()->subMonth()->translatedFormat('M Y') }})</option>
            <option value="this_year" {{ $filter === 'this_year' ? 'selected' : '' }}>Tahun Ini ({{ \Carbon\Carbon::now()->format('Y') }})</option>
            <option value="all_time" {{ $filter === 'all_time' ? 'selected' : '' }}>Semua Waktu</option>
        </select>
</div>

<div class="grid-4">
    <div class="kpi-card">
        <div class="kpi-bg">👥</div>
        <div class="kpi-lbl">Total Pengguna Aktif</div>
        <div class="kpi-val">{{ number_format($totalUsers) }}</div>
        <div class="kpi-sub up">Total user terdaftar</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-bg">📦</div>
        <div class="kpi-lbl">Total Transaksi</div>
        <div class="kpi-val">{{ number_format($totalTransaksi) }}</div>
        <div class="kpi-sub up">Total seluruh pesanan</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-bg">💰</div>
        <div class="kpi-lbl">Gross Merchandise Value</div>
        <div class="kpi-val" style="color:var(--success);">Rp {{ number_format($gmv, 0, ',', '.') }}</div>
        <div class="kpi-sub up">Total nilai transaksi sukses</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-bg">🏢</div>
        <div class="kpi-lbl">Platform Revenue (Fee)</div>
        <div class="kpi-val" style="color:var(--color-admin);">Rp {{ number_format($platformFee, 0, ',', '.') }}</div>
        <div class="kpi-sub up">Total 10% potongan platform</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
    {{-- Main Chart --}}
    <div class="chart-box">
        <div class="cb-header">
            <span class="cb-title">Tren Pertumbuhan Transaksi</span>
        </div>
        <div class="line-chart">
            <div class="lc-grid">
                <div class="lc-line"></div><div class="lc-line"></div><div class="lc-line"></div><div class="lc-line"></div><div class="lc-line"></div>
            </div>
            @php
            $pts = $trenData;
            $lbls = $trenLabels;
            $maxPts = count($pts) > 0 ? max($pts) : 1;
            @endphp
            @if(count($pts) > 0)
                @foreach($pts as $i => $p)
                @php $h = ($p / $maxPts) * 100; @endphp
                <div class="lc-col">
                    <div class="lc-tooltip">{{ $p }} Trx</div>
                    <div class="lc-dot" style="margin-bottom:{{ $h }}px;"></div>
                    @if(isset($lbls[$i])) <div class="lc-lbl">{{ $lbls[$i] }}</div> @endif
                </div>
                @endforeach
            @else
                <div style="text-align:center; width:100%; color:var(--text-muted); font-size:0.8rem; margin-top:20px;">Belum ada data transaksi 10 hari terakhir.</div>
            @endif
        </div>
    </div>

    {{-- Kategori Terpopuler --}}
    <div class="chart-box">
        <div class="cb-header"><span class="cb-title">Kategori Terlaris</span></div>
        @if($kategoriTerlaris->isEmpty())
             <div style="text-align:center; color:var(--text-muted); font-size:0.8rem; padding: 2rem 0;">Belum ada data penjualan.</div>
        @else
            @php $colors = ['#2563EB', '#0D9488', '#D97706', '#7C3AED', '#94A3B8']; @endphp
            @foreach($kategoriTerlaris as $idx => $k)
                @php 
                    $pct = $totalTerjualAll > 0 ? round(($k->total_terjual / $totalTerjualAll) * 100) : 0;
                    $color = $colors[$idx % count($colors)];
                @endphp
                <div class="prog-row">
                    <div class="prog-top"><span>{{ $k->nama }}</span><span>{{ $pct }}% ({{ $k->total_terjual }})</span></div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    {{-- Status Transaksi Keseluruhan --}}
    <div class="chart-box">
        <div class="cb-header"><span class="cb-title">Status Transaksi Keseluruhan</span></div>
        <div style="padding-top:1rem;">
            @php
                $stsArr = [
                    ['label' => 'Selesai (Completed)', 'key' => 'completed', 'color' => '#10B981'],
                    ['label' => 'Aktif Berjalan', 'key' => 'active', 'color' => '#3B82F6'],
                    ['label' => 'Menunggu Pembayaran', 'key' => 'pending_payment', 'color' => '#F59E0B'],
                    ['label' => 'Dalam Dispute', 'key' => 'disputed', 'color' => '#8B5CF6'],
                    ['label' => 'Dibatalkan / Refund', 'key' => 'cancelled', 'color' => '#EF4444'],
                ];
                $grandTotal = $statusTransaksi->sum('total');
            @endphp
            
            @if($grandTotal == 0)
                <div style="text-align:center; color:var(--text-muted); font-size:0.8rem; padding: 2rem 0;">Belum ada transaksi di platform.</div>
            @else
                @foreach($stsArr as $s)
                    @php 
                        $val = 0;
                        if ($s['key'] === 'cancelled') {
                            $val = (isset($statusTransaksi['cancelled']) ? $statusTransaksi['cancelled']->total : 0) + (isset($statusTransaksi['refunded']) ? $statusTransaksi['refunded']->total : 0);
                        } else {
                            $val = isset($statusTransaksi[$s['key']]) ? $statusTransaksi[$s['key']]->total : 0;
                        }
                        $pct = $grandTotal > 0 ? round(($val / $grandTotal) * 100) : 0;
                    @endphp
                    <div class="prog-row" style="margin-bottom:1rem;">
                        <div class="prog-top">
                            <span style="display:flex;align-items:center;gap:6px;">
                                <span style="width:10px;height:10px;border-radius:50%;background:{{ $s['color'] }};"></span>
                                {{ $s['label'] }}
                            </span>
                            <span>{{ $val }} ({{ $pct }}%)</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $s['color'] }};"></div></div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- User Retention --}}
    <div class="chart-box">
        <div class="cb-header"><span class="cb-title">Distribusi Pengguna</span></div>
        <div style="display: flex; gap: 1rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--card-border);">
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-penyewa);">{{ number_format($penyewaCount) }}</div>
                <div style="font-size: 0.72rem; color: var(--text-muted);">Penyewa</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-penyedia);">{{ number_format($penyediaCount) }}</div>
                <div style="font-size: 0.72rem; color: var(--text-muted);">Penyedia</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-admin);">{{ number_format($adminCount) }}</div>
                <div style="font-size: 0.72rem; color: var(--text-muted);">Admin</div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
