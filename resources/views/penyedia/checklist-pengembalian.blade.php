@extends('layouts.app')
@section('title', 'Konfirmasi Pengembalian Barang')
@section('breadcrumb', 'Checklist Pengembalian')

@push('styles')
<style>
    .return-wrap {
        max-width: 760px;
        margin: 0 auto;
    }

    /* ── Step bar ── */
    .step-bar {
        display: flex;
        margin-bottom: 2rem;
        position: relative;
    }
    .step-bar::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 20px;
        right: 20px;
        height: 2px;
        background: #e2e8f0;
        z-index: 0;
    }
    .step-bar::after {
        content: '';
        position: absolute;
        top: 18px;
        left: 20px;
        width: calc(66.6% - 20px);
        height: 2px;
        background: #059669;
        z-index: 0;
    }
    .step { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; position: relative; z-index: 1; }
    .step-num {
        width: 36px; height: 36px; border-radius: 50%;
        background: #e2e8f0; color: #94a3b8;
        font-weight: 700; font-size: 0.85rem;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #e2e8f0;
    }
    .step.done .step-num { background: #059669; color: white; box-shadow: 0 0 0 2px rgba(5,150,105,0.25); }
    .step.current .step-num { background: #f59e0b; color: white; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    .step-label { font-size: 0.72rem; font-weight: 600; color: var(--text-muted); text-align: center; }
    .step.done .step-label { color: #059669; }
    .step.current .step-label { color: #d97706; }

    /* ── Item hero ── */
    .item-hero {
        display: flex; gap: 1.25rem; align-items: center;
        background: white; border: 1px solid var(--card-border);
        border-radius: var(--radius-lg); padding: 1.4rem 1.5rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-xs);
    }
    .item-hero-img {
        width: 80px; height: 80px; border-radius: var(--radius-md);
        object-fit: cover; flex-shrink: 0; background: #f1f5f9;
        display: flex; align-items: center; justify-content: center; font-size: 2rem;
    }
    .item-hero-info h2 { font-size: 1.05rem; font-weight: 700; color: var(--text); margin-bottom: 0.3rem; }
    .item-hero-meta { display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.79rem; color: var(--text-muted); }

    /* ── Kondisi awal card ── */
    .kondisi-awal-card {
        background: #F0FDF4; border: 1px solid #BBF7D0;
        border-radius: var(--radius-md); padding: 1rem 1.25rem;
        margin-bottom: 1.25rem; font-size: 0.82rem;
    }
    .kondisi-awal-card .ka-title {
        font-weight: 700; color: #065F46; margin-bottom: 0.6rem;
        display: flex; align-items: center; gap: 6px;
    }
    .kondisi-awal-tags { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .kondisi-awal-tags .tag {
        background: #059669; color: white;
        padding: 3px 12px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 700;
    }

    /* ── Form card ── */
    .form-card {
        background: white; border: 1px solid var(--card-border);
        border-radius: var(--radius-lg); padding: 1.75rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-xs);
    }
    .form-card-title {
        font-size: 1rem; font-weight: 700; color: var(--text);
        margin-bottom: 1.25rem; padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--card-border);
        display: flex; align-items: center; gap: 0.5rem;
    }

    /* ── Radio grid ── */
    .kondisi-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.6rem; margin-top: 0.5rem;
    }
    .ko input[type="radio"] { display: none; }
    .ko label {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 0.85rem 0.5rem; border-radius: var(--radius-md);
        border: 2px solid var(--card-border); cursor: pointer;
        transition: all 200ms; text-align: center;
        font-size: 0.79rem; font-weight: 600; color: var(--text-secondary);
    }
    .ko label .icon { font-size: 1.4rem; }
    .ko.good input:checked + label { border-color: #059669; background: rgba(5,150,105,0.07); color: #059669; }
    .ko.warn input:checked + label { border-color: #d97706; background: rgba(217,119,6,0.07); color: #d97706; }
    .ko.bad input:checked + label { border-color: #dc2626; background: rgba(220,38,38,0.07); color: #dc2626; }

    .field-group { margin-bottom: 1.4rem; }
    .field-group:last-child { margin-bottom: 0; }
    .field-label {
        font-size: 0.84rem; font-weight: 700; color: var(--text);
        margin-bottom: 0.5rem; display: flex; align-items: center; gap: 6px;
    }
    .field-label .req { color: #dc2626; }

    /* ── Upload zone ── */
    .upload-zone {
        border: 2px dashed var(--card-border); border-radius: var(--radius-md);
        padding: 1.5rem; text-align: center; cursor: pointer;
        transition: all 200ms; position: relative;
    }
    .upload-zone:hover { border-color: #f59e0b; background: rgba(245,158,11,0.02); }
    .upload-zone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-zone .u-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .upload-zone .u-text { font-size: 0.82rem; color: var(--text-muted); }
    .upload-zone .u-preview { width: 100%; max-height: 200px; object-fit: contain; border-radius: var(--radius-sm); display: none; }

    textarea.form-control { min-height: 90px; resize: vertical; }

    /* ── Alert ── */
    .alert-return {
        display: flex; gap: 0.75rem; padding: 1rem 1.2rem;
        background: #FFF7ED; border: 1px solid #FED7AA;
        border-radius: var(--radius-md); margin-bottom: 1.5rem;
        font-size: 0.82rem; color: #9A3412;
    }

    /* ── Done state ── */
    .done-card {
        background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
        border: 1px solid #6EE7B7; border-radius: var(--radius-lg);
        padding: 2rem; text-align: center; margin-bottom: 1.5rem;
    }
    .done-card .done-icon { font-size: 3rem; margin-bottom: 0.75rem; }
    .done-card h3 { font-size: 1.1rem; font-weight: 700; color: #065F46; margin-bottom: 0.5rem; }
    .done-card p { font-size: 0.85rem; color: #047857; }
    .done-tags { display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; margin-top: 1rem; }
    .done-tag { padding: 4px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; background: #059669; color: white; }

    /* ── Submit button ── */
    .btn-selesai {
        width: 100%; padding: 1rem;
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: white; border: none; border-radius: var(--radius-md);
        font-size: 1rem; font-weight: 700; cursor: pointer;
        font-family: var(--font-body); transition: all 200ms;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(245,158,11,0.35);
    }
    .btn-selesai:hover {
        background: linear-gradient(135deg, #b45309, #d97706);
        transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245,158,11,0.45);
    }
    .btn-selesai:active { transform: translateY(0); }

    .btn-back {
        display: inline-flex; align-items: center; gap: 0.4rem;
        color: var(--text-secondary); font-size: 0.85rem; font-weight: 600;
        text-decoration: none; margin-bottom: 1.25rem; transition: color 150ms;
    }
    .btn-back:hover { color: var(--text); }
</style>
@endpush

@section('content')
<div class="return-wrap">

    <a href="{{ route('penyedia.request-sewa') }}" class="btn-back">← Kembali ke Request Sewa</a>

    {{-- Step indicator (step 3: penyedia konfirmasi pengembalian) --}}
    <div class="step-bar">
        <div class="step done">
            <div class="step-num">✓</div>
            <div class="step-label">Penyewa Bayar</div>
        </div>
        <div class="step done">
            <div class="step-num">✓</div>
            <div class="step-label">Penyewa Ambil<br>& Isi Checklist</div>
        </div>
        <div class="step current">
            <div class="step-num">3</div>
            <div class="step-label">Penyedia Konfirmasi<br>Pengembalian</div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <div class="step-label">Sewa Selesai ✓</div>
        </div>
    </div>

    {{-- Item hero card --}}
    @php
        $photo = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
        if (!$photo && $item->barang->fotos) $photo = $item->barang->fotos->first();
        $tglMulai  = \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y');
        $tglSelesai = \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y');
        $terlambat  = now()->gt(\Carbon\Carbon::parse($item->tanggal_selesai));
    @endphp
    <div class="item-hero">
        @if($photo)
            <img src="{{ asset('storage/' . $photo->path_foto) }}" class="item-hero-img" alt="Foto">
        @else
            <div class="item-hero-img">📦</div>
        @endif
        <div class="item-hero-info">
            <h2>{{ $item->barang->nama }}</h2>
            <div class="item-hero-meta">
                <span>👤 <strong>{{ $item->pesanan->pemesan->name ?? 'Penyewa' }}</strong></span>
                <span>📅 {{ $tglMulai }} – {{ $tglSelesai }}</span>
                <span>⏱ {{ $item->durasi_hari }} Hari</span>
                @if($terlambat)
                    <span style="color:#dc2626; font-weight:700;">⚠️ Terlambat {{ now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai)) }} Hari</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Referensi kondisi saat pengambilan --}}
    @if($checklistPengambilan)
        @php $k = $checklistPengambilan->kondisi_barang; @endphp
        <div class="kondisi-awal-card">
            <div class="ka-title">📋 Kondisi Awal Saat Diambil Penyewa</div>
            <div class="kondisi-awal-tags">
                @if(is_array($k))
                    @if(isset($k['fungsionalitas']))<span class="tag">⚙️ {{ ucfirst(str_replace('_', ' ', $k['fungsionalitas'])) }}</span>@endif
                    @if(isset($k['kelengkapan']))<span class="tag">📦 {{ ucfirst(str_replace('_', ' ', $k['kelengkapan'])) }}</span>@endif
                    @if(isset($k['kondisi_fisik']))<span class="tag">🔍 {{ ucfirst(str_replace('_', ' ', $k['kondisi_fisik'])) }}</span>@endif
                @endif
                @if($checklistPengambilan->catatan)
                    <span style="font-style:italic; color:#065F46;">"{{ $checklistPengambilan->catatan }}"</span>
                @endif
            </div>
        </div>
    @endif

    @if($existingChecklist)
        {{-- Sudah diisi sebelumnya --}}
        <div class="done-card">
            <div class="done-icon">🎊</div>
            <h3>Pengembalian Sudah Dikonfirmasi</h3>
            <p>Anda telah mengisi checklist pengembalian. Transaksi sewa dinyatakan selesai.</p>
            @php $kr = $existingChecklist->kondisi_barang; @endphp
            @if(is_array($kr))
                <div class="done-tags">
                    @if(isset($kr['fungsionalitas']))<span class="done-tag">⚙️ {{ ucfirst(str_replace('_', ' ', $kr['fungsionalitas'])) }}</span>@endif
                    @if(isset($kr['kelengkapan']))<span class="done-tag">📦 {{ ucfirst(str_replace('_', ' ', $kr['kelengkapan'])) }}</span>@endif
                    @if(isset($kr['kondisi_fisik']))<span class="done-tag">🔍 {{ ucfirst(str_replace('_', ' ', $kr['kondisi_fisik'])) }}</span>@endif
                </div>
            @endif
            @if($existingChecklist->catatan)
                <p style="margin-top:0.75rem; font-style:italic;">"{{ $existingChecklist->catatan }}"</p>
            @endif
        </div>
        <a href="{{ route('penyedia.request-sewa') }}" class="btn-selesai" style="text-decoration:none;">
            ← Kembali ke Daftar Pesanan
        </a>

    @else
        {{-- Form pengembalian --}}
        <div class="alert-return">
            <span style="font-size:1.2rem; flex-shrink:0;">📌</span>
            <div>
                <strong>Verifikasi Kondisi Barang yang Dikembalikan</strong> — Bandingkan kondisi saat ini dengan kondisi awal pengambilan di atas. Isi dengan jujur. Data ini menjadi dasar jika ada klaim kerusakan atau kehilangan.
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1rem;">
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('penyedia.request-sewa.pengembalian.simpan', $item->id) }}" method="POST" enctype="multipart/form-data" id="returnForm">
            @csrf

            {{-- Fungsionalitas --}}
            <div class="form-card">
                <div class="form-card-title">⚙️ Kondisi Fungsionalitas Saat Dikembalikan</div>
                <div class="field-group">
                    <div class="field-label">Apakah semua fungsi barang masih bekerja? <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="ko good">
                            <input type="radio" name="fungsionalitas" id="f-baik" value="baik" {{ old('fungsionalitas') === 'baik' ? 'checked' : '' }} required>
                            <label for="f-baik"><span class="icon">✅</span>Berfungsi Baik</label>
                        </div>
                        <div class="ko warn">
                            <input type="radio" name="fungsionalitas" id="f-minor" value="minor_issue" {{ old('fungsionalitas') === 'minor_issue' ? 'checked' : '' }}>
                            <label for="f-minor"><span class="icon">⚠️</span>Ada Masalah Minor</label>
                        </div>
                        <div class="ko bad">
                            <input type="radio" name="fungsionalitas" id="f-rusak" value="tidak_berfungsi" {{ old('fungsionalitas') === 'tidak_berfungsi' ? 'checked' : '' }}>
                            <label for="f-rusak"><span class="icon">❌</span>Tidak Berfungsi</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kelengkapan --}}
            <div class="form-card">
                <div class="form-card-title">📦 Kelengkapan Barang</div>
                <div class="field-group">
                    <div class="field-label">Status kelengkapan aksesori saat dikembalikan <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="ko good">
                            <input type="radio" name="kelengkapan" id="k-lengkap" value="lengkap" {{ old('kelengkapan') === 'lengkap' ? 'checked' : '' }} required>
                            <label for="k-lengkap"><span class="icon">✅</span>Lengkap Semua</label>
                        </div>
                        <div class="ko warn">
                            <input type="radio" name="kelengkapan" id="k-sebagian" value="sebagian" {{ old('kelengkapan') === 'sebagian' ? 'checked' : '' }}>
                            <label for="k-sebagian"><span class="icon">📋</span>Sebagian Lengkap</label>
                        </div>
                        <div class="ko bad">
                            <input type="radio" name="kelengkapan" id="k-tidak" value="tidak_lengkap" {{ old('kelengkapan') === 'tidak_lengkap' ? 'checked' : '' }}>
                            <label for="k-tidak"><span class="icon">❌</span>Ada yang Hilang</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kondisi fisik --}}
            <div class="form-card">
                <div class="form-card-title">🔍 Kondisi Fisik Barang</div>
                <div class="field-group">
                    <div class="field-label">Kondisi fisik / tampilan luar barang saat dikembalikan <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="ko good">
                            <input type="radio" name="kondisi_fisik" id="kf-sempurna" value="sempurna" {{ old('kondisi_fisik') === 'sempurna' ? 'checked' : '' }} required>
                            <label for="kf-sempurna"><span class="icon">✨</span>Sempurna</label>
                        </div>
                        <div class="ko good">
                            <input type="radio" name="kondisi_fisik" id="kf-baik" value="baik" {{ old('kondisi_fisik') === 'baik' ? 'checked' : '' }}>
                            <label for="kf-baik"><span class="icon">👍</span>Baik</label>
                        </div>
                        <div class="ko warn">
                            <input type="radio" name="kondisi_fisik" id="kf-goresan" value="ada_goresan" {{ old('kondisi_fisik') === 'ada_goresan' ? 'checked' : '' }}>
                            <label for="kf-goresan"><span class="icon">🔧</span>Ada Goresan Baru</label>
                        </div>
                        <div class="ko bad">
                            <input type="radio" name="kondisi_fisik" id="kf-rusak" value="rusak_fisik" {{ old('kondisi_fisik') === 'rusak_fisik' ? 'checked' : '' }}>
                            <label for="kf-rusak"><span class="icon">💔</span>Rusak / Cacat</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Foto & catatan --}}
            <div class="form-card">
                <div class="form-card-title">📷 Foto & Catatan Pengembalian</div>

                <div class="field-group">
                    <div class="field-label">Foto Kondisi Barang Saat Dikembalikan <span style="font-weight:400;color:var(--text-muted);">(sangat disarankan)</span></div>
                    <div class="upload-zone">
                        <input type="file" name="foto_bukti" id="fotoInput" accept="image/*" onchange="previewImg(this)">
                        <div id="uploadPlaceholder">
                            <div class="u-icon">📸</div>
                            <div class="u-text">Klik atau seret foto ke sini<br><span style="font-size:0.75rem;">JPG, PNG, WEBP • Max 3MB</span></div>
                        </div>
                        <img id="fotoPreview" class="u-preview" alt="Preview">
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-label">Catatan Pengembalian</div>
                    <textarea name="catatan" class="form-control" placeholder="Mis: Terdapat goresan baru di bagian kanan. Charger dikembalikan. Kondisi baterai normal. Dll.">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-selesai" id="submitBtn" onclick="return doSubmit()">
                <span>🎊</span>
                <span>Konfirmasi Pengembalian — Selesaikan Transaksi</span>
            </button>
            <p style="text-align:center; font-size:0.75rem; color:var(--text-muted); margin-top:0.75rem;">
                Dengan mengklik tombol di atas, status sewa akan dinyatakan <strong>Selesai</strong> dan penyewa akan diberitahu.
            </p>
        </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function previewImg(input) {
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function doSubmit() {
        const f  = document.querySelector('input[name="fungsionalitas"]:checked');
        const k  = document.querySelector('input[name="kelengkapan"]:checked');
        const kf = document.querySelector('input[name="kondisi_fisik"]:checked');
        if (!f || !k || !kf) {
            alert('Harap lengkapi semua pilihan kondisi barang sebelum melanjutkan.');
            return false;
        }
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span><span>Menyimpan & menyelesaikan transaksi...</span>';
        return true;
    }

    // Drag & drop
    const zone = document.querySelector('.upload-zone');
    if (zone) {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = '#f59e0b'; });
        zone.addEventListener('dragleave', () => { zone.style.borderColor = ''; });
        zone.addEventListener('drop', e => {
            e.preventDefault(); zone.style.borderColor = '';
            if (e.dataTransfer.files.length) {
                const inp = document.getElementById('fotoInput');
                inp.files = e.dataTransfer.files;
                previewImg(inp);
            }
        });
    }
</script>
@endpush
