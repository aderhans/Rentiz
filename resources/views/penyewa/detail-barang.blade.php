@extends('layouts.app')

@section('title', $barang->nama)
@section('breadcrumb')
    <a href="{{ route('penyewa.cari-barang') }}" style="color:var(--text-muted); text-decoration:none;">Cari Barang</a> / Detail
@endsection

@push('styles')
<style>
    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 2rem;
        align-items: start;
    }

    .main-content-wrapper {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 2rem;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .main-content-wrapper { grid-template-columns: 1fr; }
    }

    @media (max-width: 800px) {
        .detail-layout { grid-template-columns: 1fr; }
    }

    /* Left Column */
    .gallery-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: sticky;
        top: calc(var(--header-h) + 1.5rem);
    }

    .main-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        height: auto;
        border-radius: var(--radius-lg);
        object-fit: cover;
        background: #f1f5f9;
        border: 1px solid var(--card-border);
    }

    .thumbnail-list {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .thumbnail {
        width: 75px;
        height: 75px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 200ms;
    }

    .thumbnail:hover, .thumbnail.active {
        border-color: var(--color-penyewa);
    }

    .content-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--card-border);
    }

    .content-title {
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: #f8fafc;
        padding: 1rem;
        border-radius: var(--radius-sm);
        border: 1px solid #e2e8f0;
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-weight: 600;
        color: var(--text);
    }

    /* Right Column (Sticky Panel) */
    .sticky-panel {
        position: sticky;
        top: calc(var(--header-h) + 1.5rem);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .action-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow-md);
    }

    .price-tag {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-penyewa);
        margin-bottom: 0.25rem;
    }

    .calendar-widget {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        padding: 1rem;
        margin-top: 1rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .provider-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
    }

    .provider-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 1.2rem;
        font-size: 1.2rem;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

<div class="detail-layout">
    {{-- Left Main Content --}}
    <div class="main-content-wrapper">
        {{-- Gallery --}}
        <div class="gallery-container">
            @php
                $fotos = $barang->fotos ?? collect([]);
                $primaryPhoto = $fotos->where('is_primary', true)->first() ?? $fotos->first();
            @endphp
            
            @if($primaryPhoto)
                <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" class="main-image" id="mainImage">
            @else
                <div class="main-image" style="display:flex;align-items:center;justify-content:center;color:#94a3b8;">
                    <svg style="width:64px;height:64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif

            @if($fotos->count() > 1)
            <div class="thumbnail-list">
                @foreach($fotos as $foto)
                    <img src="{{ asset('storage/' . $foto->path_foto) }}" class="thumbnail {{ $loop->first ? 'active' : '' }}" onclick="changeImage(this, '{{ asset('storage/' . $foto->path_foto) }}')">
                @endforeach
            </div>
            @endif
        </div>

        {{-- Detail Information --}}
        <div class="content-card">
            <h1 style="font-family:var(--font-heading); font-size:1.75rem; font-weight:800; color:var(--text); margin-bottom:0.5rem;">{{ $barang->nama }}</h1>
            
            <div style="display:flex; gap:1rem; align-items:center; margin-bottom:1.5rem; font-size:0.9rem; color:var(--text-secondary);">
                <span>⭐ 0.0 (0 Ulasan)</span>
                <span>•</span>
                <span>Tersewa 0 kali</span>
                <span>•</span>
                <span style="color: {{ $barang->status == 'active' ? 'var(--success)' : 'var(--danger)' }}; font-weight:600;">
                    {{ $barang->status == 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
                </span>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Kondisi Fisik</div>
                    <div class="info-value">{{ ucfirst($barang->kondisi ?? 'Baik') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lokasi Pengambilan</div>
                    <div class="info-value">{{ $barang->kota }}</div>
                </div>
            </div>

            <h2 class="content-title">Deskripsi Barang</h2>
            <div style="line-height:1.6; color:var(--text-secondary); margin-bottom:2rem; white-space:pre-wrap;">{{ $barang->deskripsi ?: 'Tidak ada deskripsi.' }}</div>
            
            <h2 class="content-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:var(--warning);"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Syarat & Ketentuan
            </h2>
            <div class="info-grid">
                <div class="info-item" style="border-left: 3px solid var(--warning);">
                    <div class="info-label">Ketentuan Jaminan</div>
                    <div class="info-value" style="font-size:0.9rem;">{{ $barang->ketentuan_jaminan ?: 'KTP Asli / Dokumen yang setara' }}</div>
                </div>
                <div class="info-item" style="border-left: 3px solid var(--danger);">
                    <div class="info-label">Besaran Denda (Keterlambatan)</div>
                    <div class="info-value" style="font-size:0.9rem; color:var(--danger);">Rp {{ number_format($barang->denda_per_hari ?? 0, 0, ',', '.') }} / hari</div>
                </div>
            </div>
            @if($barang->min_durasi_sewa || $barang->max_durasi_sewa)
            <div style="font-size:0.85rem; color:var(--text-secondary); background:#f1f5f9; padding:0.75rem; border-radius:6px; margin-top:-0.5rem;">
                Durasi Sewa: Minimal {{ $barang->min_durasi_sewa ?? 1 }} hari, Maksimal {{ $barang->max_durasi_sewa ?? '~' }} hari.
            </div>
            @endif
        </div>
    </div>

    {{-- Right Sticky Panel --}}
    <div class="sticky-panel">
        
        <div class="action-card">
            <div style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:0.25rem;">Harga Sewa</div>
            <div class="price-tag">Rp {{ number_format($barang->harga_per_hari, 0, ',', '.') }} <span style="font-size:0.9rem;font-weight:400;color:var(--text-muted);">/hari</span></div>

            <div class="calendar-widget">
                <div style="font-weight:700; margin-bottom:0.5rem; color:var(--text);">📅 Kalender Ketersediaan</div>
                <div style="font-size:0.8rem; color:var(--text-secondary);">Barang ini saat ini berstatus <strong>{{ $barang->status == 'active' ? 'Tersedia' : 'Disewa' }}</strong> untuk disewa. Silakan pilih tanggal pada saat proses penyewaan.</div>
            </div>

            @if(auth()->check() && auth()->id() == $barang->user_id)
                <button type="button" class="btn btn-block" disabled style="background:#94a3b8; color:white; width:100%; border:none; padding:0.8rem; border-radius:6px; font-weight:700;">Barang Anda Sendiri</button>
            @elseif($barang->status != 'active')
                <button type="button" class="btn btn-block" disabled style="background:#94a3b8; color:white; width:100%; border:none; padding:0.8rem; border-radius:6px; font-weight:700;">Tidak Tersedia</button>
            @else
                <button type="button" onclick="openSewaModal('{{ $barang->id }}', '{{ addslashes($barang->nama) }}', 'sewa')" style="background:var(--color-penyewa); color:white; border:none; width:100%; padding:0.85rem; border-radius:6px; font-weight:700; font-size:1rem; cursor:pointer; margin-bottom:0.75rem;">Sewa Sekarang</button>
                <button type="button" onclick="openSewaModal('{{ $barang->id }}', '{{ addslashes($barang->nama) }}', 'keranjang')" style="background:white; color:var(--color-penyewa); border:1.5px solid var(--color-penyewa); width:100%; padding:0.85rem; border-radius:6px; font-weight:700; font-size:1rem; cursor:pointer;">+ Keranjang</button>
            @endif
        </div>

        <div class="provider-card">
            <div class="provider-avatar">
                {{ substr($barang->user->name ?? 'P', 0, 1) }}
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:0.2rem;">Profil Penyedia</div>
                <div style="font-weight:700; color:var(--text);">{{ $barang->user->name ?? 'Tidak Diketahui' }}</div>
                <div style="font-size:0.8rem; color:var(--text-secondary);">Bergabung sejak {{ $barang->user->created_at ? $barang->user->created_at->format('Y') : '-' }}</div>
            </div>
        </div>
        
    </div>
</div>

{{-- Sewa Modal (Reused from cari-barang) --}}
<div id="sewaModal" style="display:none; position:fixed; inset:0; background:rgba(15,32,68,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(2px);">
    <div style="background:white; border-radius:var(--radius-lg); width:90%; max-width:400px; padding:1.5rem; position:relative; box-shadow:var(--shadow-lg);">
        <button type="button" onclick="closeSewaModal()" style="position:absolute; top:1rem; right:1.2rem; background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        <h3 id="modalTitle" style="margin-bottom:0.25rem; font-family:var(--font-heading); font-size:1.2rem; color:var(--text);">Pilih Tanggal Sewa</h3>
        <p id="modalItemName" style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1.25rem;"></p>
        
        <form action="{{ route('penyewa.keranjang.tambah') }}" method="POST">
            @csrf
            <input type="hidden" name="barang_id" id="modalBarangId">
            <input type="hidden" name="action_type" id="modalActionType">
            
            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.85rem; margin-bottom:0.4rem; font-weight:600; color:var(--text-secondary);">Tanggal Pengambilan</label>
                <input type="text" name="tanggal_mulai" id="tanggal_mulai" required placeholder="Pilih Tanggal Pengambilan..." style="width:100%; padding:0.65rem; border:1.5px solid var(--card-border); border-radius:var(--radius-sm); font-family:var(--font-body); font-size:0.9rem; background:white;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; font-size:0.85rem; margin-bottom:0.4rem; font-weight:600; color:var(--text-secondary);">Tanggal Pengembalian</label>
                <input type="text" name="tanggal_selesai" id="tanggal_selesai" required placeholder="Pilih Tanggal Pengembalian..." style="width:100%; padding:0.65rem; border:1.5px solid var(--card-border); border-radius:var(--radius-sm); font-family:var(--font-body); font-size:0.9rem; background:white;">
            </div>
            
            <button type="submit" class="btn-sewa-mini" id="modalSubmitBtn" style="width:100%; margin:0; padding:0.75rem; font-size:0.9rem; background:var(--color-penyewa); color:white; border:none; border-radius:6px; font-weight:700; cursor:pointer;">Lanjut</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function changeImage(thumbnail, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    function openSewaModal(id, name, actionType) {
        document.getElementById('modalBarangId').value = id;
        document.getElementById('modalActionType').value = actionType;
        document.getElementById('modalItemName').innerText = name;
        
        if(actionType === 'sewa') {
            document.getElementById('modalSubmitBtn').innerText = "Sewa Sekarang";
        } else {
            document.getElementById('modalSubmitBtn').innerText = "Masukkan Keranjang";
        }
        
        document.getElementById('sewaModal').style.display = 'flex';
    }

    function closeSewaModal() {
        document.getElementById('sewaModal').style.display = 'none';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const bookedDates = @json($bookedDates ?? []);
        const disabledRanges = bookedDates.map(range => ({
            from: range.from,
            to: range.to
        }));

        let startDatePicker = flatpickr("#tanggal_mulai", {
            minDate: "today",
            disable: disabledRanges,
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    endDatePicker.set('minDate', dateStr);
                }
            }
        });

        let endDatePicker = flatpickr("#tanggal_selesai", {
            minDate: "today",
            disable: disabledRanges,
            dateFormat: "Y-m-d"
        });
    });
</script>
@endpush
