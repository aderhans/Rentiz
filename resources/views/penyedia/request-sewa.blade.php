@extends('layouts.app')
@section('title', 'Request & Pesanan Sewa')
@section('breadcrumb', 'Request Sewa')

@push('styles')
<style>
    .req-tabs {
        display: flex;
        gap: 0;
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 4px;
        margin-bottom: 1.5rem;
        overflow-x: auto;
    }
    .req-tab {
        flex: 1;
        min-width: 120px;
        padding: 0.55rem 1rem;
        border-radius: calc(var(--radius-md) - 4px);
        border: none;
        background: transparent;
        font-size: 0.82rem;
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
    .req-tab .tb {
        background: var(--card-border);
        color: var(--text-muted);
        font-size: 0.68rem;
        padding: 1px 7px;
        border-radius: 50px;
    }
    .req-tab.active {
        background: var(--color-penyedia);
        color: white;
    }
    .req-tab.active .tb {
        background: rgba(255,255,255,0.25);
        color: white;
    }

    .req-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.4rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 1.2rem;
        transition: box-shadow 200ms;
    }
    .req-card:hover { box-shadow: var(--shadow-sm); }
    .req-card.border-l-menunggu { border-left: 4px solid var(--warning); }
    .req-card.border-l-aktif    { border-left: 4px solid var(--success); }
    .req-card.border-l-selesai  { border-left: 4px solid var(--info); }

    .req-thumb {
        width: 68px;
        height: 68px;
        border-radius: var(--radius-md);
        object-fit: cover;
        background: #f1f5f9;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .req-info { flex: 1; min-width: 0; }
    .req-header-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
        flex-wrap: wrap;
    }
    .req-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text);
    }
    .req-renter {
        font-size: 0.8rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 0.6rem;
    }
    .req-meta {
        display: flex;
        gap: 1.2rem;
        flex-wrap: wrap;
        font-size: 0.78rem;
        color: var(--text-muted);
    }
    .req-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .req-meta-item svg { width: 14px; height: 14px; flex-shrink: 0; }

    .req-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .btn-handover {
        background: #003d82;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        font-family: var(--font-body);
        transition: all 150ms;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
    }
    .btn-handover:hover {
        background: #002855;
        color: white;
    }

    .badge-paid {
        background: #DCFCE7;
        color: #15803D;
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
    <div>
        <h1 class="page-title">🔔 Request & Pesanan Masuk</h1>
        <p class="page-subtitle">Kelola pesanan dari penyewa dan lakukan serah terima barang secara aman</p>
    </div>
    <div>
        @if($totalMenunggu > 0)
            <span class="badge badge-warning" style="font-size:0.84rem;padding:6px 14px;font-weight:700;">
                ⏳ {{ $totalMenunggu }} Menunggu Serah Terima
            </span>
        @else
            <span class="badge badge-success" style="font-size:0.84rem;padding:6px 14px;font-weight:600;">
                ✓ Semua Pesanan Tertangani
            </span>
        @endif
    </div>
</div>

<div class="req-tabs">
    <button class="req-tab active" onclick="switchReqTab('all', this)">
        Semua <span class="tb">{{ $totalSemua }}</span>
    </button>
    <button class="req-tab" onclick="switchReqTab('menunggu', this)">
        Menunggu Pengambilan <span class="tb">{{ $totalMenunggu }}</span>
    </button>
    <button class="req-tab" onclick="switchReqTab('aktif', this)">
        Sedang Disewa <span class="tb">{{ $totalAktif }}</span>
    </button>
    <button class="req-tab" onclick="switchReqTab('selesai', this)">
        Selesai <span class="tb">{{ $totalSelesai }}</span>
    </button>
</div>

<div id="req-list">
@forelse($items as $item)
    @php
        $type = 'menunggu';
        $borderClass = 'border-l-menunggu';
        if ($item->status === 'active') {
            $type = 'aktif';
            $borderClass = 'border-l-aktif';
        } elseif (in_array($item->status, ['completed', 'returned'])) {
            $type = 'selesai';
            $borderClass = 'border-l-selesai';
        }

        $photo = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
        if (!$photo && $item->barang->fotos) {
            $photo = $item->barang->fotos->first();
        }

        $hasChecklist = $item->checklistPengambilan !== null;
    @endphp

    <div class="req-card {{ $borderClass }}" data-type="{{ $type }}">
        @if($photo)
            <img src="{{ asset('storage/' . $photo->path_foto) }}" class="req-thumb" alt="Foto">
        @else
            <div class="req-thumb">📦</div>
        @endif

        <div class="req-info">
            <div class="req-header-row">
                <span class="req-name">{{ $item->barang->nama }}</span>
                <span class="badge-paid">✓ LUNAS (Rp {{ number_format($item->subtotal, 0, ',', '.') }})</span>
            </div>

            <div class="req-renter">
                <span>Penyewa: <strong>{{ $item->pesanan->pemesan->name ?? 'Penyewa' }}</strong></span>
                @if($item->pesanan->pemesan && $item->pesanan->pemesan->phone)
                    <span>· 📞 {{ $item->pesanan->pemesan->phone }}</span>
                @endif
            </div>

            <div class="req-meta">
                <span class="req-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                </span>
                <span class="req-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Durasi: {{ $item->durasi_hari }} Hari
                </span>
                <span class="req-meta-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $item->barang->alamat_pengambilan ?: $item->barang->kota }}
                </span>
            </div>

            @if($hasChecklist)
                <div style="margin-top: 0.6rem; font-size: 0.76rem; color: #059669; display: flex; align-items: center; gap: 4px;">
                    <span>✓ Penyewa telah melengkapi checklist serah terima barang</span>
                </div>
            @endif
        </div>

        <div class="req-actions">
            @if($item->status === 'confirmed')
                <span class="badge badge-warning" style="font-size:0.75rem;">⏳ Menunggu Pengambilan</span>
                <span style="font-size:0.72rem;color:var(--text-muted);text-align:right;margin-top:4px;">Penyewa akan mengambil<br>& mengisi checklist</span>
            @elseif($item->status === 'active')
                <span class="badge badge-success" style="font-size:0.75rem;">● Sedang Disewa</span>
                <a href="{{ route('penyedia.request-sewa.pengembalian', $item->id) }}" class="btn-handover" style="margin-top:0.4rem;">
                    <span>📋 Konfirmasi Pengembalian</span>
                </a>
            @else
                <span class="badge badge-info" style="font-size:0.75rem;">✓ Selesai</span>
            @endif
        </div>
    </div>
@empty
    <div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted); background: white; border-radius: var(--radius-lg); border: 1px solid var(--card-border);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text); margin-bottom: 0.5rem;">Tidak ada pesanan masuk</h3>
        <p>Belum ada permintaan sewa atau pesanan baru dari pengguna.</p>
    </div>
@endforelse
</div>
@endsection

@push('scripts')
<script>
    function switchReqTab(type, btn) {
        document.querySelectorAll('.req-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        var cards = document.querySelectorAll('#req-list .req-card');
        var visibleCount = 0;

        cards.forEach(function(c) {
            if (type === 'all' || c.dataset.type === type) {
                c.style.display = '';
                visibleCount++;
            } else {
                c.style.display = 'none';
            }
        });
    }
</script>
@endpush
