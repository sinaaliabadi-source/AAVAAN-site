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
            src: url('{{ asset("fonts/YekanBakh-Regular.woff2") }}') format('woff2');
            font-weight: 400;
        }
        :root {
            --color-bg: #F6F1E7;
            --color-primary: #1F2A44;
            --color-accent: #C9A24B;
            --color-text: #232323;
            --color-success: #5C6F4F;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'YekanBakh', Tahoma, sans-serif; background: #f0ede6; color: var(--color-text); direction: rtl; }
        .dashboard-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--color-primary); color: #fff; padding: 1.5rem; flex-shrink: 0; }
        .sidebar a { color: #ccc; display: block; padding: 0.6rem 0; border-bottom: 1px solid #333; text-decoration: none; }
        .sidebar a:hover { color: var(--color-accent); }
        .sidebar a.active { color: var(--color-accent); font-weight: bold; }
        .sidebar .logo { color: var(--color-accent); font-size: 1.3rem; font-weight: bold; margin-bottom: 1.5rem; display: block; }
        .main-content { flex: 1; padding: 2rem; overflow-x: auto; }
        .card { background: #fff; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .btn { display: inline-block; padding: 0.5rem 1.2rem; border-radius: 5px; border: none; cursor: pointer; font-family: inherit; text-decoration: none; font-size: .9rem; }
        .btn-primary { background: var(--color-primary); color: #fff; }
        .btn-accent { background: var(--color-accent); color: #fff; }
        .btn-outline { background: transparent; border: 2px solid var(--color-primary); color: var(--color-primary); }
        .btn-danger { background: #c0392b; color: #fff; }
        .alert { padding: 0.8rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: 600; font-size: .9rem; }
        .form-control { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: .9rem; }
        .form-error { color: #dc3545; font-size: .82rem; margin-top: .25rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        @media (max-width: 768px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } .dashboard-layout { flex-direction: column; } .sidebar { width: 100%; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <a href="{{ route('home') }}" class="logo">آوان</a>
        @auth
            @if(auth()->user()->isArtist())
                <a href="{{ route('artist.dashboard') }}">خانه</a>
                <a href="{{ route('artist.profile') }}">پروفایل</a>
                <a href="{{ route('artist.subscription') }}">اشتراک</a>
            @else
                <a href="{{ route('production.dashboard') }}">خانه</a>
                <a href="{{ route('production.search') }}">جستجوی هنرمندان</a>
                <a href="{{ route('production.access') }}">خرید دسترسی</a>
            @endif
        @endauth
        <form method="POST" action="{{ route('auth.logout') }}" style="margin-top: 2rem;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width:100%;">خروج</button>
        </form>
    </aside>
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>
