@extends('layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil Saya')

@push('styles')
<style>
    .profile-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .profile-layout { grid-template-columns: 1fr; }
    }

    /* Left Panel */
    .profile-panel {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }
    .profile-banner {
        height: 90px;
        background: linear-gradient(135deg, #0D9488, #065f52);
        position: relative;
    }
    .profile-avatar-wrap {
        position: relative;
        display: flex;
        justify-content: center;
        margin-top: -36px;
        margin-bottom: 0.75rem;
    }
    .profile-avatar {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: var(--color-penyewa);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-heading);
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        position: relative;
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: 0; right: 0;
        width: 22px; height: 22px;
        border-radius: 50%;
        background: var(--color-penyewa);
        color: white;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        cursor: pointer;
    }
    .profile-name {
        text-align: center;
        font-family: var(--font-heading);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        padding: 0 1rem;
    }
    .profile-email {
        text-align: center;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 2px;
        margin-bottom: 0.75rem;
    }
    .profile-role-badge {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .profile-stats-row {
        display: flex;
        border-top: 1px solid var(--card-border);
        border-bottom: 1px solid var(--card-border);
    }
    .profile-stat-item {
        flex: 1;
        text-align: center;
        padding: 0.85rem 0.5rem;
        border-right: 1px solid var(--card-border);
    }
    .profile-stat-item:last-child { border-right: none; }
    .profile-stat-item .val {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--text);
    }
    .profile-stat-item .lbl {
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-top: 1px;
    }
    .profile-nav {
        padding: 0.5rem 0;
    }
    .profile-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1.25rem;
        font-size: 0.84rem;
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 0;
        transition: background 150ms, color 150ms;
        border: none;
        background: transparent;
        width: 100%;
        font-family: var(--font-body);
        text-align: left;
    }
    .profile-nav-item:hover { background: var(--content-bg); color: var(--text); }
    .profile-nav-item.active { background: rgba(13,148,136,0.08); color: var(--color-penyewa); font-weight: 600; }
    .profile-nav-item svg { width: 17px; height: 17px; flex-shrink: 0; }
    .profile-nav-item .nav-arrow { margin-left: auto; width: 14px; height: 14px; opacity: 0.5; }

    /* Main Form */
    .form-section-title {
        font-family: var(--font-heading);
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1.1rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid var(--card-border);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
    }
    .form-group { margin-bottom: 1.1rem; }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
    }
    .form-input {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1.5px solid var(--card-border);
        border-radius: var(--radius-sm);
        font-size: 0.88rem;
        font-family: var(--font-body);
        color: var(--text);
        outline: none;
        transition: border-color 200ms;
        background: white;
    }
    .form-input:focus { border-color: var(--color-penyewa); }
    .form-input:disabled { background: #F8FAFF; color: var(--text-muted); }

    /* Achievement badges */
    .achievement-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }
    .achievement-badge {
        background: white;
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 1rem 0.75rem;
        text-align: center;
        transition: box-shadow 150ms;
    }
    .achievement-badge:hover { box-shadow: var(--shadow-sm); }
    .ach-icon { font-size: 2rem; margin-bottom: 0.4rem; }
    .ach-name { font-size: 0.73rem; font-weight: 600; color: var(--text-secondary); }
    .ach-desc { font-size: 0.66rem; color: var(--text-muted); margin-top: 2px; }

    /* Tab content */
    .profile-tab-content { display: none; }
    .profile-tab-content.active { display: block; }

    /* Success toast */
    .profile-toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem;
        background: var(--success); color: white;
        padding: 0.75rem 1.25rem; border-radius: var(--radius-sm);
        font-size: 0.86rem; font-weight: 600;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        z-index: 9999;
        transform: translateY(100px); opacity: 0;
        transition: all 300ms;
    }
    .profile-toast.show { transform: translateY(0); opacity: 1; }
</style>
@endpush

@section('content')

@php
    $user = Auth::user();
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="page-header">
    <h1 class="page-title">👤 Profil Saya</h1>
    <p class="page-subtitle">Kelola informasi akun dan preferensi kamu</p>
</div>

<div class="profile-layout">

    {{-- ── Left Panel ── --}}
    <div>
        <div class="profile-panel">
            <div class="profile-banner"></div>
            <div class="profile-avatar-wrap">
                <div class="profile-avatar">
                    {{ $initials }}
                    <div class="avatar-edit-btn" onclick="showToast('Fitur upload foto dalam pengembangan!')">✏️</div>
                </div>
            </div>
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>
            <div class="profile-role-badge">
                <span class="badge badge-success">✓ Terverifikasi</span>
            </div>
            <div class="profile-stats-row">
                <div class="profile-stat-item">
                    <div class="val">0</div>
                    <div class="lbl">Transaksi</div>
                </div>
                <div class="profile-stat-item">
                    <div class="val" style="color:var(--warning);">0.0</div>
                    <div class="lbl">Rating</div>
                </div>
            </div>
            <div class="profile-nav">
                <button class="profile-nav-item {{ session('active_tab', 'info') === 'info' ? 'active' : '' }}" onclick="switchTab('info', this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informasi Pribadi
                    <svg class="nav-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button class="profile-nav-item {{ session('active_tab', 'info') === 'security' ? 'active' : '' }}" onclick="switchTab('security', this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Keamanan Akun
                    <svg class="nav-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button class="profile-nav-item" onclick="switchTab('notif', this)">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi
                    <svg class="nav-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

            </div>
        </div>

        {{-- Member since --}}
        <div style="margin-top:0.75rem;background:white;border:1px solid var(--card-border);border-radius:var(--radius-md);padding:0.85rem 1.1rem;">
            <div style="font-size:0.76rem;color:var(--text-muted);margin-bottom:3px;">Bergabung sejak</div>
            <div style="font-weight:600;font-size:0.88rem;color:var(--text);">{{ $user->created_at ? $user->created_at->format('d F Y') : 'Mei 2026' }}</div>
        </div>
    </div>

    {{-- ── Right Panel ── --}}
    <div>

        {{-- Info Pribadi --}}
        <div class="card profile-tab-content {{ session('active_tab', 'info') === 'info' ? 'active' : '' }}" id="tab-info">
            <form action="{{ route('penyewa.profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <span class="card-title">✏️ Informasi Pribadi</span>
                    <button type="submit" class="btn btn-primary-teal btn-sm">Simpan Perubahan</button>
                </div>
                <div class="card-body">
                    @if ($errors->any() && session('active_tab', 'info') === 'info')
                        <div style="background:var(--danger);color:white;padding:0.75rem;border-radius:var(--radius-sm);margin-bottom:1rem;font-size:0.85rem;">
                            <ul style="margin:0;padding-left:1.5rem;">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-section-title">Data Diri</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Depan</label>
                            <input type="text" name="first_name" class="form-input" id="first-name" value="{{ old('first_name', explode(' ', $user->name)[0]) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Belakang</label>
                            <input type="text" name="last_name" class="form-input" id="last-name" value="{{ old('last_name', count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                        <p style="font-size:0.74rem;color:var(--text-muted);margin-top:4px;">Email tidak dapat diubah. Hubungi support jika perlu.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="phone" class="form-input" placeholder="+62 812-xxxx-xxxx" value="{{ old('phone', $user->phone) }}">
                    </div>


                </div>
            </form>
        </div>

        {{-- Keamanan --}}
        <div class="card profile-tab-content {{ session('active_tab', 'info') === 'security' ? 'active' : '' }}" id="tab-security">
            <div class="card-header">
                <span class="card-title">🔐 Keamanan Akun</span>
            </div>
            <div class="card-body">
                <form action="{{ route('penyewa.profil.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-section-title">Ubah Password</div>

                    @if ($errors->any() && session('active_tab', 'info') === 'security')
                        <div style="background:var(--danger);color:white;padding:0.75rem;border-radius:var(--radius-sm);margin-bottom:1rem;font-size:0.85rem;">
                            <ul style="margin:0;padding-left:1.5rem;">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-input" placeholder="••••••••" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input" placeholder="Min. 8 karakter" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password baru" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-teal">Update Password</button>
                </form>

                <div class="form-section-title" style="margin-top:1.5rem;">Autentikasi 2 Faktor</div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem;background:var(--content-bg);border-radius:var(--radius-sm);">
                    <div>
                        <div style="font-weight:600;font-size:0.88rem;color:var(--text);">Two-Factor Authentication</div>
                        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:3px;">Tambahkan lapisan keamanan ekstra pada akun kamu</div>
                    </div>
                    <label style="display:flex;align-items:center;cursor:pointer;gap:8px;">
                        <input type="checkbox" id="2fa-toggle" onchange="showToast(this.checked ? '🔐 2FA diaktifkan!' : '⚠️ 2FA dinonaktifkan!')" style="width:18px;height:18px;accent-color:var(--color-penyewa);">
                        <span style="font-size:0.82rem;color:var(--text-secondary);">Aktifkan</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="card profile-tab-content" id="tab-notif">
            <div class="card-header">
                <span class="card-title">🔔 Preferensi Notifikasi</span>
            </div>
            <div class="card-body">
                @php
                $notifOptions = [
                    ['label'=>'Konfirmasi Sewa', 'desc'=>'Notifikasi saat penyedia mengkonfirmasi request', 'checked'=>true],
                    ['label'=>'Pengingat Pengembalian', 'desc'=>'Notifikasi H-1 sebelum waktu pengembalian', 'checked'=>true],
                    ['label'=>'Promo & Penawaran', 'desc'=>'Penawaran spesial dan diskon eksklusif', 'checked'=>false],
                    ['label'=>'Update Status Sewa', 'desc'=>'Perubahan status pesanan secara real-time', 'checked'=>true],
                    ['label'=>'Rekomendasi Barang', 'desc'=>'Barang baru sesuai preferensi kamu', 'checked'=>false],
                    ['label'=>'Ulasan & Rating', 'desc'=>'Pengingat untuk memberikan rating setelah sewa', 'checked'=>true],
                ];
                @endphp
                @foreach($notifOptions as $n)
                <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:0.85rem 0;border-bottom:1px solid #F1F5F9;gap:1rem;">
                    <div>
                        <div style="font-weight:600;font-size:0.87rem;color:var(--text);">{{ $n['label'] }}</div>
                        <div style="font-size:0.76rem;color:var(--text-muted);margin-top:2px;">{{ $n['desc'] }}</div>
                    </div>
                    <label style="cursor:pointer;flex-shrink:0;padding-top:3px;">
                        <input type="checkbox" {{ $n['checked'] ? 'checked' : '' }} onchange="showToast('⚙️ Preferensi notifikasi diperbarui!')" style="width:16px;height:16px;accent-color:var(--color-penyewa);">
                    </label>
                </div>
                @endforeach
            </div>
        </div>



    </div>
</div>

<div class="profile-toast" id="profile-toast"></div>

@endsection

@push('scripts')
<script>
    function switchTab(tab, btn) {
        // Update nav
        document.querySelectorAll('.profile-nav-item').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        // Update content
        document.querySelectorAll('.profile-tab-content').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
    }

    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            showToast('{{ session('success') }}');
        @endif
    });

    function saveProfile() {
        // Obsolete, we use standard form submission now
        showToast('✅ Profil berhasil disimpan!');
    }

    function showToast(msg) {
        var toast = document.getElementById('profile-toast');
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(window._profileToastTimer);
        window._profileToastTimer = setTimeout(function() {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endpush
