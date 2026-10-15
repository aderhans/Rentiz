@extends('layouts.app')
@section('title', 'Penarikan Dana')
@section('breadcrumb', 'Penarikan Dana')

@push('styles')
<style>
    .withdraw-layout { display:grid;grid-template-columns:1fr 380px;gap:1.25rem;align-items:start; }
    @media(max-width:960px){ .withdraw-layout{ grid-template-columns:1fr; } }

    .balance-card { background:linear-gradient(135deg,#2563EB,#1a3a8f);border-radius:var(--radius-xl);padding:1.6rem 1.5rem;color:white;margin-bottom:1.25rem;position:relative;overflow:hidden;box-shadow:0 10px 25px -5px rgba(37,99,235,.25); }
    .balance-card::before { content:'';position:absolute;top:-30px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07);pointer-events:none; }
    .balance-label { font-size:.82rem;font-weight:600;color:rgba(255,255,255,.8);margin-bottom:4px;letter-spacing:0.02em; }
    .balance-amount { font-family:var(--font-heading);font-size:2.3rem;font-weight:700;margin-bottom:.4rem;letter-spacing:-0.02em; }
    .balance-sub { font-size:.8rem;color:rgba(255,255,255,.75);display:flex;gap:1rem;flex-wrap:wrap; }
    .balance-chips { display:flex;gap:.5rem;margin-top:1.1rem;flex-wrap:wrap; }
    .balance-chip { background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.25);border-radius:50px;padding:4px 13px;font-size:.75rem;font-weight:600;color:white;display:inline-flex;align-items:center;gap:4px; }

    .withdraw-form { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:1.4rem;margin-bottom:1.25rem; }
    .wf-title { font-family:var(--font-heading);font-weight:700;font-size:.98rem;color:var(--text);margin-bottom:1rem;padding-bottom:.7rem;border-bottom:1px solid var(--card-border);display:flex;align-items:center;justify-content:space-between; }
    .wf-group { margin-bottom:1rem; }
    .wf-label { display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.35rem; }
    .wf-input { width:100%;padding:.65rem .9rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);font-size:.88rem;font-family:var(--font-body);color:var(--text);outline:none;transition:border-color 200ms; }
    .wf-input:focus { border-color:var(--color-penyedia);box-shadow:0 0 0 3px rgba(37,99,235,.1); }

    .amount-chips { display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem; }
    .amount-chip { padding:.35rem .85rem;border:1.5px solid var(--card-border);border-radius:50px;font-size:.77rem;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all 150ms;user-select:none; }
    .amount-chip:hover { border-color:var(--color-penyedia);color:var(--color-penyedia); }
    .amount-chip.active { background:var(--color-penyedia);border-color:var(--color-penyedia);color:white; }

    .bank-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(105px,1fr));gap:.55rem;margin-bottom:.85rem; }
    .bank-item { border:1.5px solid var(--card-border);border-radius:var(--radius-sm);padding:.6rem .5rem;text-align:center;cursor:pointer;transition:all 180ms;background:white; }
    .bank-item:hover { border-color:var(--color-penyedia);background:#F8FAFC; }
    .bank-item.selected { border-color:var(--color-penyedia);background:rgba(37,99,235,.05);font-weight:700;color:var(--color-penyedia);box-shadow:0 0 0 1px var(--color-penyedia); }
    .bank-item .b-icon { font-size:1.25rem;margin-bottom:2px; }
    .bank-item .b-name { font-size:.78rem;font-weight:600; }

    .sidebar-card { background:white;border:1px solid var(--card-border);border-radius:var(--radius-lg);overflow:hidden; }
    .sidebar-tabs { display:flex;border-bottom:1px solid var(--card-border);background:#F8FAFC; }
    .side-tab-btn { flex:1;padding:.85rem .65rem;border:none;background:none;font-size:.8rem;font-weight:600;color:var(--text-secondary);cursor:pointer;text-align:center;transition:all 180ms;border-bottom:2px solid transparent;font-family:var(--font-body); }
    .side-tab-btn:hover { color:var(--text); }
    .side-tab-btn.active { background:white;color:var(--color-penyedia);border-bottom-color:var(--color-penyedia); }

    .side-pane { max-height:550px;overflow-y:auto; }
    .side-pane::-webkit-scrollbar { width:4px; }
    .side-pane::-webkit-scrollbar-thumb { background:#CBD5E1;border-radius:4px; }

    .trx-income-item { display:flex;align-items:flex-start;gap:.75rem;padding:.9rem 1.15rem;border-bottom:1px solid #F1F5F9;transition:background 150ms; }
    .trx-income-item:last-child { border-bottom:none; }
    .trx-income-item:hover { background:#FAFBFF; }
    .trx-ava { width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#3B82F6,#1D4ED8);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;box-shadow:0 2px 6px rgba(59,130,246,.25); }
    .trx-info { flex:1;min-width:0; }
    .trx-user { font-weight:700;font-size:.84rem;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .trx-id { font-size:.7rem;font-weight:500;color:var(--text-muted);margin-left:3px; }
    .trx-item-name { font-size:.78rem;color:var(--text-secondary);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .trx-duration { font-size:.72rem;color:var(--text-muted);font-weight:400; }
    .trx-meta { display:flex;align-items:center;gap:.45rem;margin-top:4px;font-size:.7rem;color:var(--text-muted); }
    .trx-amount-wrap { text-align:right;flex-shrink:0;margin-left:.5rem; }
    .trx-amount { font-weight:700;font-size:.88rem;color:var(--success); }
    .trx-method { font-size:.68rem;color:var(--text-muted);margin-top:2px;text-transform:capitalize; }

    .hist-row { display:flex;align-items:center;gap:.75rem;padding:.85rem 1.15rem;border-bottom:1px solid #F1F5F9; }
    .hist-row:last-child { border-bottom:none; }
    .hist-icon { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
    .hist-info { flex:1;min-width:0; }
    .hist-label { font-weight:600;font-size:.83rem;color:var(--text); }

    .empty-state { text-align:center;padding:3rem 1.25rem;color:var(--text-muted); }

    .alert-banner { padding:.85rem 1.1rem;border-radius:var(--radius-md);margin-bottom:1.25rem;font-size:.84rem;font-weight:600;display:flex;align-items:center;gap:.6rem; }
    .alert-banner.success { background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46; }
    .alert-banner.error { background:#FEF2F2;border:1px solid #FECACA;color:#991B1B; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">💸 Penarikan Dana</h1>
    <p class="page-subtitle">Kelola dan cairkan pendapatan dari barang yang telah disewa oleh penyewa</p>
</div>

@if(session('success'))
<div class="alert-banner success">
    <span>✅</span>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="alert-banner error">
    <span>⚠️</span>
    <div>{{ session('error') }}</div>
</div>
@endif

@if($errors->any())
<div class="alert-banner error">
    <span>⚠️</span>
    <div>{{ $errors->first() }}</div>
</div>
@endif

{{-- Saldo Card Riil --}}
<div class="balance-card">
    <div class="balance-label">RentizPay — Saldo Tersedia (Siap Ditarik)</div>
    <div class="balance-amount">Rp {{ number_format($saldoTersedia, 0, ',', '.') }}</div>
    <div class="balance-sub">
        <span>Total Pendapatan: <strong>Rp {{ number_format($saldoTotal, 0, ',', '.') }}</strong></span>
        <span>·</span>
        <span>Sudah Ditarik: <strong>Rp {{ number_format($totalDitarik, 0, ',', '.') }}</strong></span>
        @if($dalamProses > 0)
        <span>·</span>
        <span>Menunggu Bayar: <strong>Rp {{ number_format($dalamProses, 0, ',', '.') }}</strong></span>
        @endif
    </div>
    <div class="balance-chips">
        <div class="balance-chip">✓ Terverifikasi</div>
        <div class="balance-chip">⚡ Pencairan Instan</div>
        <div class="balance-chip">🛡️ Pendapatan Utuh (100% Milik Penyedia)</div>
    </div>
</div>

<div class="withdraw-layout">
    {{-- Form Penarikan --}}
    <div>
        <div class="withdraw-form">
            <div class="wf-title">
                <span>💰 Ajukan Penarikan Dana</span>
                <span style="font-size:.75rem;font-weight:500;color:var(--text-muted);">Biaya Transfer: Rp 2.500</span>
            </div>

            <form action="{{ route('penyedia.penarikan-dana.proses') }}" method="POST" id="form-withdraw" onsubmit="return validateWithdrawForm()">
                @csrf
                <div class="wf-group">
                    <label class="wf-label">Jumlah Penarikan (Rp)</label>
                    <input 
                        type="number" 
                        name="jumlah" 
                        class="wf-input" 
                        id="withdraw-amount" 
                        placeholder="Minimal Rp 10.000" 
                        value="{{ $saldoTersedia >= 100000 ? 100000 : ($saldoTersedia >= 10000 ? $saldoTersedia : '') }}" 
                        min="10000" 
                        max="{{ (int)$saldoTersedia }}"
                        oninput="updateFee()"
                        required
                    >
                    <div class="amount-chips">
                        @if($saldoTersedia >= 50000)
                        <div class="amount-chip" onclick="setAmount(this, 50000)">Rp 50K</div>
                        @endif
                        @if($saldoTersedia >= 100000)
                        <div class="amount-chip {{ $saldoTersedia >= 100000 ? 'active' : '' }}" onclick="setAmount(this, 100000)">Rp 100K</div>
                        @endif
                        @if($saldoTersedia >= 250000)
                        <div class="amount-chip" onclick="setAmount(this, 250000)">Rp 250K</div>
                        @endif
                        @if($saldoTersedia >= 500000)
                        <div class="amount-chip" onclick="setAmount(this, 500000)">Rp 500K</div>
                        @endif
                        @if($saldoTersedia > 0)
                        <div class="amount-chip {{ $saldoTersedia < 100000 ? 'active' : '' }}" onclick="setAmount(this, {{ (int)$saldoTersedia }})">Tarik Semua (Rp {{ number_format($saldoTersedia, 0, ',', '.') }})</div>
                        @endif
                    </div>
                </div>

                <div class="wf-group">
                    <label class="wf-label">Bank / E-Wallet Tujuan</label>
                    <input type="hidden" name="bank_tujuan" id="input-bank-tujuan" value="BCA">
                    <div class="bank-grid">
                        <div class="bank-item selected" data-bank="BCA" onclick="selectBank('BCA', this)">
                            <div class="b-icon">🏦</div>
                            <div class="b-name">BCA</div>
                        </div>
                        <div class="bank-item" data-bank="Mandiri" onclick="selectBank('Mandiri', this)">
                            <div class="b-icon">🏦</div>
                            <div class="b-name">Mandiri</div>
                        </div>
                        <div class="bank-item" data-bank="BRI" onclick="selectBank('BRI', this)">
                            <div class="b-icon">🏦</div>
                            <div class="b-name">BRI</div>
                        </div>
                        <div class="bank-item" data-bank="BNI" onclick="selectBank('BNI', this)">
                            <div class="b-icon">🏦</div>
                            <div class="b-name">BNI</div>
                        </div>
                        <div class="bank-item" data-bank="GoPay" onclick="selectBank('GoPay', this)">
                            <div class="b-icon">📱</div>
                            <div class="b-name">GoPay</div>
                        </div>
                        <div class="bank-item" data-bank="DANA" onclick="selectBank('DANA', this)">
                            <div class="b-icon">📱</div>
                            <div class="b-name">DANA</div>
                        </div>
                    </div>
                </div>

                <div class="wf-group">
                    <label class="wf-label" id="label-nomor">Nomor Rekening / No. HP E-Wallet</label>
                    <input 
                        type="text" 
                        name="nomor_rekening" 
                        id="nomor-rekening"
                        class="wf-input" 
                        placeholder="Contoh: 1234567890 atau 08123456789" 
                        value="{{ Auth::user()->phone ?? '' }}" 
                        required
                    >
                </div>

                <div class="wf-group">
                    <label class="wf-label">Nama Pemilik Rekening / Akun</label>
                    <input 
                        type="text" 
                        name="atas_nama" 
                        class="wf-input" 
                        placeholder="Nama sesuai buku tabungan atau akun e-wallet" 
                        value="{{ Auth::user()->name }}" 
                        required
                    >
                </div>

                {{-- Kalkulasi Biaya --}}
                <div style="background:var(--content-bg);border-radius:var(--radius-sm);padding:.85rem 1rem;margin-bottom:1.15rem;border:1px solid var(--card-border);">
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:.4rem;">
                        <span style="color:var(--text-secondary);">Jumlah Ditarik</span>
                        <span id="fee-amount" style="font-weight:600;color:var(--text);">Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:.4rem;">
                        <span style="color:var(--text-secondary);">Biaya Transfer Admin</span>
                        <span style="font-weight:600;color:var(--danger);">- Rp 2.500</span>
                    </div>
                    <div style="border-top:1px dashed var(--card-border);margin:.6rem 0;"></div>
                    <div style="display:flex;justify-content:space-between;font-size:.9rem;">
                        <span style="font-weight:700;color:var(--text);">Bersih Diterima</span>
                        <span id="fee-result" style="font-weight:700;color:var(--color-penyedia);">Rp 0</span>
                    </div>
                </div>

                @if($saldoTersedia >= 10000)
                <button type="submit" class="btn btn-primary-blue" style="width:100%;justify-content:center;padding:.75rem 1rem;" id="btn-submit-withdraw">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ajukan Penarikan Sekarang
                </button>
                @else
                <button type="button" class="btn" style="width:100%;justify-content:center;padding:.75rem 1rem;background:#E2E8F0;color:#94A3B8;cursor:not-allowed;" disabled>
                    Saldo Tidak Mencukupi (Min. Rp 10.000)
                </button>
                @endif
            </form>
        </div>
    </div>

    {{-- Panel Kanan: Pemasukan dari Pesanan Penyewa & Riwayat Pencairan --}}
    <div>
        <div class="sidebar-card">
            <div class="sidebar-tabs">
                <button type="button" class="side-tab-btn active" id="tab-btn-income" onclick="switchSideTab('income')">
                    📥 Pemasukan ({{ $paidOrders->count() }})
                </button>
                <button type="button" class="side-tab-btn" id="tab-btn-history" onclick="switchSideTab('history')">
                    📤 Riwayat Tarik ({{ $riwayatPenarikan->count() }})
                </button>
            </div>

            {{-- Tab 1: Pemasukan Riil dari Transaksi Penyewa --}}
            <div class="side-pane" id="pane-income">
                @forelse($paidOrders as $order)
                    @php
                        $pemesan = $order->pemesan;
                        $firstItem = $order->items ? $order->items->first() : null;
                        $barang = $firstItem ? $firstItem->barang : null;
                        $orderIncome = ($order->items && $order->items->sum('subtotal') > 0) ? (float)$order->items->sum('subtotal') : max(0, (float)$order->total_biaya - 2000);
                        $init = strtoupper(substr($pemesan && $pemesan->name ? $pemesan->name : 'P', 0, 2));
                        $metodeBayar = ($order->pembayaran && $order->pembayaran->metode_bayar) ? ucwords(str_replace('_', ' ', $order->pembayaran->metode_bayar)) : 'Online';
                    @endphp
                    <div class="trx-income-item">
                        <div class="trx-ava">{{ $init }}</div>
                        <div class="trx-info">
                            <div class="trx-user">
                                {{ $pemesan ? $pemesan->name : 'Penyewa' }}
                                <span class="trx-id">#{{ substr($order->id, 0, 8) }}</span>
                            </div>
                            <div class="trx-item-name">
                                {{ $barang ? $barang->nama : 'Sewa Barang' }}
                                @if($firstItem && $firstItem->durasi_hari)
                                    <span class="trx-duration">· {{ $firstItem->durasi_hari }} hari</span>
                                @endif
                            </div>
                            <div class="trx-meta">
                                <span>{{ $order->payment_timestamp ? $order->payment_timestamp->translatedFormat('d M Y, H:i') : $order->created_at->translatedFormat('d M Y') }}</span>
                                <span class="badge badge-success" style="font-size:0.62rem;padding:2px 6px;">Lunas</span>
                            </div>
                        </div>
                        <div class="trx-amount-wrap">
                            <div class="trx-amount">+ Rp {{ number_format($orderIncome, 0, ',', '.') }}</div>
                            <div class="trx-method">
                                {{ $metodeBayar }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div style="font-size:2.8rem;margin-bottom:0.6rem;">📭</div>
                        <div style="font-weight:700;color:var(--text);font-size:0.95rem;margin-bottom:0.35rem;">Belum Ada Pemasukan</div>
                        <p style="font-size:0.78rem;color:var(--text-muted);margin:0;line-height:1.5;">
                            Ketika penyewa menyelesaikan pembayaran untuk barang Anda, dana dan rincian transaksi otomatis tercatat di sini.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Tab 2: Riwayat Penarikan Dana ke Rekening --}}
            <div class="side-pane" id="pane-history" style="display:none;">
                @forelse($riwayatPenarikan as $p)
                    <div class="hist-row">
                        <div class="hist-icon" style="background:#EFF6FF;">
                            {{ in_array(strtolower($p->bank_tujuan), ['gopay','ovo','dana']) ? '📱' : '🏦' }}
                        </div>
                        <div class="hist-info">
                            <div class="hist-label">Transfer ke {{ strtoupper($p->bank_tujuan) }}</div>
                            <div style="font-size:0.72rem;color:var(--text-muted);margin-top:1px;">
                                {{ $p->nomor_rekening }} · a.n. {{ $p->atas_nama }}
                            </div>
                            <div style="display:flex;align-items:center;gap:.4rem;margin-top:3px;">
                                <span style="font-size:.7rem;color:var(--text-muted);">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                                <span class="badge badge-success" style="font-size:.62rem;padding:2px 6px;">Berhasil</span>
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div class="hist-amount" style="color:#DC2626;font-weight:700;font-size:0.86rem;">
                                - Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </div>
                            <div style="font-size:0.68rem;color:var(--text-muted);margin-top:1px;">
                                Bersih: Rp {{ number_format($p->jumlah_diterima, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div style="font-size:2.8rem;margin-bottom:0.6rem;">💳</div>
                        <div style="font-weight:700;color:var(--text);font-size:0.95rem;margin-bottom:0.35rem;">Belum Ada Penarikan</div>
                        <p style="font-size:0.78rem;color:var(--text-muted);margin:0;line-height:1.5;">
                            Anda belum pernah melakukan penarikan dana ke rekening atau e-wallet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var currentBalance = {{ (int)$saldoTersedia }};

    function updateFee() {
        var inputEl = document.getElementById('withdraw-amount');
        var amt = parseInt(inputEl.value) || 0;
        var fee = 2500;
        var result = amt > fee ? amt - fee : 0;
        
        document.getElementById('fee-amount').textContent = 'Rp ' + amt.toLocaleString('id-ID');
        document.getElementById('fee-result').textContent = 'Rp ' + result.toLocaleString('id-ID');
    }

    function setAmount(el, val) {
        document.querySelectorAll('.amount-chip').forEach(function(c) {
            c.classList.remove('active');
        });
        el.classList.add('active');
        
        var inputEl = document.getElementById('withdraw-amount');
        inputEl.value = val;
        updateFee();
    }

    function selectBank(bankName, el) {
        document.querySelectorAll('.bank-item').forEach(function(b) {
            b.classList.remove('selected');
        });
        el.classList.add('selected');
        document.getElementById('input-bank-tujuan').value = bankName;

        var labelEl = document.getElementById('label-nomor');
        var inputRek = document.getElementById('nomor-rekening');
        if (['gopay', 'dana', 'ovo'].includes(bankName.toLowerCase())) {
            labelEl.textContent = 'Nomor HP E-Wallet (' + bankName + ')';
            inputRek.placeholder = 'Contoh: 08123456789';
        } else {
            labelEl.textContent = 'Nomor Rekening Bank (' + bankName + ')';
            inputRek.placeholder = 'Contoh: 1234567890';
        }
    }

    function switchSideTab(tab) {
        var btnIncome = document.getElementById('tab-btn-income');
        var btnHist = document.getElementById('tab-btn-history');
        var paneIncome = document.getElementById('pane-income');
        var paneHist = document.getElementById('pane-history');

        if (tab === 'income') {
            btnIncome.classList.add('active');
            btnHist.classList.remove('active');
            paneIncome.style.display = 'block';
            paneHist.style.display = 'none';
        } else {
            btnIncome.classList.remove('active');
            btnHist.classList.add('active');
            paneIncome.style.display = 'none';
            paneHist.style.display = 'block';
        }
    }

    function validateWithdrawForm() {
        var amt = parseInt(document.getElementById('withdraw-amount').value) || 0;
        if (amt < 10000) {
            alert('Minimum nominal penarikan dana adalah Rp 10.000.');
            return false;
        }
        if (amt > currentBalance) {
            alert('Saldo Anda tidak mencukupi! Saldo tersedia Anda saat ini adalah Rp ' + currentBalance.toLocaleString('id-ID'));
            return false;
        }
        return confirm('Apakah Anda yakin ingin menarik dana sebesar Rp ' + amt.toLocaleString('id-ID') + '?');
    }

    // Inisialisasi hitungan fee saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateFee();
    });
</script>
@endpush
