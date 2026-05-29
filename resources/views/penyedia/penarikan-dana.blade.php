@extends('layouts.app')
@section('title', 'Penarikan Dana')
@section('breadcrumb', 'Penarikan Dana')

@push('styles')
<style>
    .withdraw-layout { display:grid;grid-template-columns:1fr 340px;gap:1.25rem;align-items:start; }
    @media(max-width:900px){ .withdraw-layout{ grid-template-columns:1fr; } }

    .balance-card { background:linear-gradient(135deg,#2563EB,#1a3a8f);border-radius:var(--radius-xl);padding:1.5rem 1.4rem;color:white;margin-bottom:1.25rem;position:relative;overflow:hidden; }
    .balance-card::before { content:'';position:absolute;top:-30px;right:-30px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none; }
    .balance-label { font-size:.8rem;color:rgba(255,255,255,.7);margin-bottom:3px; }
    .balance-amount { font-family:var(--font-heading);font-size:2.2rem;font-weight:700;margin-bottom:.35rem; }
    .balance-sub { font-size:.78rem;color:rgba(255,255,255,.65); }
    .balance-chips { display:flex;gap:.5rem;margin-top:1rem;flex-wrap:wrap; }
    .balance-chip { background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);border-radius:50px;padding:4px 12px;font-size:.75rem;font-weight:600;color:white; }

    .withdraw-form { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.4rem;margin-bottom:1.25rem; }
    .wf-title { font-family:var(--font-heading);font-weight:700;font-size:.95rem;color:var(--text);margin-bottom:1rem;padding-bottom:.7rem;border-bottom:1px solid var(--card-border); }
    .wf-group { margin-bottom:1rem; }
    .wf-label { display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.35rem; }
    .wf-input { width:100%;padding:.65rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.88rem;font-family:var(--font-body);color:var(--text);outline:none;transition:border-color 200ms; }
    .wf-input:focus { border-color:var(--color-penyedia); }

    .amount-chips { display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.4rem; }
    .amount-chip { padding:.35rem .85rem;border:1.5px solid var(--card-border);border-radius:50px;font-size:.77rem;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all 150ms; }
    .amount-chip:hover { border-color:var(--color-penyedia);color:var(--color-penyedia); }
    .amount-chip.active { background:var(--color-penyedia);border-color:var(--color-penyedia);color:white; }

    .bank-option { border:2px solid var(--card-border);border-radius:var(--radius-sm);padding:.85rem 1rem;cursor:pointer;display:flex;align-items:center;gap:.75rem;margin-bottom:.6rem;transition:border-color 200ms,background 200ms; }
    .bank-option:hover { border-color:var(--color-penyedia); }
    .bank-option.selected { border-color:var(--color-penyedia);background:rgba(37,99,235,.03); }
    .bank-icon { width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
    .bank-name { font-weight:600;font-size:.85rem;color:var(--text); }
    .bank-num { font-size:.74rem;color:var(--text-muted);margin-top:1px; }
    .bank-radio { width:16px;height:16px;border-radius:50%;border:2px solid var(--card-border);margin-left:auto;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:border-color 200ms; }
    .bank-option.selected .bank-radio { border-color:var(--color-penyedia);background:var(--color-penyedia); }
    .bank-option.selected .bank-radio::after { content:'';width:6px;height:6px;border-radius:50%;background:white; }

    .hist-row { display:flex;align-items:center;gap:.85rem;padding:.8rem 1.25rem;border-bottom:1px solid #F1F5F9; }
    .hist-row:last-child { border-bottom:none; }
    .hist-icon { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .hist-info { flex:1;min-width:0; }
    .hist-label { font-weight:600;font-size:.84rem;color:var(--text); }
    .hist-date { font-size:.72rem;color:var(--text-muted);margin-top:1px; }
    .hist-amount { font-weight:700;font-size:.88rem;color:var(--success); }

    .toast { position:fixed;bottom:1.5rem;right:1.5rem;background:var(--color-penyedia);color:white;padding:.7rem 1.2rem;border-radius:var(--radius-sm);font-size:.84rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.2);z-index:9999;transform:translateY(100px);opacity:0;transition:all 300ms; }
    .toast.show { transform:translateY(0);opacity:1; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">💸 Penarikan Dana</h1>
    <p class="page-subtitle">Cairkan pendapatan kamu ke rekening bank atau e-wallet</p>
</div>

{{-- Saldo Card --}}
<div class="balance-card">
    <div style="font-size:.82rem;font-weight:600;color:rgba(255,255,255,.75);margin-bottom:2px;">RentizPay — Saldo Tersedia</div>
    <div class="balance-amount">Rp 3.820.000</div>
    <div class="balance-sub">Saldo total: Rp 4.200.000 · Dalam proses: Rp 380.000</div>
    <div class="balance-chips">
        <div class="balance-chip">✓ Terverifikasi</div>
        <div class="balance-chip">🏦 2 Rekening Terdaftar</div>
        <div class="balance-chip">⚡ Proses 1×24 Jam</div>
    </div>
</div>

<div class="withdraw-layout">
    {{-- Form Penarikan --}}
    <div>
        <div class="withdraw-form">
            <div class="wf-title">💰 Buat Permintaan Penarikan</div>
            <div class="wf-group">
                <label class="wf-label">Jumlah Penarikan (Rp)</label>
                <input type="number" class="wf-input" id="withdraw-amount" placeholder="Minimum Rp 100.000" oninput="updateFee()">
                <div class="amount-chips">
                    <div class="amount-chip" onclick="setAmount(this,'500000')">Rp 500K</div>
                    <div class="amount-chip active" onclick="setAmount(this,'1000000')">Rp 1Jt</div>
                    <div class="amount-chip" onclick="setAmount(this,'2000000')">Rp 2Jt</div>
                    <div class="amount-chip" onclick="setAmount(this,'3820000')">Semua</div>
                </div>
            </div>
            <div class="wf-group">
                <label class="wf-label">Rekening Tujuan</label>
                <div class="bank-option selected" id="bank-bca" onclick="selectBank('bca',this)">
                    <div class="bank-icon" style="background:#EFF6FF;">🏦</div>
                    <div>
                        <div class="bank-name">BCA — a.n. {{ Auth::user()->name }}</div>
                        <div class="bank-num">1234 5678 90 · Rekening Utama</div>
                    </div>
                    <div class="bank-radio"></div>
                </div>
                <div class="bank-option" id="bank-gopay" onclick="selectBank('gopay',this)">
                    <div class="bank-icon" style="background:#F0FDF4;">📱</div>
                    <div>
                        <div class="bank-name">GoPay — +62 812-3456-7890</div>
                        <div class="bank-num">E-wallet</div>
                    </div>
                    <div class="bank-radio"></div>
                </div>
                <button class="btn btn-outline btn-sm" style="margin-top:.5rem;width:100%;justify-content:center;" onclick="alert('Fitur tambah rekening segera hadir!')">+ Tambah Rekening</button>
            </div>

            <div style="background:var(--content-bg);border-radius:var(--radius-sm);padding:.85rem 1rem;margin-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;font-size:.81rem;margin-bottom:.4rem;">
                    <span style="color:var(--text-secondary);">Jumlah Ditarik</span>
                    <span id="fee-amount" style="font-weight:600;color:var(--text);">Rp 1.000.000</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.81rem;margin-bottom:.4rem;">
                    <span style="color:var(--text-secondary);">Biaya Admin</span>
                    <span style="font-weight:600;color:var(--danger);">- Rp 2.500</span>
                </div>
                <div style="border-top:1px solid var(--card-border);margin:.6rem 0;"></div>
                <div style="display:flex;justify-content:space-between;font-size:.88rem;">
                    <span style="font-weight:700;color:var(--text);">Diterima</span>
                    <span id="fee-result" style="font-weight:700;color:var(--color-penyedia);">Rp 997.500</span>
                </div>
            </div>

            <button class="btn btn-primary-blue" style="width:100%;justify-content:center;" onclick="submitWithdraw()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Ajukan Penarikan
            </button>
        </div>
    </div>

    {{-- Riwayat Penarikan --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Riwayat Penarikan</span></div>
            @php
            $history=[
                ['icon'=>'🏦','bg'=>'#EFF6FF','label'=>'Transfer ke BCA','date'=>'24 Mei 2026','amount'=>'2.000.000','status'=>'success','slabel'=>'Sukses'],
                ['icon'=>'📱','bg'=>'#F0FDF4','label'=>'Transfer ke GoPay','date'=>'15 Mei 2026','amount'=>'500.000','status'=>'success','slabel'=>'Sukses'],
                ['icon'=>'🏦','bg'=>'#EFF6FF','label'=>'Transfer ke BCA','date'=>'1 Mei 2026','amount'=>'1.500.000','status'=>'success','slabel'=>'Sukses'],
                ['icon'=>'🏦','bg'=>'#EFF6FF','label'=>'Transfer ke BCA','date'=>'20 Apr 2026','amount'=>'3.000.000','status'=>'success','slabel'=>'Sukses'],
                ['icon'=>'⏳','bg'=>'#FEF3C7','label'=>'Transfer ke BCA','date'=>'10 Apr 2026','amount'=>'800.000','status'=>'warning','slabel'=>'Diproses'],
            ];
            @endphp
            @foreach($history as $h)
            <div class="hist-row">
                <div class="hist-icon" style="background:{{ $h['bg'] }};">{{ $h['icon'] }}</div>
                <div class="hist-info">
                    <div class="hist-label">{{ $h['label'] }}</div>
                    <div style="display:flex;align-items:center;gap:.4rem;margin-top:2px;">
                        <span style="font-size:.72rem;color:var(--text-muted);">{{ $h['date'] }}</span>
                        <span class="badge badge-{{ $h['status'] }}" style="font-size:.62rem;">{{ $h['slabel'] }}</span>
                    </div>
                </div>
                <div class="hist-amount">Rp {{ $h['amount'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="toast" id="wd-toast"></div>
@endsection

@push('scripts')
<script>
    document.getElementById('withdraw-amount').value = 1000000;

    function setAmount(el, val) {
        document.querySelectorAll('.amount-chip').forEach(c=>c.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('withdraw-amount').value = val;
        updateFee();
    }

    function updateFee() {
        var amt = parseInt(document.getElementById('withdraw-amount').value)||0;
        var fee = 2500;
        var result = Math.max(0, amt - fee);
        document.getElementById('fee-amount').textContent = 'Rp ' + amt.toLocaleString('id-ID');
        document.getElementById('fee-result').textContent = 'Rp ' + result.toLocaleString('id-ID');
    }

    function selectBank(id, el) {
        document.querySelectorAll('.bank-option').forEach(b=>b.classList.remove('selected'));
        el.classList.add('selected');
    }

    function submitWithdraw() {
        var amt = parseInt(document.getElementById('withdraw-amount').value)||0;
        if (amt < 100000) { alert('Minimum penarikan Rp 100.000'); return; }
        if (amt > 3820000) { alert('Saldo tidak mencukupi!'); return; }
        showToast('✅ Penarikan Rp '+amt.toLocaleString('id-ID')+' berhasil diajukan! Proses 1×24 jam.');
    }

    function showToast(msg) {
        var t = document.getElementById('wd-toast');
        t.textContent = msg; t.classList.add('show');
        clearTimeout(window._wdt); window._wdt = setTimeout(function(){ t.classList.remove('show'); }, 4000);
    }
</script>
@endpush
