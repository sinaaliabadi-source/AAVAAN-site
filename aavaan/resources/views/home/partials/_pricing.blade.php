{{-- ===== ۷. تعرفه (خلاصه) ===== --}}
<section class="pricing-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.pricing_title') }}</h2>
        <p class="section-sub">{{ __('home.pricing_subtitle') }}</p>

        @if(!empty($festivalActive))
        <div style="max-width:760px;margin:0 auto 1.75rem">
            <x-festival-banner
                title="تا پایان تابستان رایگان"
                message="در جشنوارهٔ آغازِ آوان، عضویت هنرمندان و دسترسی تیم‌های تولید رایگان است. قیمت‌های زیر پس از پایان جشنواره اعمال می‌شوند." />
        </div>
        @endif

        <div class="grid-2">
            <div class="pricing-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <span class="pc-badge">{{ __('home.pricing_artist_badge') }}</span>
                <h3>{{ __('home.pricing_artist_title') }}</h3>
                <p class="pc-desc">{{ __('home.pricing_artist_desc') }}</p>
                @if(!empty($festivalActive))
                    <div style="margin-bottom:.4rem"><span class="festival-badge">رایگان در جشنواره</span></div>
                    <div class="pc-price"><span class="festival-price-old">{{ __('home.pricing_artist_price') }}</span> <span>{{ __('home.pricing_artist_period') }}</span></div>
                @else
                    <div class="pc-price">{{ __('home.pricing_artist_price') }} <span>{{ __('home.pricing_artist_period') }}</span></div>
                @endif
                <div class="pc-btns">
                    <a href="{{ route('pricing') }}" class="btn btn-accent btn-sm">{{ __('home.pricing_view_full') }}</a>
                    <a href="{{ route('auth') }}?role=artist" class="btn btn-outline btn-sm">{{ __('home.pricing_register_free') }}</a>
                </div>
            </div>
            <div class="pricing-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <span class="pc-badge" style="background:var(--color-primary);color:#fff;">{{ __('home.pricing_production_badge') }}</span>
                <h3>{{ __('home.pricing_production_title') }}</h3>
                <p class="pc-desc">{{ __('home.pricing_production_desc') }}</p>
                @if(!empty($festivalActive))
                    <div style="margin-bottom:.4rem"><span class="festival-badge">رایگان در جشنواره</span></div>
                    <div class="pc-price"><span class="festival-price-old">{{ __('home.pricing_production_price') }}</span> <span>{{ __('home.pricing_production_period') }}</span></div>
                @else
                    <div class="pc-price">{{ __('home.pricing_production_price') }} <span>{{ __('home.pricing_production_period') }}</span></div>
                @endif
                <div class="pc-btns">
                    <a href="{{ route('pricing') }}" class="btn btn-primary btn-sm">{{ __('home.pricing_view_full') }}</a>
                    <a href="{{ route('auth') }}?role=production" class="btn btn-outline btn-sm">{{ __('home.pricing_production_login') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
