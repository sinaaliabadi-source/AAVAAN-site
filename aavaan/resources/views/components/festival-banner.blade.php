@props([
    'title'   => 'جشنوارهٔ آغاز',
    'message' => 'به مناسبت آغاز به کار آوان، عضویت و دسترسی تا پایان تابستان رایگان است.',
    'compact' => false,
])

{{-- نوار جشنواره — طلایی دودی روی لاجوردی، هماهنگ با برند. --}}
<div class="festival-banner {{ $compact ? 'is-compact' : '' }}" role="status">
    <span class="festival-banner__spark" aria-hidden="true">🎉</span>
    <div class="festival-banner__text">
        <strong class="festival-banner__title">{{ $title }}</strong>
        <span class="festival-banner__msg">{{ $message }}</span>
    </div>
</div>

@once
@push('styles')
<style>
    .festival-banner {
        display: flex;
        align-items: center;
        gap: .9rem;
        background: linear-gradient(135deg, #1F2A44 0%, #2b3a5e 100%);
        border: 1px solid rgba(201,162,75,.55);
        border-radius: 14px;
        padding: 1rem 1.25rem;
        color: #F6F1E7;
        box-shadow: 0 6px 22px rgba(31,42,68,.18);
    }
    .festival-banner.is-compact { padding: .7rem 1rem; border-radius: 10px; }
    .festival-banner__spark { font-size: 1.6rem; line-height: 1; flex-shrink: 0; }
    .festival-banner__text { display: flex; flex-direction: column; gap: .2rem; }
    .festival-banner__title {
        font-family: 'YekanBakh', Tahoma, sans-serif;
        font-weight: 800;
        color: #C9A24B;
        font-size: 1.02rem;
    }
    .festival-banner__msg { font-size: .9rem; color: rgba(246,241,231,.9); line-height: 1.7; }
    .festival-badge {
        display: inline-block;
        background: #C9A24B;
        color: #1F2A44;
        font-weight: 800;
        font-size: .74rem;
        border-radius: 999px;
        padding: .12rem .6rem;
        font-family: 'YekanBakh', Tahoma, sans-serif;
    }
    .festival-price-old {
        text-decoration: line-through;
        opacity: .55;
        font-weight: 600;
    }
</style>
@endpush
@endonce
