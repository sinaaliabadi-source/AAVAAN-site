@extends('layouts.app')
@section('title', 'برای تیم‌های تولید — آوان')
@section('meta-description', 'هنرمندان مناسب پروژه‌تان را در آوان پیدا کنید — جستجو بر اساس رشته، شهر و سابقه، با پرداخت به‌ازای نیاز.')

@push('styles')
<style>
.page-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 5.5rem 0 4.5rem;
    text-align: center;
}
.page-hero-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 800;
    color: var(--color-accent);
    margin-bottom: 1rem;
    line-height: 1.35;
}
.page-hero-sub {
    font-size: clamp(.95rem, 2vw, 1.1rem);
    color: #c8d0e0;
    max-width: 600px;
    margin: 0 auto 2.2rem;
    line-height: 1.9;
}
.s-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--color-primary);
    text-align: center;
    margin-bottom: .5rem;
}
.s-sub { text-align: center; color: var(--color-muted); font-size: .92rem; margin-bottom: 2.8rem; }

/* Benefits */
.benefit-card { text-align: center; padding: 2rem 1.5rem; background: #fff; border-radius: var(--radius); border: 1px solid #ede8dc; }
.benefit-icon { font-size: 2.5rem; margin-bottom: 1rem; }
.benefit-title { font-family: 'YekanBakh'; font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: .4rem; }
.benefit-text { font-size: .88rem; color: var(--color-muted); line-height: 1.85; }

/* Steps */
.steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
.step-card { background: #fff; border-radius: var(--radius); padding: 1.75rem 1.25rem; text-align: center; border: 1px solid #ede8dc; }
.step-num {
    width: 48px; height: 48px; border-radius: 50%;
    background: var(--color-primary); color: var(--color-accent);
    font-family: 'YekanBakh'; font-size: 1.3rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;
}
.step-title { font-family: 'YekanBakh'; font-weight: 700; font-size: .95rem; color: var(--color-primary); margin-bottom: .4rem; }
.step-text { font-size: .83rem; color: var(--color-muted); line-height: 1.8; }

/* Payment model */
.payment-box {
    background: var(--color-primary);
    border-radius: 12px;
    padding: 2.5rem 2rem;
    color: #fff;
    max-width: 760px;
    margin: 0 auto;
    text-align: center;
}
.payment-box-title { font-family: 'YekanBakh'; font-size: 1.25rem; font-weight: 800; color: var(--color-accent); margin-bottom: .75rem; }
.payment-box-text { color: #c8d0e0; line-height: 1.85; font-size: .95rem; margin-bottom: 1.5rem; }
.payment-bundles { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1.75rem; }
.bundle-pill {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 999px;
    padding: .4rem 1.1rem;
    font-size: .85rem;
    color: #e0e8f5;
}

/* CTA */
.cta-bottom {
    padding: 5rem 0;
    background: linear-gradient(135deg, var(--color-accent) 0%, #b8883a 100%);
    text-align: center;
}
.cta-bottom-title { font-family: 'YekanBakh'; font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800; color: #fff; margin-bottom: .75rem; }
.cta-bottom-sub { color: rgba(255,255,255,.85); font-size: 1rem; margin-bottom: 2rem; }
.btn-white { background: #fff; color: var(--color-accent); border-color: #fff; }
.btn-ghost-white { background: transparent; border: 2px solid rgba(255,255,255,.6); color: #fff; }
.btn-ghost-white:hover { background: rgba(255,255,255,.15); }

@media (max-width: 900px) { .steps-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .steps-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title">دقیق‌ترین فهرست کست، در کمترین زمان.</h1>
        <p class="page-hero-sub">در آوان، عوامل و هنرمندان را بر اساس رشته، سابقه و نمونه‌کار واقعی فیلتر کنید و فقط برای دسترسی‌ای که نیاز دارید، هزینه بدهید.</p>
        <a href="{{ route('auth') }}" class="btn btn-accent btn-lg">ورود / ثبت‌نام تیم تولید</a>
    </div>
</section>

{{-- مزایا --}}
<section style="padding: 5rem 0; background: #fff;">
    <div class="container">
        <h2 class="s-title">چرا تیم‌های تولید آوان را انتخاب می‌کنند؟</h2>
        <p class="s-sub">ابزارهای کاستینگ هدفمند برای پروژه‌های واقعی</p>
        <div class="grid-3">
            <div class="benefit-card">
                <div class="benefit-icon">🎯</div>
                <div class="benefit-title">جستجوی هدفمند</div>
                <p class="benefit-text">فیلتر بر اساس رشته هنری، شهر، بازه سنی، سابقه و کلیدواژه — دقیقاً آنچه نیاز دارید</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">🎬</div>
                <div class="benefit-title">نمونه‌کار واقعی</div>
                <p class="benefit-text">دسترسی به ویدیوی ریل، گالری تصاویر و سوابق کاری هر هنرمند — پیش از تماس</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">💳</div>
                <div class="benefit-title">پرداخت به‌ازای نیاز</div>
                <p class="benefit-text">اشتراک ثابت ندارید. فقط برای دسترسی‌ای که واقعاً استفاده می‌کنید هزینه بدهید</p>
            </div>
        </div>
    </div>
</section>

{{-- مراحل کار --}}
<section style="padding: 5rem 0;">
    <div class="container">
        <h2 class="s-title">مراحل کار با آوان</h2>
        <p class="s-sub">از جستجو تا تماس مستقیم با هنرمند — چهار گام ساده</p>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">۱</div>
                <div class="step-title">ثبت‌نام تیم تولید</div>
                <p class="step-text">با ایمیل یا موبایل خود یک حساب تیم تولید بسازید. رایگان و سریع است.</p>
            </div>
            <div class="step-card">
                <div class="step-num">۲</div>
                <div class="step-title">جستجو و فیلتر</div>
                <p class="step-text">هنرمندان را بر اساس رشته هنری، شهر، سابقه و کلیدواژه جستجو و فیلتر کنید.</p>
            </div>
            <div class="step-card">
                <div class="step-num">۳</div>
                <div class="step-title">پرداخت برای دسترسی</div>
                <p class="step-text">یک دسترسی تکی یا بسته چندتایی بخرید تا به اطلاعات کامل هنرمندان دسترسی داشته باشید.</p>
            </div>
            <div class="step-card">
                <div class="step-num">۴</div>
                <div class="step-title">تماس مستقیم</div>
                <p class="step-text">اطلاعات تماس هنرمندان منتخب را ببینید و مستقیماً با آن‌ها در ارتباط باشید.</p>
            </div>
        </div>
    </div>
</section>

{{-- مدل پرداخت --}}
<section style="padding: 5rem 0; background: #fff;">
    <div class="container">
        <h2 class="s-title">مدل پرداخت</h2>
        <p class="s-sub">شفاف، انعطاف‌پذیر و بدون هزینه پنهان</p>
        <div class="payment-box">
            <div class="payment-box-title">پرداخت به‌ازای هر دسترسی</div>
            <p class="payment-box-text">در آوان اشتراک ثابت برای تیم‌های تولید وجود ندارد. بسته‌های دسترسی بخرید و از آن‌ها در هر پروژه‌ای که نیاز داشتید استفاده کنید. هر بار که پروفایل کامل هنرمندی را باز می‌کنید، یک اعتبار استفاده می‌شود. اعتبارهای خریداری‌شده بدون تاریخ انقضا هستند.</p>
            <div class="payment-bundles">
                <span class="bundle-pill">دسترسی تکی</span>
                <span class="bundle-pill">بسته ۵ دسترسی</span>
                <span class="bundle-pill">بسته ۱۰ دسترسی — بهترین ارزش</span>
                <span class="bundle-pill">سازمانی / پروژه بزرگ — تماس بگیرید</span>
            </div>
            <p class="payment-box-text" style="margin-bottom:1.5rem">
                برای انتخاب بیش از ۱۰ هنرمند یا نیازهای سازمانی و پروژه‌های بزرگ، بستهٔ اختصاصی با قیمت‌گذاری ویژه در نظر گرفته می‌شود.
            </p>
            <div style="display:flex; gap:.75rem; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('pricing') }}" class="btn btn-accent">مشاهده تعرفه کامل</a>
                <a href="{{ route('contact') }}" class="btn btn-outline" style="border-color:rgba(255,255,255,.6);color:#fff">تماس برای پلن سازمانی</a>
            </div>
        </div>
    </div>
</section>

{{-- CTA نهایی --}}
<section class="cta-bottom">
    <div class="container">
        <div class="cta-bottom-title">کستِ پروژه‌ی بعدی‌تان را همین حالا پیدا کنید</div>
        <p class="cta-bottom-sub">هزاران هنرمند حرفه‌ای در آوان منتظرند</p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('auth') }}" class="btn btn-white btn-lg">شروع جستجو</a>
            <a href="{{ route('how-it-works') }}" class="btn btn-ghost-white btn-lg">نحوه کار را بخوانید</a>
        </div>
    </div>
</section>

@endsection
