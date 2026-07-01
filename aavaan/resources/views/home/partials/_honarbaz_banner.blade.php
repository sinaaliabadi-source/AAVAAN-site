@if(!empty($honarbaz))
{{-- بنر اعلان هنرباز --}}
<section class="honarbaz-banner">
    <div class="container hb-banner-inner">
        <div class="hb-banner-text">
            <span class="hb-banner-icon">🎭</span>
            <div>
                <h2>هنرباز — استعدادیابی کودکان ایران</h2>
                <p>
                    ثبت‌نام رایگان
                    @if($honarbaz->ends_at)
                        تا {{ \Illuminate\Support\Carbon::parse($honarbaz->ends_at)->format('Y/m/d') }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('honarbaz.landing') }}" class="btn hb-banner-btn">ثبت‌نام کنید ←</a>
    </div>
</section>

@push('styles')
<style>
    .honarbaz-banner {
        background: linear-gradient(120deg, #1F2A44 0%, #2a3a63 55%, #C9A24B 130%);
        color: #fff;
        padding: 1.6rem 0;
    }
    .hb-banner-inner {
        display: flex; align-items: center; justify-content: space-between;
        gap: 1.2rem; flex-wrap: wrap;
    }
    .hb-banner-text { display: flex; align-items: center; gap: 1rem; }
    .hb-banner-icon { font-size: 2.6rem; line-height: 1; }
    .honarbaz-banner h2 { color: #fff; font-size: 1.35rem; margin: 0; }
    .honarbaz-banner p { margin: .2rem 0 0; color: rgba(255,255,255,.9); font-size: .95rem; }
    .hb-banner-btn {
        background: var(--color-accent); color: var(--color-primary); font-weight: 700;
        white-space: nowrap;
    }
    .hb-banner-btn:hover { opacity: .9; text-decoration: none; color: var(--color-primary); }
    @media (max-width: 640px) {
        .hb-banner-inner { flex-direction: column; text-align: center; }
        .hb-banner-text { flex-direction: column; }
    }
</style>
@endpush
@endif
