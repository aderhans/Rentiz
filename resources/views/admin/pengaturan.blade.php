@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('breadcrumb', 'Pengaturan')

@push('styles')
<style>
    .set-layout { display:grid;grid-template-columns:260px 1fr;gap:1.5rem;align-items:start; }
    @media(max-width:800px){ .set-layout{ grid-template-columns:1fr; } }

    .set-nav { display:flex;flex-direction:column;gap:.2rem;background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:.5rem; }
    .set-tab { display:flex;align-items:center;gap:.6rem;padding:.7rem 1rem;border:none;background:transparent;border-radius:var(--radius-sm);font-size:.85rem;font-weight:600;color:var(--text-secondary);cursor:pointer;font-family:var(--font-body);text-align:left;transition:all 150ms; }
    .set-tab:hover { background:var(--content-bg);color:var(--text); }
    .set-tab.active { background:var(--color-admin);color:white; }

    .set-pane { display:none;background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg); }
    .set-pane.active { display:block; }
    .set-header { padding:1.25rem;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between; }
    .set-title { font-family:var(--font-heading);font-size:1rem;font-weight:700;color:var(--text); }
    .set-body { padding:1.25rem; }

    .fsec { font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:.85rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-secondary); }
    .frow { display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem; }
    @media(max-width:600px){ .frow{ grid-template-columns:1fr; } }
    .fg { margin-bottom:1rem; }
    .fl { display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.4rem; }
    .fi { width:100%;padding:.65rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.87rem;font-family:var(--font-body);color:var(--text);outline:none;transition:border-color 200ms; }
    .fi:focus { border-color:var(--color-admin); }

    .toggle-row { display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid #F1F5F9; }
    .toggle-row:last-child { border-bottom:none; }
    .toggle-info .tn { font-weight:600;font-size:.86rem;color:var(--text); }
    .toggle-info .td { font-size:.75rem;color:var(--text-muted);margin-top:2px; }
    .toggle-sw { position:relative;width:40px;height:22px;flex-shrink:0; }
    .toggle-sw input { opacity:0;width:0;height:0; }
    .toggle-track { position:absolute;inset:0;background:#CBD5E1;border-radius:50px;cursor:pointer;transition:background 200ms; }
    .toggle-track::after { content:'';position:absolute;width:16px;height:16px;border-radius:50%;background:white;top:3px;left:3px;transition:transform 200ms;box-shadow:0 1px 3px rgba(0,0,0,.2); }
    .toggle-sw input:checked + .toggle-track { background:var(--color-admin); }
    .toggle-sw input:checked + .toggle-track::after { transform:translateX(18px); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;background:var(--color-admin);color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">⚙️ Pengaturan Sistem</h1>
    <p class="page-subtitle">Konfigurasi variabel utama dan kebijakan platform Rentiz</p>
</div>

<div class="set-layout">
    <div class="set-nav">
        <button class="set-tab active" onclick="switchTab('general',this)">🔧 Umum & Meta</button>
        <button class="set-tab" onclick="switchTab('payment',this)">💳 Finansial & Fee</button>
        <button class="set-tab" onclick="switchTab('notif',this)">🔔 Email & Notifikasi</button>
        <button class="set-tab" onclick="switchTab('security',this)">🛡️ Keamanan & Akses</button>
    </div>

    <div>
        {{-- General Tab --}}
        <div class="set-pane active" id="tab-general">
            <div class="set-header">
                <span class="set-title">Umum & Meta</span>
                <button class="btn btn-primary-purple btn-sm" onclick="saveSet()">Simpan Perubahan</button>
            </div>
            <div class="set-body">
                <div class="fsec">Identitas Platform</div>
                <div class="frow">
                    <div class="fg"><label class="fl">Nama Aplikasi</label><input type="text" class="fi" value="Rentiz"></div>
                    <div class="fg"><label class="fl">Slogan Utama</label><input type="text" class="fi" value="Sewa Barang Mudah & Aman"></div>
                </div>
                <div class="fg"><label class="fl">Email Kontak Support</label><input type="email" class="fi" value="support@rentiz.com"></div>
                <div class="fg"><label class="fl">Deskripsi SEO (Meta Description)</label><textarea class="fi" rows="3">Platform marketplace penyewaan barang terpercaya di Indonesia. Temukan kamera, tenda, alat elektronik, dan perlengkapan lainnya.</textarea></div>
                
                <div class="fsec" style="margin-top:1.5rem;">Fitur Global</div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Maintenance Mode</div><div class="td">Tutup akses website untuk umum saat ada perbaikan server</div></div>
                    <label class="toggle-sw"><input type="checkbox" onchange="warnMaintenance(this)"><div class="toggle-track"></div></label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Registrasi User Baru</div><div class="td">Izinkan pengguna baru untuk mendaftar akun</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>
            </div>
        </div>

        {{-- Payment Tab --}}
        <div class="set-pane" id="tab-payment">
            <div class="set-header">
                <span class="set-title">Finansial & Fee</span>
                <button class="btn btn-primary-purple btn-sm" onclick="saveSet()">Simpan Perubahan</button>
            </div>
            <div class="set-body">
                <div class="fsec">Potongan & Komisi Platform</div>
                <div class="frow">
                    <div class="fg">
                        <label class="fl">Komisi Transaksi (%)</label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);overflow:hidden;">
                            <input type="number" class="fi" style="border:none;border-radius:0;" value="10">
                            <span style="padding:0 1rem;background:var(--content-bg);font-size:.85rem;font-weight:700;color:var(--text-secondary);border-left:1px solid var(--card-border);">%</span>
                        </div>
                    </div>
                    <div class="fg">
                        <label class="fl">Biaya Layanan Penyewa (Flat)</label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);overflow:hidden;">
                            <span style="padding:0 .8rem;background:var(--content-bg);font-size:.85rem;font-weight:700;color:var(--text-secondary);border-right:1px solid var(--card-border);">Rp</span>
                            <input type="number" class="fi" style="border:none;border-radius:0;" value="2500">
                        </div>
                    </div>
                </div>

                <div class="fsec" style="margin-top:1rem;">Withdrawal (Penarikan Dana)</div>
                <div class="frow">
                    <div class="fg">
                        <label class="fl">Minimal Penarikan</label>
                        <input type="number" class="fi" value="100000">
                    </div>
                    <div class="fg">
                        <label class="fl">Biaya Transfer Bank</label>
                        <input type="number" class="fi" value="2500">
                    </div>
                </div>
                
                <div class="fsec" style="margin-top:1rem;">Payment Gateway</div>
                <div class="fg"><label class="fl">Midtrans Server Key</label><input type="password" class="fi" value="SB-Mid-server-xxxxxxxxxxxx"></div>
                <div class="fg"><label class="fl">Midtrans Client Key</label><input type="text" class="fi" value="SB-Mid-client-xxxxxxxxxxxx"></div>
            </div>
        </div>

        {{-- Notif Tab --}}
        <div class="set-pane" id="tab-notif">
            <div class="set-header">
                <span class="set-title">Email & Notifikasi</span>
                <button class="btn btn-primary-purple btn-sm" onclick="saveSet()">Simpan Perubahan</button>
            </div>
            <div class="set-body">
                <div class="fsec">Notifikasi Sistem ke Admin</div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Registrasi Penyedia Baru</div><div class="td">Kirim email saat ada toko baru dibuat</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Dispute / Laporan Baru</div><div class="td">Kirim peringatan jika ada laporan dari user</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Withdrawal Request</div><div class="td">Kirim email saat ada penarikan dana > Rp 5.000.000</div></div>
                    <label class="toggle-sw"><input type="checkbox"><div class="toggle-track"></div></label>
                </div>

                <div class="fsec" style="margin-top:1.5rem;">Konfigurasi SMTP (Mailer)</div>
                <div class="frow">
                    <div class="fg"><label class="fl">SMTP Host</label><input type="text" class="fi" value="smtp.mailtrap.io"></div>
                    <div class="fg"><label class="fl">SMTP Port</label><input type="text" class="fi" value="2525"></div>
                </div>
                <div class="frow">
                    <div class="fg"><label class="fl">SMTP Username</label><input type="text" class="fi" value="user_xxxxxxx"></div>
                    <div class="fg"><label class="fl">SMTP Password</label><input type="password" class="fi" value="pass_xxxxxxx"></div>
                </div>
            </div>
        </div>

        {{-- Security Tab --}}
        <div class="set-pane" id="tab-security">
            <div class="set-header">
                <span class="set-title">Keamanan & Akses</span>
                <button class="btn btn-primary-purple btn-sm" onclick="saveSet()">Simpan Perubahan</button>
            </div>
            <div class="set-body">
                <div class="fsec">Autentikasi User</div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Verifikasi Email (Wajib)</div><div class="td">User harus klik link verifikasi sebelum bisa transaksi</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">KYC untuk Penyedia (Wajib KTP)</div><div class="td">Mewajibkan upload KTP untuk buka toko</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info"><div class="tn">Toleransi Gagal Login</div><div class="td">Blokir IP sementara setelah 5x gagal login</div></div>
                    <label class="toggle-sw"><input type="checkbox" checked><div class="toggle-track"></div></label>
                </div>

                <div class="fsec" style="margin-top:1.5rem;">Admin Access Control</div>
                <div class="fg" style="margin-bottom:.5rem;">
                    <label class="fl">Whitelist IP Admin (Opsional)</label>
                    <textarea class="fi" rows="2" placeholder="Pisahkan dengan koma. Contoh: 192.168.1.1, 10.0.0.1"></textarea>
                    <p style="font-size:.7rem;color:var(--text-muted);margin-top:4px;">Hanya IP di atas yang bisa mengakses dashboard admin. Kosongkan untuk akses dari mana saja.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="toast" id="set-toast"></div>
@endsection

@push('scripts')
<script>
    function switchTab(tab, btn) {
        document.querySelectorAll('.set-tab').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.set-pane').forEach(p=>p.classList.remove('active'));
        document.getElementById('tab-'+tab).classList.add('active');
    }
    function warnMaintenance(cb) {
        if(cb.checked) {
            if(!confirm('⚠️ PERINGATAN: Mengaktifkan Maintenance Mode akan membuat seluruh pengguna (kecuali Admin) tidak bisa mengakses website. Lanjutkan?')) {
                cb.checked = false;
            }
        }
    }
    function saveSet() {
        var t = document.getElementById('set-toast');
        t.textContent = '✅ Pengaturan berhasil disimpan!'; t.classList.add('show');
        clearTimeout(window._st); window._st = setTimeout(function(){ t.classList.remove('show'); }, 3000);
    }
</script>
@endpush
