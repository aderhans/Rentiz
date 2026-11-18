@extends('layouts.app')

@section('title', 'Checklist Pengambilan')
@section('breadcrumb', 'Penyewaan Aktif / Checklist Pengambilan')

@push('styles')
<style>
    .checklist-form {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--card-border);
    }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text); }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #CBD5E1;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.9rem;
    }
    .form-control:focus { outline: none; border-color: var(--color-penyewa); }
    .item-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #F8FAFC;
        padding: 1rem;
        border-radius: var(--radius-sm);
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="page-header text-center">
    <h1 class="page-title">📋 Checklist Pengambilan</h1>
    <p class="page-subtitle">Pastikan kondisi barang sebelum mulai disewa</p>
</div>

<div class="checklist-form">
    <div class="item-summary">
        @php
            $primaryPhoto = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
            if (!$primaryPhoto && $item->barang->fotos) $primaryPhoto = $item->barang->fotos->first();
        @endphp
        @if($primaryPhoto)
            <img src="{{ asset('storage/' . $primaryPhoto->path_foto) }}" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
        @else
            <div style="width:60px; height:60px; background:#CBD5E1; border-radius:8px; display:flex; align-items:center; justify-content:center;">📷</div>
        @endif
        <div>
            <div style="font-weight:700; color:var(--text);">{{ $item->barang->nama }}</div>
            <div style="font-size:0.8rem; color:var(--text-muted);">{{ $item->durasi_hari }} Hari Sewa</div>
        </div>
    </div>

    <form action="{{ route('penyewa.penyewaan-aktif.checklist.simpan', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">Fungsionalitas Barang</label>
            <select name="fungsionalitas" class="form-control" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Semua fungsi berjalan normal">Semua fungsi berjalan normal</option>
                <option value="Ada fitur yang tidak berfungsi">Ada fitur yang tidak berfungsi (Tulis di catatan nanti)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Kelengkapan Barang</label>
            <select name="kelengkapan" class="form-control" required>
                <option value="">-- Pilih Kelengkapan --</option>
                <option value="Lengkap sesuai deskripsi">Lengkap sesuai deskripsi</option>
                <option value="Ada aksesoris yang kurang">Ada aksesoris yang kurang</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Kondisi Fisik</label>
            <select name="kondisi_fisik" class="form-control" required>
                <option value="">-- Pilih Kondisi Fisik --</option>
                <option value="Mulus / Sangat Baik">Mulus / Sangat Baik</option>
                <option value="Ada lecet/goresan wajar">Ada lecet/goresan wajar</option>
                <option value="Ada kerusakan fisik mencolok">Ada kerusakan fisik mencolok</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Foto Bukti Pengambilan</label>
            <input type="file" name="foto_bukti" class="form-control" accept="image/*" required>
        </div>

        <div class="form-group">
            <label class="form-label">Catatan Tambahan (Opsional)</label>
            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Ada lecet kecil di sebelah kiri..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 1rem; margin-top: 1rem;">
            Kirim untuk Verifikasi
        </button>
    </form>
</div>
@endsection
