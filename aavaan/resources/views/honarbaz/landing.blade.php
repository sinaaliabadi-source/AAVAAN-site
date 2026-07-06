@extends('layouts.app')

@section('title', 'هنرباز — استعدادیابی کودکان ایران — آوان')
@section('meta-description', 'هنرباز؛ برنامه استعدادیابی و رئالیتی شوی کودکان ایران — کشف استعدادهای پنهان کودکان در مناطق کم‌برخوردار و روستاهای ایران.')

@push('styles')
<style>
    .hb-hero {
        position: relative;
        color: #fff;
        text-align: center;
        padding: 6rem 0 5rem;
        background:
            radial-gradient(circle at 20% 20%, rgba(201,162,75,.25), transparent 45%),
            linear-gradient(135deg, #16203a 0%, #1F2A44 55%, #24325a 100%);
        overflow: hidden;
    }
    .hb-hero::after {
        content: '🎭';
        position: absolute;
        font-size: 22rem;
        opacity: .05;
        top: 50%; right: 6%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .hb-hero h1 {
        color: #fff;
        font-size: clamp(2.6rem, 7vw, 4.5rem);
        letter-spacing: -1px;
        margin-bottom: .6rem;
        text-shadow: 0 3px 18px rgba(0,0,0,.35);
    }
    .hb-hero .hb-sub {
        font-size: clamp(1.05rem, 2.6vw, 1.4rem);
        color: var(--color-accent);
        font-weight: 600;
        margin-bottom: 2rem;
    }
    .hb-hero-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem; }
    .hb-stats {
        display: flex; justify-content: center; gap: 2.5rem; flex-wrap: wrap;
        max-width: 720px; margin: 0 auto;
    }
    .hb-stat { min-width: 120px; }
    .hb-stat .num { font-size: 2.4rem; font-weight: 800; color: var(--color-accent); line-height: 1.1; }
    .hb-stat .lbl { font-size: .9rem; opacity: .85; }

    .hb-section { padding: 4.5rem 0; }
    .hb-section h2 { text-align: center; font-size: clamp(1.8rem, 4vw, 2.4rem); margin-bottom: .6rem; }
    .hb-section .lead { text-align: center; color: var(--color-muted); max-width: 780px; margin: 0 auto 2.8rem; font-size: 1.05rem; }

    .hb-features { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.4rem; }
    .hb-feature {
        background: #fff; border-radius: 14px; padding: 1.8rem 1.3rem; text-align: center;
        box-shadow: var(--shadow); border: 1px solid rgba(31,42,68,.06);
        transition: transform .2s, box-shadow .2s;
    }
    .hb-feature:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(31,42,68,.14); }
    .hb-feature .ico { font-size: 2.6rem; margin-bottom: .7rem; }
    .hb-feature h3 { font-size: 1.05rem; margin-bottom: .4rem; }
    .hb-feature p { font-size: .9rem; color: var(--color-muted); }

    .hb-team-section { background: var(--color-primary); color: #fff; }
    .hb-team-section h2 { color: #fff; }
    .hb-team { display: flex; gap: 1.8rem; justify-content: center; flex-wrap: wrap; }
    .hb-team-card {
        background: rgba(255,255,255,.06); border: 1px solid rgba(201,162,75,.3);
        border-radius: 16px; padding: 2rem; text-align: center; width: 280px;
    }
    .hb-team-card .avatar {
        width: 84px; height: 84px; margin: 0 auto 1rem; border-radius: 50%;
        background: var(--color-accent); color: var(--color-primary);
        display: flex; align-items: center; justify-content: center; font-size: 2.4rem;
    }
    .hb-team-card h3 { color: #fff; margin-bottom: .3rem; }
    .hb-team-card .role { color: var(--color-accent); font-weight: 600; font-size: .92rem; }
    .hb-team-card .note { font-size: .8rem; opacity: .7; margin-top: .6rem; }

    .hb-timeline { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.4rem; position: relative; }
    .hb-step {
        text-align: center; background: #fff; border-radius: 14px; padding: 2rem 1.4rem;
        box-shadow: var(--shadow); position: relative;
    }
    .hb-step .step-num {
        width: 46px; height: 46px; border-radius: 50%; margin: 0 auto 1rem;
        background: var(--color-accent); color: var(--color-primary);
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem;
    }
    .hb-step h3 { margin-bottom: .5rem; }
    .hb-step p { color: var(--color-muted); font-size: .92rem; }

    .hb-cta {
        text-align: center; color: #fff; padding: 4.5rem 0;
        background: linear-gradient(135deg, #1F2A44, #C9A24B);
    }
    .hb-cta h2 { color: #fff; font-size: clamp(1.9rem, 4vw, 2.6rem); margin-bottom: .8rem; }
    .hb-cta p { font-size: 1.1rem; margin-bottom: 2rem; opacity: .95; }
    .hb-free-badge {
        display: inline-block; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.4);
        padding: .35rem 1rem; border-radius: 999px; font-weight: 700; margin-bottom: 1.4rem; font-size: .9rem;
    }

    .hb-footer { background: #141c30; color: #cbd3e0; padding: 3rem 0; text-align: center; }
    .hb-footer h3 { color: #fff; margin-bottom: 1rem; }
    .hb-footer .contacts { display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap; font-size: .95rem; }
    .hb-footer a { color: var(--color-accent); }

    @media (max-width: 900px) {
        .hb-features { grid-template-columns: repeat(2, 1fr); }
        .hb-timeline { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .hb-features { grid-template-columns: 1fr; }
        .hb-stats { gap: 1.4rem; }
    }
</style>
@endpush

@section('content')

@if(session('honarbaz_registered'))
    <div class="container" style="padding-top:1.5rem">
        <div class="alert alert-success">
            ✅ {{ session('success') ?? 'ثبت‌نام شما با موفقیت انجام شد.' }}
        </div>
    </div>
@endif

{{-- ===== Hero ===== --}}
<section class="hb-hero">
    <div class="container" style="position:relative;z-index:1">
        <h1>هنرباز</h1>
        <p class="hb-sub">{{ $program->meta['subtitle'] ?? 'هر کودک یک استعداد دارد، هر روستا یک داستان' }}</p>
        <div class="hb-hero-btns">
            @if($program->registrationOpen())
                <a href="{{ route('honarbaz.register') }}" class="btn btn-accent btn-lg">ثبت‌نام کنید</a>
            @endif
            <a href="{{ route('honarbaz.contestants') }}" class="btn btn-outline btn-lg" style="border-color:#fff;color:#fff">مشاهده شرکت‌کنندگان</a>
        </div>
        <div class="hb-stats">
            <div class="hb-stat">
                <div class="num">{{ number_format($stats['registrations']) }}</div>
                <div class="lbl">ثبت‌نام</div>
            </div>
            <div class="hb-stat">
                <div class="num">{{ number_format($stats['provinces']) }}</div>
                <div class="lbl">استان</div>
            </div>
            <div class="hb-stat">
                <div class="num">{{ number_format($stats['votes']) }}</div>
                <div class="lbl">رأی مردمی</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== معرفی ===== --}}
<section class="hb-section" style="background:var(--color-bg)">
    <div class="container">
        <h2>درباره هنرباز</h2>
        <p class="lead">{{ $program->description }}</p>
        <div class="hb-features">
            <div class="hb-feature">
                <div class="ico">🎯</div>
                <h3>اولین استعدادیابی روستامحور</h3>
                <p>تمرکز بر روستاها و مناطق کمتر دیده‌شده ایران</p>
            </div>
            <div class="hb-feature">
                <div class="ico">🚐</div>
                <h3>سفر با خانه سیار</h3>
                <p>حضور در سراسر کشور برای کشف استعدادها</p>
            </div>
            <div class="hb-feature">
                <div class="ico">🌟</div>
                <h3>کشف استعدادهای پنهان</h3>
                <p>فرصتی برابر برای هر کودک بااستعداد</p>
            </div>
            <div class="hb-feature">
                <div class="ico">🏆</div>
                <h3>صندوق حمایت هنرباز</h3>
                <p>پشتیبانی و پرورش استعدادهای برگزیده</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== تیم ===== --}}
<section class="hb-section hb-team-section">
    <div class="container">
        <h2>عوامل برنامه</h2>
        <p class="lead" style="color:rgba(255,255,255,.7)">با هدایت و طراحی حرفه‌ای‌ها، هنرباز شکل گرفت.</p>
        <div class="hb-team">
            <div class="hb-team-card">
                <div class="avatar">🎬</div>
                <h3>مجید بذرپاچ</h3>
                <div class="role">کارگردان و تهیه‌کننده</div>
            </div>
            <div class="hb-team-card">
                <div class="avatar">✍️</div>
                <h3>فرشته صفری</h3>
                <div class="role">طراح و نویسنده</div>
                <div class="note">ثبت‌شده در بانک خانه سینما — شماره ۵۵۰۴۰۰۵۰۴۱</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== مراحل ===== --}}
<section class="hb-section" style="background:#fff">
    <div class="container">
        <h2>مسیر هنرباز</h2>
        <p class="lead">از کشف تا رقابت؛ در کنار هر استعداد.</p>
        <div class="hb-timeline">
            <div class="hb-step">
                <div class="step-num">۱</div>
                <h3>کشف</h3>
                <p>سفر به روستاها و شهرها برای یافتن استعدادهای پنهان کودکان.</p>
            </div>
            <div class="hb-step">
                <div class="step-num">۲</div>
                <h3>آموزش</h3>
                <p>پرورش و آموزش استعدادها زیر نظر مربیان و هنرمندان.</p>
            </div>
            <div class="hb-step">
                <div class="step-num">۳</div>
                <h3>رقابت</h3>
                <p>حضور روی صحنه و رقابت با رأی مردمی برای درخشیدن.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="hb-cta">
    <div class="container">
        <span class="hb-free-badge">ثبت‌نام کاملاً رایگان است</span>
        <h2>استعداد کودک شما منتظر کشف‌شدن است</h2>
        <p>همین حالا ثبت‌نام کنید و بخشی از بزرگ‌ترین استعدادیابی روستامحور ایران باشید.</p>
        @if($program->registrationOpen())
            <a href="{{ route('honarbaz.register') }}" class="btn btn-accent btn-lg">ثبت‌نام کنید ←</a>
        @else
            <span class="btn btn-outline btn-lg" style="border-color:#fff;color:#fff;cursor:default">ثبت‌نام فعلاً بسته است</span>
        @endif
    </div>
</section>

{{-- ===== footer اختصاصی ===== --}}
<section class="hb-footer">
    <div class="container">
        <h3>تماس با هنرباز</h3>
        <div class="contacts">
            <span>📞 {{ config('honarbaz.contact.phone') }}</span>
            <span>📱 {{ config('honarbaz.contact.mobile') }}</span>
            <span>✉️ <a href="mailto:{{ config('honarbaz.contact.email') }}">{{ config('honarbaz.contact.email') }}</a></span>
        </div>
    </div>
</section>

@endsection
