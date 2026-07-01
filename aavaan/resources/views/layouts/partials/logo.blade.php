{{--
  Logo partial — single source of truth for the Aavaan brand logo.

  Usage:
    @include('layouts.partials.logo')                         — default (44px, light variant for dark bg)
    @include('layouts.partials.logo', ['height' => '36px'])   — custom height
    @include('layouts.partials.logo', ['variant' => 'dark'])  — on light backgrounds

  Variants:
    'light'  (default) — cream/gold logo for dark backgrounds (header/footer); text fallback in gold
    'dark'             — navy/gold logo for light backgrounds; text fallback in primary

  To swap the logo: replace public/images/logo.webp (navy version) and
  public/images/logo-light.webp (cream version). If the light file is missing,
  the navy logo is used with a CSS brightness filter so it stays visible.
--}}
@php
    $logoHeight  = $height  ?? '44px';
    $logoVariant = $variant ?? 'light';

    // نسخه‌ی روشن (عاجی/طلایی) برای پس‌زمینه‌ی لاجوردی؛ نسخه‌ی اصلی (لاجوردی/طلایی) برای پس‌زمینه‌ی روشن
    $lightExists = file_exists(public_path('images/logo-light.webp'));
    if ($logoVariant === 'light' && $lightExists) {
        $logoSrc    = asset('images/logo-light.webp');
        $logoFilter = '';
    } elseif ($logoVariant === 'light') {
        // فالبک: اگر نسخه‌ی روشن نبود، لوگوی اصلی را روشن کن تا روی زمینه‌ی تیره دیده شود
        $logoSrc    = asset('images/logo.webp');
        $logoFilter = 'filter:brightness(0) invert(1);';
    } else {
        $logoSrc    = asset('images/logo.webp');
        $logoFilter = '';
    }
    $logoExists  = file_exists(public_path('images/logo.webp')) || $lightExists;

    $fallbackColor = $logoVariant === 'dark' ? 'var(--color-primary)' : 'var(--color-accent)';
    $subtitleColor = $logoVariant === 'dark' ? '#6b7280'              : 'rgba(201,162,75,.7)';
@endphp

@if($logoExists)
    <img
        src="{{ $logoSrc }}"
        alt="آوان"
        style="height:{{ $logoHeight }};width:auto;display:block;object-fit:contain;{{ $logoFilter }}"
        loading="eager"
    >
@else
    {{-- Text fallback — shown until logo.webp is placed at public/images/logo.webp --}}
    <span style="display:inline-flex;flex-direction:column;align-items:flex-start;line-height:1;gap:2px;">
        <span style="
            font-family:'YekanBakh',Tahoma,sans-serif;
            font-size:calc({{ $logoHeight }} * 0.65);
            font-weight:800;
            color:{{ $fallbackColor }};
            letter-spacing:-.03em;
            line-height:1;
        ">آوان</span>
        <span style="
            font-family:'IRANSansX','YekanBakh',Tahoma,sans-serif;
            font-size:calc({{ $logoHeight }} * 0.28);
            color:{{ $subtitleColor }};
            letter-spacing:.04em;
            line-height:1;
        ">زمانش رسید</span>
    </span>
@endif
