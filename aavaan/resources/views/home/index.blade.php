@extends('layouts.app')
@section('title', __('home.title'))
@section('meta-description', __('home.meta_description'))

@push('styles')
<style>
:root { --color-coral: #D9724F; }

/* ===== Language switch ===== */
.lang-switch-bar {
    position: absolute;
    top: 1rem;
    inset-inline-start: 1.5rem;
    z-index: 3;
}
.lang-switch {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .85rem;
    border-radius: 999px;
    background: rgba(255,255,255,.12);
    border: 1.5px solid rgba(255,255,255,.35);
    color: #fff;
    font-size: .82rem;
    font-weight: 600;
    transition: background .2s, border-color .2s;
}
.lang-switch:hover { background: rgba(255,255,255,.22); border-color: #fff; color: #fff; text-decoration: none; }

/* ===== Hero ===== */
.home-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 7rem 0 6rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.home-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23C9A24B' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.hero-bg-deco {
    position: absolute;
    inset: -10% 0 auto 0;
    height: 130%;
    background: radial-gradient(circle at 30% 20%, rgba(201,162,75,.14), transparent 55%),
                radial-gradient(circle at 75% 60%, rgba(201,162,75,.10), transparent 50%);
    pointer-events: none;
    will-change: transform;
}
.home-hero h1 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(2rem, 5vw, 3.4rem);
    color: var(--color-accent);
    margin-bottom: 1.5rem;
    line-height: 1.25;
    position: relative;
}
.home-hero p {
    color: rgba(255,255,255,.88);
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    max-width: 700px;
    margin: 0 auto 2.5rem;
    line-height: 1.9;
    position: relative;
}
.hero-btns {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
}
.btn-outline-white {
    background: transparent;
    border: 2px solid rgba(255,255,255,.6);
    color: #fff;
    font-weight: 600;
}
.btn-outline-white:hover {
    background: rgba(255,255,255,.12);
    border-color: #fff;
    color: #fff;
    text-decoration: none;
}
.btn-white {
    background: #fff;
    color: var(--color-accent);
    font-weight: 700;
}
.btn-white:hover {
    background: rgba(255,255,255,.9);
    color: var(--color-accent);
    text-decoration: none;
}

/* ===== Section shared ===== */
.section-title {
    text-align: center;
    font-size: clamp(1.5rem, 3vw, 2rem);
    color: var(--color-primary);
    margin-bottom: .5rem;
}
.section-sub {
    text-align: center;
    color: var(--color-muted);
    margin-bottom: 3rem;
    font-size: .98rem;
}

/* ===== Why Aavaan ===== */
.why-section {
    padding: 5rem 0;
    background: var(--color-bg);
}
.why-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 2.2rem 1.75rem;
    box-shadow: var(--shadow);
    text-align: center;
    border-top: 3px solid var(--color-accent);
    transition: transform .2s, box-shadow .2s;
}
.why-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.why-card .icon { font-size: 2.8rem; margin-bottom: 1rem; display: block; }
.why-card h3 { font-size: 1.08rem; margin-bottom: .6rem; color: var(--color-primary); }
.why-card p  { color: var(--color-muted); font-size: .93rem; line-height: 1.8; }

/* ===== How It Works ===== */
.how-section {
    background: var(--color-primary);
    padding: 5rem 0;
    color: #fff;
}
.how-section .section-title { color: var(--color-accent); }
.how-section .section-sub  { color: rgba(255,255,255,.6); }
.how-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; }
.how-col-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--color-accent);
    margin-bottom: 1.4rem;
    padding-bottom: .6rem;
    border-bottom: 1px solid rgba(201,162,75,.25);
}
.how-step {
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    margin-bottom: 1.2rem;
}
.how-step-num {
    flex-shrink: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: var(--color-accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .85rem;
    font-family: 'YekanBakh', Tahoma, sans-serif;
}
.how-step p { color: rgba(255,255,255,.82); font-size: .94rem; margin: 0; padding-top: .25rem; line-height: 1.7; }
.how-link { text-align: center; margin-top: 2.5rem; }
.how-link a { color: var(--color-accent); font-weight: 600; font-size: .95rem; }
.how-link a:hover { text-decoration: underline; }

/* ===== Fields / Categories ===== */
.fields-section { padding: 4.5rem 0; background: #fff; }
.cat-groups-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    max-width: 860px;
    margin: 0 auto 1.75rem;
}
.cat-group-card {
    background: var(--color-bg);
    border-radius: var(--radius);
    padding: 1.4rem 1.5rem;
    border: 1.5px solid rgba(31,42,68,.1);
}
.cat-group-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: .9rem;
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: .85rem;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.cat-pills { display: flex; flex-wrap: wrap; gap: .45rem; }
.cat-pill {
    display: inline-block;
    padding: .3rem .85rem;
    border-radius: 999px;
    background: #fff;
    color: var(--color-primary);
    font-size: .82rem;
    font-weight: 600;
    border: 1.5px solid rgba(31,42,68,.12);
    transition: background .2s, color .2s, border-color .2s;
}
.cat-pill:hover { background: var(--color-accent); color: #fff; border-color: var(--color-accent); }

/* ===== Featured Artists ===== */
.featured-section { padding: 5rem 0; background: var(--color-bg); }
.artists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
}
.artist-card {
    position: relative;
    background: #fff;
    border-radius: var(--radius);
    padding: 1.6rem 1rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: transform .2s, box-shadow .2s;
}
.artist-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.artist-card-overlay {
    position: absolute;
    inset: 0;
    border-radius: var(--radius);
    background: linear-gradient(160deg, rgba(201,162,75,.12), rgba(31,42,68,.06));
    opacity: 0;
    pointer-events: none;
}
.artist-card img {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto .85rem;
    border: 3px solid var(--color-accent);
    display: block;
}
.artist-card h3 { font-size: .97rem; color: var(--color-primary); margin-bottom: .3rem; }
.artist-card .field-tag {
    display: inline-block;
    background: #f5f0e8;
    color: var(--color-primary);
    font-size: .8rem;
    font-weight: 600;
    padding: .15rem .7rem;
    border-radius: 999px;
    margin-bottom: .9rem;
}
.artists-empty { text-align: center; color: var(--color-muted); padding: 3rem 0; grid-column: 1 / -1; font-size: 1rem; }

/* ===== Pricing Summary ===== */
.pricing-section { padding: 5rem 0; background: #fff; }
.pricing-card {
    background: var(--color-bg);
    border-radius: var(--radius);
    padding: 2.2rem 2rem;
    border: 1.5px solid rgba(31,42,68,.1);
    transition: transform .2s, box-shadow .2s;
}
.pricing-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.pricing-card .pc-badge {
    display: inline-block;
    background: var(--color-accent);
    color: var(--color-primary);
    font-size: .78rem;
    font-weight: 700;
    padding: .2rem .75rem;
    border-radius: 999px;
    margin-bottom: 1rem;
    font-family: 'YekanBakh', Tahoma, sans-serif;
}
.pricing-card h3 { font-size: 1.2rem; color: var(--color-primary); margin-bottom: .5rem; }
.pricing-card .pc-desc { color: var(--color-muted); font-size: .91rem; margin-bottom: 1.4rem; line-height: 1.8; }
.pricing-card .pc-price { font-size: 1.45rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.2rem; font-family: 'YekanBakh', Tahoma, sans-serif; }
.pricing-card .pc-price span { font-size: .82rem; font-weight: 400; color: var(--color-muted); }
.pricing-card .pc-btns { display: flex; gap: .75rem; flex-wrap: wrap; }

/* ===== Final CTA ===== */
.final-cta {
    background: var(--color-accent);
    padding: 5.5rem 0;
    text-align: center;
}
.final-cta h2 {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 900;
    font-size: clamp(1.6rem, 4vw, 2.4rem);
    color: var(--color-primary);
    margin-bottom: 1rem;
}
.final-cta p { color: rgba(31,42,68,.82); margin-bottom: 2rem; font-size: 1.05rem; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .how-cols { grid-template-columns: 1fr; gap: 2rem; }
    .artists-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    .pricing-card .pc-btns { flex-direction: column; }
    .cat-groups-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .home-hero { padding: 4.5rem 0 3.5rem; }
    .final-cta { padding: 4rem 0; }
}
</style>
@endpush

@section('content')

{{-- سوییچ زبان: بین مسیرهای /fa و /en لینک می‌دهد (سئو-دوست) --}}
<div class="lang-switch-bar">
    <a class="lang-switch"
       href="{{ ($locale ?? app()->getLocale()) === 'en' ? route('home.fa') : route('home.en') }}"
       hreflang="{{ ($locale ?? app()->getLocale()) === 'en' ? 'fa' : 'en' }}" rel="alternate">
        🌐 {{ __('home.lang_switch') }}
    </a>
</div>

@include('home.partials._hero')
@include('home.partials._honarbaz_banner')
@include('home.partials._map')
@include('home.partials._why')
@include('home.partials._audience')
@include('home.partials._specialties')
@include('home.partials._featured')
@include('home.partials._pricing')
@include('home.partials._cta')

@endsection
