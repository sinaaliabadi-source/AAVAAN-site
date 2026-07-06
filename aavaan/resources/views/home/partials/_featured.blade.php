{{-- ===== ۶. هنرمندان برگزیده (دارندگان تیک آبی آوان) ===== --}}
{{-- هویت این هنرمندان عمومی است: نام واقعی، رشتهٔ اصلی، شهر و لینک پروفایل نمایش داده می‌شود.
     اگر هیچ هنرمند تیک‌آبی‌داری نباشد، این بخش اصلاً رندر نمی‌شود (بدون حالت خالی). --}}
@if($featuredArtists->isNotEmpty())
<section class="featured-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.featured_title') }}</h2>
        <p class="section-sub">هنرمندانِ برگزیدهٔ آوان — پروفایل‌های تأییدشده و مورد اعتماد</p>
        <div class="artists-grid">
            @foreach($featuredArtists as $artist)
                @php
                    $primary   = $artist->user?->primarySpecialty;
                    $fieldName = $primary?->category?->name_fa ?? $artist->field;
                @endphp
                <div class="artist-card artist-card--featured" data-animate="artist-card">
                    <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
                    <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}" data-card-img>
                    <h3 class="featured-name">
                        {{ $artist->user->name }}
                        <x-blue-tick :size="18" />
                    </h3>
                    @if($fieldName)
                        <p class="field-tag">{{ $fieldName }}</p>
                    @endif
                    @if($artist->city)
                        <p class="featured-city">📍 {{ $artist->city }}</p>
                    @endif
                    <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary btn-sm">{{ __('home.featured_view') }}</a>
                </div>
            @endforeach
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
</style>
@endpush
@endif
