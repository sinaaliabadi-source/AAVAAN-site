{{-- ===== ۵. رشته‌های هنری ===== --}}
<section class="fields-section">
    <div class="container">
        <h2 class="section-title">{{ __('home.fields_title') }}</h2>
        <p class="section-sub">
            {!! __('home.fields_subtitle', ['count' => '<span data-counter="'.$allCategories->count().'">'.$allCategories->count().'</span>']) !!}
        </p>
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
            <a href="{{ route('artists') }}" class="btn btn-outline btn-sm">{{ __('home.fields_link') }}</a>
        </div>
    </div>
</section>
