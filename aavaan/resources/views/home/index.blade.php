@extends('layouts.app')
@section('title', 'خانه')
@section('meta-description', 'آوان — پلتفرم تخصصی کاستینگ هنرمندان ایران. پروفایل بسازید، دیده شوید، تیم بسازید.')

@push('styles')
<style>
/* ===== Hero ===== */
.home-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 7rem 0 6rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.home-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23C9A24B' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.hero-bg-deco {
    position: absolute;
    inset: -10% 0 auto 0;
    height: 130%;
    background: radial-gradient(circle at 30% 20%, rgba(201,162,75,.14), transparent 55%),
                radial-gradient(circle at 75% 60%, rgba(201,162,75,.10), transparent 50%);
    pointer-events: none;
    will-change: transform;
}
.home-hero h1 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(2rem, 5vw, 3.4rem);
    color: var(--color-accent);
    margin-bottom: 1.5rem;
    line-height: 1.25;
    position: relative;
}
.home-hero p {
    color: rgba(255,255,255,.88);
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    max-width: 700px;
    margin: 0 auto 2.5rem;
    line-height: 1.9;
    position: relative;
}
.hero-btns {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
}
.btn-outline-white {
    background: transparent;
    border: 2px solid rgba(255,255,255,.6);
    color: #fff;
    font-weight: 600;
}
.btn-outline-white:hover {
    background: rgba(255,255,255,.12);
    border-color: #fff;
    color: #fff;
    text-decoration: none;
}
.btn-white {
    background: #fff;
    color: var(--color-accent);
    font-weight: 700;
}
.btn-white:hover {
    background: rgba(255,255,255,.9);
    color: var(--color-accent);
    text-decoration: none;
}

/* ===== Section shared ===== */
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
}

/* ===== Why Aavaan ===== */
.why-section {
    padding: 5rem 0;
    background: var(--color-bg);
}
.why-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 2.2rem 1.75rem;
    box-shadow: var(--shadow);
    text-align: center;
    border-top: 3px solid var(--color-accent);
    transition: transform .2s, box-shadow .2s;
}
.why-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.why-card .icon { font-size: 2.8rem; margin-bottom: 1rem; display: block; }
.why-card h3 { font-size: 1.08rem; margin-bottom: .6rem; color: var(--color-primary); }
.why-card p  { color: var(--color-muted); font-size: .93rem; line-height: 1.8; }

/* ===== How It Works ===== */
.how-section {
    background: var(--color-primary);
    padding: 5rem 0;
    color: #fff;
}
.how-section .section-title { color: var(--color-accent); }
.how-section .section-sub  { color: rgba(255,255,255,.6); }
.how-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
.how-col-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-accent);
    margin-bottom: 1.4rem;
    padding-bottom: .6rem;
    border-bottom: 1px solid rgba(201,162,75,.25);
}
.how-step {
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    margin-bottom: 1.2rem;
}
.how-step-num {
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: var(--color-accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .85rem;
    font-family: 'YekanBakh', Tahoma, sans-serif;
}
.how-step p { color: rgba(255,255,255,.82); font-size: .94rem; margin: 0; padding-top: .25rem; line-height: 1.7; }
.how-link { text-align: center; margin-top: 2.5rem; }
.how-link a { color: var(--color-accent); font-weight: 600; font-size: .95rem; }
.how-link a:hover { text-decoration: underline; }

/* ===== Fields / Categories ===== */
.fields-section { padding: 4.5rem 0; background: #fff; }
.cat-groups-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    max-width: 860px;
    margin: 0 auto 1.75rem;
}
.cat-group-card {
    background: var(--color-bg);
    border-radius: var(--radius);
    padding: 1.4rem 1.5rem;
    border: 1.5px solid rgba(31,42,68,.1);
}
.cat-group-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: .9rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: .85rem;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.cat-pills { display: flex; flex-wrap: wrap; gap: .45rem; }
.cat-pill {
    display: inline-block;
    padding: .3rem .85rem;
    border-radius: 999px;
    background: #fff;
    color: var(--color-primary);
    font-size: .82rem;
    font-weight: 600;
    border: 1.5px solid rgba(31,42,68,.12);
    transition: background .2s, color .2s, border-color .2s;
}
.cat-pill:hover { background: var(--color-accent); color: #fff; border-color: var(--color-accent); }

/* ===== Featured Artists ===== */
.featured-section { padding: 5rem 0; background: var(--color-bg); }
.artists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}
.artist-card {
    position: relative;
    background: #fff;
    border-radius: var(--radius);
    padding: 1.6rem 1rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: transform .2s, box-shadow .2s;
}
.artist-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.artist-card-overlay {
    position: absolute;
    inset: 0;
    border-radius: var(--radius);
    background: linear-gradient(160deg, rgba(201,162,75,.12), rgba(31,42,68,.06));
    opacity: 0;
    pointer-events: none;
}
.artist-card img {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto .85rem;
    border: 3px solid var(--color-accent);
    display: block;
}
.artist-card h3 { font-size: .97rem; color: var(--color-primary); margin-bottom: .3rem; }
.artist-card .field-tag { color: var(--color-accent); font-size: .84rem; font-weight: 600; margin-bottom: .9rem; }
.artists-empty { text-align: center; color: var(--color-muted); padding: 3rem 0; grid-column: 1 / -1; font-size: 1rem; }

/* ===== Pricing Summary ===== */
.pricing-section { padding: 5rem 0; background: #fff; }
.pricing-card {
    background: var(--color-bg);
    border-radius: var(--radius);
    padding: 2.2rem 2rem;
    border: 1.5px solid rgba(31,42,68,.1);
    transition: transform .2s, box-shadow .2s;
}
.pricing-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.pricing-card .pc-badge {
    display: inline-block;
    background: var(--color-accent);
    color: #fff;
    font-size: .78rem;
    font-weight: 700;
    padding: .2rem .75rem;
    border-radius: 999px;
    margin-bottom: 1rem;
    font-family: 'YekanBakh', Tahoma, sans-serif;
}
.pricing-card h3 { font-size: 1.2rem; color: var(--color-primary); margin-bottom: .5rem; }
.pricing-card .pc-desc { color: var(--color-muted); font-size: .91rem; margin-bottom: 1.4rem; line-height: 1.8; }
.pricing-card .pc-price { font-size: 1.45rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.2rem; font-family: 'YekanBakh', Tahoma, sans-serif; }
.pricing-card .pc-price span { font-size: .82rem; font-weight: 400; color: var(--color-muted); }
.pricing-card .pc-btns { display: flex; gap: .75rem; flex-wrap: wrap; }

/* ===== Final CTA ===== */
.final-cta {
    background: var(--color-accent);
    padding: 5.5rem 0;
    text-align: center;
}
.final-cta h2 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(1.6rem, 4vw, 2.4rem);
    color: #fff;
    margin-bottom: 1rem;
}
.final-cta p { color: rgba(255,255,255,.85); margin-bottom: 2rem; font-size: 1.05rem; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .how-cols { grid-template-columns: 1fr; gap: 2rem; }
    .artists-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    .pricing-card .pc-btns { flex-direction: column; }
    .cat-groups-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .home-hero { padding: 4.5rem 0 3.5rem; }
    .final-cta { padding: 4rem 0; }
}
</style>
@endpush

@section('content')

{{-- ===== ۱. Hero ===== --}}
<section class="home-hero" data-animate="hero">
    <div class="hero-bg-deco" data-hero-bg aria-hidden="true"></div>
    <div class="container">
        <h1 data-hero-title>هر هنرمندی، آوانی برای درخشیدن دارد.</h1>
        <p data-hero-subtitle>آوان جایی‌ست که عوامل و هنرمندانِ سینما، تئاتر، موسیقی و دیگر هنرها پروفایل خود را می‌سازند و تیم‌های تولید، دقیق‌ترین فهرست کست را از میان آن‌ها پیدا می‌کنند.</p>
        <div class="hero-btns">
            <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg" data-hero-cta data-magnetic>ثبت‌نام به‌عنوان هنرمند</a>
            <a href="{{ route('auth') }}?role=production" class="btn btn-outline-white btn-lg" data-hero-cta data-magnetic>ورود برای تیم‌های تولید</a>
        </div>
    </div>
</section>

{{-- ===== ۲. چرا آوان ===== --}}
<section class="why-section">
    <div class="container">
        <h2 class="section-title">چرا آوان؟</h2>
        <p class="section-sub">سه دلیل که هنرمندان و تیم‌های تولید آوان را انتخاب می‌کنند</p>
        <div class="grid-3">
            <div class="why-card" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
                <span class="icon">🎭</span>
                <h3>پروفایل کامل و نمونه‌کار</h3>
                <p>از تصویر و بیوگرافی تا ویدیوی ریل — همه چیز در یک پروفایل</p>
            </div>
            <div class="why-card" data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                <span class="icon">🔍</span>
                <h3>دیده‌شدن توسط تیم‌های تولید</h3>
                <p>هزاران کارگردان، تهیه‌کننده و مدیر تولید جستجو می‌کنند</p>
            </div>
            <div class="why-card" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
                <span class="icon">🌐</span>
                <h3>برای همه‌ی رشته‌های هنری</h3>
                <p>سینما، تئاتر، موسیقی، طراحی صحنه، گریم و بیشتر</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== ۳. چگونه کار می‌کند ===== --}}
<section class="how-section">
    <div class="container">
        <h2 class="section-title">چگونه کار می‌کند؟</h2>
        <p class="section-sub">مسیر ساده برای هنرمندان و تیم‌های تولید</p>
        <div class="how-cols">
            <div data-aos="fade-right" data-aos-duration="800">
                <div class="how-col-title">🎭 هنرمند</div>
                <div class="how-step">
                    <div class="how-step-num">۱</div>
                    <p>ثبت‌نام رایگان با ایمیل یا شماره موبایل</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۲</div>
                    <p>تکمیل پروفایل، آپلود نمونه‌کار و ویدیوی ریل</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۳</div>
                    <p>انتخاب اشتراک و فعال‌شدن در فهرست کست</p>
                </div>
            </div>
            <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="150">
                <div class="how-col-title">🎬 تیم تولید</div>
                <div class="how-step">
                    <div class="how-step-num">۱</div>
                    <p>ساخت حساب تیم تولید در چند دقیقه</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۲</div>
                    <p>جستجو و فیلتر هنرمندان بر اساس رشته، شهر و سابقه</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۳</div>
                    <p>پرداخت برای دسترسی و تماس مستقیم با هنرمندان</p>
                </div>
            </div>
        </div>
        <div class="how-link">
            <a href="/how-it-works">راهنمای کامل نحوه کار آوان ←</a>
        </div>
    </div>
</section>

{{-- ===== ۴. رشته‌های هنری ===== --}}
<section class="fields-section">
    <div class="container">
        <h2 class="section-title">رشته‌های هنری</h2>
        <p class="section-sub">آوان همه‌ی <span data-counter="{{ $allCategories->count() }}">{{ $allCategories->count() }}</span> حوزه‌ی هنری را پوشش می‌دهد</p>
        <div class="cat-groups-grid">
            @foreach($categoryGroups as $group)
            <div class="cat-group-card">
                <div class="cat-group-title">
                    {{ $group['icon'] }} {{ $group['label'] }}
                </div>
                <div class="cat-pills">
                    @foreach($group['categories'] as $cat)
                        <span class="cat-pill">{{ $cat->name_fa }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <div style="text-align:center">
            <a href="{{ route('artists') }}" class="btn btn-outline btn-sm">مشاهده همه رشته‌ها ←</a>
        </div>
    </div>
</section>

{{-- ===== ۵. هنرمندان برگزیده ===== --}}
<section class="featured-section">
    <div class="container">
        <h2 class="section-title">هنرمندان برگزیده</h2>
        <p class="section-sub">نمونه‌ای از پروفایل‌های فعال در آوان</p>
        <div class="artists-grid">
            @forelse($featuredArtists as $artist)
                <div class="artist-card" data-animate="artist-card">
                    <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
                    <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}" data-card-img>
                    <h3>{{ $artist->user->name }}</h3>
                    <p class="field-tag">{{ $artist->field }}</p>
                    <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary btn-sm">مشاهده پروفایل</a>
                </div>
            @empty
                <p class="artists-empty">به زودی پروفایل هنرمندان نمایش داده می‌شود.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== ۶. تعرفه (خلاصه) ===== --}}
<section class="pricing-section">
    <div class="container">
        <h2 class="section-title">تعرفه‌ها</h2>
        <p class="section-sub">ساده، شفاف و مناسب برای همه</p>
        <div class="grid-2">
            <div class="pricing-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <span class="pc-badge">هنرمند</span>
                <h3>اشتراک هنرمند</h3>
                <p class="pc-desc">پروفایل کامل، نمونه‌کار، ویدیوی ریل و حضور در فهرست کست تیم‌های تولید سراسر کشور.</p>
                <div class="pc-price">از ۹۹,۰۰۰ تومان <span>/ ماهانه</span></div>
                <div class="pc-btns">
                    <a href="{{ route('pricing') }}" class="btn btn-accent btn-sm">مشاهده تعرفه کامل</a>
                    <a href="{{ route('auth') }}?role=artist" class="btn btn-outline btn-sm">ثبت‌نام رایگان</a>
                </div>
            </div>
            <div class="pricing-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <span class="pc-badge" style="background:var(--color-primary);">تیم تولید</span>
                <h3>بسته‌های دسترسی تولید</h3>
                <p class="pc-desc">جستجو رایگان — فقط برای دسترسی به اطلاعات تماس و فهرست نهایی کست هزینه بدهید.</p>
                <div class="pc-price">پرداخت به‌ازای دسترسی <span>/ هر پروژه</span></div>
                <div class="pc-btns">
                    <a href="{{ route('pricing') }}" class="btn btn-primary btn-sm">مشاهده تعرفه کامل</a>
                    <a href="{{ route('auth') }}?role=production" class="btn btn-outline btn-sm">ورود تیم تولید</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== ۷. CTA نهایی ===== --}}
<section class="final-cta">
    <div class="container">
        <h2>آوانِ شما رسیده — همین حالا شروع کنید</h2>
        <p>هنرمند هستید یا تیم تولید؟ هر دو راه به آوان ختم می‌شود.</p>
        <div class="hero-btns">
            <a href="{{ route('auth') }}?role=artist" class="btn btn-white btn-lg" data-magnetic>ثبت‌نام به‌عنوان هنرمند</a>
            <a href="{{ route('auth') }}?role=production" class="btn btn-outline-white btn-lg" data-magnetic>ورود برای تیم‌های تولید</a>
        </div>
    </div>
</section>

@endsection
