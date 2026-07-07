{{-- ===== ۶. هنرمندان حاضر در آوان (کروسل تصادفی) ===== --}}
{{-- نمونه‌ای تصادفی از هنرمندانِ فعال در قالب کروسل. هویت عمومی است: نام واقعی،
     رشتهٔ اصلی، شهر و لینک پروفایل نمایش داده می‌شود. تیک آبی فقط برای دارندگانش.
     اگر هیچ هنرمندی نباشد، این بخش اصلاً رندر نمی‌شود (بدون حالت خالی). --}}
@if($featuredArtists->isNotEmpty())
<section class="featured-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.featured_title') }}</h2>
        <p class="section-sub">گوشه‌ای از هنرمندانِ حاضر در آوان</p>

        <div class="swiper featured-swiper">
            <div class="swiper-wrapper">
                @foreach($featuredArtists as $artist)
                    @php
                        $primary   = $artist->user?->primarySpecialty;
                        $fieldName = $primary?->category?->name_fa ?? $artist->field;
                    @endphp
                    <div class="swiper-slide">
                        <div class="artist-card artist-card--featured" data-animate="artist-card">
                            <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
                            {{-- برای حفظ کستینگ ناشناس، نام واقعیِ هنرمندانِ بدون تیک آبی
                                 در هیچ جای HTML (حتی alt تصویر) نمی‌آید؛ فقط عکس و تخصص. --}}
                            <img src="{{ $artist->avatar_url }}"
                                 alt="{{ $artist->has_blue_tick ? $artist->user->name : ($fieldName ?? 'هنرمند') }}"
                                 data-card-img>
                            @if($artist->has_blue_tick)
                                {{-- هنرمند تیک‌آبی هویت عمومی دارد: نام واقعی + تیک نمایش داده می‌شود --}}
                                <h3 class="featured-name">
                                    {{ $artist->user->name }}
                                    <x-blue-tick :size="18" />
                                </h3>
                            @endif
                            @if($fieldName)
                                <p class="field-tag">{{ $fieldName }}</p>
                            @endif
                            @if($artist->city)
                                <p class="featured-city">📍 {{ $artist->city }}</p>
                            @endif
                            <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary btn-sm">{{ __('home.featured_view') }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .featured-name {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
    }
    .featured-city {
        font-size: .82rem;
        color: var(--color-muted);
        margin: .15rem 0 .6rem;
    }
    /* کروسل هنرمندان — فضای لازم برای فلش‌ها و pagination */
    .featured-swiper {
        padding: .5rem 0 3rem;
    }
    .featured-swiper .swiper-slide {
        height: auto;
        display: flex;
    }
    .featured-swiper .swiper-slide .artist-card {
        width: 100%;
    }
    /* رنگ فلش‌ها و pagination هماهنگ با برند (لاجوردی/طلایی) */
    .featured-swiper .swiper-button-prev,
    .featured-swiper .swiper-button-next {
        color: var(--color-primary);
    }
    .featured-swiper .swiper-button-prev:hover,
    .featured-swiper .swiper-button-next:hover {
        color: var(--color-accent);
    }
    .featured-swiper .swiper-pagination-bullet {
        background: var(--color-primary);
        opacity: .35;
    }
    .featured-swiper .swiper-pagination-bullet-active {
        background: var(--color-accent);
        opacity: 1;
    }
    @media (max-width: 640px) {
        .featured-swiper .swiper-button-prev,
        .featured-swiper .swiper-button-next {
            display: none;
        }
    }
</style>
@endpush
@endif
