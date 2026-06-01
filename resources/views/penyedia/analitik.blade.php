@extends('layouts.app')
@section('title', 'Analitik Toko')
@section('breadcrumb', 'Analitik')

@push('styles')
<style>
    .analytics-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;margin-bottom:1.5rem; }
    .an-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.1rem 1.25rem;transition:box-shadow 200ms,transform 200ms; }
    .an-card:hover { box-shadow:var(--shadow-md);transform:translateY(-2px); }
    .an-top { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.75rem; }
    .an-icon { width:42px;height:42px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center; }
    .an-icon svg { width:20px;height:20px; }
    .an-change { font-size:.72rem;font-weight:600;display:flex;align-items:center;gap:2px; }
    .an-change.up { color:var(--success); }
    .an-change.down { color:var(--danger); }
    .an-val { font-family:var(--font-heading);font-size:1.5rem;font-weight:700;color:var(--text);line-height:1; }
    .an-lbl { font-size:.74rem;color:var(--text-muted);margin-top:4px; }

    /* Bar chart */
    .bar-chart { display:flex;align-items:flex-end;gap:6px;height:140px;padding:0 .5rem; }
    .bar-col { flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;height:100%; }
    .bar-inner { flex:1;display:flex;align-items:flex-end;width:100%; }
    .bar-fill { width:100%;border-radius:5px 5px 0 0;transition:height .6s ease;position:relative;cursor:pointer; }
    .bar-fill:hover::after { content:attr(data-val);position:absolute;top:-22px;left:50%;transform:translateX(-50%);font-size:.65rem;font-weight:700;white-space:nowrap;color:var(--color-penyedia); }
    .bar-lbl { font-size:.65rem;color:var(--text-muted); }

    /* Donut */
    .donut-wrap { display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap; }
    .donut-svg { flex-shrink:0; }
    .donut-legend { display:flex;flex-direction:column;gap:.6rem; }
    .legend-item { display:flex;align-items:center;gap:.5rem;font-size:.78rem; }
    .legend-dot { width:10px;height:10px;border-radius:50%;flex-shrink:0; }

    /* Top items table */
    .top-item-row { display:flex;align-items:center;gap:.85rem;padding:.75rem 1.25rem;border-bottom:1px solid #F1F5F9; }
    .top-item-row:last-child { border-bottom:none; }
    .ti-rank { width:24px;height:24px;border-radius:50%;background:var(--content-bg);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:var(--text-muted);flex-shrink:0; }
    .ti-rank.gold { background:#FFFBEB;color:#D97706; }
    .ti-rank.silver { background:#F1F5F9;color:#64748B; }
    .ti-rank.bronze { background:#FFF7ED;color:#C2410C; }
    .ti-info { flex:1;min-width:0; }
    .ti-name { font-weight:600;font-size:.85rem;color:var(--text); }
    .ti-sub { font-size:.73rem;color:var(--text-muted); }
    .ti-bar { width:80px;height:6px;background:var(--content-bg);border-radius:50px;overflow:hidden; }
    .ti-bar-fill { height:100%;border-radius:50px;background:var(--color-penyedia); }
    .ti-amount { font-weight:700;font-size:.85rem;color:var(--color-penyedia);text-align:right;min-width:80px; }

    /* Period selector */
    .period-tabs { display:flex;gap:0;background:var(--content-bg);border-radius:var(--radius-sm);padding:3px; }
    .period-tab { flex:1;padding:.38rem .7rem;border-radius:calc(var(--radius-sm)-3px);border:none;background:transparent;font-size:.77rem;font-weight:600;color:var(--text-secondary);cursor:pointer;font-family:var(--font-body);transition:all 200ms;white-space:nowrap; }
    .period-tab.active { background:white;color:var(--text);box-shadow:0 1px 4px rgba(0,0,0,.06); }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 class="page-title">📈 Analitik Toko</h1>
        <p class="page-subtitle">Pantau performa dan pertumbuhan toko kamu</p>
    </div>
    <div class="period-tabs">
        <button class="period-tab" onclick="setPeriod(this)">7 Hari</button>
        <button class="period-tab active" onclick="setPeriod(this)">30 Hari</button>
        <button class="period-tab" onclick="setPeriod(this)">3 Bulan</button>
        <button class="period-tab" onclick="setPeriod(this)">1 Tahun</button>
    </div>
</div>

@php
    $revenueThisMonth = $revenueThisMonth ?? 0;
    $revenueChangeLabel = $revenueChangeLabel ?? 'Belum ada pendapatan';
    $revenueChangeClass = $revenueChangeClass ?? 'down';
    $completedRentals = $completedRentals ?? 0;
    $completedChangeLabel = $completedChangeLabel ?? 'Belum ada sewa selesai';
    $completedChangeClass = $completedChangeClass ?? 'down';
    $averageRating = $averageRating ?? 0;
    $monthlyRevenue = $monthlyRevenue ?? [];
    $maxRevenue = $maxRevenue ?? 1;
    $categoryDistribution = $categoryDistribution ?? [];
    $topItems = $topItems ?? collect();
    $totalViews = 0;
@endphp

{{-- KPI Cards --}}
<div class="analytics-grid">
    <div class="an-card">
        <div class="an-top">
            <div class="an-icon" style="background:#EFF6FF;"><svg fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            <span class="an-change {{ $revenueChangeClass }}">▲ {{ $revenueChangeLabel }}</span>
        </div>
        <div class="an-val">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
        <div class="an-lbl">Pendapatan Bulan Ini</div>
    </div>
    <div class="an-card">
        <div class="an-top">
            <div class="an-icon" style="background:#D1FAE5;"><svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
            <span class="an-change {{ $completedChangeClass }}">▲ {{ $completedChangeLabel }}</span>
        </div>
        <div class="an-val">{{ $completedRentals }}</div>
        <div class="an-lbl">Total Sewa Selesai</div>
    </div>
    <div class="an-card">
        <div class="an-top">
            <div class="an-icon" style="background:#FEF3C7;"><svg fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></div>
            <span class="an-change up">▲ {{ $totalViews ? '+0' : '0' }}</span>
        </div>
        <div class="an-val">{{ $totalViews }}</div>
        <div class="an-lbl">{{ $totalViews ? 'Total Tayangan' : 'Data tayangan belum tersedia' }}</div>
    </div>
    <div class="an-card">
        <div class="an-top">
            <div class="an-icon" style="background:#FFFBEB;"><svg fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
            <span class="an-change up">▲ {{ $averageRating ? 'Top 10%' : '' }}</span>
        </div>
        <div class="an-val">{{ number_format($averageRating, 1) }}</div>
        <div class="an-lbl">{{ $averageRating ? 'Rating rata-rata' : 'Belum ada rating' }}</div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:1.25rem;">
    {{-- Pendapatan Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💰 Tren Pendapatan</span>
            <span class="badge badge-success">{{ $revenueChangeLabel }}</span>
        </div>
        <div class="card-body">
            <div class="bar-chart" id="main-chart">
                @forelse($monthlyRevenue as $i => $bar)
                @php
                    $height = $maxRevenue > 0 ? ($bar['value'] / $maxRevenue) * 100 : 0;
                    $color = $i === count($monthlyRevenue) - 1 ? 'var(--color-penyedia)' : '#BFDBFE';
                @endphp
                <div class="bar-col">
                    <div class="bar-inner">
                        <div class="bar-fill" style="height:{{ $height }}%;background:{{ $color }};" data-val="{{ $bar['formatted'] }}"></div>
                    </div>
                    <div class="bar-lbl">{{ $bar['label'] }}</div>
                </div>
                @empty
                <div class="bar-col">
                    <div class="bar-inner">
                        <div class="bar-fill" style="height:10%;background:#BFDBFE;" data-val="Rp 0"></div>
                    </div>
                    <div class="bar-lbl">-</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kategori Donut --}}
    <div class="card">
        <div class="card-header"><span class="card-title">🍩 Sewa per Kategori</span></div>
        <div class="card-body">
            <div class="donut-wrap">
                @php
                    $colors = ['#2563EB', '#0D9488', '#D97706', '#7C3AED', '#F97316', '#6366F1'];
                    $offset = 0;
                @endphp
                <svg class="donut-svg" width="120" height="120" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#EFF6FF" stroke-width="3.5"/>
                    @foreach($categoryDistribution as $index => $category)
                        @php
                            $dash = max($category['percent'], 1);
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $color }}" stroke-width="3.5"
                            stroke-dasharray="{{ $dash }} {{ 100 - $dash }}" stroke-dashoffset="{{ $offset }}"/>
                        @php $offset -= $dash; @endphp
                    @endforeach
                    <text x="18" y="20" text-anchor="middle" font-size="5" font-weight="700" fill="#1A2540">{{ array_sum(array_column($categoryDistribution, 'count')) ?: 0 }}x</text>
                </svg>
                <div class="donut-legend">
                    @forelse($categoryDistribution as $index => $category)
                        @php $color = $colors[$index % count($colors)]; @endphp
                        <div class="legend-item"><div class="legend-dot" style="background:{{ $color }};"></div><span>{{ $category['label'] }} <strong>{{ $category['percent'] }}%</strong></span></div>
                    @empty
                        <div class="legend-item"><div class="legend-dot" style="background:#2563EB;"></div><span>Tidak ada data kategori</span></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Top Listing --}}
<div class="card">
    <div class="card-header"><span class="card-title">🏆 Performa Listing Teratas</span></div>
    @if($topItems->isEmpty())
        <div style="text-align:center;color:var(--text-muted);padding:2rem;">Belum ada listing aktif untuk analitik.</div>
    @else
        @php
            $maxRentals = $topItems->max('total_rentals') ?: 1;
            $rankClasses=['gold','silver','bronze','',''];
            $rankSymbols=['🥇','🥈','🥉','4','5'];
        @endphp
        @foreach($topItems as $i => $item)
        <div class="top-item-row">
            <div class="ti-rank {{ $rankClasses[$i] ?? '' }}">{{ $rankSymbols[$i] ?? ($i + 1) }}</div>
            <div style="font-size:1.5rem;flex-shrink:0;">📦</div>
            <div class="ti-info">
                <div class="ti-name">{{ $item->nama }}</div>
                <div class="ti-sub">{{ $item->kategori ?? 'Lainnya' }} · {{ $item->total_rentals }}x disewa</div>
            </div>
            <div class="ti-bar"><div class="ti-bar-fill" style="width:{{ round($item->total_rentals / $maxRentals * 100) }}%;"></div></div>
            <div class="ti-amount">Rp {{ number_format($item->revenue, 0, ',', '.') }}</div>
        </div>
        @endforeach
    @endif
</div>
@endsection

@push('scripts')
<script>
    function setPeriod(btn) {
        btn.parentElement.querySelectorAll('.period-tab').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
    }
</script>
@endpush
