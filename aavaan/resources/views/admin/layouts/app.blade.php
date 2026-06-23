<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پنل مدیریت') | آوان</title>
    <style>
        @font-face {
            font-family: 'YekanBakh';
            src: url('{{ asset("fonts/YekanBakh-VF.woff2") }}') format('woff2'),
                 url('{{ asset("fonts/YekanBakh-VF.ttf") }}') format('truetype');
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
            --color-bg:      #F6F1E7;
            --color-primary: #1F2A44;
            --color-accent:  #C9A24B;
            --color-text:    #232323;
            --color-muted:   #7a7a8a;
            --color-success: #5C6F4F;
            --color-danger:  #c0392b;
            --radius:        10px;
            --sidebar-w:     260px;
            --topbar-h:      56px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

        a { color: inherit; text-decoration: none; }

        /* ── Layout ─────────────────────────────── */
        .layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ─────────────────────────────── */
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
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
            transition: transform .25s ease;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-logo-text {
            font-family: 'YekanBakh', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--color-accent);
            letter-spacing: -.5px;
        }
        .sidebar-logo-badge {
            font-size: .68rem;
            background: rgba(201,162,75,.2);
            color: var(--color-accent);
            border: 1px solid rgba(201,162,75,.3);
            padding: .15rem .5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .sidebar-user {
            padding: .9rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex;
            align-items: center;
            gap: .7rem;
        }
        .sidebar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--color-accent);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; font-weight: 700;
            color: var(--color-primary);
            flex-shrink: 0;
        }
        .sidebar-user-name  { font-size: .85rem; font-weight: 600; color: #fff; }
        .sidebar-user-role  { font-size: .72rem; color: #aaa; margin-top: .1rem; }

        .sidebar-section-title {
            padding: .85rem 1.5rem .3rem;
            font-size: .68rem;
            font-weight: 700;
            color: rgba(255,255,255,.35);
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .sidebar-nav { flex: 1; padding-bottom: .5rem; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .65rem;
            color: rgba(255,255,255,.65);
            padding: .55rem 1.5rem;
            font-size: .87rem;
            transition: all .15s;
            border-right: 3px solid transparent;
            position: relative;
        }
        .sidebar-nav a .nav-icon {
            font-size: .95rem;
            width: 1.1rem;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-nav a .nav-badge {
            margin-right: auto;
            font-size: .68rem;
            background: rgba(201,162,75,.25);
            color: var(--color-accent);
            padding: .1rem .45rem;
            border-radius: 20px;
            font-weight: 700;
        }
        .sidebar-nav a:hover {
            color: #fff;
            background: rgba(255,255,255,.05);
        }
        .sidebar-nav a.active {
            color: var(--color-accent);
            border-right-color: var(--color-accent);
            background: rgba(201,162,75,.07);
        }
        .sidebar-nav a.coming-soon {
            opacity: .45;
            cursor: default;
            pointer-events: none;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ── Main ────────────────────────────────── */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e0d4;
            padding: 0 2rem;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .topbar-title {
            font-family: 'YekanBakh', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--color-primary);
            white-space: nowrap;
        }
        .topbar-search {
            display: flex;
            align-items: center;
            gap: .4rem;
            background: #f5f0e8;
            border: 1.5px solid #e0dbd0;
            border-radius: 8px;
            padding: .35rem .75rem;
            flex: 1;
            max-width: 380px;
            transition: border-color .15s;
        }
        .topbar-search:focus-within {
            border-color: var(--color-accent);
            background: #fff;
        }
        .topbar-search input {
            background: transparent;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: .88rem;
            color: var(--color-text);
            width: 100%;
        }
        .topbar-search input::placeholder { color: var(--color-muted); }
        .topbar-search button {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-muted);
            padding: 0;
            font-size: .9rem;
            display: flex;
            align-items: center;
        }
        .topbar-actions { display: flex; align-items: center; gap: .5rem; }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-primary);
            font-size: 1.3rem;
            padding: .2rem;
        }

        /* ── Page body ───────────────────────────── */
        .page-body { padding: 1.75rem 2rem; flex: 1; }

        /* ── Cards ───────────────────────────────── */
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
            font-weight: 700;
            font-size: 1rem;
            color: var(--color-primary);
            margin-bottom: 1.1rem;
            padding-bottom: .8rem;
            border-bottom: 1px solid #f0ede6;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        /* ── Stat cards ──────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.3rem 1.5rem;
            border: 1px solid #ede8dc;
            box-shadow: 0 1px 4px rgba(31,42,68,.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .stat-icon-primary  { background: rgba(31,42,68,.08); }
        .stat-icon-accent   { background: rgba(201,162,75,.12); }
        .stat-icon-success  { background: rgba(92,111,79,.1); }
        .stat-icon-info     { background: rgba(59,130,246,.1); }
        .stat-value {
            font-family: 'YekanBakh', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1.1;
        }
        .stat-label {
            font-size: .8rem;
            color: var(--color-muted);
            margin-top: .2rem;
        }

        /* ── Buttons ─────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            padding: .55rem 1.2rem; border-radius: 7px;
            border: none; cursor: pointer;
            font-family: 'YekanBakh', sans-serif; font-size: .88rem;
            text-decoration: none; transition: all .15s;
            line-height: 1; white-space: nowrap;
        }
        .btn-primary  { background: var(--color-primary); color: #fff; }
        .btn-primary:hover  { background: #2a3a5c; }
        .btn-accent   { background: var(--color-accent); color: #fff; }
        .btn-accent:hover   { background: #b8913d; }
        .btn-outline  { background: transparent; border: 1.5px solid var(--color-primary); color: var(--color-primary); }
        .btn-outline:hover  { background: var(--color-primary); color: #fff; }
        .btn-danger   { background: var(--color-danger); color: #fff; }
        .btn-danger:hover   { background: #a93226; }
        .btn-ghost    { background: #f3efe6; color: var(--color-text); border: 1px solid #e0dbd0; }
        .btn-ghost:hover    { background: #e8e3d8; }
        .btn-sm  { padding: .35rem .85rem; font-size: .8rem; }
        .btn-lg  { padding: .75rem 1.6rem; font-size: .95rem; }
        .btn-block { width: 100%; }

        /* ── Alerts ──────────────────────────────── */
        .alert {
            padding: .85rem 1.1rem; border-radius: 8px;
            margin-bottom: 1.2rem; font-size: .88rem;
            display: flex; align-items: flex-start; gap: .6rem;
        }
        .alert-success { background: #ecf5ec; color: #2d5a2d; border: 1px solid #c3dfc3; }
        .alert-error   { background: #fdf0f0; color: #8b1a1a; border: 1px solid #f0c4c4; }
        .alert-warning { background: #fef9ec; color: #7a5c00; border: 1px solid #f0dda0; }
        .alert-info    { background: #edf4fb; color: #1a4a7a; border: 1px solid #b8d4ef; }

        /* ── Forms ───────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block; margin-bottom: .35rem;
            font-weight: 600; font-size: .85rem; color: var(--color-primary);
        }
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
        .form-error { color: var(--color-danger); font-size: .8rem; margin-top: .25rem; display: block; }
        .form-hint  { color: var(--color-muted); font-size: .78rem; margin-top: .25rem; display: block; }

        /* ── Grids ───────────────────────────────── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; }

        /* ── Badges ──────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .22rem .75rem; border-radius: 20px;
            font-size: .75rem; font-weight: 600;
        }
        .badge-success { background: #e5f0e5; color: #2d5a2d; }
        .badge-danger  { background: #fde8e8; color: #b91c1c; }
        .badge-warning { background: #fef3cd; color: #92400e; }
        .badge-info    { background: #e0edf8; color: #1a4a7a; }
        .badge-muted   { background: #f0ede6; color: var(--color-muted); }

        /* ── Tables ──────────────────────────────── */
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td {
            text-align: right;
            padding: .65rem .85rem;
            font-size: .87rem;
        }
        .table th {
            border-bottom: 2px solid #ede8dc;
            color: var(--color-primary);
            font-weight: 700;
            font-family: 'YekanBakh', sans-serif;
            background: #faf7f2;
        }
        .table td { border-bottom: 1px solid #f0ede8; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: #faf7f2; }

        /* ── Misc ────────────────────────────────── */
        .divider  { border: none; border-top: 1px solid #ede8dc; margin: 1.5rem 0; }
        .text-muted { color: var(--color-muted); }
        .text-sm    { font-size: .85rem; }
        .text-accent { color: var(--color-accent); }

        .placeholder-section {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-muted);
        }
        .placeholder-section .ph-icon { font-size: 3rem; margin-bottom: 1rem; opacity: .4; }
        .placeholder-section h3 { color: var(--color-muted); font-size: 1.1rem; margin-bottom: .5rem; }
        .placeholder-section p { font-size: .88rem; }

        /* ── Sidebar overlay for mobile ──────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 90;
        }

        /* ── Responsive ──────────────────────────── */
        @media (max-width: 960px) {
            :root { --sidebar-w: 240px; }
            .grid-4 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 720px) {
            .grid-3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 680px) {
            .sidebar {
                position: fixed;
                right: 0;
                top: 0;
                height: 100vh;
                transform: translateX(100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open { display: block; }
            .mobile-menu-btn { display: flex; }
            .topbar { padding: 0 1rem; }
            .topbar-search { max-width: none; }
            .page-body { padding: 1rem; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .main { width: 100%; }
        }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

<div
    class="sidebar-overlay"
    :class="{ 'open': sidebarOpen }"
    @click="sidebarOpen = false"
></div>

<div class="layout">
    {{-- ═══ Sidebar ═══ --}}
    <aside class="sidebar" :class="{ 'open': sidebarOpen }">
        <div class="sidebar-logo">
            <span class="sidebar-logo-text">آوان</span>
            <span class="sidebar-logo-badge">مدیریت</span>
        </div>

        @auth
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">
                    @php
                        $roleLabels = [
                            'super_admin'       => 'ادمین کل',
                            'support'           => 'پشتیبانی',
                            'finance'           => 'مالی',
                            'content_moderator' => 'ناظر محتوا',
                        ];
                    @endphp
                    {{ $roleLabels[auth()->user()->admin_role] ?? 'مدیر سیستم' }}
                </div>
            </div>
        </div>
        @endauth

        <nav class="sidebar-nav">
            <div class="sidebar-section-title">داشبورد</div>
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> نمای کلی
            </a>

            <div class="sidebar-section-title">مدیریت کاربران</div>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">👤</span> کاربران / هنرمندان
                <span class="nav-badge">فاز ۲</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🎬</span> تیم‌های تولید
                <span class="nav-badge">فاز ۲</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">✅</span> تأیید تخصص
                <span class="nav-badge">فاز ۲</span>
            </a>

            <div class="sidebar-section-title">اشتراک و مالی</div>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">⭐</span> پلن‌های اشتراک
                <span class="nav-badge">فاز ۳</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">💳</span> پرداخت‌ها
                <span class="nav-badge">فاز ۳</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🎫</span> کدهای تخفیف
                <span class="nav-badge">فاز ۳</span>
            </a>

            <div class="sidebar-section-title">محتوا و بررسی</div>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🔍</span> صف بررسی محتوا
                <span class="nav-badge">فاز ۴</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🚨</span> گزارش‌های تخلف
                <span class="nav-badge">فاز ۴</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">📝</span> مدیریت محتوا (CMS)
                <span class="nav-badge">فاز ۵</span>
            </a>

            <div class="sidebar-section-title">پشتیبانی</div>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🎫</span> تیکتینگ
                <span class="nav-badge">فاز ۶</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">🔔</span> سیستم اعلان
                <span class="nav-badge">فاز ۶</span>
            </a>

            <div class="sidebar-section-title">گزارش‌ها</div>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">📈</span> گزارش‌گیری پیشرفته
                <span class="nav-badge">فاز ۷</span>
            </a>
            <a href="{{ route('admin.placeholder') }}" class="coming-soon">
                <span class="nav-icon">💾</span> پشتیبان‌گیری
                <span class="nav-badge">فاز ۷</span>
            </a>

            <div class="sidebar-section-title">سیستم</div>
            <a href="{{ route('admin.activity-logs') }}"
               class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                <span class="nav-icon">📋</span> لاگ فعالیت
            </a>
            <a href="{{ route('admin.email.index') }}"
               class="{{ request()->routeIs('admin.email.*') ? 'active' : '' }}">
                <span class="nav-icon">✉️</span> ارسال ایمیل
            </a>
            <a href="{{ route('admin.settings') }}"
               class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <span class="nav-icon">⚙️</span> تنظیمات عمومی
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm btn-block">خروج از حساب</button>
            </form>
        </div>
    </aside>

    {{-- ═══ Main ═══ --}}
    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" @click="sidebarOpen = !sidebarOpen" aria-label="منوی ناوبری">
                    ☰
                </button>
                <span class="topbar-title">@yield('page-title', 'پنل مدیریت')</span>
            </div>

            <form class="topbar-search" method="GET" action="{{ route('admin.search') }}" role="search">
                <button type="submit" aria-label="جستجو">🔍</button>
                <input
                    type="search"
                    name="q"
                    placeholder="جستجو در کاربران، پرداخت‌ها..."
                    value="{{ request('q') }}"
                    autocomplete="off"
                >
            </form>

            <div class="topbar-actions">
                @yield('topbar-actions')
                <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" target="_blank" title="مشاهده سایت">
                    🌐
                </a>
            </div>
        </div>

        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">✕ {{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <div>
                        @foreach($errors->all() as $error)
                            <div>• {{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
