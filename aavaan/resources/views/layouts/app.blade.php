<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description', 'آوان — پلتفرم تخصصی کاستینگ هنرمندان ایران')">
    <title>@yield('title', 'آوان') — پلتفرم کاستینگ هنرمندان ایران</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}"  type="image/svg+xml">
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
            --color-success: #5C6F4F;
            --color-muted:   #6b7280;
            --color-border:  #d1d5db;
            --radius:        8px;
            --shadow:        0 1px 6px rgba(0,0,0,.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'IRANSansX', 'YekanBakh', Tahoma, Arial, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            direction: rtl;
            line-height: 1.85;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'YekanBakh', Tahoma, sans-serif;
            font-weight: 700;
            line-height: 1.3;
            color: var(--color-primary);
        }

        a { color: var(--color-accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        img { max-width: 100%; display: block; }

        /* دسترس‌پذیری: حالت فوکوس برای پیمایش با کیبورد */
        a:focus-visible,
        button:focus-visible,
        .btn:focus-visible,
        .form-control:focus-visible {
            outline: 2px solid var(--color-accent);
            outline-offset: 2px;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .6rem 1.5rem;
            border-radius: var(--radius);
            border: 2px solid transparent;
            cursor: pointer;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            white-space: nowrap;
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.12); text-decoration: none; }
        .btn:active { transform: translateY(0); }
        .btn-primary  { background: var(--color-primary); color: #fff; }
        /* متن لاجوردی روی طلایی — کنتراست خواناتر و هماهنگ‌تر با برند */
        .btn-accent   { background: var(--color-accent);  color: var(--color-primary); font-weight: 700; }
        .btn-outline  { background: transparent; border-color: var(--color-primary); color: var(--color-primary); }
        .btn-outline:hover { background: var(--color-primary); color: #fff; }
        .btn-success  { background: var(--color-success); color: #fff; }
        .btn-sm       { padding: .4rem 1rem; font-size: .85rem; }
        .btn-lg       { padding: .8rem 2rem; font-size: 1.05rem; }
        .btn-block    { width: 100%; justify-content: center; }

        .card {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.75rem;
            box-shadow: var(--shadow);
        }

        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block;
            margin-bottom: .3rem;
            font-weight: 600;
            font-size: .9rem;
            color: var(--color-primary);
        }
        .form-control {
            width: 100%;
            padding: .6rem .9rem;
            border: 1.5px solid var(--color-border);
            border-radius: 6px;
            font-family: inherit;
            font-size: .95rem;
            background: #fafafa;
            color: var(--color-text);
            transition: border-color .2s, background .2s;
        }
        .form-control:focus { outline: none; border-color: var(--color-accent); background: #fff; }
        .form-control.is-invalid { border-color: #c0392b; }
        .form-error { color: #c0392b; font-size: .82rem; margin-top: .25rem; display: block; }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 100px; }

        .alert {
            padding: .9rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.2rem;
            font-size: .92rem;
            border-right: 4px solid;
        }
        .alert-success { background: #ecf5ec; color: #1a4a1a; border-color: var(--color-success); }
        .alert-error   { background: #fdecea; color: #7f1d1d; border-color: #c0392b; }
        .alert-info    { background: #e8f4fd; color: #1a4a7a; border-color: #3b82f6; }
        .alert-warning { background: #fef9e7; color: #7d4a00; border-color: var(--color-accent); }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.2rem; }

        .badge {
            display: inline-block;
            padding: .2rem .65rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
        }
        .badge-active  { background: #d4edda; color: #155724; }
        .badge-expired { background: #f8d7da; color: #721c24; }
        .badge-pending { background: #fff3cd; color: #856404; }

        @media (max-width: 640px) {
            .container { padding: 0 1rem; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body data-page="{{ optional(request()->route())->getName() }}">
@include('layouts.partials.header')

<main>
    @if(session('success'))
        <div class="container" style="padding-top:1rem">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="container" style="padding-top:1rem">
            <div class="alert alert-error">{{ session('error') }}</div>
        </div>
    @endif
    @yield('content')
</main>

@include('layouts.partials.footer')
@stack('scripts')
</body>
</html>
