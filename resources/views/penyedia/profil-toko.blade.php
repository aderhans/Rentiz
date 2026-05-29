@extends('layouts.app')
@section('title', 'Profil & Toko')
@section('breadcrumb', 'Profil & Toko')

@push('styles')
<style>
    .shop-layout { display:grid;grid-template-columns:300px 1fr;gap:1.25rem;align-items:start; }
    @media(max-width:900px){ .shop-layout{ grid-template-columns:1fr; } }

    .shop-panel { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);overflow:hidden; }
    .shop-banner { height:100px;background:linear-gradient(135deg,#2563EB,#1a3a8f);position:relative;cursor:pointer; }
    .shop-banner-edit { position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.4);color:white;border:none;border-radius:6px;padding:4px 10px;font-size:.72rem;font-weight:600;cursor:pointer;font-family:var(--font-body); }
    .shop-logo-wrap { display:flex;justify-content:center;margin-top:-36px;margin-bottom:.65rem; }
    .shop-logo { width:72px;height:72px;border-radius:var(--radius-md);background:#2563EB;display:flex;align-items:center;justify-content:center;font-size:2rem;border:3px solid white;box-shadow:0 4px 12px rgba(0,0,0,.12);cursor:pointer; }
    .shop-name { text-align:center;font-family:var(--font-heading);font-size:1.1rem;font-weight:700;color:var(--text); }
    .shop-tagline { text-align:center;font-size:.76rem;color:var(--text-muted);margin-top:2px;margin-bottom:.65rem; }
    .shop-badges { display:flex;justify-content:center;gap:.4rem;margin-bottom:.85rem;flex-wrap:wrap; }

    .shop-stats { display:flex;border-top:1px solid var(--card-border);border-bottom:1px solid var(--card-border); }
    .shop-stat { flex:1;text-align:center;padding:.75rem .4rem;border-right:1px solid var(--card-border); }
    .shop-stat:last-child { border-right:none; }
    .shop-stat .sv { font-family:var(--font-heading);font-size:1.1rem;font-weight:700;color:var(--text); }
    .shop-stat .sl { font-size:.66rem;color:var(--text-muted); }

    .shop-nav { padding:.5rem 0; }
    .shop-nav-btn { display:flex;align-items:center;gap:.7rem;padding:.6rem 1.2rem;width:100%;border:none;background:transparent;font-size:.83rem;font-weight:500;color:var(--text-secondary);cursor:pointer;transition:all 150ms;font-family:var(--font-body);text-align:left; }
    .shop-nav-btn:hover { background:var(--content-bg);color:var(--text); }
    .shop-nav-btn.active { background:rgba(37,99,235,.07);color:var(--color-penyedia);font-weight:600; }
    .shop-nav-btn svg { width:16px;height:16px;flex-shrink:0; }
    .nav-arr { margin-left:auto;width:14px;height:14px;opacity:.4; }

    .tab-pane { display:none; }
    .tab-pane.active { display:block; }

    .fl { display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.35rem; }
    .fi { width:100%;padding:.65rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.87rem;font-family:var(--font-body);color:var(--text);outline:none;transition:border-color 200ms; }
    .fi:focus { border-color:var(--color-penyedia); }
    .fg { margin-bottom:1rem; }
    .frow { display:grid;grid-template-columns:1fr 1fr;gap:.85rem; }
    @media(max-width:600px){ .frow{ grid-template-columns:1fr; } }
    .fsec { font-family:var(--font-heading);font-size:.88rem;font-weight:700;color:var(--text);margin-bottom:.85rem;padding-bottom:.6rem;border-bottom:1px solid var(--card-border); }

    .toggle-row { display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid #F1F5F9; }
    .toggle-row:last-child { border-bottom:none; }
    .toggle-info .tn { font-weight:600;font-size:.86rem;color:var(--text); }
    .toggle-info .td { font-size:.75rem;color:var(--text-muted);margin-top:2px; }
    .toggle-sw { position:relative;width:40px;height:22px;flex-shrink:0; }
    .toggle-sw input { opacity:0;width:0;height:0; }
    .toggle-track { position:absolute;inset:0;background:#CBD5E1;border-radius:50px;cursor:pointer;transition:background 200ms; }
    .toggle-track::after { content:'';position:absolute;width:16px;height:16px;border-radius:50%;background:white;top:3px;left:3px;transition:transform 200ms;box-shadow:0 1px 3px rgba(0,0,0,.2); }
    .toggle-sw input:checked + .toggle-track { background:var(--color-penyedia); }
    .toggle-sw input:checked + .toggle-track::after { transform:translateX(18px); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;background:var(--color-penyedia);color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
@php $user = Auth::user(); $initials = collect(explode(' ', $user->name))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->join(''); @endphp

<div class="page-header">
    <h1 class="page-title">🏪 Profil & Toko</h1>
    <p class="page-subtitle">Kelola identitas dan pengaturan toko kamu</p>
</div>

<div class="shop-layout">
    {{-- Left Panel --}}
    <div>
        <div class="shop-panel">
            <div class="shop-banner">
                <button class="shop-banner-edit" onclick="showToast('Fitur edit banner segera hadir!')">✏️ Edit Banner</button>
            </div>
            <div class="shop-logo-wrap">
                <div class="shop-logo" onclick="showToast('Fitur upload logo segera hadir!')">📷</div>
            </div>
            <div class="shop-name">{{ $user->name }}'s Studio</div>
            <div class="shop-tagline">Penyewaan peralatan profesional terpercaya</div>
            <div class="shop-badges">
                <span class="badge badge-success" style="font-size:.64rem;">✓ Terverifikasi</span>
                <span class="badge badge-warning" style="font-size:.64rem;">⭐ Top Rated</span>
                <span class="badge badge-info" style="font-size:.64rem;">🚀 Fast Respon</span>
            </div>
            <div class="shop-stats">
                <div class="shop-stat"><div class="sv" style="color:var(--color-penyedia);">14</div><div class="sl">Listing</div></div>
                <div class="shop-stat"><div class="sv">128</div><div class="sl">Sewa</div></div>
                <div class="shop-stat"><div class="sv" style="color:var(--warning);">4.9</div><div class="sl">Rating</div></div>
            </div>
            <div class="shop-nav">
                <button class="shop-nav-btn active" onclick="switchShopTab('profil',this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Pribadi <svg class="nav-arr" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button class="shop-nav-btn" onclick="switchShopTab('toko',this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    Info Toko <svg class="nav-arr" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button class="shop-nav-btn" onclick="switchShopTab('kebijakan',this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Kebijakan Sewa <svg class="nav-arr" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
        <div style="margin-top:.75rem;background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:.85rem 1.1rem;">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:3px;">Bergabung sebagai Penyedia</div>
            <div style="font-weight:600;font-size:.87rem;color:var(--text);">Januari 2026 · 5 bulan</div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div>
        <div class="tab-pane active card" id="tab-profil">
            <div class="card-header"><span class="card-title">✏️ Profil Pribadi</span><button class="btn btn-primary-blue btn-sm" onclick="showToast('✅ Profil berhasil disimpan!')">Simpan</button></div>
            <div class="card-body">
                <div class="fsec">Data Diri</div>
                <div class="frow">
                    <div class="fg"><label class="fl">Nama Depan</label><input type="text" class="fi" value="{{ explode(' ', $user->name)[0] }}"></div>
                    <div class="fg"><label class="fl">Nama Belakang</label><input type="text" class="fi" value="{{ count(explode(' ', $user->name))>1?implode(' ',array_slice(explode(' ',$user->name),1)):'' }}"></div>
                </div>
                <div class="fg"><label class="fl">Email</label><input type="email" class="fi" value="{{ $user->email }}" disabled style="background:#F8FAFF;color:var(--text-muted);"></div>
                <div class="frow">
                    <div class="fg"><label class="fl">Nomor HP</label><input type="text" class="fi" value="+62 812-3456-7890"></div>
                    <div class="fg"><label class="fl">Kota</label><input type="text" class="fi" value="Jakarta Pusat"></div>
                </div>
                <div class="fg"><label class="fl">Bio Singkat</label><textarea class="fi" rows="2">Penyedia peralatan fotografi & teknologi profesional. Barang terawat, respon cepat, harga bersahabat.</textarea></div>
            </div>
        </div>

        <div class="tab-pane card" id="tab-toko">
            <div class="card-header"><span class="card-title">🏪 Informasi Toko</span><button class="btn btn-primary-blue btn-sm" onclick="showToast('✅ Info toko diperbarui!')">Simpan</button></div>
            <div class="card-body">
                <div class="fsec">Identitas Toko</div>
                <div class="fg"><label class="fl">Nama Toko</label><input type="text" class="fi" value="{{ $user->name }}'s Studio"></div>
                <div class="fg"><label class="fl">Slogan Toko</label><input type="text" class="fi" value="Penyewaan peralatan profesional terpercaya"></div>
                <div class="frow">
                    <div class="fg"><label class="fl">Kategori Utama</label><select class="fi"><option selected>Fotografi & Videografi</option><option>Elektronik</option><option>Outdoor</option></select></div>
                    <div class="fg"><label class="fl">Jam Operasional</label><select class="fi"><option>08:00 – 20:00</option><option>09:00 – 21:00</option><option>24 Jam</option></select></div>
                </div>
                <div class="fg"><label class="fl">Alamat Toko</label><input type="text" class="fi" value="Jl. Sudirman No. 88, Jakarta Pusat"></div>
                <div class="fg"><label class="fl">WhatsApp (untuk request langsung)</label><input type="text" class="fi" value="+62 812-3456-7890"></div>
            </div>
        </div>

        <div class="tab-pane card" id="tab-kebijakan">
            <div class="card-header"><span class="card-title">📋 Kebijakan Sewa</span><button class="btn btn-primary-blue btn-sm" onclick="showToast('✅ Kebijakan diperbarui!')">Simpan</button></div>
            <div class="card-body">
                <div class="fsec">Pengaturan Sewa</div>
                @php
                $toggles=[
                    ['label'=>'Layani Pengiriman','desc'=>'Penyewa bisa request antar-jemput barang','checked'=>true],
                    ['label'=>'Deposit Wajib','desc'=>'Minta deposit keamanan sebelum barang dipinjam','checked'=>true],
                    ['label'=>'Konfirmasi Manual','desc'=>'Setiap request harus dikonfirmasi manual','checked'=>false],
                    ['label'=>'Notifikasi Instan','desc'=>'Terima notifikasi push setiap ada request baru','checked'=>true],
                    ['label'=>'Review Otomatis','desc'=>'Minta penyewa beri ulasan setelah selesai','checked'=>true],
                ];
                @endphp
                @foreach($toggles as $tg)
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="tn">{{ $tg['label'] }}</div>
                        <div class="td">{{ $tg['desc'] }}</div>
                    </div>
                    <label class="toggle-sw">
                        <input type="checkbox" {{ $tg['checked']?'checked':'' }} onchange="showToast('⚙️ Pengaturan diperbarui!')">
                        <div class="toggle-track"></div>
                    </label>
                </div>
                @endforeach
                <div class="fg" style="margin-top:1rem;"><label class="fl">Syarat & Ketentuan Sewa</label><textarea class="fi" rows="4">1. Barang wajib dikembalikan dalam kondisi seperti semula.
2. Keterlambatan pengembalian dikenakan denda 25% per hari.
3. Kerusakan ditanggung penyewa sepenuhnya.
4. Deposit dikembalikan 1x24 jam setelah barang diterima.</textarea></div>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="shop-toast"></div>
@endsection

@push('scripts')
<script>
    function switchShopTab(tab, btn) {
        document.querySelectorAll('.shop-nav-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
        document.getElementById('tab-'+tab).classList.add('active');
    }
    function showToast(msg) {
        var t = document.getElementById('shop-toast');
        t.textContent = msg; t.classList.add('show');
        clearTimeout(window._sht); window._sht = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
