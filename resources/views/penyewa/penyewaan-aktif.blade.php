@extends('layouts.app')

@section('title', 'Penyewaan Aktif')
@section('breadcrumb', 'Penyewaan Aktif')

@push('styles')
<style>
    .rental-tabs {
        display: flex;
        gap: 0;
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 4px;
        margin-bottom: 1.5rem;
        overflow-x: auto;
    }
    .rental-tab {
        flex: 1;
        min-width: 100px;
        padding: 0.55rem 1rem;
        border-radius: calc(var(--radius-md) - 4px);
        border: none;
        background: transparent;
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 200ms;
        white-space: nowrap;
        font-family: var(--font-body);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .rental-tab .tab-badge {
        background: var(--card-border);
        color: var(--text-muted);
        font-size: 0.68rem;
        padding: 1px 6px;
        border-radius: 50px;
    }
    .rental-tab.active {
        background: var(--color-penyewa);
        color: white;
    }
    .rental-tab.active .tab-badge {
        background: rgba(255,255,255,0.25);
        color: white;
    }

    /* Rental card */
    .rental-list { display: flex; flex-direction: column; gap: 1rem; }
    .rental-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.4rem;
        display: flex;
        gap: 1.1rem;
        align-items: flex-start;
        transition: box-shadow 200ms;
    }
    .rental-card:hover { box-shadow: var(--shadow-md); }
    .rental-card.border-aktif  { border-left: 4px solid var(--success); }
    .rental-card.border-proses { border-left: 4px solid var(--warning); }
    .rental-card.border-jadwal { border-left: 4px solid var(--info); }

    .rental-emoji {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        flex-shrink: 0;
    }
    .rental-info { flex: 1; min-width: 0; }
    .rental-name { font-weight: 700; font-size: 0.95rem; color: var(--text); margin-bottom: 3px; }
    .rental-seller { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.6rem; }
    .rental-meta {
        display: flex;
        gap: 1.2rem;
        flex-wrap: wrap;
    }
    .rental-meta-item {
        font-size: 0.79rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .rental-meta-item svg { width: 14px; height: 14px; flex-shrink: 0; }

    .rental-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
        flex-shrink: 0;
    }

    /* Progress bar */
    .rental-progress {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #F1F5F9;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.74rem;
        color: var(--text-muted);
        margin-bottom: 5px;
    }
    .progress-bar-wrap {
        background: #F1F5F9;
        border-radius: 50px;
        height: 6px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 50px;
        transition: width 0.8s ease;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    .empty-state .empty-icon { font-size: 3.5rem; margin-bottom: 0.75rem; }
    .empty-state h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.4rem; }
    .empty-state p { font-size: 0.86rem; margin-bottom: 1.25rem; }

    /* Timeline modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,32,68,0.45);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 250ms;
        backdrop-filter: blur(3px);
    }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-box {
        background: white;
        border-radius: var(--radius-xl);
        width: 100%;
        max-width: 480px;
        box-shadow: var(--shadow-lg);
        transform: translateY(20px);
        transition: transform 250ms;
        overflow: hidden;
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-header {
        padding: 1.25rem 1.4rem;
        border-bottom: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title { font-family: var(--font-heading); font-weight: 700; font-size: 1rem; color: var(--text); }
    .modal-close { background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; padding: 4px; border-radius: 6px; }
    .modal-close:hover { background: var(--content-bg); color: var(--text); }
    .modal-body { padding: 1.4rem; }

    /* Timeline */
    .timeline { list-style: none; position: relative; }
    .timeline::before {
        content:'';
        position: absolute;
        left: 15px;
        top: 0; bottom: 0;
        width: 2px;
        background: #E2E8F0;
    }
    .timeline li {
        position: relative;
        padding-left: 2.5rem;
        padding-bottom: 1.2rem;
    }
    .timeline li:last-child { padding-bottom: 0; }
    .tl-dot {
        position: absolute;
        left: 7px;
        top: 3px;
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid white;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        font-weight: 700;
        color: white;
    }
    .tl-done { background: var(--success); }
    .tl-current { background: var(--color-penyewa); box-shadow: 0 0 0 4px rgba(13,148,136,0.2); }
    .tl-pending { background: #CBD5E1; }
    .tl-text { font-size: 0.83rem; font-weight: 600; color: var(--text); }
    .tl-date { font-size: 0.74rem; color: var(--text-muted); margin-top: 2px; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
    <div>
        <h1 class="page-title">📋 Penyewaan Aktif</h1>
        <p class="page-subtitle">Pantau semua transaksi sewa yang sedang berjalan</p>
    </div>
    <a href="{{ route('penyewa.cari-barang') }}" class="btn btn-primary-teal">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Sewa Barang Baru
    </a>
</div>

{{-- Tab Filter --}}
<div class="rental-tabs" role="tablist">
    @php
        $countAktif = $items->where('status', 'active')->count();
        $countProses = $items->where('status', 'confirmed')->count();
        $countAll = $items->count();
    @endphp
    <button class="rental-tab active" id="tab-all"    onclick="switchTab('all',this)">
        Semua <span class="tab-badge">{{ $countAll }}</span>
    </button>
    <button class="rental-tab" id="tab-aktif"  onclick="switchTab('aktif',this)">
        Aktif <span class="tab-badge">{{ $countAktif }}</span>
    </button>
    <button class="rental-tab" id="tab-proses" onclick="switchTab('proses',this)">
        Diproses <span class="tab-badge">{{ $countProses }}</span>
    </button>
</div>

{{-- Rental List --}}
<div class="rental-list" id="rental-list">
    @if($items->isEmpty())
        <div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada penyewaan aktif</h3>
            <p>Anda belum memiliki transaksi penyewaan yang sedang berjalan.</p>
        </div>
    @else
        @foreach($items as $item)
            @php
                $primaryPhoto = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
                if (!$primaryPhoto && $item->barang->fotos) $primaryPhoto = $item->barang->fotos->first();
                $dataStatus = $item->status === 'active' ? 'aktif' : 'proses';
                $borderClass = $item->status === 'active' ? 'border-aktif' : 'border-proses';
            @endphp
            <div class="rental-card {{ $borderClass }}" data-status="{{ $dataStatus }}">
                @if($primaryPhoto)
                    <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" class="rental-emoji" style="object-fit:cover;">
                @else
                    <div class="rental-emoji" style="background:var(--card-border);">📷</div>
                @endif
                
                <div class="rental-info">
                    <div class="rental-name">{{ $item->barang->nama }}</div>
                    <div class="rental-seller">Toko: {{ $item->pesanan->pemilik->name ?? 'Penyedia' }}</div>
                    <div class="rental-meta">
                        <div class="rental-meta-item">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                        </div>
                        <div class="rental-meta-item">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $item->durasi_hari }} Hari
                        </div>
                    </div>

                    @if($item->status === 'active')
                        <div class="rental-progress">
                            <div class="progress-label">
                                <span>Sisa Waktu</span>
                                @php
                                    $end = \Carbon\Carbon::parse($item->tanggal_selesai)->endOfDay();
                                    $now = now();
                                    $sisa = $end->diffInDays($now);
                                    if ($end->isPast()) $sisa = 0;
                                @endphp
                                <span style="color:var(--text);font-weight:600;">{{ $sisa }} hari lagi</span>
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width: 50%; background:var(--color-penyewa);"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="rental-actions">
                    @if($item->status === 'active')
                        <span class="badge" style="background: rgba(16,185,129,0.1); color: #10B981; border:none;">
                            Sedang Disewa
                        </span>
                    @elseif($item->status === 'confirmed')
                        @if($item->checklistPengambilan)
                            <span class="badge" style="background: rgba(59,130,246,0.1); color: #3B82F6; border:none;">
                                ⏳ Menunggu Verifikasi Penyedia
                            </span>
                        @else
                            <span class="badge" style="background: rgba(245,158,11,0.1); color: #F59E0B; border:none;">
                                Menunggu Pengambilan
                            </span>
                            <a href="{{ route('penyewa.penyewaan-aktif.checklist', $item->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size:0.75rem; margin-top: 0.5rem;">
                                Isi Checklist
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- Modal Detail Sewa --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Detail Sewa</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modal-body">
            {{-- Filled by JS --}}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var currentTab = 'all';

    function switchTab(tab, btn) {
        currentTab = tab;
        document.querySelectorAll('.rental-tab').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('#rental-list .rental-card').forEach(function(card) {
            if (tab === 'all' || card.dataset.status === tab) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    var modals = {};

    function openModal(key) {
        var data = modals[key];
        document.getElementById('modal-title').textContent = data.title;
        document.getElementById('modal-body').innerHTML = data.body;
        document.getElementById('modal-overlay').classList.add('open');
    }

    function closeModal(e) {
        if (!e || e.target === document.getElementById('modal-overlay')) {
            document.getElementById('modal-overlay').classList.remove('open');
        }
    }

    function showAlert(msg) {
        alert(msg);
    }
</script>
@endpush
