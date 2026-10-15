@extends('layouts.app')
@section('title', 'Checklist Serah Terima Barang')
@section('breadcrumb', 'Checklist Serah Terima')

@push('styles')
<style>
    .checklist-wrap {
        max-width: 720px;
        margin: 0 auto;
    }

    /* ── Hero card barang ── */
    .item-hero {
        display: flex;
        gap: 1.25rem;
        align-items: center;
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.4rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-xs);
    }
    .item-hero-img {
        width: 88px;
        height: 88px;
        border-radius: var(--radius-md);
        object-fit: cover;
        flex-shrink: 0;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
    }
    .item-hero-info h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.3rem;
    }
    .item-hero-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    .item-hero-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Step indicator ── */
    .step-bar {
        display: flex;
        gap: 0;
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
        background: var(--card-border);
        z-index: 0;
    }
    .step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        position: relative;
        z-index: 1;
    }
    .step-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--card-border);
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--card-border);
        transition: all 300ms;
    }
    .step.done .step-num {
        background: #059669;
        color: white;
        box-shadow: 0 0 0 2px rgba(5,150,105,0.25);
    }
    .step.current .step-num {
        background: var(--color-penyedia);
        color: white;
        box-shadow: 0 0 0 3px rgba(0,61,130,0.2);
    }
    .step-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-align: center;
    }
    .step.current .step-label { color: var(--color-penyedia); }
    .step.done .step-label { color: #059669; }

    /* ── Form card ── */
    .form-card {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-xs);
    }
    .form-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ── Radio grid kondisi ── */
    .kondisi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.6rem;
        margin-top: 0.5rem;
    }
    .kondisi-option input[type="radio"] { display: none; }
    .kondisi-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 0.85rem 0.5rem;
        border-radius: var(--radius-md);
        border: 2px solid var(--card-border);
        cursor: pointer;
        transition: all 200ms;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .kondisi-option label .icon {
        font-size: 1.5rem;
    }
    .kondisi-option input:checked + label {
        border-color: var(--color-penyedia);
        background: rgba(0,61,130,0.06);
        color: var(--color-penyedia);
    }
    .kondisi-option.good input:checked + label {
        border-color: #059669;
        background: rgba(5,150,105,0.07);
        color: #059669;
    }
    .kondisi-option.warn input:checked + label {
        border-color: #d97706;
        background: rgba(217,119,6,0.07);
        color: #d97706;
    }
    .kondisi-option.bad input:checked + label {
        border-color: #dc2626;
        background: rgba(220,38,38,0.07);
        color: #dc2626;
    }

    /* ── Field grup ── */
    .field-group {
        margin-bottom: 1.4rem;
    }
    .field-group:last-child { margin-bottom: 0; }
    .field-label {
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .field-label .req { color: #dc2626; }

    .upload-zone {
        border: 2px dashed var(--card-border);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 200ms;
        position: relative;
    }
    .upload-zone:hover { border-color: var(--color-penyedia); background: rgba(0,61,130,0.02); }
    .upload-zone input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-zone .u-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .upload-zone .u-text { font-size: 0.82rem; color: var(--text-muted); }
    .upload-zone .u-preview {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: var(--radius-sm);
        display: none;
    }

    textarea.form-control {
        min-height: 90px;
        resize: vertical;
    }

    /* ── Warning notice ── */
    .notice-box {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.2rem;
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        font-size: 0.82rem;
        color: #92400E;
    }
    .notice-box .n-icon { font-size: 1.2rem; flex-shrink: 0; }

    /* ── Already filled state ── */
    .checklist-done-card {
        background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
        border: 1px solid #6EE7B7;
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .checklist-done-card .done-icon { font-size: 3rem; margin-bottom: 0.75rem; }
    .checklist-done-card h3 { font-size: 1.1rem; font-weight: 700; color: #065F46; margin-bottom: 0.5rem; }
    .checklist-done-card p { font-size: 0.85rem; color: #047857; }

    .kondisi-summary {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 1rem;
    }
    .kondisi-tag {
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        background: #059669;
        color: white;
    }

    /* ── Submit button ── */
    .btn-sewa {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #003d82, #0055b3);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        font-family: var(--font-body);
        transition: all 200ms;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(0,61,130,0.3);
    }
    .btn-sewa:hover {
        background: linear-gradient(135deg, #002855, #003d82);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,61,130,0.4);
    }
    .btn-sewa:active { transform: translateY(0); }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--text-secondary);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.25rem;
        transition: color 150ms;
    }
    .btn-back:hover { color: var(--text); }
</style>
@endpush

@section('content')
<div class="checklist-wrap">

    {{-- Back button --}}
    <a href="{{ route('penyedia.request-sewa') }}" class="btn-back">
        ← Kembali ke Request Sewa
    </a>

    {{-- Step indicator --}}
    <div class="step-bar">
        <div class="step done">
            <div class="step-num">✓</div>
            <div class="step-label">Penyewa Bayar</div>
        </div>
        <div class="step current">
            <div class="step-num">2</div>
            <div class="step-label">Penyedia Isi Checklist</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-label">Sewa Aktif</div>
        </div>
    </div>

    {{-- Item hero card --}}
    @php
        $photo = $item->barang->fotos ? $item->barang->fotos->where('is_primary', true)->first() : null;
        if (!$photo && $item->barang->fotos) $photo = $item->barang->fotos->first();
    @endphp
    <div class="item-hero">
        @if($photo)
            <img src="{{ asset('storage/' . $photo->path_foto) }}" class="item-hero-img" alt="Foto barang">
        @else
            <div class="item-hero-img">📦</div>
        @endif
        <div class="item-hero-info">
            <h2>{{ $item->barang->nama }}</h2>
            <div class="item-hero-meta">
                <span>👤 Penyewa: <strong>{{ $item->pesanan->pemesan->name ?? 'Penyewa' }}</strong></span>
                <span>📅 {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M') }} – {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</span>
                <span>⏱ {{ $item->durasi_hari }} Hari</span>
                <span>💰 Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if($existingChecklist)
        {{-- Checklist sudah diisi sebelumnya --}}
        <div class="checklist-done-card">
            <div class="done-icon">✅</div>
            <h3>Checklist Sudah Diisi</h3>
            <p>Anda telah mengisi checklist serah terima untuk barang ini. Penyewa telah diberitahu dan masa sewa sedang aktif.</p>
            @php $k = $existingChecklist->kondisi_barang; @endphp
            @if(is_array($k))
                <div class="kondisi-summary">
                    @if(isset($k['fungsionalitas']))<span class="kondisi-tag">⚙️ {{ ucfirst($k['fungsionalitas']) }}</span>@endif
                    @if(isset($k['kelengkapan']))<span class="kondisi-tag">📦 {{ ucfirst($k['kelengkapan']) }}</span>@endif
                    @if(isset($k['kondisi_fisik']))<span class="kondisi-tag">🔍 {{ ucfirst($k['kondisi_fisik']) }}</span>@endif
                </div>
            @endif
            @if($existingChecklist->catatan)
                <p style="margin-top:0.75rem; font-style:italic;">"{{ $existingChecklist->catatan }}"</p>
            @endif
        </div>
        <a href="{{ route('penyedia.request-sewa') }}" class="btn-sewa" style="text-decoration:none;">
            ← Kembali ke Daftar Pesanan
        </a>

    @else
        {{-- Notice --}}
        <div class="notice-box">
            <span class="n-icon">⚠️</span>
            <div>
                <strong>Penting!</strong> Isi checklist ini dengan jujur dan akurat. Data ini akan menjadi bukti kondisi barang saat diserahkan, dan akan digunakan sebagai referensi jika terjadi dispute di kemudian hari. Klik <strong>"Mulai Sewa"</strong> setelah checklist lengkap untuk mengaktifkan masa sewa.
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1rem;">
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('penyedia.request-sewa.checklist.simpan', $item->id) }}" method="POST" enctype="multipart/form-data" id="checklistForm">
            @csrf

            {{-- Kondisi Fungsionalitas --}}
            <div class="form-card">
                <div class="form-card-title">
                    ⚙️ Kondisi Fungsionalitas
                </div>
                <div class="field-group">
                    <div class="field-label">Apakah semua fungsi barang berjalan normal? <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="kondisi-option good">
                            <input type="radio" name="fungsionalitas" id="f-baik" value="baik" {{ old('fungsionalitas') === 'baik' ? 'checked' : '' }} required>
                            <label for="f-baik">
                                <span class="icon">✅</span>
                                Berfungsi Baik
                            </label>
                        </div>
                        <div class="kondisi-option warn">
                            <input type="radio" name="fungsionalitas" id="f-minor" value="minor_issue" {{ old('fungsionalitas') === 'minor_issue' ? 'checked' : '' }}>
                            <label for="f-minor">
                                <span class="icon">⚠️</span>
                                Ada Masalah Minor
                            </label>
                        </div>
                        <div class="kondisi-option bad">
                            <input type="radio" name="fungsionalitas" id="f-rusak" value="tidak_berfungsi" {{ old('fungsionalitas') === 'tidak_berfungsi' ? 'checked' : '' }}>
                            <label for="f-rusak">
                                <span class="icon">❌</span>
                                Tidak Berfungsi
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kelengkapan Barang --}}
            <div class="form-card">
                <div class="form-card-title">
                    📦 Kelengkapan Barang
                </div>
                <div class="field-group">
                    <div class="field-label">Status kelengkapan aksesori/komponen barang <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="kondisi-option good">
                            <input type="radio" name="kelengkapan" id="k-lengkap" value="lengkap" {{ old('kelengkapan') === 'lengkap' ? 'checked' : '' }} required>
                            <label for="k-lengkap">
                                <span class="icon">✅</span>
                                Lengkap Semua
                            </label>
                        </div>
                        <div class="kondisi-option warn">
                            <input type="radio" name="kelengkapan" id="k-sebagian" value="sebagian" {{ old('kelengkapan') === 'sebagian' ? 'checked' : '' }}>
                            <label for="k-sebagian">
                                <span class="icon">📋</span>
                                Sebagian Lengkap
                            </label>
                        </div>
                        <div class="kondisi-option bad">
                            <input type="radio" name="kelengkapan" id="k-tidak" value="tidak_lengkap" {{ old('kelengkapan') === 'tidak_lengkap' ? 'checked' : '' }}>
                            <label for="k-tidak">
                                <span class="icon">❌</span>
                                Tidak Lengkap
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kondisi Fisik --}}
            <div class="form-card">
                <div class="form-card-title">
                    🔍 Kondisi Fisik Barang
                </div>
                <div class="field-group">
                    <div class="field-label">Kondisi fisik / tampilan luar barang <span class="req">*</span></div>
                    <div class="kondisi-grid">
                        <div class="kondisi-option good">
                            <input type="radio" name="kondisi_fisik" id="kf-sempurna" value="sempurna" {{ old('kondisi_fisik') === 'sempurna' ? 'checked' : '' }} required>
                            <label for="kf-sempurna">
                                <span class="icon">✨</span>
                                Sempurna
                            </label>
                        </div>
                        <div class="kondisi-option good">
                            <input type="radio" name="kondisi_fisik" id="kf-baik" value="baik" {{ old('kondisi_fisik') === 'baik' ? 'checked' : '' }}>
                            <label for="kf-baik">
                                <span class="icon">👍</span>
                                Baik
                            </label>
                        </div>
                        <div class="kondisi-option warn">
                            <input type="radio" name="kondisi_fisik" id="kf-goresan" value="ada_goresan" {{ old('kondisi_fisik') === 'ada_goresan' ? 'checked' : '' }}>
                            <label for="kf-goresan">
                                <span class="icon">🔧</span>
                                Ada Goresan
                            </label>
                        </div>
                        <div class="kondisi-option bad">
                            <input type="radio" name="kondisi_fisik" id="kf-rusak" value="rusak_fisik" {{ old('kondisi_fisik') === 'rusak_fisik' ? 'checked' : '' }}>
                            <label for="kf-rusak">
                                <span class="icon">💔</span>
                                Rusak Fisik
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Foto Bukti & Catatan --}}
            <div class="form-card">
                <div class="form-card-title">
                    📷 Foto & Catatan Tambahan
                </div>

                <div class="field-group">
                    <div class="field-label">Foto Kondisi Barang <span style="font-weight:400; color:var(--text-muted);">(opsional, tapi sangat disarankan)</span></div>
                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="foto_bukti" id="fotoInput" accept="image/*" onchange="previewImage(this)">
                        <div id="uploadPlaceholder">
                            <div class="u-icon">📸</div>
                            <div class="u-text">Klik atau seret foto kondisi barang ke sini<br><span style="font-size:0.75rem;">JPG, PNG, WEBP • Max 3MB</span></div>
                        </div>
                        <img id="fotoPreview" class="u-preview" alt="Preview">
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-label">Catatan Tambahan</div>
                    <textarea name="catatan" class="form-control" placeholder="Mis: Terdapat bekas sticket di bagian bawah. Charger sudah disertakan. Dll." rows="3">{{ old('catatan') }}</textarea>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-sewa" id="submitBtn" onclick="return confirmSubmit()">
                <span id="btnIcon">🚀</span>
                <span id="btnText">Mulai Sewa — Aktifkan Masa Sewa</span>
            </button>

            <p style="text-align:center; font-size:0.75rem; color:var(--text-muted); margin-top:0.75rem;">
                Dengan mengklik tombol di atas, Anda mengonfirmasi bahwa barang telah diperiksa dan siap diserahterimakan kepada penyewa.
            </p>
        </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
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

    function confirmSubmit() {
        const f = document.querySelector('input[name="fungsionalitas"]:checked');
        const k = document.querySelector('input[name="kelengkapan"]:checked');
        const kf = document.querySelector('input[name="kondisi_fisik"]:checked');

        if (!f || !k || !kf) {
            alert('Harap lengkapi semua pilihan kondisi barang sebelum melanjutkan.');
            return false;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> <span>Menyimpan checklist & mengaktifkan sewa...</span>';
        return true;
    }

    // Drag & drop enhancement
    const zone = document.getElementById('uploadZone');
    if (zone) {
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = '#003d82'; });
        zone.addEventListener('dragleave', () => { zone.style.borderColor = ''; });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.style.borderColor = '';
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('fotoInput');
                input.files = files;
                previewImage(input);
            }
        });
    }
</script>
@endpush
