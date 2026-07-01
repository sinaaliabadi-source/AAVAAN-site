{{-- ===== ۶. هنرمندان برگزیده (داینامیک از دیتابیس) ===== --}}
<section class="featured-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.featured_title') }}</h2>
        <p class="section-sub">{{ __('home.featured_subtitle') }}</p>
        <div class="artists-grid">
            @forelse($featuredArtists as $artist)
                <div class="artist-card" data-animate="artist-card">
                    <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
                    <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}" data-card-img>
                    <h3>{{ $artist->user->name }}</h3>
                    <p class="field-tag">{{ $artist->field }}</p>
                    <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary btn-sm">{{ __('home.featured_view') }}</a>
                </div>
            @empty
                <p class="artists-empty">{{ __('home.featured_empty') }}</p>
            @endforelse
        </div>
    </div>
</section>
