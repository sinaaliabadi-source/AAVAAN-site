{{-- ===== ۶. هنرمندان برگزیده (داینامیک از دیتابیس) ===== --}}
<section class="featured-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.featured_title') }}</h2>
        <p class="section-sub">{{ __('home.featured_subtitle') }}</p>
        <div class="artists-grid">
            @forelse($featuredArtists as $artist)
                {{-- کستینگ ناشناس: این بخش کاملاً عمومی است و هیچ حالت unlocked ندارد،
                     پس همیشه شناسهٔ مستعار پایدار به‌جای نام واقعی نمایش داده می‌شود. --}}
                @php $aliasName = 'هنرمند #' . $artist->id; @endphp
                <div class="artist-card" data-animate="artist-card">
                    <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
                    <img src="{{ $artist->avatar_url }}" alt="{{ $aliasName }}" data-card-img>
                    <h3>{{ $aliasName }}</h3>
                    <p class="field-tag">{{ $artist->field }}</p>
                    <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary btn-sm">{{ __('home.featured_view') }}</a>
                </div>
            @empty
                <p class="artists-empty">{{ __('home.featured_empty') }}</p>
            @endforelse
        </div>
    </div>
</section>
