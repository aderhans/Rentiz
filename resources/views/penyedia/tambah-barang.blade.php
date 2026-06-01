@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('breadcrumb', 'Tambah Barang')

@push('styles')
<style>
    .form-layout { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }
    @media(max-width:900px){ .form-layout{ grid-template-columns:1fr; } }

    .step-indicator { display:flex; align-items:center; margin-bottom:1.75rem; }
    .step { display:flex; align-items:center; gap:.5rem; }
    .step-dot { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0;border:2px solid; }
    .step-dot.done { background:var(--color-penyedia);border-color:var(--color-penyedia);color:white; }
    .step-dot.active { background:white;border-color:var(--color-penyedia);color:var(--color-penyedia); }
    .step-dot.pending { background:white;border-color:var(--card-border);color:var(--text-muted); }
    .step-label { font-size:.78rem;font-weight:600; }
    .step-label.active { color:var(--color-penyedia); }
    .step-label.pending { color:var(--text-muted); }
    .step-line { flex:1;height:2px;background:var(--card-border);margin:0 .75rem; }
    .step-line.done { background:var(--color-penyedia); }

    .section-title { font-family:var(--font-heading);font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:1rem;padding-bottom:.65rem;border-bottom:1px solid var(--card-border); }
    .fgroup { margin-bottom:1rem; }
    .flabel { display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.35rem; }
    .finput { width:100%;padding:.65rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.87rem;font-family:var(--font-body);color:var(--text);outline:none;transition:border-color 200ms; }
    .finput:focus { border-color:var(--color-penyedia); }
    .frow { display:grid;grid-template-columns:1fr 1fr;gap:.85rem; }
    @media(max-width:600px){ .frow{ grid-template-columns:1fr; } }

    /* Upload area */
    .upload-area { border:2px dashed var(--card-border);border-radius:var(--radius-md);padding:2rem 1rem;text-align:center;cursor:pointer;transition:all 200ms;background:#FAFBFF; }
    .upload-area:hover { border-color:var(--color-penyedia);background:rgba(37,99,235,.03); }
    .upload-icon { font-size:2.5rem;margin-bottom:.5rem; }
    .upload-text { font-size:.84rem;color:var(--text-secondary);font-weight:500; }
    .upload-sub { font-size:.74rem;color:var(--text-muted);margin-top:3px; }

    /* Preview images */
    .img-preview { display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem; }
    .img-thumb { width:64px;height:64px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;border:1.5px solid var(--card-border);position:relative;background:#F8FAFF; }
    .img-remove { position:absolute;top:-6px;right:-6px;width:16px;height:16px;border-radius:50%;background:var(--danger);color:white;font-size:.6rem;display:flex;align-items:center;justify-content:center;cursor:pointer;border:1.5px solid white; }

    /* Pricing preview */
    .price-preview { background:linear-gradient(135deg,#2563EB,#1a3a8f);border-radius:var(--radius-lg);padding:1.1rem 1.2rem;color:white;margin-bottom:1rem; }
    .price-preview-label { font-size:.75rem;color:rgba(255,255,255,.7);margin-bottom:3px; }
    .price-preview-val { font-family:var(--font-heading);font-size:1.5rem;font-weight:700; }

    /* Tag input */
    .tags-wrap { display:flex;flex-wrap:wrap;gap:.4rem;padding:.5rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);min-height:42px;cursor:text;align-items:center; }
    .tags-wrap:focus-within { border-color:var(--color-penyedia); }
    .tag-chip { background:#EFF6FF;color:var(--color-penyedia);border-radius:50px;padding:2px 10px;font-size:.76rem;font-weight:600;display:flex;align-items:center;gap:4px; }
    .tag-chip span { cursor:pointer;opacity:.6; }
    .tag-chip span:hover { opacity:1; }
    .tag-input { border:none;outline:none;font-size:.82rem;font-family:var(--font-body);color:var(--text);min-width:80px;flex:1; }
</style>
@endpush

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1 class="page-title">➕ Tambah Barang Baru</h1>
        <p class="page-subtitle">Lengkapi informasi barang untuk mulai mendapatkan penyewa</p>
    </div>
    <a href="{{ route('penyedia.daftar-barang') }}" class="btn btn-outline btn-sm">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>
</div>

{{-- Step Indicator --}}
<div class="step-indicator">
    <div class="step">
        <div class="step-dot done" id="dot1">1</div>
        <div class="step-label active" id="lbl1">Informasi</div>
    </div>
    <div class="step-line done" id="line1"></div>
    <div class="step">
        <div class="step-dot active" id="dot2">2</div>
        <div class="step-label active" id="lbl2">Foto & Harga</div>
    </div>
    <div class="step-line" id="line2"></div>
    <div class="step">
        <div class="step-dot pending" id="dot3">3</div>
        <div class="step-label pending" id="lbl3">Konfirmasi</div>
    </div>
</div>

<div class="form-layout">
    {{-- Kiri: Form Utama --}}
    <form method="POST" action="{{ route('penyedia.store-barang') }}" enctype="multipart/form-data" id="form-tambah">
        @csrf
        {{-- Informasi Dasar --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header"><span class="card-title">📋 Informasi Dasar</span></div>
            <div class="card-body">
                <div class="fgroup">
                    <label class="flabel">Nama Barang <span style="color:var(--danger);">*</span></label>
                    <input type="text" class="finput" id="item-name" name="title" placeholder="Contoh: Kamera Sony A7III + 2 Lensa" required oninput="updatePreview()">
                </div>
                <div class="frow">
                    <div class="fgroup">
                        <label class="flabel">Kategori <span style="color:var(--danger);">*</span></label>
                        <select class="finput" name="category" required>
                            <option value="">Pilih kategori...</option>
                            <option value="Fotografi">Fotografi</option>
                            <option value="Drone">Drone</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Outdoor & Camping">Outdoor & Camping</option>
                            <option value="Gaming">Gaming</option>
                            <option value="Audio & Musik">Audio & Musik</option>
                            <option value="Kendaraan">Kendaraan</option>
                            <option value="Alat Rumah">Alat Rumah</option>
                        </select>
                    </div>
                    <div class="fgroup">
                        <label class="flabel">Kondisi Barang</label>
                        <select class="finput" name="kondisi" required>
                            <option value="Baru (99%)">Baru (99%)</option>
                            <option value="Seperti Baru (95%)">Seperti Baru (95%)</option>
                            <option value="Sangat Baik (90%)">Sangat Baik (90%)</option>
                            <option value="Baik (80%)">Baik (80%)</option>
                        </select>
                    </div>
                </div>
                <div class="fgroup">
                    <label class="flabel">Deskripsi <span style="color:var(--danger);">*</span></label>
                    <textarea class="finput" name="description" rows="4" placeholder="Jelaskan kondisi, kelengkapan, cara penggunaan, dan aturan penyewaan barang..." required></textarea>
                </div>
                <div class="fgroup">
                    <label class="flabel">Tag / Kata Kunci</label>
                    <div class="tags-wrap" id="tags-wrap" onclick="document.getElementById('tag-input').focus()">
                        <div class="tag-chip">kamera <span onclick="removeTag(this)">✕</span></div>
                        <div class="tag-chip">sony <span onclick="removeTag(this)">✕</span></div>
                        <div class="tag-chip">mirrorless <span onclick="removeTag(this)">✕</span></div>
                        <input type="text" class="tag-input" id="tag-input" placeholder="Tambah tag..." onkeydown="addTag(event)">
                    </div>
                    <p style="font-size:.72rem;color:var(--text-muted);margin-top:4px;">Tekan Enter untuk menambahkan tag</p>
                </div>
            </div>
        </div>

        {{-- Foto Barang --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header"><span class="card-title">📸 Foto Barang</span></div>
            <div class="card-body">
                <div class="upload-area" onclick="document.getElementById('image-upload').click()">
                    <div class="upload-icon">📁</div>
                    <div class="upload-text">Klik untuk pilih foto (maksimal 4 gambar)</div>
                    <div class="upload-sub">PNG, JPG, WEBP — Maks 2MB per gambar</div>
                    <input type="file" id="image-upload" name="images[]" accept="image/*" style="display:none;" multiple required onchange="previewImage(event)">
                </div>
                <div class="img-preview" id="img-preview">
                    {{-- Preview generated by JS --}}
                </div>
            </div>
        </div>

        {{-- Harga & Ketersediaan --}}
        <div class="card">
            <div class="card-header"><span class="card-title">💰 Harga & Ketersediaan</span></div>
            <div class="card-body">
                <div class="frow">
                    <div class="fgroup">
                        <label class="flabel">Harga per Hari (Rp) <span style="color:var(--danger);">*</span></label>
                        <input type="number" class="finput" id="price-input" name="price" placeholder="0" oninput="updatePreview()" required>
                    </div>
                    <div class="fgroup">
                        <label class="flabel">Harga per Minggu (Rp)</label>
                        <input type="number" class="finput" placeholder="Opsional — otomatis diskon" value="2000000">
                    </div>
                </div>
                <div class="frow">
                    <div class="fgroup">
                        <label class="flabel">Min. Durasi Sewa</label>
                        <select class="finput" name="min_durasi_sewa">
                            <option value="1">1 hari</option>
                            <option value="2">2 hari</option>
                            <option value="3">3 hari</option>
                            <option value="7">1 minggu</option>
                        </select>
                    </div>
                    <div class="fgroup">
                        <label class="flabel">Maks. Durasi Sewa</label>
                        <select class="finput" name="max_durasi_sewa">
                            <option value="7">7 hari</option>
                            <option value="14">14 hari</option>
                            <option value="30">30 hari</option>
                            <option value="0">Tidak terbatas</option>
                        </select>
                    </div>
                </div>
                <div class="fgroup">
                    <label class="flabel">Kota / Lokasi Pengambilan <span style="color:var(--danger);">*</span></label>
                    <input type="text" class="finput" name="city" placeholder="Contoh: Jakarta Selatan" required>
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;padding:0.75rem;background:var(--content-bg);border-radius:var(--radius-sm);">
                    <input type="checkbox" id="delivery" style="width:16px;height:16px;accent-color:var(--color-penyedia);" checked>
                    <label for="delivery" style="font-size:.84rem;font-weight:500;color:var(--text);cursor:pointer;">Layani pengiriman / delivery</label>
                </div>
            </div>
        </div>
        </div>
    </form>

    {{-- Kanan: Preview --}}
    <div>
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-header"><span class="card-title">👁️ Preview Listing</span></div>
            <div class="card-body" style="padding:0;">
                <div style="height:120px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;font-size:3rem;">📷</div>
                <div style="padding:1rem;">
                    <div id="preview-name" style="font-weight:700;font-size:.95rem;color:var(--text);margin-bottom:4px;">Kamera Sony A7III + 2 Lensa</div>
                    <div style="font-size:.76rem;color:var(--text-muted);margin-bottom:.75rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Jakarta Pusat · Fotografi
                    </div>
                    <div class="price-preview">
                        <div class="price-preview-label">Harga Sewa</div>
                        <div class="price-preview-val" id="preview-price">Rp 350.000<span style="font-size:.8rem;font-weight:400;opacity:.7;">/hari</span></div>
                    </div>
                    <div style="display:flex;gap:.5rem;margin-top:.25rem;">
                        <span class="badge badge-success" style="font-size:.65rem;">● Tersedia</span>
                        <span class="badge badge-neutral" style="font-size:.65rem;">Min. 1 hari</span>
                        <span class="badge badge-info" style="font-size:.65rem;">🚚 Delivery</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="card" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-color:#BFDBFE;">
            <div class="card-body">
                <div style="font-weight:700;font-size:.85rem;color:var(--color-penyedia);margin-bottom:.5rem;">💡 Tips Listing Terbaik</div>
                <ul style="font-size:.77rem;color:var(--text-secondary);padding-left:1rem;display:flex;flex-direction:column;gap:.3rem;">
                    <li>Foto minimal 5 sudut berbeda</li>
                    <li>Tulis deskripsi lengkap & jujur</li>
                    <li>Tambahkan kelengkapan barang</li>
                    <li>Harga kompetitif = lebih cepat disewa</li>
                    <li>Respon request dalam 1 jam</li>
                </ul>
            </div>
        </div>

        <button type="submit" form="form-tambah" class="btn btn-primary-blue" style="width:100%;justify-content:center;margin-top:1rem;padding:.75rem;">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Publikasikan Listing
        </button>
        <button class="btn btn-outline" style="width:100%;justify-content:center;margin-top:.5rem;" onclick="alert('Draft tersimpan!')">Simpan Draft</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var selectedImages = [];

    function updatePreview() {
        var name = document.getElementById('item-name').value;
        var price = parseInt(document.getElementById('price-input').value)||0;
        document.getElementById('preview-name').textContent = name || 'Nama barang akan tampil di sini';
        document.getElementById('preview-price').innerHTML = 'Rp ' + price.toLocaleString('id-ID') + '<span style="font-size:.8rem;font-weight:400;opacity:.7;">/hari</span>';
    }
    function addTag(e) {
        if (e.key !== 'Enter') return;
        var val = e.target.value.trim();
        if (!val) return;
        var chip = document.createElement('div');
        chip.className = 'tag-chip';
        chip.innerHTML = val + ' <span onclick="removeTag(this)">✕</span>';
        document.getElementById('tags-wrap').insertBefore(chip, e.target);
        e.target.value = '';
        e.preventDefault();
    }
    function removeTag(el) { el.parentElement.remove(); }
    function previewImage(event) {
        var files = Array.from(event.target.files);
        if (!files.length) return;

        if (selectedImages.length + files.length > 4) {
            alert('Maksimal 4 gambar saja.');
            return;
        }

        files.forEach(function(file) {
            if (!file.type.startsWith('image/')) return;
            selectedImages.push(file);
        });

        updateImageInput();
        renderImagePreview();
    }
    function updateImageInput() {
        var dataTransfer = new DataTransfer();
        selectedImages.forEach(function(file) {
            dataTransfer.items.add(file);
        });
        document.getElementById('image-upload').files = dataTransfer.files;
    }
    function renderImagePreview() {
        var preview = document.getElementById('img-preview');
        preview.innerHTML = '';
        selectedImages.forEach(function(file, index) {
            var reader = new FileReader();
            reader.onload = function() {
                var thumb = document.createElement('div');
                thumb.className = 'img-thumb';
                thumb.innerHTML = '<img src="' + reader.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:8px;"><div class="img-remove" onclick="removeImage(' + index + ')">✕</div>';
                preview.appendChild(thumb);
            };
            reader.readAsDataURL(file);
        });
    }
    function removeImage(index) {
        selectedImages.splice(index, 1);
        updateImageInput();
        renderImagePreview();
    }
</script>
@endpush
