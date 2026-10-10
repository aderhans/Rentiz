@extends('layouts.app')
@section('title', 'Detail Barang — ' . $barang->nama)
@section('breadcrumb', 'Detail Barang')

@push('styles')
<style>
    .detail-wrap { display:grid; grid-template-columns:1fr 360px; gap:1.5rem; align-items:start; }
    @media(max-width:900px){ .detail-wrap { grid-template-columns:1fr; } }

    /* Gallery */
    .gallery-main { width:100%;border-radius:var(--radius-md);overflow:hidden;background:#F8FAFF;display:flex;align-items:center;justify-content:center;font-size:3rem;margin-bottom:.75rem;min-height:200px; }
    .gallery-main img { width:100%;height:auto;object-fit:contain;display:block;border-radius:var(--radius-md); }
    .gallery-thumbs { display:flex;gap:.5rem;flex-wrap:wrap; }
    .g-thumb { width:68px;height:52px;border-radius:8px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color 150ms;background:#F8FAFF;display:flex;align-items:center;justify-content:center; }
    .g-thumb.active { border-color:var(--color-admin); }
    .g-thumb img { width:100%;height:100%;object-fit:contain; }

    /* Info card */
    .info-section { margin-bottom:1.25rem; }
    .info-section-title { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:.6rem;padding-bottom:.4rem;border-bottom:1px solid var(--card-border); }
    .info-row { display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;padding:.35rem 0;font-size:.84rem; }
    .info-row .label { color:var(--text-secondary);flex-shrink:0;min-width:120px; }
    .info-row .value { font-weight:600;color:var(--text);text-align:right; }

    /* Action card */
    .action-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.25rem;box-shadow:var(--shadow-sm);position:sticky;top:80px; }
    .action-card .price { font-family:var(--font-heading);font-size:1.6rem;font-weight:800;color:var(--color-penyedia);line-height:1; }
    .action-card .price-label { font-size:.76rem;color:var(--text-muted);margin-top:3px; }
    .action-sep { border:none;border-top:1px solid var(--card-border);margin:1rem 0; }

    .desc-box { font-size:.85rem;color:var(--text-secondary);line-height:1.65;white-space:pre-line; }

    .back-link { display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;color:var(--text-secondary);text-decoration:none;margin-bottom:1.25rem;transition:color 150ms; }
    .back-link:hover { color:var(--color-admin); }
    .back-link svg { width:15px;height:15px; }
</style>
@endpush

@section('content')
<a href="{{ route('admin.semua-listing') }}" class="back-link">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali ke Semua Listing
</a>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem;">
    <div>
        <h1 class="page-title">{{ $barang->nama }}</h1>
        <p class="page-subtitle">
            Diupload oleh <strong>{{ $barang->user->name ?? 'Unknown' }}</strong>
            · {{ $barang->created_at->translatedFormat('d M Y, H:i') }} WIB
        </p>
    </div>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
        @if($barang->status === 'active')
            <span class="badge badge-success" style="font-size:.8rem;padding:.4rem 1rem;">✅ Aktif / Publik</span>
        @elseif($barang->status === 'pending')
            <span class="badge badge-warning" style="font-size:.8rem;padding:.4rem 1rem;">⏳ Menunggu Persetujuan</span>
        @else
            <span class="badge badge-neutral" style="font-size:.8rem;padding:.4rem 1rem;">🚫 {{ ucfirst($barang->status) }}</span>
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:var(--success-bg);color:var(--success);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;margin-bottom:1rem;">
    ✅ {{ session('success') }}
</div>
@endif

<div class="detail-wrap">
    {{-- Left: Galeri + Deskripsi --}}
    <div>
        {{-- Galeri Foto --}}
        <div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
            @php $fotos = $barang->fotos; @endphp
            @if($fotos->isNotEmpty())
                <div class="gallery-main" id="gallery-main">
                    <img src="{{ asset('storage/' . $fotos->first()->path_foto) }}" id="main-img" alt="{{ $barang->nama }}">
                </div>
                @if($fotos->count() > 1)
                <div class="gallery-thumbs">
                    @foreach($fotos as $idx => $foto)
                    <div class="g-thumb {{ $idx === 0 ? 'active' : '' }}" onclick="switchPhoto('{{ asset('storage/' . $foto->path_foto) }}', this)">
                        <img src="{{ asset('storage/' . $foto->path_foto) }}" alt="Foto {{ $idx+1 }}">
                    </div>
                    @endforeach
                </div>
                @endif
            @else
                <div class="gallery-main">📷</div>
                <p style="text-align:center;font-size:.82rem;color:var(--text-muted);margin-top:.5rem;">Belum ada foto yang diupload</p>
            @endif
        </div>

        {{-- Deskripsi --}}
        <div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
            <div class="info-section-title">Deskripsi Barang</div>
            <p class="desc-box">{{ $barang->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
        </div>

        {{-- Ketentuan Jaminan --}}
        @if($barang->ketentuan_jaminan)
        <div class="card" style="padding:1.25rem;">
            <div class="info-section-title">Ketentuan Jaminan</div>
            <p class="desc-box">{{ $barang->ketentuan_jaminan }}</p>
        </div>
        @endif
    </div>

    {{-- Right: Info + Aksi Admin --}}
    <div>
        {{-- Info Barang --}}
        <div class="card" style="padding:1.25rem;margin-bottom:1.25rem;">
            <div class="info-section">
                <div class="info-section-title">Informasi Barang</div>
                <div class="info-row">
                    <span class="label">Kategori</span>
                    <span class="value" style="{{ !$barang->kategori ? 'color:var(--text-muted);font-weight:400;font-style:italic;' : '' }}">{{ $barang->kategori->nama ?? 'Tidak ada kategori' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Kondisi</span>
                    <span class="value">{{ ucfirst($barang->kondisi) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Kota</span>
                    <span class="value">{{ $barang->kota }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Min. Sewa</span>
                    <span class="value">{{ $barang->min_durasi_sewa }} hari</span>
                </div>
                <div class="info-row">
                    <span class="label">Maks. Sewa</span>
                    <span class="value">{{ $barang->max_durasi_sewa ? $barang->max_durasi_sewa . ' hari' : 'Tidak dibatasi' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Denda/hari</span>
                    <span class="value">Rp {{ number_format($barang->denda_per_hari, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="info-section">
                <div class="info-section-title">Alamat Pengambilan</div>
                @if($barang->alamat_pengambilan)
                    <p style="font-size:.84rem;color:var(--text-secondary);line-height:1.5;">{{ $barang->alamat_pengambilan }}</p>
                @else
                    <p style="font-size:.84rem;color:var(--text-muted);font-style:italic;">Belum diisi oleh pemilik.</p>
                @endif
            </div>

            <div class="info-section">
                <div class="info-section-title">Pemilik</div>
                <div class="info-row">
                    <span class="label">Nama</span>
                    <span class="value">{{ $barang->user->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value" style="font-size:.78rem;">{{ $barang->user->email ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">No. HP</span>
                    <span class="value">{{ $barang->user->phone ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Kartu Aksi Admin --}}
        <div class="action-card">
            <div class="price">Rp {{ number_format($barang->harga_per_hari, 0, ',', '.') }}</div>
            <div class="price-label">per hari</div>
            <hr class="action-sep">

            @if($barang->status === 'pending')
                <p style="font-size:.82rem;color:var(--text-secondary);margin-bottom:1rem;line-height:1.5;">
                    Barang ini menunggu persetujuan Anda. Tinjau detail di atas sebelum memutuskan.
                </p>
                <form action="{{ route('admin.approve-barang', $barang->id) }}" method="POST" style="margin-bottom:.6rem;">
                    @csrf
                    <button type="submit" class="btn btn-primary-teal" style="width:100%;justify-content:center;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Setujui & Tampilkan ke Publik
                    </button>
                </form>
                <form action="{{ route('admin.reject-barang', $barang->id) }}" method="POST" onsubmit="return confirm('Yakin menolak listing ini?');">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;color:var(--danger);border-color:var(--danger);">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak Listing
                    </button>
                </form>
            @elseif($barang->status === 'active')
                <p style="font-size:.82rem;color:var(--success);font-weight:600;margin-bottom:1rem;">
                    ✅ Listing ini sudah aktif dan terlihat oleh publik.
                </p>
                <form action="{{ route('admin.reject-barang', $barang->id) }}" method="POST" onsubmit="return confirm('Yakin ingin men-takedown listing ini dari publik?');">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;color:var(--danger);border-color:var(--danger);">
                        🚫 Takedown dari Publik
                    </button>
                </form>
            @else
                <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.5;">
                    Listing ini berstatus <strong>{{ ucfirst($barang->status) }}</strong> dan tidak terlihat publik.
                    Pulihkan ke antrian <strong>Pending</strong> agar dapat ditinjau dan disetujui kembali.
                </p>
                <form action="{{ route('admin.restore-barang', $barang->id) }}" method="POST" onsubmit="return confirm('Kembalikan listing ini ke status Pending untuk ditinjau ulang?');">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;color:var(--warning);border-color:var(--warning);">
                        ↩ Pulihkan ke Pending
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchPhoto(src, el) {
        document.getElementById('main-img').src = src;
        document.querySelectorAll('.g-thumb').forEach(function(t){ t.classList.remove('active'); });
        el.classList.add('active');
    }
</script>
@endpush
