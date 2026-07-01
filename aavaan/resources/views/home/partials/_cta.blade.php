{{-- ===== ۸. CTA نهایی ===== --}}
<section class="final-cta">
    <div class="container">
        <h2>{{ __('home.cta_title') }}</h2>
        <p>{{ __('home.cta_desc') }}</p>
        <div class="hero-btns">
            <a href="{{ route('auth') }}?role=artist" class="btn btn-white btn-lg" data-magnetic>{{ __('home.cta_artist') }}</a>
            <a href="{{ route('auth') }}?role=production" class="btn btn-outline-white btn-lg" data-magnetic>{{ __('home.cta_production') }}</a>
        </div>
    </div>
</section>
