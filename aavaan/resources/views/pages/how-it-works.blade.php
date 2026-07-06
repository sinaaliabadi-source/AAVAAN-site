@extends('layouts.app')
@section('title', 'چگونه آوان کار می‌کند؟ — آوان')

@push('styles')
<style>
    /* ─── Hero ─── */
    .hiw-hero {
        background: var(--color-primary);
        color: #fff;
        text-align: center;
        padding: 5rem 1.5rem 4.5rem;
        position: relative;
        overflow: hidden;
    }
    .hiw-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 30% 60%, rgba(201,162,75,.16) 0%, transparent 60%);
        pointer-events: none;
    }
    .hiw-hero-inner { position: relative; z-index: 1; }
    .hiw-hero .overline {
        display: inline-block;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--color-accent);
        margin-bottom: 1rem;
    }
    .hiw-hero h1 {
        font-size: 2.6rem;
        color: #fff;
        margin-bottom: 1rem;
        line-height: 1.25;
    }
    .hiw-hero p {
        color: rgba(255,255,255,.75);
        font-size: 1.07rem;
        max-width: 580px;
        margin: 0 auto;
        line-height: 1.8;
    }

    /* ─── Section Wrapper ─── */
    .hiw-section {
        padding: 5rem 0;
    }
    .hiw-section + .hiw-section {
        border-top: 1px solid #e5e0d6;
    }
    .hiw-section.dark {
        background: var(--color-primary);
        border-top: none;
    }
    .hiw-section-header {
        text-align: center;
        margin-bottom: 3.5rem;
    }
    .hiw-section-header .audience-label {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--color-accent);
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
        padding: .3rem 1rem;
        border-radius: 50px;
        margin-bottom: 1rem;
    }
    .hiw-section.dark .hiw-section-header .audience-label {
        background: rgba(201,162,75,.25);
        color: var(--color-accent);
    }
    .hiw-section-header h2 {
        font-size: 2rem;
        margin-bottom: .6rem;
    }
    .hiw-section.dark .hiw-section-header h2 {
        color: #fff;
    }
    .hiw-section-header p {
        color: var(--color-muted);
        font-size: .97rem;
        max-width: 520px;
        margin: 0 auto;
    }
    .hiw-section.dark .hiw-section-header p {
        color: rgba(255,255,255,.65);
    }

    /* ─── Steps Grid ─── */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.75rem;
        max-width: 860px;
        margin: 0 auto;
    }

    /* ─── Step Card ─── */
    .step-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 2rem 1.75rem;
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
        transition: transform .2s, box-shadow .2s;
    }
    .step-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
    }
    .hiw-section.dark .step-card {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        box-shadow: none;
    }
    .hiw-section.dark .step-card:hover {
        background: rgba(255,255,255,.1);
    }

    .step-num {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--color-primary);
        color: #fff;
        font-family: 'YekanBakh', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .hiw-section.dark .step-num {
        background: var(--color-accent);
        color: #fff;
    }

    .step-body h3 {
        font-size: 1.07rem;
        margin-bottom: .5rem;
        color: var(--color-primary);
        display: flex;
        align-items: center;
        gap: .45rem;
    }
    .hiw-section.dark .step-body h3 {
        color: #fff;
    }
    .step-icon {
        font-size: 1.15rem;
    }
    .step-body p {
        font-size: .9rem;
        color: var(--color-muted);
        line-height: 1.8;
    }
    .hiw-section.dark .step-body p {
        color: rgba(255,255,255,.65);
    }

    /* ─── FAQ Section ─── */
    .hiw-faq {
        padding: 5rem 0;
        background: #fff;
    }
    .faq-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .faq-header h2 {
        font-size: 1.9rem;
        margin-bottom: .5rem;
    }
    .faq-header p {
        color: var(--color-muted);
        font-size: .95rem;
    }
    .faq-list {
        max-width: 720px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .faq-item {
        background: var(--color-bg);
        border-radius: var(--radius);
        border: 1px solid #e5e0d6;
        overflow: hidden;
    }
    .faq-question {
        padding: 1.1rem 1.5rem;
        font-weight: 700;
        font-size: .97rem;
        color: var(--color-primary);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        user-select: none;
        list-style: none;
    }
    .faq-question::-webkit-details-marker { display: none; }
    .faq-toggle {
        width: 28px;
        height: 28px;
        background: var(--color-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .8rem;
        flex-shrink: 0;
        transition: background .2s, transform .2s;
    }
    details[open] .faq-toggle {
        background: var(--color-accent);
        transform: rotate(45deg);
    }
    .faq-answer {
        padding: 0 1.5rem 1.1rem;
        color: var(--color-muted);
        font-size: .91rem;
        line-height: 1.85;
        border-top: 1px solid #e5e0d6;
        padding-top: .9rem;
    }
    .faq-more {
        text-align: center;
        margin-top: 2rem;
    }

    /* ─── CTA ─── */
    .hiw-cta {
        background: var(--color-primary);
        padding: 5rem 0;
        text-align: center;
    }
    .hiw-cta h2 {
        color: #fff;
        font-size: 2rem;
        margin-bottom: .75rem;
    }
    .hiw-cta p {
        color: rgba(255,255,255,.7);
        font-size: 1rem;
        margin-bottom: 2rem;
        line-height: 1.7;
    }
    .cta-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        max-width: 640px;
        margin: 0 auto;
    }
    .cta-card {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: var(--radius);
        padding: 2rem 1.5rem;
        text-align: center;
    }
    .cta-card .cta-icon {
        font-size: 2.5rem;
        margin-bottom: .75rem;
    }
    .cta-card h3 {
        color: #fff;
        font-size: 1.05rem;
        margin-bottom: .5rem;
    }
    .cta-card p {
        color: rgba(255,255,255,.6);
        font-size: .85rem;
        margin-bottom: 1.25rem;
        line-height: 1.6;
    }

    /* ─── Responsive ─── */
    @media (max-width: 760px) {
        .steps-grid { grid-template-columns: 1fr; max-width: 500px; }
        .cta-cards { grid-template-columns: 1fr; max-width: 380px; }
    }
    @media (max-width: 640px) {
        .hiw-hero h1 { font-size: 1.9rem; }
        .hiw-section-header h2 { font-size: 1.6rem; }
        .hiw-faq, .hiw-cta, .hiw-section { padding: 3.5rem 0; }
        .hiw-cta h2 { font-size: 1.6rem; }
    }
</style>
@endpush

@section('content')

{{-- ═══ HERO ═══ --}}
<section class="hiw-hero">
    <div class="container">
        <div class="hiw-hero-inner">
            <span class="overline">راهنمای آوان</span>
            <h1>چگونه آوان کار می‌کند؟</h1>
            <p>
                آوان فرآیند کاستینگ را ساده، شفاف و دوطرفه کرده است —
                هنرمندان دیده می‌شوند و تیم‌های تولید درست‌ترین انتخاب را می‌کنند.
            </p>
        </div>
    </div>
</section>

{{-- ═══ ARTISTS SECTION ═══ --}}
<section class="hiw-section">
    <div class="container">

        <div class="hiw-section-header">
            <div class="audience-label">
                <span>🎭</span> برای هنرمندان
            </div>
            <h2>برای هنرمندان</h2>
            <p>پروفایل خود را بسازید، نمونه‌کارتان را نمایش دهید و در فهرست کاستینگ آوان فعال باشید</p>
        </div>

        <div class="steps-grid">

            <div class="step-card">
                <div class="step-num">۱</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">📝</span>
                        ثبت‌نام
                    </h3>
                    <p>
                        با ایمیل یا شماره موبایل‌تان یک حساب هنرمند بسازید.
                        فرآیند سریع و رایگان است و کمتر از دو دقیقه طول می‌کشد.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۲</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">👤</span>
                        تکمیل پروفایل
                    </h3>
                    <p>
                        عکس، بیوگرافی، رشته‌ی هنری، شهر و سابقه‌ی کاری را وارد کنید.
                        هرچه پروفایل کامل‌تر باشد، شانس دیده‌شدن بیشتر است.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۳</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">🎬</span>
                        آپلود نمونه‌کار
                    </h3>
                    <p>
                        تصاویر و ویدیوی ریل بهترین کارتان را آپلود کنید.
                        تیم‌های تولید این را اول می‌بینند — پس بهترین را انتخاب کنید.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۴</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">⭐</span>
                        انتخاب اشتراک
                    </h3>
                    <p>
                        یک پلن ماهانه یا سالانه انتخاب کنید و در فهرست کست آوان
                        فعال شوید. اشتراک سالانه اولویت نمایش بیشتری دارد.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ PRODUCTION SECTION ═══ --}}
<section class="hiw-section dark">
    <div class="container">

        <div class="hiw-section-header">
            <div class="audience-label">
                <span>🎥</span> برای تیم‌های تولید
            </div>
            <h2>برای تیم‌های تولید</h2>
            <p>هنرمند مناسب پروژه‌تان را پیدا کنید — با فیلتر دقیق و دسترسی مستقیم</p>
        </div>

        <div class="steps-grid">

            <div class="step-card">
                <div class="step-num">۱</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">🏢</span>
                        ثبت‌نام تیم
                    </h3>
                    <p>
                        حساب تیم تولید با مشخصات پروژه یا شرکت بسازید.
                        ثبت‌نام رایگان است و دسترسی به جستجوی پایه را باز می‌کند.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۲</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">🔍</span>
                        جستجو و فیلتر
                    </h3>
                    <p>
                        بر اساس رشته، شهر، بازه سنی و سابقه جستجو کنید.
                        نتایج فیلترشده را ببینید و پروفایل‌ها را بررسی کنید.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۳</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">💳</span>
                        پرداخت و دسترسی
                    </h3>
                    <p>
                        برای فهرست‌هایی که نیاز دارید، یک بسته دسترسی بخرید.
                        دسترسی تکی، بسته ۵ یا بسته ۱۰ — هر چه مناسب‌تر است.
                    </p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">۴</div>
                <div class="step-body">
                    <h3>
                        <span class="step-icon">📞</span>
                        تماس مستقیم
                    </h3>
                    <p>
                        اطلاعات تماس هنرمندان منتخب را ببینید و مستقیم
                        با آن‌ها تماس بگیرید. بدون واسطه، بدون تأخیر.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ FAQ ═══ --}}
<section class="hiw-faq">
    <div class="container">

        <div class="faq-header">
            <h2>سوالات پرتکرار</h2>
            <p>پاسخ سریع به رایج‌ترین سوال‌ها</p>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    آیا ثبت‌نام در آوان رایگان است؟
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    بله، ثبت‌نام برای هم هنرمندان و هم تیم‌های تولید کاملاً رایگان است.
                    هنرمندان برای فعال شدن در فهرست کاستینگ نیاز به اشتراک دارند،
                    و تیم‌های تولید برای دیدن اطلاعات تماس باید یک بسته دسترسی بخرند.
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    تیم‌های تولید چطور به اطلاعات هنرمندان دسترسی پیدا می‌کنند؟
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    تیم‌های تولید می‌توانند پروفایل و نمونه‌کار هنرمندان را رایگان ببینند.
                    برای مشاهده اطلاعات تماس (شماره موبایل و ایمیل)، باید یک بسته دسترسی خریداری کنند.
                    این روش از حریم خصوصی هنرمندان حفاظت می‌کند.
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    آوان شامل چه رشته‌های هنری می‌شود؟
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    آوان چندرشته‌ای است و شامل سینما، تئاتر، تلویزیون، موسیقی، طراحی صحنه،
                    گریم، تدوین، دوبله، صداپیشگی، رقص، عکاسی هنری و رشته‌های دیگر می‌شود.
                    اگر رشته‌تان را در لیست نمی‌بینید، با ما در تماس باشید.
                </div>
            </details>

        </div>

        <div class="faq-more">
            <a href="/faq" class="btn btn-outline">مشاهده همه سوالات</a>
        </div>

    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section class="hiw-cta">
    <div class="container">
        <h2>آماده‌اید شروع کنید؟</h2>
        <p>
            هنرمند هستید یا تیم تولید — آوان برای شما ساخته شده است.<br>
            همین امروز ثبت‌نام کنید.
        </p>

        <div class="cta-cards">

            <div class="cta-card">
                <div class="cta-icon">🎭</div>
                <h3>هنرمندان</h3>
                <p>پروفایل بسازید و دیده شوید</p>
                <a href="{{ route('auth') }}" class="btn btn-accent btn-block">ثبت‌نام هنرمند</a>
            </div>

            <div class="cta-card">
                <div class="cta-icon">🎬</div>
                <h3>تیم‌های تولید</h3>
                <p>هنرمند مناسب پیدا کنید</p>
                <a href="{{ route('auth') }}" class="btn btn-accent btn-block">ثبت‌نام تیم</a>
            </div>

        </div>
    </div>
</section>

@endsection
