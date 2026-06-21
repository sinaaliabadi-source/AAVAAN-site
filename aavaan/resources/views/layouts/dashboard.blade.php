<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'داشبورد') | آوان</title>
    <style>
        @font-face {
            font-family: 'YekanBakh';
            src: url('{{ asset("fonts/YekanBakh-VF.ttf") }}') format('truetype');
            font-weight: 100 900;
            font-display: swap;
        }
        @font-face {
            font-family: 'IRANSansX';
            src: url('{{ asset("fonts/IRANSansXV.woff2") }}') format('woff2');
            font-weight: 100 900;
            font-display: swap;
        }
        :root {
            --color-bg: #F6F1E7;
            --color-primary: #1F2A44;
            --color-accent: #C9A24B;
            --color-text: #232323;
            --color-muted: #7a7a8a;
            --color-success: #5C6F4F;
            --radius: 10px;
            --sidebar-w: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'IRANSansX', 'YekanBakh', Tahoma, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            direction: rtl;
            line-height: 1.75;
            font-size: 15px;
        }
        h1, h2, h3, h4, h5 {
            font-family: 'YekanBakh', Tahoma, sans-serif;
            line-height: 1.4;
        }
        a { color: inherit; }

        /* Layout */
        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--color-primary);
            color: #fff;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-logo {
            padding: 1.3rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo a {
            font-family: 'YekanBakh', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--color-accent);
            text-decoration: none;
            letter-spacing: -.5px;
        }
        .sidebar-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .sidebar-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--color-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 700; color: var(--color-primary);
            flex-shrink: 0; overflow: hidden;
        }
        .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-user-name { font-size: .88rem; font-weight: 600; color: #fff; }
        .sidebar-user-role { font-size: .75rem; color: #aaa; }
        .sidebar-nav { padding: .75rem 0; flex: 1; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: .7rem;
            color: #bbb; padding: .6rem 1.5rem;
            text-decoration: none; font-size: .88rem;
            transition: all .15s;
            border-right: 3px solid transparent;
        }
        .sidebar-nav a .nav-icon { font-size: 1rem; width: 1.2rem; text-align: center; flex-shrink: 0; }
        .sidebar-nav a:hover { color: #fff; background: rgba(255,255,255,.05); }
        .sidebar-nav a.active {
            color: var(--color-accent);
            border-right-color: var(--color-accent);
            background: rgba(201,162,75,.07);
        }
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* Main */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e0d4;
            padding: .85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar-title {
            font-family: 'YekanBakh', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--color-primary);
        }
        .page-body { padding: 1.75rem 2rem; flex: 1; }

        /* Cards */
        .card {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 4px rgba(31,42,68,.06);
            border: 1px solid #ede8dc;
        }
        .card-title {
            font-family: 'YekanBakh', sans-serif;
            font-weight: 700; font-size: 1rem;
            color: var(--color-primary);
            margin-bottom: 1.1rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid #f0ede6;
            display: flex; align-items: center; gap: .5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            padding: .55rem 1.2rem; border-radius: 7px;
            border: none; cursor: pointer;
            font-family: 'YekanBakh', sans-serif; font-size: .88rem;
            text-decoration: none; transition: all .15s;
            line-height: 1; white-space: nowrap;
        }
        .btn-primary { background: var(--color-primary); color: #fff; }
        .btn-primary:hover { background: #2a3a5c; }
        .btn-accent { background: var(--color-accent); color: #fff; }
        .btn-accent:hover { background: #b8913d; }
        .btn-outline { background: transparent; border: 1.5px solid var(--color-primary); color: var(--color-primary); }
        .btn-outline:hover { background: var(--color-primary); color: #fff; }
        .btn-danger { background: #c0392b; color: #fff; }
        .btn-danger:hover { background: #a93226; }
        .btn-ghost { background: #f3efe6; color: var(--color-text); border: 1px solid #e0dbd0; }
        .btn-ghost:hover { background: #e8e3d8; }
        .btn-sm { padding: .35rem .85rem; font-size: .8rem; }
        .btn-block { width: 100%; }

        /* Alerts */
        .alert {
            padding: .85rem 1.1rem; border-radius: 8px;
            margin-bottom: 1.2rem; font-size: .88rem;
            display: flex; align-items: flex-start; gap: .6rem;
        }
        .alert-success { background: #ecf5ec; color: #2d5a2d; border: 1px solid #c3dfc3; }
        .alert-error   { background: #fdf0f0; color: #8b1a1a; border: 1px solid #f0c4c4; }
        .alert-warning { background: #fef9ec; color: #7a5c00; border: 1px solid #f0dda0; }
        .alert-info    { background: #edf4fb; color: #1a4a7a; border: 1px solid #b8d4ef; }

        /* Forms */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block; margin-bottom: .35rem;
            font-weight: 600; font-size: .85rem; color: var(--color-primary);
        }
        .form-group label .req { color: #c0392b; margin-right: .1rem; }
        .form-control {
            width: 100%; padding: .6rem .85rem;
            border: 1.5px solid #d5cfc4; border-radius: 7px;
            font-family: inherit; font-size: .88rem; color: var(--color-text);
            background: #fdfaf6; transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            outline: none; border-color: var(--color-accent);
            background: #fff; box-shadow: 0 0 0 3px rgba(201,162,75,.12);
        }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .form-control[type="file"] { padding: .45rem .75rem; background: #f9f6f0; }
        .form-error { color: #c0392b; font-size: .8rem; margin-top: .25rem; display: block; }
        .form-hint  { color: var(--color-muted); font-size: .78rem; margin-top: .25rem; display: block; }

        /* Grids */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .22rem .75rem; border-radius: 20px;
            font-size: .78rem; font-weight: 600;
        }
        .badge-success { background: #e5f0e5; color: #2d5a2d; }
        .badge-danger  { background: #fde8e8; color: #b91c1c; }
        .badge-warning { background: #fef3cd; color: #92400e; }
        .badge-info    { background: #e0edf8; color: #1a4a7a; }

        /* Misc */
        .divider { border: none; border-top: 1px solid #ede8dc; margin: 1.5rem 0; }
        .text-muted { color: var(--color-muted); }
        .text-sm { font-size: .85rem; }

        /* Tables */
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { text-align: right; padding: .6rem .75rem; font-size: .88rem; }
        .table th { border-bottom: 2px solid #ede8dc; color: var(--color-primary); font-weight: 700; font-family: 'YekanBakh', sans-serif; }
        .table td { border-bottom: 1px solid #f0ede8; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: #faf7f2; }

        /* Responsive */
        @media (max-width: 900px) {
            :root { --sidebar-w: 220px; }
            .grid-3, .grid-4 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 680px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; flex-direction: row; flex-wrap: wrap; }
            .sidebar-nav { width: 100%; display: flex; flex-wrap: wrap; padding: .5rem; }
            .sidebar-nav a { padding: .5rem .75rem; border-right: none; border-bottom: 2px solid transparent; flex: 1; justify-content: center; text-align: center; }
            .sidebar-nav a.active { border-bottom-color: var(--color-accent); border-right: none; }
            .topbar { padding: .75rem 1rem; }
            .page-body { padding: 1rem; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="{{ route('home') }}">آوان</a>
        </div>

        @auth
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                @if(auth()->user()->isArtist() && auth()->user()->artistProfile?->avatar)
                    <img src="{{ asset('uploads/' . auth()->user()->artistProfile->avatar) }}" alt="">
                @else
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->isArtist() ? 'هنرمند' : 'تیم تولید' }}</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->isArtist())
                <a href="{{ route('artist.dashboard') }}" class="{{ request()->routeIs('artist.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span> داشبورد
                </a>
                <a href="{{ route('artist.profile') }}" class="{{ request()->routeIs('artist.profile') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span> پروفایل و نمونه‌کار
                </a>
                <a href="{{ route('artist.subscription') }}" class="{{ request()->routeIs('artist.subscription') ? 'active' : '' }}">
                    <span class="nav-icon">⭐</span> اشتراک
                </a>
                @if(auth()->user()->artistProfile?->username)
                <a href="{{ route('profile.show', auth()->user()->artistProfile->username) }}" target="_blank">
                    <span class="nav-icon">🔗</span> پروفایل عمومی
                </a>
                @endif
            @else
                <a href="{{ route('production.dashboard') }}" class="{{ request()->routeIs('production.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span> داشبورد
                </a>
                <a href="{{ route('production.search') }}" class="{{ request()->routeIs('production.search') ? 'active' : '' }}">
                    <span class="nav-icon">🔍</span> جستجوی هنرمندان
                </a>
                <a href="{{ route('production.access') }}" class="{{ request()->routeIs('production.access*') ? 'active' : '' }}">
                    <span class="nav-icon">💳</span> خرید دسترسی
                </a>
            @endif
        </nav>
        @endauth

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm btn-block">خروج از حساب</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <span class="topbar-title">@yield('page-title', 'داشبورد')</span>
            <div style="display:flex;gap:.5rem;align-items:center;">
                @yield('topbar-actions')
            </div>
        </div>
        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">✕ {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
