{{-- ===== ۴. چگونه کار می‌کند — کارت‌های دوطرفه هنرمند / تیم تولید ===== --}}
<section class="how-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.how_title') }}</h2>
        <p class="section-sub">{{ __('home.how_subtitle') }}</p>
        <div class="how-cols">
            <div data-aos="fade-right" data-aos-duration="800">
                <div class="how-col-title">🎭 {{ __('home.how_artist') }}</div>
                <div class="how-step">
                    <div class="how-step-num">۱</div>
                    <p>{{ __('home.how_artist_1') }}</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۲</div>
                    <p>{{ __('home.how_artist_2') }}</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۳</div>
                    <p>{{ __('home.how_artist_3') }}</p>
                </div>
            </div>
            <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="150">
                <div class="how-col-title">🎬 {{ __('home.how_production') }}</div>
                <div class="how-step">
                    <div class="how-step-num">۱</div>
                    <p>{{ __('home.how_production_1') }}</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۲</div>
                    <p>{{ __('home.how_production_2') }}</p>
                </div>
                <div class="how-step">
                    <div class="how-step-num">۳</div>
                    <p>{{ __('home.how_production_3') }}</p>
                </div>
            </div>
        </div>
        <div class="how-link">
            <a href="{{ route('how-it-works') }}">{{ __('home.how_link') }}</a>
        </div>
    </div>
</section>
