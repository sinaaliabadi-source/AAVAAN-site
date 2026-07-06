@extends('layouts.app')
@section('title', 'تعرفه‌ها — آوان')

@push('styles')
<style>
    /* ─── Page Hero ─── */
    .pricing-hero {
        background: var(--color-primary);
        color: #fff;
        text-align: center;
        padding: 4rem 1.5rem 3rem;
    }
    .pricing-hero h1 {
        font-size: 2.4rem;
        color: #fff;
        margin-bottom: .75rem;
    }
    .pricing-hero p {
        color: rgba(255,255,255,.75);
        font-size: 1.05rem;
        max-width: 540px;
        margin: 0 auto;
    }

    /* ─── Tab Switcher ─── */
    .tab-switcher {
        display: flex;
        justify-content: center;
        gap: 0;
        margin: 2.5rem auto 0;
        background: rgba(255,255,255,.12);
        border-radius: 50px;
        padding: 4px;
        width: fit-content;
    }
    .tab-btn {
        padding: .55rem 1.8rem;
        border-radius: 50px;
        border: none;
        background: transparent;
        color: rgba(255,255,255,.8);
        font-family: inherit;
        font-size: .95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, color .2s;
    }
    .tab-btn.active {
        background: var(--color-accent);
        color: #fff;
    }

    /* ─── Sections ─── */
    .pricing-section {
        padding: 4rem 0;
    }
    .pricing-section.hidden { display: none; }

    .section-heading {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .section-heading h2 {
        font-size: 1.8rem;
        margin-bottom: .5rem;
    }
    .section-heading p {
        color: var(--color-muted);
        font-size: .97rem;
    }

    /* ─── Pricing Cards ─── */
    .pricing-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        max-width: 780px;
        margin: 0 auto;
    }
    .pricing-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        max-width: 980px;
        margin: 0 auto;
    }
    .pricing-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        max-width: 1180px;
        margin: 0 auto;
    }

    .pricing-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 2.25rem 2rem;
        text-align: center;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        border: 2px solid transparent;
        position: relative;
        display: flex;
        flex-direction: column;
        transition: transform .2s, box-shadow .2s;
    }
    .pricing-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(0,0,0,.12);
    }
    .pricing-card.featured {
        border-color: var(--color-accent);
    }
    /* سطح سازمانی — قاب لاجوردی */
    .pricing-card.enterprise {
        border-color: var(--color-primary);
    }
    .pricing-card.enterprise .price-amount {
        color: var(--color-primary);
    }
    .pricing-card.featured-primary {
        border-color: var(--color-primary);
        background: var(--color-primary);
        color: #fff;
    }
    .pricing-card.featured-primary h3,
    .pricing-card.featured-primary .price-amount,
    .pricing-card.featured-primary .price-period {
        color: #fff;
    }
    .pricing-card.featured-primary .feature-item {
        color: rgba(255,255,255,.85);
    }
    .pricing-card.featured-primary .feature-item::before {
        color: var(--color-accent);
    }

    /* Badge */
    .pricing-badge {
        position: absolute;
        top: -13px;
        right: 50%;
        transform: translateX(50%);
        background: var(--color-accent);
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        padding: .25rem .9rem;
        border-radius: 50px;
        white-space: nowrap;
    }

    .pricing-card h3 {
        font-size: 1.2rem;
        margin-bottom: 1.25rem;
        color: var(--color-primary);
    }

    .price-block {
        margin-bottom: 1.5rem;
    }
    .price-amount {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--color-accent);
        font-family: 'YekanBakh', sans-serif;
        line-height: 1;
    }
    .price-period {
        font-size: .88rem;
        color: var(--color-muted);
        margin-top: .3rem;
    }

    .feature-list {
        list-style: none;
        text-align: right;
        margin-bottom: 1.75rem;
        flex: 1;
    }
    .feature-item {
        padding: .45rem 0;
        font-size: .92rem;
        color: var(--color-muted);
        border-bottom: 1px solid #f0ece4;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .feature-item:last-child { border-bottom: none; }
    .feature-item::before {
        content: '✓';
        color: var(--color-success);
        font-weight: 700;
        flex-shrink: 0;
    }

    .card-desc {
        font-size: .93rem;
        color: var(--color-muted);
        margin-bottom: 1.5rem;
        flex: 1;
        line-height: 1.7;
    }

    /* ─── Footer Note ─── */
    .pricing-note {
        text-align: center;
        padding: 2.5rem 1rem 3.5rem;
        color: var(--color-muted);
        font-size: .88rem;
        border-top: 1px solid #e5e0d6;
    }
    .pricing-note strong {
        color: var(--color-primary);
    }

    /* ─── Responsive ─── */
    @media (max-width: 980px) {
        .pricing-grid-4 { grid-template-columns: 1fr 1fr; max-width: 620px; }
    }
    @media (max-width: 780px) {
        .pricing-grid-3 { grid-template-columns: 1fr; max-width: 420px; }
    }
    @media (max-width: 640px) {
        .pricing-hero h1 { font-size: 1.8rem; }
        .pricing-grid-2 { grid-template-columns: 1fr; max-width: 420px; }
        .pricing-grid-3 { grid-template-columns: 1fr; max-width: 420px; }
        .pricing-grid-4 { grid-template-columns: 1fr; max-width: 420px; }
        .tab-btn { padding: .5rem 1.2rem; font-size: .88rem; }
    }
</style>
@endpush

@section('content')

{{-- ═══ PAGE HERO ═══ --}}
<section class="pricing-hero">
    <div class="container">
        <h1>تعرفه‌ها</h1>
        <p>پلانی انتخاب کنید که با مسیر هنری یا نیاز تولیدی شما هم‌خوانی دارد</p>

        @if($festivalActive)
        <div style="max-width:680px;margin:1.75rem auto 0">
            <x-festival-banner
                title="جشنوارهٔ آغاز — تا پایان تابستان رایگان"
                message="به مناسبت شروع به کار آوان، عضویت هنرمندان و دسترسی تیم‌های تولید تا پایان تابستان رایگان است. قیمت‌های زیر پس از پایان جشنواره اعمال می‌شوند." />
        </div>
        @endif

        <div class="tab-switcher" id="tabSwitcher">
            <button class="tab-btn active" data-target="artists" onclick="switchTab('artists')">
                هنرمندان
            </button>
            <button class="tab-btn" data-target="production" onclick="switchTab('production')">
                تیم‌های تولید
            </button>
        </div>
    </div>
</section>

{{-- ═══ SECTION A: هنرمندان ═══ --}}
<section class="pricing-section" id="section-artists">
    <div class="container">
        <div class="section-heading">
            <h2>تعرفه هنرمندان</h2>
            <p>دیده شوید — توسط تیم‌های تولیدی که واقعاً دنبال شما می‌گردند</p>
        </div>

        <div class="pricing-grid-2">

            {{-- اشتراک ماهانه --}}
            <div class="pricing-card">
                <h3>اشتراک ماهانه</h3>
                <div class="price-block">
                    @if($festivalActive)<span class="festival-badge" style="margin-bottom:.5rem;display:inline-block">رایگان در جشنواره</span>@endif
                    <div class="price-amount {{ $festivalActive ? 'festival-price-old' : '' }}">{{ number_format($monthlyPrice) }}</div>
                    <div class="price-period">تومان / ماه</div>
                </div>
                <ul class="feature-list">
                    <li class="feature-item">پروفایل کامل هنرمند</li>
                    <li class="feature-item">آپلود نمونه‌کار و ویدیو</li>
                    <li class="feature-item">حضور در نتایج جستجو</li>
                </ul>
                <a href="{{ route('auth') }}" class="btn btn-primary btn-block">شروع کنید</a>
            </div>

            {{-- اشتراک سالانه --}}
            <div class="pricing-card featured">
                <span class="pricing-badge">صرفه‌جویی بیشتر</span>
                <h3>اشتراک سالانه</h3>
                <div class="price-block">
                    @if($festivalActive)<span class="festival-badge" style="margin-bottom:.5rem;display:inline-block">رایگان در جشنواره</span>@endif
                    <div class="price-amount {{ $festivalActive ? 'festival-price-old' : '' }}">{{ number_format($yearlyPrice) }}</div>
                    <div class="price-period">تومان / سال</div>
                </div>
                <ul class="feature-list">
                    <li class="feature-item">پروفایل کامل هنرمند</li>
                    <li class="feature-item">آپلود نمونه‌کار و ویدیو</li>
                    <li class="feature-item">حضور در نتایج جستجو</li>
                    <li class="feature-item">اولویت نمایش در نتایج</li>
                </ul>
                <a href="{{ route('auth') }}" class="btn btn-accent btn-block">انتخاب سالانه</a>
            </div>

        </div>
    </div>
</section>

{{-- ═══ SECTION B: تیم‌های تولید ═══ --}}
<section class="pricing-section hidden" id="section-production">
    <div class="container">
        <div class="section-heading">
            <h2>تعرفه تیم‌های تولید</h2>
            <p>به فهرست کامل هنرمندان فیلترشده دسترسی پیدا کنید</p>
        </div>

        <div class="pricing-grid-4">

            {{-- دسترسی تکی --}}
            <div class="pricing-card">
                <h3>دسترسی تکی</h3>
                <div class="price-block">
                    @if($festivalActive)<span class="festival-badge" style="margin-bottom:.5rem;display:inline-block">رایگان در جشنواره</span>@endif
                    <div class="price-amount {{ $festivalActive ? 'festival-price-old' : '' }}">{{ number_format($singlePrice) }}</div>
                    <div class="price-period">تومان</div>
                </div>
                <p class="card-desc">باز کردن فهرست فیلترشده برای یک پروژه. مناسب برای کاستینگ‌های اتفاقی.</p>
                <a href="{{ route('auth') }}" class="btn btn-outline btn-block">شروع کنید</a>
            </div>

            {{-- بسته ۵ دسترسی --}}
            <div class="pricing-card">
                <h3>بسته ۵ دسترسی</h3>
                <div class="price-block">
                    @if($festivalActive)<span class="festival-badge" style="margin-bottom:.5rem;display:inline-block">رایگان در جشنواره</span>@endif
                    <div class="price-amount {{ $festivalActive ? 'festival-price-old' : '' }}">{{ number_format($bundle5Price) }}</div>
                    <div class="price-period">تومان</div>
                </div>
                <p class="card-desc">مناسب برای پروژه‌های متعدد. پنج بار دسترسی به فهرست‌های فیلترشده.</p>
                <a href="{{ route('auth') }}" class="btn btn-primary btn-block">انتخاب کنید</a>
            </div>

            {{-- بسته ۱۰ دسترسی --}}
            <div class="pricing-card featured-primary">
                <span class="pricing-badge">بهترین ارزش</span>
                <h3>بسته ۱۰ دسترسی</h3>
                <div class="price-block">
                    @if($festivalActive)<span class="festival-badge" style="margin-bottom:.5rem;display:inline-block">رایگان در جشنواره</span>@endif
                    <div class="price-amount {{ $festivalActive ? 'festival-price-old' : '' }}">{{ number_format($bundle10Price) }}</div>
                    <div class="price-period">تومان</div>
                </div>
                <p class="card-desc" style="color:rgba(255,255,255,.8)">بهترین قیمت برای استودیوها. ده دسترسی کامل برای پروژه‌های مختلف.</p>
                <a href="{{ route('auth') }}" class="btn btn-accent btn-block">انتخاب کنید</a>
            </div>

            {{-- سازمانی / پروژه بزرگ --}}
            <div class="pricing-card enterprise">
                <h3>سازمانی</h3>
                <div class="price-block">
                    <div class="price-amount" style="font-size:1.6rem">تماس بگیرید</div>
                    <div class="price-period">قیمت‌گذاری اختصاصی</div>
                </div>
                <p class="card-desc">برای پروژه‌های بزرگ با بیش از ۱۰ نفر کست و نیازهای سازمانی.</p>
                <ul class="feature-list">
                    <li class="feature-item">انتخاب بیش از ۱۰ هنرمند</li>
                    <li class="feature-item">پشتیبانی اختصاصی</li>
                    <li class="feature-item">مشاوره کستینگ</li>
                    <li class="feature-item">قرارداد سازمانی</li>
                    <li class="feature-item">فاکتور رسمی</li>
                </ul>
                <a href="{{ route('contact') }}" class="btn btn-primary btn-block">مشاوره رایگان</a>
            </div>

        </div>
    </div>
</section>

@if($festivalActive)
{{-- ═══ FAQ کوتاه جشنواره ═══ --}}
<section style="max-width:680px;margin:1rem auto 0;padding:0 1.5rem">
    <div style="background:#fff;border:1px solid #ece7dd;border-radius:14px;padding:1.5rem 1.75rem;box-shadow:0 2px 12px rgba(0,0,0,.05)">
        <h3 style="color:var(--color-primary);font-size:1.15rem;margin-bottom:.6rem">بعد از پایان جشنواره چه می‌شود؟</h3>
        <p style="color:var(--color-muted);font-size:.95rem;line-height:1.9;margin:0">
            با پایان جشنواره ({{ $festivalEndsFa }})، اشتراک رایگانِ جشنواره به‌پایان می‌رسد و برای ادامهٔ حضور،
            تهیهٔ یکی از پلن‌های بالا لازم است. نگران نباشید — <strong style="color:var(--color-primary)">هیچ مبلغی به‌صورت خودکار از شما کسر نمی‌شود</strong>؛
            انتخاب و پرداخت کاملاً با خودتان است.
        </p>
    </div>
</section>
@endif

{{-- ═══ FOOTER NOTE ═══ --}}
<div class="pricing-note">
    <strong>توجه:</strong> قیمت‌ها به تومان هستند. فاکتور رسمی پس از پرداخت صادر می‌شود.
</div>

@endsection

@push('scripts')
<script>
    function switchTab(target) {
        // Update buttons
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.toggle('active', btn.dataset.target === target);
        });
        // Show/hide sections
        document.getElementById('section-artists').classList.toggle('hidden', target !== 'artists');
        document.getElementById('section-production').classList.toggle('hidden', target !== 'production');
    }
</script>
@endpush
