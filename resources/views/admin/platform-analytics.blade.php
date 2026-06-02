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
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
    <div>
        <h1 class="page-title">📈 Platform Analytics</h1>
        <p class="page-subtitle">Visualisasi performa keseluruhan platform Rentiz</p>
    </div>
    <select style="padding:.5rem 1rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.82rem;font-weight:600;color:var(--text);outline:none;background:white;font-family:var(--font-body);">
        <option>Bulan Ini (Mei 2026)</option>
        <option>Bulan Lalu (Apr 2026)</option>
        <option>Tahun Ini (2026)</option>
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
            $pts = [20, 25, 22, 35, 45, 40, 55, 65, 60, 75, 85, 100];
            $lbls = ['1 Mei','3','6','9','12','15','18','21','24','27','30'];
            @endphp
            @foreach($pts as $i=>$p)
            <div class="lc-col">
                <div class="lc-tooltip">{{ $p * 15 }} Trx</div>
                <div class="lc-dot" style="margin-bottom:{{ $p }}px;"></div>
                @if(isset($lbls[$i])) <div class="lc-lbl">{{ $lbls[$i] }}</div> @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Kategori Terpopuler --}}
    <div class="chart-box">
        <div class="cb-header"><span class="cb-title">Kategori Terlaris</span></div>
        <div class="prog-row">
            <div class="prog-top"><span>📷 Fotografi</span><span>42%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:42%;background:#2563EB;"></div></div>
        </div>
        <div class="prog-row">
            <div class="prog-top"><span>⛺ Outdoor & Camp</span><span>24%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:24%;background:#0D9488;"></div></div>
        </div>
        <div class="prog-row">
            <div class="prog-top"><span>🎮 Console Game</span><span>18%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:18%;background:#D97706;"></div></div>
        </div>
        <div class="prog-row">
            <div class="prog-top"><span>🚁 Drone</span><span>10%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:10%;background:#7C3AED;"></div></div>
        </div>
        <div class="prog-row">
            <div class="prog-top"><span>Lainnya</span><span>6%</span></div>
            <div class="prog-bar"><div class="prog-fill" style="width:6%;background:#94A3B8;"></div></div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    {{-- Peta Sebaran --}}
    <div class="chart-box">
        <div class="cb-header"><span class="cb-title">Sebaran Transaksi (Simulasi Map)</span></div>
        <div class="map-box">
            <div style="position:absolute;font-weight:700;color:var(--text-muted);font-size:2rem;opacity:.3;letter-spacing:5px;">INDONESIA</div>
            <div class="map-pin" style="top:40%;left:30%;"><div class="pin-pulse"></div><div class="pin-dot"></div><div class="pin-lbl">Jabodetabek (65%)</div></div>
            <div class="map-pin" style="top:55%;left:50%;"><div class="pin-pulse" style="animation-delay:.5s;"></div><div class="pin-dot"></div><div class="pin-lbl">Surabaya (15%)</div></div>
            <div class="map-pin" style="top:65%;left:75%;"><div class="pin-pulse" style="animation-delay:1s;"></div><div class="pin-dot"></div><div class="pin-lbl">Bali (10%)</div></div>
            <div class="map-pin" style="top:45%;left:40%;"><div class="pin-dot" style="width:8px;height:8px;"></div></div>
            <div class="map-pin" style="top:30%;left:60%;"><div class="pin-dot" style="width:8px;height:8px;"></div></div>
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
@endsection
