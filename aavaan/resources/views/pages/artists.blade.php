@extends('layouts.app')
@section('title', 'هنرمندان — آوان')
@section('meta-description', 'پروفایل هنری خود را در آوان بسازید و توسط تیم‌های تولید سینما، تئاتر و موسیقی دیده شوید.')

@push('styles')
<style>
/* ===== Shared ===== */
.section-title {
    text-align: center;
    font-size: clamp(1.5rem, 3vw, 2rem);
    color: var(--color-primary);
    margin-bottom: .5rem;
}
.section-sub {
    text-align: center;
    color: var(--color-muted);
    margin-bottom: 3rem;
    font-size: .98rem;
    max-width: 620px;
    margin-left: auto;
    margin-right: auto;
}
.btn-outline-white {
    background: transparent;
    border: 2px solid rgba(255,255,255,.65);
    color: #fff;
    font-weight: 600;
}
.btn-outline-white:hover {
    background: rgba(255,255,255,.12);
    border-color: #fff;
    color: #fff;
    text-decoration: none;
}

/* ===== Hero ===== */
.artists-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 7rem 0 6rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.artists-hero::after {
    content: '🎭';
    position: absolute;
    font-size: 18rem;
    opacity: .04;
    bottom: -3rem;
    left: -2rem;
    pointer-events: none;
    user-select: none;
    line-height: 1;
}
.artists-hero h1 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--color-accent);
    margin-bottom: 1.4rem;
    line-height: 1.25;
    position: relative;
}
.artists-hero p {
    color: rgba(255,255,255,.87);
    font-size: clamp(.98rem, 2.2vw, 1.15rem);
    max-width: 680px;
    margin: 0 auto 2.5rem;
    line-height: 1.9;
    position: relative;
}

/* ===== Benefits ===== */
.benefits-section { padding: 5rem 0; background: var(--color-bg); }
.benefit-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 2.2rem 1.75rem;
    box-shadow: var(--shadow);
    text-align: center;
    border-top: 3px solid var(--color-accent);
    transition: transform .2s, box-shadow .2s;
}
.benefit-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.benefit-card .icon { font-size: 2.8rem; margin-bottom: 1rem; display: block; }
.benefit-card h3 { font-size: 1.08rem; margin-bottom: .6rem; color: var(--color-primary); }
.benefit-card p  { color: var(--color-muted); font-size: .93rem; line-height: 1.8; }

/* ===== Steps ===== */
.steps-section { padding: 5rem 0; background: #fff; }
.steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    position: relative;
}
.steps-grid::before {
    content: '';
    position: absolute;
    top: 2.5rem;
    right: calc(12.5% + 1rem);
    left: calc(12.5% + 1rem);
    height: 2px;
    background: linear-gradient(to left, var(--color-accent), rgba(201,162,75,.2));
    z-index: 0;
}
.step-item { text-align: center; position: relative; z-index: 1; }
.step-num {
    width: 5rem;
    height: 5rem;
    border-radius: 50%;
    background: var(--color-accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    font-weight: 800;
    font-family: 'YekanBakh', Tahoma, sans-serif;
    margin: 0 auto 1rem;
    box-shadow: 0 4px 16px rgba(201,162,75,.35);
}
.step-item h3 { font-size: .96rem; color: var(--color-primary); line-height: 1.5; }

/* ===== Pricing ===== */
.artist-pricing { padding: 5rem 0; background: var(--color-bg); }
.price-table {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    max-width: 700px;
    margin: 0 auto;
}
.price-box {
    background: #fff;
    border-radius: var(--radius);
    padding: 2rem 1.75rem;
    box-shadow: var(--shadow);
    text-align: center;
    border: 1.5px solid transparent;
    transition: border-color .2s, transform .2s, box-shadow .2s;
}
.price-box:hover { border-color: var(--color-accent); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.price-box.featured { border-color: var(--color-accent); }
.price-box .pb-label {
    display: inline-block;
    background: var(--color-accent);
    color: #fff;
    font-size: .76rem;
    font-weight: 700;
    padding: .15rem .7rem;
    border-radius: 999px;
    margin-bottom: .85rem;
    font-family: 'YekanBakh', Tahoma, sans-serif;
}
.price-box .pb-label.secondary { background: var(--color-success); }
.price-box h3 { font-size: 1.05rem; color: var(--color-primary); margin-bottom: .4rem; }
.price-box .pb-amount {
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--color-primary);
    font-family: 'YekanBakh', Tahoma, sans-serif;
    margin: .5rem 0;
}
.price-box .pb-amount span { font-size: .8rem; font-weight: 400; color: var(--color-muted); }
.price-box .pb-note { color: var(--color-muted); font-size: .85rem; margin-bottom: 1.2rem; }
.price-box .pb-discount {
    display: inline-block;
    background: #d4edda;
    color: #155724;
    font-size: .78rem;
    font-weight: 700;
    padding: .15rem .6rem;
    border-radius: 999px;
    margin-bottom: .85rem;
}

/* ===== FAQ ===== */
.faq-section { padding: 5rem 0; background: #fff; }
.faq-list { max-width: 760px; margin: 0 auto; }
details {
    border: 1.5px solid rgba(31,42,68,.1);
    border-radius: var(--radius);
    margin-bottom: .85rem;
    overflow: hidden;
    transition: box-shadow .2s;
}
details[open] { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
details summary {
    padding: 1.1rem 1.4rem;
    font-weight: 600;
    color: var(--color-primary);
    font-size: .97rem;
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    user-select: none;
    transition: background .15s;
}
details summary:hover { background: var(--color-bg); }
details summary::-webkit-details-marker { display: none; }
details summary::after {
    content: '+';
    font-size: 1.3rem;
    color: var(--color-accent);
    font-weight: 700;
    flex-shrink: 0;
    margin-right: 1rem;
    transition: transform .2s;
}
details[open] summary::after { content: '−'; }
details .faq-body {
    padding: 1rem 1.4rem 1.2rem;
    color: var(--color-muted);
    font-size: .93rem;
    line-height: 1.85;
    border-top: 1px solid rgba(31,42,68,.07);
    background: #fafafa;
}

/* ===== Final CTA ===== */
.final-cta-artists {
    background: var(--color-accent);
    padding: 5.5rem 0;
    text-align: center;
}
.final-cta-artists h2 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(1.6rem, 4vw, 2.4rem);
    color: #fff;
    margin-bottom: 1rem;
}
.final-cta-artists p { color: rgba(255,255,255,.85); margin-bottom: 2rem; font-size: 1.05rem; }
.btn-white { background: #fff; color: var(--color-accent); font-weight: 700; }
.btn-white:hover { background: rgba(255,255,255,.9); color: var(--color-accent); text-decoration: none; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .steps-grid {
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
    }
    .steps-grid::before { display: none; }
    .price-table { grid-template-columns: 1fr; max-width: 400px; }
}
@media (max-width: 480px) {
    .artists-hero { padding: 4.5rem 0 3.5rem; }
    .steps-grid { grid-template-columns: 1fr; }
    .final-cta-artists { padding: 4rem 0; }
}
</style>
@endpush

@section('content')

{{-- ===== ۱. Hero ===== --}}
<section class="artists-hero">
    <div class="container">
        <h1>پروفایلت را بساز، دیده شو.</h1>
        <p>رزومه، نمونه‌کار و ویدیوی هنری خودت را در آوان قرار بده تا تیم‌های تولید در سراسر کشور بتوانند تو را برای پروژه‌ی بعدی‌شان انتخاب کنند.<br>
        <strong style="color:var(--color-accent)">{{ $categoryCount }} رشته‌ی تخصصی</strong> — از بازیگری و تصویربرداری تا موسیقی، انیمیشن و مدیریت تولید.</p>
        <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg">همین حالا ثبت‌نام کن</a>
    </div>
</section>

{{-- ===== ۲. مزایا ===== --}}
<section class="benefits-section">
    <div class="container">
        <h2 class="section-title">آوان برای هنرمندان چه می‌کند؟</h2>
        <p class="section-sub">همه‌ی ابزارهایی که برای دیده‌شدن نیاز داری در یک جا</p>
        <div class="grid-3">
            <div class="benefit-card">
                <span class="icon">🎭</span>
                <h3>پروفایل کامل</h3>
                <p>بیوگرافی، رزومه کاری و تصاویر نمونه‌کار در یک جا</p>
            </div>
            <div class="benefit-card">
                <span class="icon">🎬</span>
                <h3>ویدیوی ریل</h3>
                <p>بهترین لحظه‌های کارت را در ویدیوی ریل نمایش بده</p>
            </div>
            <div class="benefit-card">
                <span class="icon">🔍</span>
                <h3>دیده‌شدن در {{ $categoryCount }} رشته</h3>
                <p>تیم‌های تولید فعال در سراسر کشور پروفایل تو را می‌بینند — در هر رشته‌ای که هستی</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== ۳. مراحل ثبت‌نام ===== --}}
<section class="steps-section">
    <div class="container">
        <h2 class="section-title">مراحل ثبت‌نام</h2>
        <p class="section-sub">در چهار گام ساده وارد آوان شو و در مقابل تیم‌های تولید ظاهر شو</p>
        <div class="steps-grid">
            <div class="step-item">
                <div class="step-num">۱</div>
                <h3>ساخت حساب با ایمیل یا موبایل</h3>
            </div>
            <div class="step-item">
                <div class="step-num">۲</div>
                <h3>تکمیل پروفایل و آپلود نمونه‌کار</h3>
            </div>
            <div class="step-item">
                <div class="step-num">۳</div>
                <h3>انتخاب اشتراک ماهانه یا سالانه</h3>
            </div>
            <div class="step-item">
                <div class="step-num">۴</div>
                <h3>فعال‌شدن در فهرست و دیده‌شدن</h3>
            </div>
        </div>
    </div>
</section>

{{-- ===== ۴. تعرفه هنرمند ===== --}}
<section class="artist-pricing">
    <div class="container">
        <h2 class="section-title">تعرفه هنرمند</h2>
        <p class="section-sub">اشتراک ماهانه یا سالانه — هر کدام را که ترجیح می‌دهی انتخاب کن</p>
        <div class="price-table">
            <div class="price-box">
                <span class="pb-label">ماهانه</span>
                <h3>اشتراک ماهانه</h3>
                <div class="pb-amount">۹۹,۰۰۰ <span>تومان</span></div>
                <p class="pb-note">هر ماه تمدید می‌شود — هر زمان لغو کن</p>
                <a href="{{ route('pricing') }}" class="btn btn-outline btn-sm">مشاهده تعرفه کامل</a>
            </div>
            <div class="price-box featured">
                <span class="pb-label secondary">سالانه</span>
                <h3>اشتراک سالانه</h3>
                <div class="pb-amount">۸۹۰,۰۰۰ <span>تومان</span></div>
                <span class="pb-discount">۲۵٪ تخفیف نسبت به ماهانه</span>
                <p class="pb-note">یک‌بار پرداخت برای تمام سال</p>
                <a href="{{ route('pricing') }}" class="btn btn-accent btn-sm">مشاهده تعرفه کامل</a>
            </div>
        </div>
    </div>
</section>

{{-- ===== ۵. سوالات پرتکرار ===== --}}
<section class="faq-section">
    <div class="container">
        <h2 class="section-title">سوالات پرتکرار</h2>
        <p class="section-sub">پاسخ سوال‌هایی که هنرمندان بیشتر می‌پرسند</p>
        <div class="faq-list">
            <details>
                <summary>آیا ثبت‌نام اولیه رایگان است؟</summary>
                <div class="faq-body">
                    بله، ساخت حساب در آوان کاملاً رایگان است. برای فعال‌شدن پروفایل در فهرست کست و دیده‌شدن توسط تیم‌های تولید، باید یکی از اشتراک‌های ماهانه یا سالانه را انتخاب کنید.
                </div>
            </details>
            <details>
                <summary>چه نوع فایل‌هایی می‌توانم آپلود کنم؟</summary>
                <div class="faq-body">
                    می‌توانید تصاویر نمونه‌کار (JPG، PNG)، فیلم‌های کوتاه برای ریل (MP4 تا ۲۰۰ مگابایت) و فایل‌های صوتی (MP3) آپلود کنید. ویدیوها را همچنین می‌توانید از طریق لینک یوتیوب یا آپارات اضافه کنید.
                </div>
            </details>
            <details>
                <summary>تیم‌های تولید چطور مرا پیدا می‌کنند؟</summary>
                <div class="faq-body">
                    تیم‌های تولید از طریق موتور جستجوی آوان بر اساس رشته هنری، شهر، سابقه کاری و کلیدواژه جستجو می‌کنند. هرچه پروفایلت کامل‌تر باشد، در نتایج جستجو بیشتر دیده می‌شوی.
                </div>
            </details>
            <details>
                <summary>آیا می‌توانم اشتراکم را لغو کنم؟</summary>
                <div class="faq-body">
                    بله. اشتراک ماهانه را هر زمان از پنل کاربری‌ات می‌توانی لغو کنی. در صورت لغو، پروفایلت تا پایان دوره‌ی پرداختی فعال می‌ماند و پس از آن از فهرست کست حذف می‌شود.
                </div>
            </details>
        </div>
    </div>
</section>

{{-- ===== ۶. CTA پایانی ===== --}}
<section class="final-cta-artists">
    <div class="container">
        <h2>حالا آوانِ شما رسیده</h2>
        <p>پروفایلت را بساز، نمونه‌کارت را به نمایش بگذار و اجازه بده تیم‌های تولید تو را پیدا کنند.</p>
        <a href="{{ route('auth') }}?role=artist" class="btn btn-white btn-lg">همین حالا ثبت‌نام کن</a>
    </div>
</section>

@endsection
