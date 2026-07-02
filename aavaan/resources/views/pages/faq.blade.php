@extends('layouts.app')
@section('title', 'سوالات متداول — آوان')
@section('meta-description', 'پاسخ سوالات پرتکرار هنرمندان و تیم‌های تولید درباره آوان')

@push('styles')
<style>
.faq-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 4rem 0 3.5rem;
    text-align: center;
}
.faq-hero-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 800;
    color: var(--color-accent);
    margin-bottom: .6rem;
}
.faq-hero-sub { color: #c8d0e0; font-size: .95rem; }

.faq-body { padding: 4rem 0 5rem; }
.faq-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
.faq-section-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--color-primary);
    margin-bottom: 1.2rem;
    padding-right: 1rem;
    border-right: 4px solid var(--color-accent);
    display: flex;
    align-items: center;
    gap: .5rem;
}
details.faq-item {
    background: #fff;
    border-radius: var(--radius);
    border: 1px solid #ede8dc;
    margin-bottom: .55rem;
    overflow: hidden;
}
details.faq-item summary {
    cursor: pointer;
    padding: 1rem 1.1rem;
    font-weight: 600;
    font-size: .9rem;
    color: var(--color-primary);
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
    user-select: none;
}
details.faq-item summary::-webkit-details-marker { display: none; }
details.faq-item summary::after {
    content: '＋';
    font-size: 1.1rem;
    color: var(--color-accent);
    flex-shrink: 0;
    transition: transform .2s;
}
details.faq-item[open] summary::after { content: '−'; }
details.faq-item[open] summary { border-bottom: 1px solid #f0ede8; }
.faq-answer {
    padding: .85rem 1.1rem 1.1rem;
    font-size: .88rem;
    color: var(--color-muted);
    line-height: 1.9;
}

.faq-cta {
    background: var(--color-primary);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    margin-top: 3rem;
}
.faq-cta p { color: #c8d0e0; font-size: .92rem; margin-bottom: 1rem; }

@media (max-width: 820px) {
    .faq-columns { grid-template-columns: 1fr; gap: 2rem; }
}
</style>
@endpush

@section('content')

<section class="faq-hero">
    <div class="container">
        <h1 class="faq-hero-title">سوالات متداول</h1>
        <p class="faq-hero-sub">پاسخ سریع سوال‌های رایج — برای هنرمندان و تیم‌های تولید</p>
    </div>
</section>

<section class="faq-body">
    <div class="container">
        <div class="faq-columns">

            {{-- سوالات هنرمندان --}}
            <div>
                <div class="faq-section-title">🎭 هنرمندان</div>

                <details class="faq-item">
                    <summary>برای ثبت‌نام در آوان به چه چیزی نیاز دارم؟</summary>
                    <div class="faq-answer">فقط یک آدرس ایمیل یا شماره موبایل کافی است. بعد از ثبت‌نام، پروفایل خود را تکمیل کنید و نمونه‌کارتان را آپلود کنید.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا ثبت‌نام در آوان رایگان است؟</summary>
                    <div class="faq-answer">ثبت‌نام اولیه رایگان است. برای فعال‌شدن در فهرست و دیده‌شدن توسط تیم‌های تولید، نیاز به خرید اشتراک ماهانه یا سالانه دارید.</div>
                </details>

                <details class="faq-item">
                    <summary>چه نوع فایل‌هایی می‌توانم آپلود کنم؟</summary>
                    <div class="faq-answer">تصاویر پروفایل و نمونه‌کار در فرمت JPG، PNG یا WebP تا سقف ۵ مگابایت. ویدیوی ریل تا ۱۰۰ مگابایت پشتیبانی می‌شود.</div>
                </details>

                <details class="faq-item">
                    <summary>تیم‌های تولید چه اطلاعاتی از من می‌بینند؟</summary>
                    <div class="faq-answer">همه کاربران نام، رشته هنری و عکس شما را می‌بینند. اطلاعات تماس (موبایل و ایمیل) فقط برای تیم‌های تولیدی که دسترسی خریده‌اند نمایش داده می‌شود.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا آوان از قراردادهایم درصد می‌گیرد؟</summary>
                    <div class="faq-answer">خیر. آوان فقط از فروش اشتراک هنرمندان و بسته‌های دسترسی تیم تولید درآمد دارد. از قراردادهای بین هنرمند و تیم تولید هیچ کارمزدی دریافت نمی‌شود.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا می‌توانم اشتراکم را لغو کنم؟</summary>
                    <div class="faq-answer">بله، در هر زمان می‌توانید اشتراک را تمدید نکنید. پس از پایان دوره اشتراک، پروفایل شما از نتایج جستجو حذف می‌شود اما اطلاعاتتان حفظ می‌ماند.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا می‌توانم چند رشته هنری داشته باشم؟</summary>
                    <div class="faq-answer">در حال حاضر می‌توانید یک رشته اصلی انتخاب کنید. در بیوگرافی خود می‌توانید رشته‌های جانبی را هم توضیح دهید تا تیم‌های تولید بهتر شما را پیدا کنند.</div>
                </details>
            </div>

            {{-- سوالات تیم‌های تولید --}}
            <div>
                <div class="faq-section-title">🎬 تیم‌های تولید</div>

                <details class="faq-item">
                    <summary>چطور هنرمندان را جستجو کنم؟</summary>
                    <div class="faq-answer">بعد از ثبت‌نام، می‌توانید بر اساس رشته هنری، شهر، بازه سنی، سابقه و کلیدواژه فیلتر کنید. نتایج را می‌بینید اما برای دیدن اطلاعات کامل باید دسترسی بخرید.</div>
                </details>

                <details class="faq-item">
                    <summary>مدل پرداخت برای تیم تولید چطور کار می‌کند؟</summary>
                    <div class="faq-answer">پرداخت به‌ازای استفاده: یک دسترسی تکی یا بسته‌های چندتایی (۵ یا ۱۰ دسترسی) بخرید. هر بار که پروفایل کاملی را باز می‌کنید، یک اعتبار استفاده می‌شود.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا می‌توانم قبل از پرداخت پروفایل هنرمندان را ببینم؟</summary>
                    <div class="faq-answer">بله، نام، رشته هنری و عکس همه هنرمندان قابل مشاهده است. اطلاعات تماس، بیوگرافی کامل و ویدیوی ریل فقط بعد از خرید دسترسی نمایش داده می‌شود.</div>
                </details>

                <details class="faq-item">
                    <summary>اعتبار خریداری‌شده تا کِی معتبر است؟</summary>
                    <div class="faq-answer">بسته‌های دسترسی تیم تولید بدون تاریخ انقضا هستند. می‌توانید در هر پروژه‌ای از آن‌ها استفاده کنید.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا می‌توانم هنرمندان را ذخیره کنم؟</summary>
                    <div class="faq-answer">بله، بعد از باز کردن پروفایل هنرمند، می‌توانید آن را در فهرست ذخیره‌شده خود نگه دارید و در هر زمان به آن دسترسی داشته باشید.</div>
                </details>

                <details class="faq-item">
                    <summary>آیا برای هر پروژه جداگانه باید پرداخت کنم؟</summary>
                    <div class="faq-answer">نه. اعتبارهایی که خریداری کرده‌اید برای هر پروژه‌ای که نیاز داشته باشید قابل استفاده است. بسته‌های چندتایی بهترین گزینه برای تیم‌هایی با پروژه‌های متعدد است.</div>
                </details>

                <details class="faq-item">
                    <summary>اگر اطلاعات هنرمندی قدیمی یا نادرست بود چه کار کنم؟</summary>
                    <div class="faq-answer">هنرمندانی که اشتراک فعال ندارند از نتایج جستجو حذف می‌شوند. اگر اطلاعاتی قدیمی یا نادرست است، از طریق فرم <a href="{{ route('contact') }}">تماس با ما</a> اطلاع دهید.</div>
                </details>
            </div>
        </div>

        <div class="faq-cta">
            <p>سوالتان اینجا نبود؟ مستقیم با ما در میان بگذارید.</p>
            <a href="{{ route('contact') }}" class="btn btn-accent btn-sm">تماس با پشتیبانی</a>
        </div>
    </div>
</section>

@endsection
