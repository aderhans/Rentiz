<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Rentiz</title>
    <meta name="description" content="@yield('meta_description', 'Rentiz — Platform marketplace penyewaan barang terpercaya.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════
           DESIGN TOKENS
        ═══════════════════════════════════════ */
        :root {
            /* Sidebar */
            --sidebar-bg:          #0F2044;
            --sidebar-bg-hover:    rgba(255,255,255,0.06);
            --sidebar-bg-active:   rgba(255,255,255,0.10);
            --sidebar-border:      rgba(255,255,255,0.07);
            --sidebar-text:        rgba(255,255,255,0.55);
            --sidebar-text-active: #FFFFFF;
            --sidebar-accent:      #4F8EF7;
            --sidebar-width:       240px;

            /* Content */
            --content-bg:  #F0F4FB;
            --card-bg:     #FFFFFF;
            --card-border: #E8EDF5;

            /* Role colors */
            --color-penyedia: #2563EB;
            --color-penyewa:  #0D9488;
            --color-admin:    #7C3AED;

            /* Text */
            --text:           #1A2540;
            --text-secondary: #5C6E8A;
            --text-muted:     #94A3B8;

            /* Status */
            --success:      #059669;
            --success-bg:   #D1FAE5;
            --warning:      #D97706;
            --warning-bg:   #FEF3C7;
            --danger:       #DC2626;
            --danger-bg:    #FEE2E2;
            --info:         #0EA5E9;
            --info-bg:      #E0F2FE;

            /* Misc */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-sm: 0 1px 4px rgba(15,32,68,0.06);
            --shadow-md: 0 4px 16px rgba(15,32,68,0.08);
            --shadow-lg: 0 10px 40px rgba(15,32,68,0.12);

            --font-body:    'Inter', -apple-system, sans-serif;
            --font-heading: 'Outfit', 'Inter', sans-serif;

            --header-h: 60px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; -webkit-font-smoothing: antialiased; scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--content-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 200;
            transition: transform 300ms cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }

        /* Subtle noise/grain overlay */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 0%, rgba(79,142,247,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 100%, rgba(79,142,247,0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Logo */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 1.25rem;
            height: var(--header-h);
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .sidebar-logo-icon {
            width: 34px;
            height: 34px;
            background: var(--sidebar-accent);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(79,142,247,0.4);
            flex-shrink: 0;
        }

        .sidebar-logo-icon svg { width: 19px; height: 19px; fill: white; }

        .sidebar-logo-text {
            font-family: var(--font-heading);
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.01em;
        }

        /* Nav groups */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
            position: relative;
            z-index: 1;
            scrollbar-width: none;
        }

        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-group { margin-bottom: 1.5rem; }

        .nav-group-label {
            font-size: 0.67rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sidebar-text);
            padding: 0 1.25rem;
            margin-bottom: 0.4rem;
            opacity: 0.6;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 1.25rem;
            margin: 0 0.5rem;
            border-radius: var(--radius-sm);
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 500;
            transition: all 180ms ease;
            position: relative;
            cursor: pointer;
        }

        .nav-item:hover {
            background: var(--sidebar-bg-hover);
            color: rgba(255,255,255,0.85);
        }

        .nav-item.active {
            background: var(--sidebar-bg-active);
            color: var(--sidebar-text-active);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--sidebar-accent);
            border-radius: 0 2px 2px 0;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 50px;
            background: rgba(79,142,247,0.2);
            color: var(--sidebar-accent);
        }

        /* Sidebar footer */
        .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 1rem;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius-sm);
            transition: background 180ms;
            cursor: pointer;
        }

        .sidebar-user:hover { background: var(--sidebar-bg-hover); }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-size: 0.82rem;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }

        .user-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-tag {
            font-size: 0.7rem;
            color: var(--sidebar-text);
            margin-top: 1px;
        }

        .sidebar-logout {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: var(--sidebar-text);
            transition: color 150ms;
            display: flex;
        }

        .sidebar-logout:hover { color: #FC8181; }
        .sidebar-logout svg { width: 17px; height: 17px; }

        /* ═══════════════════════════════════════
           MAIN CONTENT AREA
        ═══════════════════════════════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Header Bar */
        .top-header {
            background: white;
            border-bottom: 1px solid var(--card-border);
            height: var(--header-h);
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 0 rgba(15,32,68,0.04);
        }

        .header-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 4px;
        }

        .header-toggle svg { width: 22px; height: 22px; display: block; }

        .header-breadcrumb {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .breadcrumb-item {
            font-size: 0.83rem;
            color: var(--text-muted);
        }

        .breadcrumb-item.current {
            color: var(--text);
            font-weight: 600;
        }

        .breadcrumb-sep { color: var(--text-muted); }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--card-border);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 180ms;
            position: relative;
        }

        .header-btn:hover { background: var(--content-bg); color: var(--text); }
        .header-btn svg { width: 18px; height: 18px; }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: #EF4444;
            border-radius: 50%;
            border: 1.5px solid white;
        }

        .header-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-size: 0.8rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 180ms;
        }

        .header-avatar:hover { border-color: var(--card-border); }

        /* Main content scroll zone */
        .page-content {
            flex: 1;
            padding: 1.75rem;
            overflow-y: auto;
        }

        /* ═══════════════════════════════════════
           UTILITY CLASSES (shared across views)
        ═══════════════════════════════════════ */

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid var(--card-border);
        }

        .card-title {
            font-family: var(--font-heading);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
        }

        .card-body { padding: 1.4rem; }

        /* Stat cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.4rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 200ms, transform 200ms;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 22px; height: 22px; }

        .stat-info { flex: 1; min-width: 0; }

        .stat-label {
            font-size: 0.77rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat-change {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .stat-change.up   { color: var(--success); }
        .stat-change.down { color: var(--danger); }
        .stat-change.neu  { color: var(--text-muted); }

        .stat-change svg { width: 13px; height: 13px; }

        /* Grid layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.73rem;
            font-weight: 600;
        }
        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger  { background: var(--danger-bg);  color: var(--danger); }
        .badge-info    { background: var(--info-bg);    color: var(--info); }
        .badge-neutral { background: #F1F5F9; color: var(--text-secondary); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.55rem 1.1rem;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 180ms;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .btn svg { width: 16px; height: 16px; }

        .btn-primary-blue {
            background: var(--color-penyedia);
            color: white;
        }
        .btn-primary-blue:hover { background: #1D4ED8; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }

        .btn-primary-teal {
            background: var(--color-penyewa);
            color: white;
        }
        .btn-primary-teal:hover { background: #0F766E; box-shadow: 0 4px 12px rgba(13,148,136,0.3); }

        .btn-primary-purple {
            background: var(--color-admin);
            color: white;
        }
        .btn-primary-purple:hover { background: #6D28D9; box-shadow: 0 4px 12px rgba(124,58,237,0.3); }

        .btn-outline {
            background: transparent;
            border-color: var(--card-border);
            color: var(--text-secondary);
        }
        .btn-outline:hover { background: var(--content-bg); color: var(--text); }

        .btn-sm { padding: 0.4rem 0.85rem; font-size: 0.78rem; }

        /* Tables */
        .table-wrapper { overflow-x: auto; }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        table.data-table thead tr {
            border-bottom: 1px solid var(--card-border);
        }

        table.data-table th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.73rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        table.data-table td {
            padding: 0.85rem 1rem;
            color: var(--text);
            border-bottom: 1px solid #F1F5F9;
        }

        table.data-table tbody tr:last-child td { border-bottom: none; }
        table.data-table tbody tr:hover td { background: #FAFBFF; }

        /* Page header */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .page-subtitle {
            font-size: 0.87rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        /* Overlay (mobile) */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,32,68,0.5);
            z-index: 199;
            backdrop-filter: blur(2px);
        }

        /* ═══════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════ */
        @media (max-width: 1024px) {
            .grid-2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open { display: block; }
            .main-wrapper { margin-left: 0; }
            .header-toggle { display: flex; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .page-content { padding: 1rem; }
        }

        @media (max-width: 480px) {
            .stat-grid { grid-template-columns: 1fr; }
        }

        /* ═══════════════════════════════════════
           MODE SWITCHER (sidebar)
        ═══════════════════════════════════════ */
        .mode-switcher {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--sidebar-border);
            position: relative;
            z-index: 1;
        }

        .mode-switcher-track {
            display: flex;
            background: rgba(255,255,255,0.06);
            border-radius: 8px;
            padding: 3px;
            position: relative;
        }

        .mode-switcher-track::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: calc(50% - 3px);
            height: calc(100% - 6px);
            background: var(--sidebar-accent);
            border-radius: 6px;
            transition: transform 280ms cubic-bezier(0.4,0,0.2,1);
            box-shadow: 0 2px 8px rgba(79,142,247,0.4);
        }

        .mode-switcher-track.penyedia::before {
            transform: translateX(100%);
        }

        .mode-switch-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 0.45rem 0.5rem;
            border: none;
            background: transparent;
            color: var(--sidebar-text);
            font-family: var(--font-body);
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            position: relative;
            z-index: 1;
            transition: color 200ms;
            white-space: nowrap;
        }

        .mode-switch-btn.active {
            color: white;
        }

        .mode-switch-btn:hover:not(.active) {
            color: rgba(255,255,255,0.7);
        }

        .mode-switch-btn svg {
            width: 14px;
            height: 14px;
        }

        @stack('app-styles')
    </style>
    @stack('styles')
</head>
<body>
    {{-- ── Sidebar ── --}}
    <aside class="sidebar" id="app-sidebar">
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                </svg>
            </div>
            <span class="sidebar-logo-text">Rentiz</span>
        </a>

        {{-- Mode Switcher (non-admin only) --}}
        @php
            $isAdmin = Auth::user()->isAdmin();
            $activeMode = $isAdmin ? 'admin' : session('active_mode', 'penyewa');
            $routeName = request()->route() ? request()->route()->getName() : '';
        @endphp

        @if(!$isAdmin)
        <div class="mode-switcher">
            <form method="POST" action="{{ route('switch-mode') }}" id="switch-mode-form">
                @csrf
                <div class="mode-switcher-track {{ $activeMode }}" id="mode-track">
                    <button type="{{ $activeMode === 'penyewa' ? 'button' : 'submit' }}" class="mode-switch-btn {{ $activeMode === 'penyewa' ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Penyewa
                    </button>
                    <button type="{{ $activeMode === 'penyedia' ? 'button' : 'submit' }}" class="mode-switch-btn {{ $activeMode === 'penyedia' ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Penyedia
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            {{-- ── PENYEWA MENU ── --}}
            @if($activeMode === 'penyewa')
            <div class="nav-group">
                <div class="nav-group-label">Beranda</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ $routeName === 'dashboard' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Sewa Barang</div>
                <a href="{{ route('penyewa.cari-barang') }}" class="nav-item {{ $routeName === 'penyewa.cari-barang' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Barang
                </a>
                <a href="{{ route('penyewa.penyewaan-aktif') }}" class="nav-item {{ $routeName === 'penyewa.penyewaan-aktif' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Penyewaan Aktif
                </a>
                <a href="{{ route('penyewa.riwayat-sewa') }}" class="nav-item {{ $routeName === 'penyewa.riwayat-sewa' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Sewa
                </a>
                <a href="{{ route('penyewa.wishlist') }}" class="nav-item {{ $routeName === 'penyewa.wishlist' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Wishlist
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Akun</div>
                <a href="{{ route('penyewa.profil') }}" class="nav-item {{ $routeName === 'penyewa.profil' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Saya
                </a>
                <a href="{{ route('penyewa.pembayaran') }}" class="nav-item {{ $routeName === 'penyewa.pembayaran' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Pembayaran
                </a>
            </div>
            @endif

            {{-- ── PENYEDIA MENU ── --}}
            @if($activeMode === 'penyedia')
            <div class="nav-group">
                <div class="nav-group-label">Beranda</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ $routeName === 'dashboard' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Toko Saya</div>
                <a href="{{ route('penyedia.daftar-barang') }}" class="nav-item {{ $routeName === 'penyedia.daftar-barang' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Daftar Barang
                </a>
                <a href="{{ route('penyedia.tambah-barang') }}" class="nav-item {{ $routeName === 'penyedia.tambah-barang' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Barang
                </a>
                <a href="{{ route('penyedia.request-sewa') }}" class="nav-item {{ $routeName === 'penyedia.request-sewa' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Request Sewa
                </a>
                <a href="{{ route('penyedia.riwayat-transaksi') }}" class="nav-item {{ $routeName === 'penyedia.riwayat-transaksi' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Transaksi
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Bisnis</div>
                <a href="{{ route('penyedia.analitik') }}" class="nav-item {{ $routeName === 'penyedia.analitik' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Analitik
                </a>
                <a href="{{ route('penyedia.penarikan-dana') }}" class="nav-item {{ $routeName === 'penyedia.penarikan-dana' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Penarikan Dana
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Akun</div>
                <a href="{{ route('penyedia.profil-toko') }}" class="nav-item {{ $routeName === 'penyedia.profil-toko' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil & Toko
                </a>
            </div>
            @endif

            {{-- ── ADMIN MENU ── --}}
            @if($isAdmin)
            @php $pendingCount = \App\Models\Barang::where('status', 'pending')->count(); @endphp
            <div class="nav-group">
                <div class="nav-group-label">Overview</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ $routeName === 'dashboard' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Manajemen</div>
                <a href="{{ route('admin.manajemen-user') }}" class="nav-item {{ $routeName === 'admin.manajemen-user' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Manajemen User
                </a>
                <a href="{{ route('admin.semua-listing') }}" class="nav-item {{ $routeName === 'admin.semua-listing' || $routeName === 'admin.detail-barang' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Semua Listing
                    @if($pendingCount > 0)
                    <span style="margin-left:auto;min-width:19px;height:19px;background:#EF4444;color:white;border-radius:50px;font-size:.68rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;line-height:1;">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.semua-transaksi') }}" class="nav-item {{ $routeName === 'admin.semua-transaksi' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Semua Transaksi
                </a>
                <a href="{{ route('admin.laporan-dispute') }}" class="nav-item {{ $routeName === 'admin.laporan-dispute' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    Laporan & Dispute
                </a>
            </div>

            <div class="nav-group">
                <div class="nav-group-label">Sistem</div>
                <a href="{{ route('admin.pengaturan') }}" class="nav-item {{ $routeName === 'admin.pengaturan' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
                <a href="{{ route('admin.platform-analytics') }}" class="nav-item {{ $routeName === 'admin.platform-analytics' ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Platform Analytics
                </a>
            </div>
            @endif
        </nav>

        {{-- Sidebar Footer: User --}}
        <div class="sidebar-footer">
            @php
                $initials = collect(explode(' ', Auth::user()->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                $avatarColors = ['penyewa' => '#0D9488', 'penyedia' => '#2563EB', 'admin' => '#7C3AED'];
                $avatarColor = $avatarColors[$activeMode] ?? '#4F8EF7';
            @endphp
            <div class="sidebar-user">
                <div class="user-avatar" style="background: {{ $avatarColor }}; overflow: hidden;">
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Avatar">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role-tag">
                        @if($isAdmin) Administrator
                        @elseif($activeMode === 'penyedia') Mode: Penyedia
                        @else Mode: Penyewa
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Logout">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    {{-- ── Main Wrapper ── --}}
    <div class="main-wrapper">

        {{-- Top Header --}}
        <header class="top-header">
            <button class="header-toggle" id="sidebar-toggle" aria-label="Toggle menu">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="header-breadcrumb">
                <span class="breadcrumb-item">Rentiz</span>
                <span class="breadcrumb-sep">›</span>
                <span class="breadcrumb-item current">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            <div class="header-actions">
                <button class="header-btn" title="Notifikasi">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="notif-dot"></span>
                </button>

                <div class="header-avatar" style="background: {{ $avatarColor }}; overflow: hidden;">
                    @if(Auth::user()->foto_profil)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Avatar">
                    @else
                        {{ $initials }}
                    @endif
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content" id="page-main">
            @if (session('success'))
            <div id="dashboard-success-alert" style="background:var(--success-bg); border:1px solid #A7F3D0; border-radius:var(--radius-md); padding:0.85rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.75rem; font-size:0.88rem; color:var(--success); box-shadow:0 2px 8px rgba(5,150,105,0.1); transition:opacity 0.5s ease-out;">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar toggle (mobile)
        var toggleBtn  = document.getElementById('sidebar-toggle');
        var sidebar    = document.getElementById('app-sidebar');
        var overlay    = document.getElementById('sidebar-overlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', function () {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });

        if (overlay) overlay.addEventListener('click', closeSidebar);

        // Auto-hide dashboard success alert
        var dashAlert = document.getElementById('dashboard-success-alert');
        if (dashAlert) {
            setTimeout(function() {
                dashAlert.style.opacity = '0';
                setTimeout(function() {
                    dashAlert.style.display = 'none';
                }, 500);
            }, 4000);
        }
    </script>
    @stack('scripts')
</body>
</html>
