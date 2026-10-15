@extends('layouts.app')

@section('title', 'Payment Gateway — Simulasi Midtrans')
@section('breadcrumb', 'Payment Gateway')

@push('styles')
<style>
    /* ═══════════════════════════════════════
       MIDTRANS SIMULATED PAYMENT GATEWAY
    ═══════════════════════════════════════ */

    .gateway-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.78);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }

    .gateway-container {
        background: #ffffff;
        border-radius: 24px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 30px 90px -10px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.12);
        overflow: hidden;
        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 92vh;
        overflow-y: auto;
        position: relative;
    }

    @keyframes slideUp {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Header */
    .gw-header {
        background: linear-gradient(135deg, #002855 0%, #003d82 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .gw-header::after {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .gw-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
    }
    .gw-logo-icon {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .gw-logo-text {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .gw-logo-sub {
        font-size: 0.65rem;
        color: rgba(255,255,255,0.5);
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .gw-secure {
        display: flex;
        align-items: center;
        gap: 5px;
        color: rgba(255,255,255,0.6);
        font-size: 0.72rem;
        font-weight: 500;
    }
    .gw-secure svg {
        width: 14px; height: 14px;
    }

    /* Order Summary */
    .gw-summary {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfe;
    }
    .gw-merchant {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    .gw-order-id {
        font-size: 0.72rem;
        color: #94a3b8;
        font-family: monospace;
    }
    .gw-amount-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 0.75rem;
    }
    .gw-amount-label {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }
    .gw-amount {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.7rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    /* Timer */
    .gw-timer-bar {
        background: linear-gradient(90deg, #FEF2F2 0%, #fff5f5 100%);
        padding: 0.6rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-bottom: 1px solid #fecaca;
    }
    .gw-timer-icon {
        font-size: 0.9rem;
    }
    .gw-timer-text {
        font-size: 0.78rem;
        color: #dc2626;
        font-weight: 600;
    }
    .gw-timer-countdown {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 0.95rem;
        font-weight: 800;
        color: #dc2626;
        letter-spacing: 0.04em;
    }

    /* Payment Methods */
    .gw-methods {
        padding: 1rem 1.5rem 0.5rem;
    }
    .gw-methods-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .gw-method-group {
        margin-bottom: 1rem;
    }
    .gw-method-group-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
        padding-left: 0.2rem;
    }

    .gw-method {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0.85rem;
        border: 2px solid #e8edf5;
        border-radius: 12px;
        cursor: pointer;
        transition: all 200ms ease;
        margin-bottom: 0.5rem;
        background: white;
    }
    .gw-method:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .gw-method.active {
        border-color: #003d82;
        background: #f0f6ff;
        box-shadow: 0 0 0 3px rgba(0,61,130,0.08);
    }
    .gw-method-logo {
        width: 40px; height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
        flex-shrink: 0;
        letter-spacing: -0.02em;
    }
    .gw-method-info {
        flex: 1;
        min-width: 0;
    }
    .gw-method-name {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e293b;
    }
    .gw-method-desc {
        font-size: 0.68rem;
        color: #94a3b8;
        margin-top: 1px;
    }
    .gw-method-radio {
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 200ms;
    }
    .gw-method.active .gw-method-radio {
        border-color: #003d82;
        background: #003d82;
    }
    .gw-method.active .gw-method-radio::after {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: white;
    }

    /* VA Detail Panel */
    .gw-va-panel {
        display: none;
        margin: 0 1.5rem 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        animation: fadeIn 300ms ease;
    }
    .gw-va-panel.show { display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .gw-va-label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
    }
    .gw-va-number {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 0.08em;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.75rem;
    }
    .gw-copy-btn {
        font-size: 0.68rem;
        font-weight: 700;
        color: #003d82;
        background: #e0ecff;
        border: none;
        border-radius: 6px;
        padding: 4px 10px;
        cursor: pointer;
        font-family: inherit;
        transition: all 150ms;
    }
    .gw-copy-btn:hover {
        background: #003d82;
        color: white;
    }

    .gw-steps {
        border-top: 1px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.5rem;
    }
    .gw-steps-title {
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.4rem;
    }
    .gw-step {
        font-size: 0.72rem;
        color: #64748b;
        padding: 0.2rem 0;
        display: flex;
        gap: 0.4rem;
    }
    .gw-step-num {
        font-weight: 700;
        color: #003d82;
        width: 16px;
        flex-shrink: 0;
    }

    /* E-Wallet QR */
    .gw-qr-panel {
        display: none;
        margin: 0 1.5rem 0.75rem;
        padding: 1.25rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        text-align: center;
        animation: fadeIn 300ms ease;
    }
    .gw-qr-panel.show { display: block; }
    .gw-qr-placeholder {
        width: 160px; height: 160px;
        background: white;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 3rem;
    }
    .gw-qr-placeholder-text {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 0.3rem;
    }
    .gw-qr-note {
        font-size: 0.72rem;
        color: #64748b;
    }

    /* Footer Actions */
    .gw-footer {
        padding: 1rem 1.5rem 1.25rem;
        border-top: 1px solid #f1f5f9;
    }
    .gw-btn-pay {
        width: 100%;
        padding: 0.85rem;
        background: linear-gradient(135deg, #003d82 0%, #002855 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 700;
        font-family: var(--font-heading, 'Outfit', sans-serif);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 200ms;
        letter-spacing: -0.01em;
    }
    .gw-btn-pay:hover {
        background: linear-gradient(135deg, #004da6 0%, #003d82 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,61,130,0.3);
    }
    .gw-btn-pay:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .gw-btn-pay.processing {
        pointer-events: none;
    }
    .gw-btn-pay.processing .gw-btn-text { display: none; }
    .gw-btn-pay.processing .gw-btn-loading { display: flex; }
    .gw-btn-loading {
        display: none;
        align-items: center;
        gap: 0.5rem;
    }
    .gw-spinner {
        width: 18px; height: 18px;
        border: 2.5px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .gw-cancel-link {
        display: block;
        text-align: center;
        margin-top: 0.75rem;
        font-size: 0.78rem;
        color: #94a3b8;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: color 150ms;
    }
    .gw-cancel-link:hover {
        color: #dc2626;
    }

    /* Security Badge */
    .gw-security-bar {
        padding: 0.6rem 1.5rem;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        border-top: 1px solid #f1f5f9;
    }
    .gw-security-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.65rem;
        color: #94a3b8;
        font-weight: 500;
    }

    /* Processing Overlay */
    .gw-processing-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(4px);
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 20px;
    }
    .gw-processing-overlay.show {
        display: flex;
    }
    .gw-processing-spinner {
        width: 48px; height: 48px;
        border: 4px solid #e2e8f0;
        border-top-color: #003d82;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 1rem;
    }
    .gw-processing-text {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
    .gw-processing-sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    /* Success View (Dedicated Screen) */
    .gw-success-view {
        display: none;
        padding: 2.25rem 1.75rem;
        text-align: center;
        background: #ffffff;
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .gw-success-icon-wrap {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.25rem;
        background: #ecfdf5;
        border: 4px solid #d1fae5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .gw-success-icon-wrap::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        border: 2px dashed #a7f3d0;
        animation: spinSlow 20s linear infinite;
    }
    @keyframes spinSlow { to { transform: rotate(360deg); } }
    .gw-success-icon-inner {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
    }
    .gw-success-icon-inner svg {
        width: 28px;
        height: 28px;
        color: white;
    }
    .gw-success-title {
        font-family: var(--font-heading, 'Outfit', sans-serif);
        font-size: 1.35rem;
        font-weight: 800;
        color: #065f46;
        margin-bottom: 0.35rem;
    }
    .gw-success-desc {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }
    .gw-receipt-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }
    .gw-receipt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.45rem 0;
        font-size: 0.8rem;
        border-bottom: 1px dashed #e2e8f0;
    }
    .gw-receipt-row:last-child {
        border-bottom: none;
        padding-top: 0.6rem;
        font-weight: 700;
    }
    .gw-receipt-label {
        color: #64748b;
    }
    .gw-receipt-value {
        color: #0f172a;
        font-weight: 600;
    }
    .gw-receipt-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        background: #dcfce7;
        color: #15803d;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .gw-success-actions {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .gw-btn-action-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.85rem 1.25rem;
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: white !important;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .gw-btn-action-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.45);
        color: white !important;
    }
    .gw-btn-action-secondary {
        display: block;
        width: 100%;
        padding: 0.7rem;
        background: transparent;
        color: #64748b;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.82rem;
        transition: color 0.15s;
    }
    .gw-btn-action-secondary:hover {
        color: #0f172a;
    }
    .gw-redirect-timer {
        margin-top: 1rem;
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .gw-redirect-progress {
        height: 3px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.4rem;
    }
    .gw-redirect-bar {
        height: 100%;
        width: 0%;
        background: #0d9488;
        border-radius: 3px;
        transition: width 5s linear;
    }

    /* Items Detail */
    .gw-items {
        margin: 0 1.5rem 0.5rem;
    }
    .gw-item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0;
        font-size: 0.78rem;
    }
    .gw-item-name {
        color: #475569;
        font-weight: 500;
    }
    .gw-item-price {
        color: #1e293b;
        font-weight: 700;
    }
    .gw-items-divider {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 0.4rem 0;
    }
    .gw-item-total {
        font-weight: 800 !important;
        color: #003d82 !important;
        font-size: 0.88rem !important;
    }

    /* Scrollbar */
    .gateway-container::-webkit-scrollbar { width: 4px; }
    .gateway-container::-webkit-scrollbar-track { background: transparent; }
    .gateway-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="gateway-overlay">
    <div class="gateway-container" style="position: relative;">

        {{-- Processing Overlay --}}
        <div class="gw-processing-overlay" id="processingOverlay">
            <div class="gw-processing-spinner"></div>
            <div class="gw-processing-text">Memproses Pembayaran...</div>
            <div class="gw-processing-sub">Mohon tunggu, jangan tutup halaman ini</div>
        </div>

        {{-- Form View (Akan disembunyikan saat pembayaran berhasil) --}}
        <div id="gwFormView">
            {{-- Header --}}
            <div class="gw-header">
            <div class="gw-logo">
                <div class="gw-logo-icon">R</div>
                <div>
                    <div class="gw-logo-text">RentizPay</div>
                    <div class="gw-logo-sub">Powered by Midtrans (Simulasi)</div>
                </div>
            </div>
            <div class="gw-secure">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Secured
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="gw-summary">
            <div class="gw-merchant">Pembayaran ke: Rentiz Marketplace</div>
            <div class="gw-order-id">Order #{{ substr($pembayaran->pesanan_id, 0, 8) }}...{{ substr($pembayaran->pesanan_id, -4) }}</div>
            <div class="gw-amount-row">
                <span class="gw-amount-label">Total Pembayaran</span>
                <span class="gw-amount">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Timer Bar --}}
        <div class="gw-timer-bar">
            <span class="gw-timer-icon">⏳</span>
            <span class="gw-timer-text">Bayar sebelum:</span>
            <span class="gw-timer-countdown" id="gwTimer" data-expired="{{ \Carbon\Carbon::parse($pembayaran->expired_at)->toISOString() }}">--:--</span>
        </div>

        {{-- Items Detail --}}
        <div class="gw-items" style="padding-top: 1rem;">
            @php $subtotalAll = 0; @endphp
            @foreach($pembayaran->pesanan->items as $item)
                @php
                    $subtotalAll += $item->subtotal;
                @endphp
                <div class="gw-item-row">
                    <span class="gw-item-name">📦 {{ $item->barang->nama ?? 'Barang' }} × {{ $item->durasi_hari }} hari</span>
                    <span class="gw-item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr class="gw-items-divider">
            <div class="gw-item-row">
                <span class="gw-item-name">Biaya Layanan</span>
                <span class="gw-item-price">Rp {{ number_format($pembayaran->jumlah - $subtotalAll, 0, ',', '.') }}</span>
            </div>
            <hr class="gw-items-divider">
            <div class="gw-item-row">
                <span class="gw-item-name gw-item-total">Total</span>
                <span class="gw-item-price gw-item-total">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Methods --}}
        <div class="gw-methods">
            <div class="gw-methods-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#003d82;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pilih Metode Pembayaran
            </div>

            {{-- Virtual Account --}}
            <div class="gw-method-group">
                <div class="gw-method-group-label">Virtual Account</div>

                <div class="gw-method" data-method="bca_va" data-type="va" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: #003d82; color: white; font-size: 0.65rem;">BCA</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">BCA Virtual Account</div>
                        <div class="gw-method-desc">Otomatis terverifikasi</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>

                <div class="gw-method" data-method="bni_va" data-type="va" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: #e65100; color: white; font-size: 0.65rem;">BNI</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">BNI Virtual Account</div>
                        <div class="gw-method-desc">Otomatis terverifikasi</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>

                <div class="gw-method" data-method="mandiri_va" data-type="va" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: #003f72; color: #ffc300; font-size: 0.55rem; font-weight:800;">MDR</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">Mandiri Virtual Account</div>
                        <div class="gw-method-desc">Otomatis terverifikasi</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>
            </div>

            {{-- E-Wallet --}}
            <div class="gw-method-group">
                <div class="gw-method-group-label">E-Wallet</div>

                <div class="gw-method" data-method="gopay" data-type="ewallet" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: #00aed6; color: white; font-size: 0.6rem;">GO</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">GoPay</div>
                        <div class="gw-method-desc">Scan QR atau langsung bayar</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>

                <div class="gw-method" data-method="ovo" data-type="ewallet" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: #4c3494; color: white; font-size: 0.6rem;">OVO</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">OVO</div>
                        <div class="gw-method-desc">Bayar dari saldo OVO</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>

                <div class="gw-method" data-method="qris" data-type="ewallet" onclick="selectMethod(this)">
                    <div class="gw-method-logo" style="background: linear-gradient(135deg, #e31937, #00529b); color: white; font-size: 0.55rem;">QR</div>
                    <div class="gw-method-info">
                        <div class="gw-method-name">QRIS</div>
                        <div class="gw-method-desc">Semua aplikasi e-wallet</div>
                    </div>
                    <div class="gw-method-radio"></div>
                </div>
            </div>
        </div>

        {{-- VA Detail Panel --}}
        <div class="gw-va-panel" id="vaPanel">
            <div class="gw-va-label">Nomor Virtual Account</div>
            <div class="gw-va-number">
                <span id="vaNumber">—</span>
                <button class="gw-copy-btn" onclick="copyVA()">Salin</button>
            </div>
            <div class="gw-steps">
                <div class="gw-steps-title">Cara Pembayaran:</div>
                <div class="gw-step"><span class="gw-step-num">1.</span> Buka aplikasi m-Banking atau ATM bank terkait</div>
                <div class="gw-step"><span class="gw-step-num">2.</span> Pilih menu Transfer / Virtual Account</div>
                <div class="gw-step"><span class="gw-step-num">3.</span> Masukkan nomor VA di atas</div>
                <div class="gw-step"><span class="gw-step-num">4.</span> Konfirmasi nominal dan selesaikan pembayaran</div>
            </div>
        </div>

        {{-- QR Panel --}}
        <div class="gw-qr-panel" id="qrPanel">
            <div class="gw-qr-placeholder">
                <span>📱</span>
                <span class="gw-qr-placeholder-text">QR Code Simulasi</span>
            </div>
            <div class="gw-qr-note">Scan QR code di atas menggunakan aplikasi e-wallet Anda<br><small style="color:#94a3b8;">(Ini adalah simulasi — klik "Bayar Sekarang" untuk memproses)</small></div>
        </div>

        {{-- Footer --}}
        <div class="gw-footer">
            <form id="paymentForm" action="{{ route('penyewa.pembayaran.proses', $pembayaran->id) }}" method="POST">
                @csrf
                <input type="hidden" name="metode_bayar" id="metodeBayarInput" value="">
                <button type="submit" class="gw-btn-pay" id="payButton" disabled>
                    <span class="gw-btn-text">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Pilih metode pembayaran terlebih dahulu
                    </span>
                    <span class="gw-btn-loading">
                        <span class="gw-spinner"></span>
                        Memproses pembayaran...
                    </span>
                </button>
            </form>
            <a class="gw-cancel-link" href="{{ route('penyewa.pembayaran') }}">← Kembali ke Pembayaran</a>
        </div>

        {{-- Security Badges --}}
        <div class="gw-security-bar">
            <div class="gw-security-item">🔒 SSL Encrypted</div>
            <div class="gw-security-item">🛡️ PCI DSS Compliant</div>
            <div class="gw-security-item">✓ Verified by Midtrans</div>
        </div>
        </div> {{-- End gwFormView --}}

        {{-- Success View (Dedicated Screen yang menggantikan Form View) --}}
        <div id="gwSuccessView" class="gw-success-view">
            <div class="gw-success-icon-wrap">
                <div class="gw-success-icon-inner">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <div class="gw-success-title">Pembayaran Berhasil! 🎉</div>
            <div class="gw-success-desc">Pesanan Anda telah dikonfirmasi dan dana telah diamankan oleh sistem RentizPay.</div>

            <div class="gw-receipt-box">
                <div class="gw-receipt-row">
                    <span class="gw-receipt-label">No. Pesanan</span>
                    <span class="gw-receipt-value">#{{ substr($pembayaran->pesanan_id, 0, 8) }}...{{ substr($pembayaran->pesanan_id, -4) }}</span>
                </div>
                <div class="gw-receipt-row">
                    <span class="gw-receipt-label">Metode Pembayaran</span>
                    <span class="gw-receipt-value" id="receiptMethodName">QRIS (Semua E-Wallet)</span>
                </div>
                <div class="gw-receipt-row">
                    <span class="gw-receipt-label">Waktu Transaksi</span>
                    <span class="gw-receipt-value" id="receiptTimestamp">{{ now()->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="gw-receipt-row">
                    <span class="gw-receipt-label">Status</span>
                    <span class="gw-receipt-badge">✓ LUNAS</span>
                </div>
                <div class="gw-receipt-row">
                    <span class="gw-receipt-label" style="font-size:0.88rem;color:#0f172a;font-weight:700;">Total Dibayar</span>
                    <span class="gw-receipt-value" style="font-size:1.15rem;color:#0d9488;font-weight:800;">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="gw-success-actions">
                <a href="{{ route('penyewa.penyewaan-aktif') }}" class="gw-btn-action-primary">
                    <span>Lihat Penyewaan Aktif</span>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="{{ route('penyewa.pembayaran') }}" class="gw-btn-action-secondary">
                    Kembali ke Halaman Pembayaran
                </a>
            </div>

            <div class="gw-redirect-timer">
                Otomatis dialihkan ke Penyewaan Aktif dalam <span id="autoCount" style="font-weight:700;color:#0d9488;">5</span> detik...
                <div class="gw-redirect-progress">
                    <div class="gw-redirect-bar" id="autoRedirectBar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ═══════════════════════════════════════
    // Payment Gateway Logic
    // ═══════════════════════════════════════

    var selectedMethod = null;
    var isExpired = false;

    // VA Number Generator (simulasi)
    var vaNumbers = {
        bca_va:     '{{ "8810" . rand(1000000000, 9999999999) }}',
        bni_va:     '{{ "8820" . rand(1000000000, 9999999999) }}',
        mandiri_va: '{{ "8910" . rand(1000000000, 9999999999) }}'
    };

    function selectMethod(el) {
        if (isExpired) return;

        // Deselect semua
        document.querySelectorAll('.gw-method').forEach(function(m) {
            m.classList.remove('active');
        });

        // Select yang diklik
        el.classList.add('active');
        selectedMethod = el.dataset.method;

        // Set hidden input
        document.getElementById('metodeBayarInput').value = selectedMethod;

        // Tampilkan panel sesuai tipe
        var type = el.dataset.type;
        var vaPanel = document.getElementById('vaPanel');
        var qrPanel = document.getElementById('qrPanel');

        vaPanel.classList.remove('show');
        qrPanel.classList.remove('show');

        if (type === 'va') {
            document.getElementById('vaNumber').textContent = formatVA(vaNumbers[selectedMethod]);
            vaPanel.classList.add('show');
        } else if (type === 'ewallet') {
            qrPanel.classList.add('show');
        }

        // Enable tombol bayar
        var payBtn = document.getElementById('payButton');
        payBtn.disabled = false;
        payBtn.querySelector('.gw-btn-text').innerHTML =
            '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> ' +
            'Bayar Sekarang — Rp {{ number_format($pembayaran->jumlah, 0, ",", ".") }}';
    }

    function formatVA(num) {
        // Format: XXXX XXXX XXXX XXXX
        var str = num.toString().replace(/\s/g, '');
        return str.replace(/(.{4})/g, '$1 ').trim();
    }

    function copyVA() {
        var vaText = document.getElementById('vaNumber').textContent.replace(/\s/g, '');
        navigator.clipboard.writeText(vaText).then(function() {
            var btn = document.querySelector('.gw-copy-btn');
            btn.textContent = '✓ Tersalin!';
            btn.style.background = '#059669';
            btn.style.color = 'white';
            setTimeout(function() {
                btn.textContent = 'Salin';
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    }

    // Timer countdown
    function updateGwTimer() {
        var timerEl = document.getElementById('gwTimer');
        var expiredIso = timerEl.dataset.expired;
        if (!expiredIso) return;

        var expiredTime = new Date(expiredIso).getTime();
        var now = new Date().getTime();
        var distance = expiredTime - now;

        if (distance <= 0) {
            timerEl.textContent = 'WAKTU HABIS';
            timerEl.style.color = '#64748b';
            isExpired = true;

            // Disable semua interaksi
            document.getElementById('payButton').disabled = true;
            document.getElementById('payButton').querySelector('.gw-btn-text').textContent = '⏰ Waktu Pembayaran Habis';
            document.querySelectorAll('.gw-method').forEach(function(m) {
                m.style.opacity = '0.5';
                m.style.pointerEvents = 'none';
            });

            // Auto redirect setelah 3 detik
            setTimeout(function() {
                window.location.href = '{{ route("penyewa.pembayaran") }}';
            }, 3000);
        } else {
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var minStr = minutes < 10 ? '0' + minutes : minutes;
            var secStr = seconds < 10 ? '0' + seconds : seconds;
            timerEl.textContent = minStr + ':' + secStr;
        }
    }

    // Form submission — simulasi processing
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (isExpired || !selectedMethod) return;

        var form = this;
        var payBtn = document.getElementById('payButton');
        var processingOverlay = document.getElementById('processingOverlay');
        var successOverlay = document.getElementById('successOverlay');

        // Fase 1: Tampilkan loading pada tombol
        payBtn.classList.add('processing');

        // Fase 2: Tampilkan processing overlay (setelah 500ms)
        setTimeout(function() {
            processingOverlay.classList.add('show');
        }, 500);

        // Fase 3: Kirim form sesungguhnya ke backend (simulasi processing 1.8 detik)
        setTimeout(function() {
            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                processingOverlay.classList.remove('show');
                if (data.success) {
                    var methodLabels = {
                        'bca_va': 'BCA Virtual Account',
                        'bni_va': 'BNI Virtual Account',
                        'mandiri_va': 'Mandiri Virtual Account',
                        'gopay': 'GoPay (E-Wallet)',
                        'ovo': 'OVO (E-Wallet)',
                        'qris': 'QRIS (Semua E-Wallet)'
                    };
                    var usedMethod = methodLabels[selectedMethod] || 'QRIS / E-Wallet';
                    document.getElementById('receiptMethodName').textContent = usedMethod;

                    // Ganti state tampilan secara bersih (clean state transition)
                    document.getElementById('gwFormView').style.display = 'none';
                    var successView = document.getElementById('gwSuccessView');
                    successView.style.display = 'block';
                    document.querySelector('.gateway-container').scrollTop = 0;

                    // Mulai hitung mundur auto-redirect
                    var count = 5;
                    var countEl = document.getElementById('autoCount');
                    var barEl = document.getElementById('autoRedirectBar');
                    if (barEl) {
                        setTimeout(function() { barEl.style.width = '100%'; }, 50);
                    }
                    var timerInterval = setInterval(function() {
                        count--;
                        if (countEl) countEl.textContent = count;
                        if (count <= 0) {
                            clearInterval(timerInterval);
                            window.location.href = '{{ route("penyewa.penyewaan-aktif") }}';
                        }
                    }, 1000);
                } else {
                    alert(data.message || 'Pembayaran gagal. Silakan coba lagi.');
                    window.location.href = '{{ route("penyewa.pembayaran") }}';
                }
            })
            .catch(function() {
                // Fallback: submit form biasa jika AJAX gagal
                form.submit();
            });
        }, 1800);
    });

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        updateGwTimer();
        setInterval(updateGwTimer, 1000);
    });
</script>
@endpush
