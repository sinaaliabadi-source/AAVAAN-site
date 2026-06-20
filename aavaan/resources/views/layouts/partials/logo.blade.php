{{--
  Logo partial — single source of truth for the Aavaan brand logo.

  Usage:
    @include('layouts.partials.logo')                         — default (44px, light variant for dark bg)
    @include('layouts.partials.logo', ['height' => '36px'])   — custom height
    @include('layouts.partials.logo', ['variant' => 'dark'])  — on light backgrounds

  Variants:
    'light'  (default) — full-color webp on dark background; text fallback in gold
    'dark'             — full-color webp on light background; text fallback in primary

  To swap the logo: replace public/images/logo.webp
  To add a monochrome version: add public/images/logo-mono.webp and pass variant='mono'
--}}
@php
    $logoHeight  = $height  ?? '44px';
    $logoVariant = $variant ?? 'light';
    $logoSrc     = asset('images/logo.webp');
    $logoExists  = file_exists(public_path('images/logo.webp'));

    $fallbackColor = $logoVariant === 'dark' ? 'var(--color-primary)' : 'var(--color-accent)';
    $subtitleColor = $logoVariant === 'dark' ? '#6b7280'              : 'rgba(201,162,75,.7)';
@endphp

@if($logoExists)
    <img
        src="{{ $logoSrc }}"
        alt="آوان"
        style="height:{{ $logoHeight }};width:auto;display:block;object-fit:contain;"
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
