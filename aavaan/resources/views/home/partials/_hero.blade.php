{{-- ===== ۱. Hero ===== --}}
<section class="home-hero" data-animate="hero">
    <div class="hero-bg-deco" data-hero-bg aria-hidden="true"></div>
    <div class="container">
        <h1 data-hero-title>{{ __('home.hero_title') }}</h1>
        <p data-hero-subtitle>{{ __('home.hero_subtitle') }}</p>
        <div class="hero-btns">
            <a href="{{ route('auth') }}?role=artist" class="btn btn-accent btn-lg" data-hero-cta data-magnetic>{{ __('home.cta_artist') }}</a>
            <a href="{{ route('auth') }}?role=production" class="btn btn-outline-white btn-lg" data-hero-cta data-magnetic>{{ __('home.cta_production') }}</a>
        </div>
    </div>
</section>
