@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $user = Auth::user();
    // Admin: gunakan role DB. Non-admin: gunakan session mode (bisa di-switch)
    $role = $user->isAdmin() ? 'admin' : session('active_mode', 'penyewa');

    // Greeting berdasarkan jam
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));

    // Warna per role/mode
    $roleBtnClass = $role === 'penyedia' ? 'btn-primary-blue' : ($role === 'admin' ? 'btn-primary-purple' : 'btn-primary-teal');
    $bannerGradient = $role === 'penyedia'
        ? 'linear-gradient(135deg, #2563EB 0%, #1a3a8f 100%)'
        : ($role === 'admin'
            ? 'linear-gradient(135deg, #7C3AED 0%, #4C1D95 100%)'
            : 'linear-gradient(135deg, #0D9488 0%, #065f52 100%)');
@endphp

{{-- ══════════════════════════════════
     WELCOME BANNER
══════════════════════════════════ --}}
<div class="welcome-banner" style="background: {{ $bannerGradient }}; box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
    <div class="welcome-banner-inner">
        <div>
            <p class="welcome-greeting">{{ $greeting }}, 👋</p>
            <h1 class="welcome-name">{{ $user->name }}</h1>
            <div class="welcome-meta">
                <span class="role-pill" style="background: rgba(255,255,255,0.15); color: white;">
                    @if($role === 'admin') 🛡️ Administrator
                    @elseif($role === 'penyedia') 📦 Penyedia Barang
                    @else 🔍 Penyewa Barang
                    @endif
                </span>
                <span class="welcome-date" style="color: rgba(255,255,255,0.7); font-size: 0.82rem;">
                    {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                </span>
            </div>
        </div>
        <div class="welcome-illustration">
            @if($role === 'admin')
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="rgba(255,255,255,0.08)"/>
                    <path d="M40 20C34.477 20 30 24.477 30 30C30 35.523 34.477 40 40 40C45.523 40 50 35.523 50 30C50 24.477 45.523 20 40 20Z" fill="rgba(255,255,255,0.5)"/>
                    <path d="M25 58C25 50.268 31.716 44 40 44C48.284 44 55 50.268 55 58" stroke="rgba(255,255,255,0.5)" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="60" cy="22" r="8" fill="rgba(255,255,255,0.15)"/>
                    <path d="M57 22L59.5 24.5L63 20" stroke="rgba(255,255,255,0.7)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @elseif($role === 'penyedia')
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="rgba(255,255,255,0.08)"/>
                    <path d="M20 30L40 20L60 30L40 40L20 30Z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M20 30V50L40 60V40L20 30Z" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.4)" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M60 30V50L40 60V40L60 30Z" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linejoin="round"/>
                </svg>
            @else
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="rgba(255,255,255,0.08)"/>
                    <circle cx="40" cy="38" r="14" stroke="rgba(255,255,255,0.5)" stroke-width="2.5"/>
                    <path d="M50 50L62 62" stroke="rgba(255,255,255,0.5)" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="40" cy="38" r="5" fill="rgba(255,255,255,0.3)"/>
                </svg>
            @endif
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     DASHBOARD PENYEWA
══════════════════════════════════ --}}
@if($role === 'penyewa')

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EEF2FF;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#4F46E5" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Sewa Aktif</div>
            <div class="stat-value">3</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                1 baru bulan ini
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #D1FAE5;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">12</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +3 bulan ini
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FFF0F0;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#E53E3E" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Wishlist</div>
            <div class="stat-value">7</div>
            <div class="stat-change neu">Barang tersimpan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FFFBEB;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rating Saya</div>
            <div class="stat-value">4.8</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Sangat baik
            </div>
        </div>
    </div>
</div>

{{-- Search Bar --}}
<div class="card" style="margin-bottom: 1.25rem;">
    <div class="card-body" style="padding: 1.25rem 1.5rem;">
        <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.6rem; font-weight: 500;">🔍 Cari Barang untuk Disewa</p>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="Cari kamera, drone, tenda, sepeda..." style="width: 100%; padding: 0.7rem 0.85rem 0.7rem 2.5rem; border: 1.5px solid var(--card-border); border-radius: var(--radius-sm); font-size: 0.88rem; font-family: var(--font-body); outline: none; color: var(--text); transition: border-color 200ms;" onfocus="this.style.borderColor='var(--color-penyewa)'" onblur="this.style.borderColor='var(--card-border)'">
            </div>
            <select style="padding: 0.7rem 1rem; border: 1.5px solid var(--card-border); border-radius: var(--radius-sm); font-size: 0.86rem; font-family: var(--font-body); color: var(--text-secondary); background: white; outline: none; cursor: pointer;">
                <option>Semua Kategori</option>
                <option>Elektronik & Gadget</option>
                <option>Kendaraan</option>
                <option>Olahraga & Outdoor</option>
                <option>Alat Rumah</option>
                <option>Fotografi</option>
            </select>
            <button class="btn btn-primary-teal">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 1.25rem;">
    {{-- Sewa Aktif --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 Penyewaan Aktif</span>
            <a href="#" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 0.84rem;">Sony A7III (Kamera)</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">Dari: Budi Studio</div>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem;">24 Mei – 27 Mei</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">3 hari</div>
                        </td>
                        <td><span class="badge badge-success">Aktif</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 0.84rem;">DJI Mini 3 Pro</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">Dari: Drone Indo</div>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem;">25 Mei – 26 Mei</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">1 hari</div>
                        </td>
                        <td><span class="badge badge-warning">Proses</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="font-weight: 600; font-size: 0.84rem;">Tenda Dome 4P</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">Dari: Alam Gear</div>
                        </td>
                        <td>
                            <div style="font-size: 0.82rem;">28 Mei – 31 Mei</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted);">3 hari</div>
                        </td>
                        <td><span class="badge badge-info">Terjadwal</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Barang Populer --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🔥 Barang Populer</span>
            <a href="#" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <div style="display: flex; flex-direction: column; gap: 0.7rem;">
                @php
                $popular = [
                    ['name' => 'GoPro Hero 12', 'cat' => 'Fotografi', 'price' => '150.000', 'rating' => '4.9', 'color' => '#EEF2FF', 'icon' => '📷'],
                    ['name' => 'PS5 + 2 Controller', 'cat' => 'Elektronik', 'price' => '120.000', 'rating' => '4.8', 'color' => '#EFF6FF', 'icon' => '🎮'],
                    ['name' => 'Sepeda Gunung MTB', 'cat' => 'Olahraga', 'price' => '80.000', 'rating' => '4.7', 'color' => '#F0FDF4', 'icon' => '🚵'],
                    ['name' => 'Proyektor 4K Full HD', 'cat' => 'Elektronik', 'price' => '200.000', 'rating' => '4.9', 'color' => '#FFF7ED', 'icon' => '📽️'],
                ];
                @endphp
                @foreach($popular as $item)
                <div style="display: flex; align-items: center; gap: 0.85rem; padding: 0.65rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--card-border); transition: background 150ms; cursor: pointer;" onmouseover="this.style.background='#F8FAFF'" onmouseout="this.style.background='transparent'">
                    <div style="width: 42px; height: 42px; background: {{ $item['color'] }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">{{ $item['icon'] }}</div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.84rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['name'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $item['cat'] }}</div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-weight: 700; font-size: 0.84rem; color: var(--color-penyewa);">Rp {{ $item['price'] }}<span style="font-weight: 400; color: var(--text-muted); font-size: 0.73rem;">/hari</span></div>
                        <div style="font-size: 0.73rem; color: var(--warning);">⭐ {{ $item['rating'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endif
{{-- END PENYEWA --}}


{{-- ══════════════════════════════════
     DASHBOARD PENYEDIA
══════════════════════════════════ --}}
@if($role === 'penyedia')

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Listing</div>
            <div class="stat-value">14</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                2 listing aktif baru
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #D1FAE5;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value" style="font-size: 1.25rem;">Rp 4,2Jt</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +18% vs bulan lalu
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FEF3C7;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Request Masuk</div>
            <div class="stat-value">5</div>
            <div class="stat-change down">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Perlu ditindaklanjuti
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FFF7ED;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rating Toko</div>
            <div class="stat-value">4.9</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Top 10% Penyedia
            </div>
        </div>
    </div>
</div>

{{-- Pendapatan Chart + Request --}}
<div class="grid-2" style="margin-bottom: 1.25rem;">
    {{-- Chart Pendapatan (CSS-only) --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📈 Pendapatan 6 Bulan</span>
            <span class="badge badge-success">+18% bulan ini</span>
        </div>
        <div class="card-body">
            <div style="display: flex; align-items: flex-end; gap: 8px; height: 120px; padding: 0 0.5rem;">
                @php
                $bars = [
                    ['h' => 45, 'label' => 'Des', 'val' => '1.8Jt'],
                    ['h' => 55, 'label' => 'Jan', 'val' => '2.2Jt'],
                    ['h' => 40, 'label' => 'Feb', 'val' => '1.6Jt'],
                    ['h' => 70, 'label' => 'Mar', 'val' => '2.8Jt'],
                    ['h' => 85, 'label' => 'Apr', 'val' => '3.5Jt'],
                    ['h' => 100, 'label' => 'Mei', 'val' => '4.2Jt'],
                ];
                @endphp
                @foreach($bars as $i => $bar)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%;">
                    <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                        <div class="chart-bar" style="width: 100%; height: {{ $bar['h'] }}%; background: {{ $i === count($bars)-1 ? 'var(--color-penyedia)' : 'var(--card-border)' }}; border-radius: 5px 5px 0 0; transition: height 0.6s ease {{ $i * 0.08 }}s; position: relative;" title="{{ $bar['val'] }}">
                            @if($i === count($bars)-1)
                            <div style="position: absolute; top: -22px; left: 50%; transform: translateX(-50%); font-size: 0.65rem; font-weight: 700; color: var(--color-penyedia); white-space: nowrap;">{{ $bar['val'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div style="font-size: 0.68rem; color: var(--text-muted);">{{ $bar['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Request Masuk --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🔔 Request Sewa Masuk</span>
            <span class="badge badge-warning">5 pending</span>
        </div>
        <div style="overflow: hidden;">
            @php
            $requests = [
                ['name' => 'Ahmad Fauzi', 'item' => 'Kamera Sony A7III', 'period' => '28–31 Mei', 'status' => 'warning', 'label' => 'Menunggu'],
                ['name' => 'Siti Rahma', 'item' => 'Drone DJI Mini 3', 'period' => '1–3 Jun', 'status' => 'warning', 'label' => 'Menunggu'],
                ['name' => 'Budi Santoso', 'item' => 'Tenda Dome 4P', 'period' => '5–7 Jun', 'status' => 'info', 'label' => 'Baru'],
                ['name' => 'Rina Kusuma', 'item' => 'Proyektor 4K', 'period' => '10 Jun', 'status' => 'success', 'label' => 'Disetujui'],
            ];
            @endphp
            <div style="padding: 0.25rem 0;">
                @foreach($requests as $req)
                <div style="display: flex; align-items: center; gap: 0.85rem; padding: 0.8rem 1.4rem; border-bottom: 1px solid #F1F5F9;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.82rem; flex-shrink: 0;">{{ strtoupper(substr($req['name'], 0, 1)) }}</div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.83rem; color: var(--text);">{{ $req['name'] }}</div>
                        <div style="font-size: 0.76rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $req['item'] }} · {{ $req['period'] }}</div>
                    </div>
                    <div style="display: flex; gap: 0.4rem; align-items: center;">
                        <span class="badge badge-{{ $req['status'] }}" style="font-size: 0.68rem;">{{ $req['label'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="padding: 0.85rem 1.4rem;">
                <a href="#" class="btn btn-primary-blue btn-sm" style="width: 100%; justify-content: center;">Kelola Semua Request</a>
            </div>
        </div>
    </div>
</div>

{{-- Top Listing --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">🏆 Performa Listing Teratas</span>
        <a href="#" class="btn btn-outline btn-sm">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Barang
        </a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga/Hari</th>
                    <th>Total Sewa</th>
                    <th>Rating</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                $listings = [
                    ['name' => 'Kamera Sony A7III + Lensa', 'cat' => 'Fotografi', 'price' => '350.000', 'sewa' => 28, 'rating' => '4.9', 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'DJI Mini 3 Pro Combo', 'cat' => 'Drone', 'price' => '280.000', 'sewa' => 22, 'rating' => '4.8', 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'Proyektor Epson Full HD', 'cat' => 'Elektronik', 'price' => '200.000', 'sewa' => 18, 'rating' => '4.7', 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'Tenda Dome Camping 4P', 'cat' => 'Outdoor', 'price' => '120.000', 'sewa' => 15, 'rating' => '4.6', 'status' => 'warning', 'label' => 'Disewa'],
                    ['name' => 'Sound System Portable', 'cat' => 'Audio', 'price' => '180.000', 'sewa' => 11, 'rating' => '4.5', 'status' => 'neutral', 'label' => 'Tersedia'],
                ];
                @endphp
                @foreach($listings as $i => $item)
                <tr>
                    <td style="color: var(--text-muted); font-weight: 600; font-size: 0.82rem;">{{ $i + 1 }}</td>
                    <td style="font-weight: 600; font-size: 0.85rem;">{{ $item['name'] }}</td>
                    <td><span class="badge badge-neutral">{{ $item['cat'] }}</span></td>
                    <td style="font-weight: 600; color: var(--color-penyedia);">Rp {{ $item['price'] }}</td>
                    <td style="font-weight: 600;">{{ $item['sewa'] }}x</td>
                    <td style="color: var(--warning);">⭐ {{ $item['rating'] }}</td>
                    <td><span class="badge badge-{{ $item['status'] }}">{{ $item['label'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif
{{-- END PENYEDIA --}}


{{-- ══════════════════════════════════
     DASHBOARD ADMIN
══════════════════════════════════ --}}
@if($role === 'admin')

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EDE9FE;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#7C3AED" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total User</div>
            <div class="stat-value">1,284</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +42 minggu ini
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Penyedia Aktif</div>
            <div class="stat-value">347</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +12 bulan ini
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #D1FAE5;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-value">128</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +9% vs kemarin
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #D1FAE5;">
            <svg fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pendapatan Platform</div>
            <div class="stat-value" style="font-size: 1.25rem;">Rp 48Jt</div>
            <div class="stat-change up">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                +24% bulan ini
            </div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 1.25rem;">
    {{-- Pertumbuhan User Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📊 Pertumbuhan User (6 Bulan)</span>
        </div>
        <div class="card-body">
            @php
            $adminBars = [
                ['h' => 50, 'label' => 'Des', 'val' => '980'],
                ['h' => 58, 'label' => 'Jan', 'val' => '1.050'],
                ['h' => 65, 'label' => 'Feb', 'val' => '1.100'],
                ['h' => 75, 'label' => 'Mar', 'val' => '1.180'],
                ['h' => 88, 'label' => 'Apr', 'val' => '1.240'],
                ['h' => 100, 'label' => 'Mei', 'val' => '1.284'],
            ];
            @endphp
            <div style="display: flex; align-items: flex-end; gap: 8px; height: 120px; padding: 0 0.5rem;">
                @foreach($adminBars as $i => $bar)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%;">
                    <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                        <div style="width: 100%; height: {{ $bar['h'] }}%; background: {{ $i === count($adminBars)-1 ? 'var(--color-admin)' : '#C4B5FD' }}; border-radius: 5px 5px 0 0; transition: height 0.6s ease {{ $i * 0.08 }}s; position: relative;" title="{{ $bar['val'] }}">
                            @if($i === count($adminBars)-1)
                            <div style="position: absolute; top: -22px; left: 50%; transform: translateX(-50%); font-size: 0.65rem; font-weight: 700; color: var(--color-admin); white-space: nowrap;">{{ $bar['val'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div style="font-size: 0.68rem; color: var(--text-muted);">{{ $bar['label'] }}</div>
                </div>
                @endforeach
            </div>

            {{-- Role distribution --}}
            <div style="display: flex; gap: 1rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--card-border);">
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-penyewa);">937</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">Penyewa</div>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-penyedia);">347</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">Penyedia</div>
                </div>
                <div style="flex: 1; text-align: center;">
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--color-admin);">3</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted);">Admin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚡ Transaksi Terkini</span>
            <a href="#" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div style="padding: 0.25rem 0;">
            @php
            $txns = [
                ['id' => '#TRX-2451', 'user' => 'Ahmad F.', 'item' => 'Kamera Sony A7III', 'amount' => '1.050.000', 'status' => 'success', 'label' => 'Selesai'],
                ['id' => '#TRX-2450', 'user' => 'Dewi R.', 'item' => 'DJI Mini 3 Pro', 'amount' => '560.000', 'status' => 'warning', 'label' => 'Aktif'],
                ['id' => '#TRX-2449', 'user' => 'Budi S.', 'item' => 'PS5 + Controller', 'amount' => '360.000', 'status' => 'info', 'label' => 'Terjadwal'],
                ['id' => '#TRX-2448', 'user' => 'Rina K.', 'item' => 'Proyektor 4K', 'amount' => '200.000', 'status' => 'success', 'label' => 'Selesai'],
                ['id' => '#TRX-2447', 'user' => 'Siti M.', 'item' => 'Sound System', 'amount' => '540.000', 'status' => 'danger', 'label' => 'Dispute'],
            ];
            @endphp
            @foreach($txns as $txn)
            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 1.4rem; border-bottom: 1px solid #F1F5F9;">
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-weight: 700; font-size: 0.78rem; color: var(--text-muted);">{{ $txn['id'] }}</span>
                        <span class="badge badge-{{ $txn['status'] }}" style="font-size: 0.66rem;">{{ $txn['label'] }}</span>
                    </div>
                    <div style="font-size: 0.83rem; color: var(--text); margin-top: 1px;">{{ $txn['user'] }} · {{ $txn['item'] }}</div>
                </div>
                <div style="font-weight: 700; font-size: 0.84rem; color: var(--color-admin); flex-shrink: 0;">Rp {{ $txn['amount'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- User Management Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">👥 User Terdaftar Terbaru</span>
        <a href="#" class="btn btn-primary-purple btn-sm">Kelola Semua User</a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Bergabung</th>
                    <th>Transaksi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                $users = [
                    ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com', 'role' => 'penyewa', 'join' => '25 Mei 2026', 'trx' => 12, 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'penyedia', 'join' => '24 Mei 2026', 'trx' => 45, 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'Siti Rahma', 'email' => 'siti@example.com', 'role' => 'penyewa', 'join' => '23 Mei 2026', 'trx' => 3, 'status' => 'success', 'label' => 'Aktif'],
                    ['name' => 'Dewi Rahayu', 'email' => 'dewi@example.com', 'role' => 'penyedia', 'join' => '22 Mei 2026', 'trx' => 22, 'status' => 'warning', 'label' => 'Review'],
                    ['name' => 'Eko Prasetyo', 'email' => 'eko@example.com', 'role' => 'penyewa', 'join' => '21 Mei 2026', 'trx' => 0, 'status' => 'danger', 'label' => 'Suspend'],
                ];
                @endphp
                @foreach($users as $u)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.76rem; flex-shrink: 0;">{{ strtoupper(substr($u['name'], 0, 1)) }}</div>
                            <span style="font-weight: 600; font-size: 0.85rem;">{{ $u['name'] }}</span>
                        </div>
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.83rem;">{{ $u['email'] }}</td>
                    <td>
                        <span class="badge {{ $u['role'] === 'penyedia' ? 'badge-info' : 'badge-neutral' }}">
                            {{ ucfirst($u['role']) }}
                        </span>
                    </td>
                    <td style="font-size: 0.82rem; color: var(--text-muted);">{{ $u['join'] }}</td>
                    <td style="font-weight: 600;">{{ $u['trx'] }}</td>
                    <td><span class="badge badge-{{ $u['status'] }}">{{ $u['label'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif
{{-- END ADMIN --}}

@endsection

@push('styles')
<style>
    /* Welcome Banner */
    .welcome-banner {
        border-radius: var(--radius-xl);
        margin-bottom: 1.5rem;
        overflow: hidden;
        position: relative;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -5%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-banner-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 1.75rem;
        position: relative;
        z-index: 1;
    }

    .welcome-greeting {
        font-size: 0.88rem;
        color: rgba(255,255,255,0.75);
        margin-bottom: 0.25rem;
    }

    .welcome-name {
        font-family: var(--font-heading);
        font-size: 1.6rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.6rem;
    }

    .welcome-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .welcome-illustration {
        opacity: 0.8;
        flex-shrink: 0;
    }

    @media (max-width: 600px) {
        .welcome-illustration { display: none; }
        .welcome-name { font-size: 1.35rem; }
    }
</style>
@endpush
