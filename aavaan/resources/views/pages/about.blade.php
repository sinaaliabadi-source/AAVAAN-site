@extends('layouts.app')
@section('title', 'درباره آوان — آوان')

@push('styles')
<style>
    /* ─── Hero ─── */
    .about-hero {
        background: var(--color-primary);
        color: #fff;
        text-align: center;
        padding: 5rem 1.5rem 4.5rem;
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 60% 40%, rgba(201,162,75,.18) 0%, transparent 65%);
        pointer-events: none;
    }
    .about-hero-inner { position: relative; z-index: 1; }
    .about-hero .overline {
        display: inline-block;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .12em;
        color: var(--color-accent);
        text-transform: uppercase;
        margin-bottom: 1.1rem;
    }
    .about-hero h1 {
        font-size: 3rem;
        color: #fff;
        margin-bottom: 1.1rem;
        line-height: 1.2;
    }
    .about-hero .tagline {
        font-size: 1.2rem;
        color: rgba(255,255,255,.75);
        max-width: 560px;
        margin: 0 auto 1.75rem;
        line-height: 1.75;
    }
    .about-hero .accent-word {
        color: var(--color-accent);
        font-weight: 700;
    }

    /* ─── Story Section ─── */
    .about-story {
        padding: 5rem 0;
    }
    .story-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 4rem;
        align-items: center;
    }
    .story-text h2 {
        font-size: 1.9rem;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: .75rem;
    }
    .story-text h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 60px;
        height: 3px;
        background: var(--color-accent);
        border-radius: 2px;
    }
    .story-text p {
        color: #444;
        line-height: 1.95;
        margin-bottom: 1.25rem;
        font-size: .97rem;
    }
    .story-text p:last-child { margin-bottom: 0; }
    .story-text .highlight {
        color: var(--color-primary);
        font-weight: 700;
    }

    /* Decorative element */
    .story-decor {
        background: var(--color-primary);
        border-radius: 16px;
        padding: 2.5rem 2rem;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .story-decor::before {
        content: '';
        position: absolute;
        top: -40px;
        left: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(201,162,75,.15);
    }
    .story-decor::after {
        content: '';
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(201,162,75,.1);
    }
    .story-decor-inner { position: relative; z-index: 1; }
    .story-decor .arabic-word {
        font-family: 'YekanBakh', sans-serif;
        font-size: 4rem;
        font-weight: 800;
        color: var(--color-accent);
        line-height: 1;
        margin-bottom: 1rem;
    }
    .story-decor .meaning {
        font-size: 1rem;
        color: rgba(255,255,255,.85);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    .story-decor .divider {
        width: 40px;
        height: 2px;
        background: var(--color-accent);
        margin: 1rem auto;
        border-radius: 2px;
    }
    .story-decor .poem-line {
        font-size: .88rem;
        color: rgba(255,255,255,.6);
        font-style: italic;
        line-height: 1.8;
    }

    /* ─── Values Section ─── */
    .about-values {
        background: var(--color-primary);
        padding: 5rem 0;
    }
    .about-values .section-heading {
        text-align: center;
        margin-bottom: 3rem;
    }
    .about-values .section-heading h2 {
        font-size: 1.9rem;
        color: #fff;
        margin-bottom: .6rem;
    }
    .about-values .section-heading p {
        color: rgba(255,255,255,.65);
        font-size: .97rem;
    }
    .values-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .value-card {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: var(--radius);
        padding: 2.25rem 1.75rem;
        text-align: center;
        transition: background .2s, transform .2s;
    }
    .value-card:hover {
        background: rgba(255,255,255,.1);
        transform: translateY(-4px);
    }
    .value-icon {
        width: 60px;
        height: 60px;
        background: var(--color-accent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.6rem;
    }
    .value-card h3 {
        color: #fff;
        font-size: 1.1rem;
        margin-bottom: .75rem;
    }
    .value-card p {
        color: rgba(255,255,255,.65);
        font-size: .9rem;
        line-height: 1.75;
    }

    /* ─── Disciplines Section ─── */
    .about-disciplines {
        padding: 5rem 0;
    }
    .disciplines-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .disciplines-header h2 {
        font-size: 1.9rem;
        margin-bottom: .75rem;
    }
    .disciplines-header p {
        color: var(--color-muted);
        max-width: 560px;
        margin: 0 auto;
        line-height: 1.75;
    }
    .disciplines-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        justify-content: center;
        max-width: 760px;
        margin: 0 auto;
    }
    .discipline-tag {
        background: #fff;
        border: 1.5px solid #e5e0d6;
        border-radius: 50px;
        padding: .55rem 1.4rem;
        font-size: .9rem;
        font-weight: 600;
        color: var(--color-primary);
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: border-color .2s, background .2s, color .2s;
    }
    .discipline-tag:hover {
        border-color: var(--color-accent);
        background: var(--color-accent);
        color: #fff;
    }
    .discipline-tag span {
        font-size: 1.05rem;
    }

    /* ─── CTA Section ─── */
    .about-cta {
        background: #fff;
        border-top: 1px solid #e5e0d6;
        border-bottom: 1px solid #e5e0d6;
        padding: 5rem 0;
        text-align: center;
    }
    .about-cta h2 {
        font-size: 2rem;
        margin-bottom: .75rem;
    }
    .about-cta p {
        color: var(--color-muted);
        font-size: 1rem;
        margin-bottom: 2rem;
        line-height: 1.7;
    }
    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* ─── Responsive ─── */
    @media (max-width: 900px) {
        .story-grid { grid-template-columns: 1fr; gap: 2.5rem; }
        .story-decor { max-width: 420px; }
        .values-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
    }
    @media (max-width: 640px) {
        .about-hero h1 { font-size: 2.1rem; }
        .about-hero .tagline { font-size: 1rem; }
        .story-text h2 { font-size: 1.5rem; }
        .about-values .section-heading h2,
        .disciplines-header h2,
        .about-cta h2 { font-size: 1.5rem; }
    }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<section class="about-hero">
    <div class="container">
        <div class="about-hero-inner">
            <span class="overline">پلتفرم کاستینگ هنرمندان ایران</span>
            <h1>درباره <span class="accent-word">آوان</span></h1>
            <p class="tagline">
                هر هنرمندی آوانِ خودش را دارد —<br>
                لحظه‌ای که دیده می‌شود
            </p>
        </div>
    </div>
</section>

{{-- ═══ STORY ═══ --}}
<section class="about-story">
    <div class="container">
        <div class="story-grid">

            <div class="story-text">
                <h2>داستان آوان</h2>
                <p>
                    «آوان» در زبان فارسی کلاسیک به معنای <span class="highlight">«زمان، هنگام، دوران»</span> است —
                    همان واژه‌ای که شاعران فارسی برای لحظه‌ی اوج و ظهور به‌کار می‌بردند.
                    «آوانِ جوانی»، «آوانِ گل»؛ هنگامی که چیزی به کمال خود می‌رسد و دیده می‌شود.
                </p>
                <p>
                    این نام از یک باور ساده برخاست: <span class="highlight">هر هنرمندی آوانِ خودش را دارد</span>،
                    لحظه‌ای که استعدادش با فرصت مناسب رو‌به‌رو می‌شود. اما این لحظه اتفاق نمی‌افتد
                    مگر آنکه راهی برای دیده‌شدن وجود داشته باشد.
                </p>
                <p>
                    آوان ساخته شد تا این راه را کوتاه کند. ما یک پل ساختیم میان هنرمندان ایرانی
                    در همه‌ی رشته‌ها — از سینما و تئاتر تا موسیقی و طراحی صحنه — و تیم‌های تولیدی
                    که به دنبال درست‌ترین چهره برای پروژه‌هایشان می‌گردند.
                </p>
                <p>
                    ما به شفافیت، دقت و عدالت در فرآیند کاستینگ ایمان داریم.
                    صنعت هنر ایران ظرفیتی بسیار بیشتر از آن دارد که می‌بینیم —
                    و آوان اینجاست تا بخشی از این ظرفیت را آشکار کند.
                </p>
            </div>

            <div class="story-decor">
                <div class="story-decor-inner">
                    <div class="arabic-word">آوان</div>
                    <div class="meaning">
                        زمان · هنگام · دوران<br>
                        <small>واژه‌ای از فارسی کلاسیک</small>
                    </div>
                    <div class="divider"></div>
                    <div class="poem-line">
                        «آوانِ گل رسید و من<br>
                        هنوز در انتظارم»
                    </div>
                    <div class="divider"></div>
                    <div class="poem-line" style="color:rgba(255,255,255,.8); font-style:normal; font-size:.9rem;">
                        هر هنرمندی آوانِ خودش را دارد
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ VALUES ═══ --}}
<section class="about-values">
    <div class="container">
        <div class="section-heading">
            <h2>ما باور داریم</h2>
            <p>اصول بنیادینی که آوان را شکل داده‌اند</p>
        </div>

        <div class="values-grid">

            <div class="value-card">
                <div class="value-icon">🎭</div>
                <h3>هر هنرمندی لایق دیده‌شدن است</h3>
                <p>
                    استعداد در همه‌جا هست — در تهران، در شهرستان، در اتاق‌های تمرین بی‌نام.
                    آوان این استعداد را از پنهانی به دیده‌شدن می‌رساند.
                </p>
            </div>

            <div class="value-card">
                <div class="value-icon">⚖️</div>
                <h3>کاستینگ باید دقیق، شفاف و عادلانه باشد</h3>
                <p>
                    انتخاب هنرمند باید بر اساس شایستگی باشد، نه روابط.
                    آوان اطلاعات را شفاف می‌کند تا تصمیم بهتری گرفته شود.
                </p>
            </div>

            <div class="value-card">
                <div class="value-icon">🌱</div>
                <h3>صنعت هنر ایران ظرفیت بیشتری دارد</h3>
                <p>
                    ما به پتانسیل بی‌نظیر هنرمندان ایرانی ایمان داریم.
                    آوان ابزاری است برای آشکار کردن این ظرفیت.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- ═══ DISCIPLINES ═══ --}}
<section class="about-disciplines">
    <div class="container">
        <div class="disciplines-header">
            <h2>چندرشته‌ای بودن</h2>
            <p>
                آوان فقط برای سینما نیست. ما پلتفرمی هستیم برای تمام دنیای هنر —
                {{ $categories->count() }} رشته‌ی تخصصی که هر کدام جایگاه خودشان را در آوان دارند.
            </p>
        </div>

        <div class="disciplines-wrap">
            @foreach($categories as $cat)
                <span class="discipline-tag">{{ $cat->name_fa }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section class="about-cta">
    <div class="container">
        <h2>بپیوندید به آوان</h2>
        <p>
            چه هنرمند باشید چه تیم تولید — آوان برای شماست.<br>
            همین امروز آوانِ خودتان را شروع کنید.
        </p>
        <div class="cta-buttons">
            <a href="{{ route('auth') }}" class="btn btn-primary btn-lg">ثبت‌نام هنرمند</a>
            <a href="{{ route('auth') }}" class="btn btn-accent btn-lg">ثبت‌نام تیم تولید</a>
        </div>
    </div>
</section>

@endsection
