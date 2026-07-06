{{-- ===== ۱. Hero — اسلایدر Swiper ===== --}}
<section class="home-hero" data-animate="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">

            @if(!empty($festivalActive))
            {{-- اسلاید جشنوارهٔ افتتاح — فقط در دورهٔ جشنواره رندر می‌شود --}}
            <div class="swiper-slide hero-slide hero-slide--festival">
                <div class="container hero-slide-inner">
                    <div class="hero-festival-badge" aria-hidden="true">🎉 جشنوارهٔ آغاز</div>
                    <h1>جشنوارهٔ آغاز — آوانِ رایگانِ شما</h1>
                    <p>به مناسبت شروع به کار آوان، تا پایان تابستان عضویت هنرمندان و دسترسی تیم‌های تولید رایگان است.</p>
                    <div class="hero-btns">
                        <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg" data-magnetic>ثبت‌نام رایگان هنرمند</a>
                        <a href="{{ route('pricing') }}" class="btn btn-outline-white btn-lg" data-magnetic>جزئیات جشنواره</a>
                    </div>
                </div>
            </div>
            @endif

            {{-- اسلاید ۱ — معرفی آوان --}}
            <div class="swiper-slide hero-slide hero-slide--intro">
                <div class="container hero-slide-inner">
                    <img src="{{ asset('images/logo-light.webp') }}" alt="آوان"
                         class="hero-logo" data-aos="zoom-in" data-aos-duration="600">
                    <h1 data-hero-title>{{ __('home.hero_title') }}</h1>
                    <p data-hero-subtitle>آوان جایی‌ست که عوامل و هنرمندان سینما، تئاتر، موسیقی پروفایل می‌سازند</p>
                    <div class="hero-btns">
                        <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg" data-magnetic>{{ __('home.cta_artist') }}</a>
                        <a href="{{ route('auth') }}?role=production" class="btn btn-outline-white btn-lg" data-magnetic>{{ __('home.cta_production') }}</a>
                    </div>
                </div>
            </div>

            {{-- اسلاید ۲ — برای هنرمندان --}}
            <div class="swiper-slide hero-slide hero-slide--artists">
                <div class="container hero-slide-inner">
                    <svg class="hero-motif" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12 2l2.6 6.9L22 9.3l-5.4 4.7L18.2 22 12 17.8 5.8 22l1.6-8L2 9.3l7.4-.4L12 2z"
                              fill="#C9A24B"/>
                    </svg>
                    <h1>پروفایلت را بساز، دیده شو</h1>
                    <p>رزومه و نمونه‌کار هنری خودت را در آوان قرار بده</p>
                    <div class="hero-btns">
                        <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg" data-magnetic>شروع کن</a>
                    </div>
                </div>
            </div>

            {{-- اسلاید ۳ — هنرباز --}}
            <div class="swiper-slide hero-slide hero-slide--honarbaz">
                <div class="container hero-slide-inner">
                    <div class="hero-motif hero-motif--emoji" aria-hidden="true">🎭</div>
                    <h1>هنرباز — استعدادیابی کودکان ایران</h1>
                    <p>هر کودک یک استعداد دارد، هر روستا یک داستان</p>
                    <div class="hero-btns">
                        <a href="{{ route('honarbaz.landing') }}" class="btn btn-accent btn-lg" data-magnetic>ثبت‌نام رایگان</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

@push('styles')
<style>
    /* ===== Hero Swiper ===== */
    .home-hero {
        padding: 0;
        position: relative;
        overflow: hidden;
    }
    .home-hero .hero-swiper { width: 100%; }
    .hero-slide {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero-slide::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23C9A24B' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }
    /* پس‌زمینه هر اسلاید */
    .hero-slide--intro    { background: linear-gradient(135deg, #1F2A44 0%, #0d1a2e 100%); }
    .hero-slide--artists  { background: linear-gradient(135deg, #1a1a2e 0%, #1F2A44 100%); }
    .hero-slide--honarbaz { background: linear-gradient(135deg, #7a5a1a 0%, #1F2A44 100%); }
    .hero-slide--festival { background: linear-gradient(135deg, #23304f 0%, #1F2A44 55%, #3a2f14 100%); }

    .hero-festival-badge {
        display: inline-block;
        background: #C9A24B;
        color: #1F2A44;
        font-family: 'YekanBakh', Tahoma, sans-serif;
        font-weight: 800;
        font-size: .95rem;
        padding: .35rem 1.1rem;
        border-radius: 999px;
        margin-bottom: 1.4rem;
        box-shadow: 0 4px 16px rgba(201,162,75,.35);
    }

    .hero-slide-inner { position: relative; z-index: 1; }

    .hero-logo {
        width: 220px;
        height: auto;
        margin: 0 auto 24px;
        display: block;
    }
    .hero-motif {
        width: 76px;
        height: 76px;
        margin: 0 auto 1.5rem;
        display: block;
        filter: drop-shadow(0 4px 14px rgba(201,162,75,.35));
    }
    .hero-motif--emoji {
        font-size: 4rem;
        line-height: 1;
        width: auto;
        height: auto;
        filter: none;
    }

    .home-hero h1 {
        font-family: 'YekanBakh', Tahoma, sans-serif;
        font-weight: 900;
        font-size: clamp(2rem, 5vw, 3.4rem);
        color: var(--color-accent);
        margin-bottom: 1.5rem;
        line-height: 1.25;
    }
    .home-hero p {
        color: rgba(255,255,255,.88);
        font-size: clamp(1rem, 2.5vw, 1.2rem);
        max-width: 700px;
        margin: 0 auto 2.5rem;
        line-height: 1.9;
    }
    .hero-btns {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Pagination dots — طلایی روی پس‌زمینه تیره */
    .home-hero .swiper-pagination { bottom: 2rem; }
    .home-hero .swiper-pagination-bullet {
        width: 11px;
        height: 11px;
        background: rgba(201,162,75,.4);
        opacity: 1;
        transition: transform .25s, background .25s;
    }
    .home-hero .swiper-pagination-bullet-active {
        background: #C9A24B;
        transform: scale(1.25);
    }

    @media (max-width: 640px) {
        .hero-logo { width: 100%; max-width: 280px; }
    }
</style>
@endpush
