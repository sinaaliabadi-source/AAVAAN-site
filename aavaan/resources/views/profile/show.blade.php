@php
    $reel        = $profile->portfolioVideos->where('is_reel', true)->first()
                    ?? $profile->portfolioVideos->first();
    $images      = $profile->portfolioImages;
    $histories   = $profile->workHistories;

    // ── Blind casting (کستینگ ناشناس) ──
    // نام واقعی هنرمند فقط زمانی نمایش داده می‌شود که بازدیدکننده خودِ هنرمند باشد
    // یا کاربر تیم تولیدی که برای این پروفایل رکورد ProductionAccessLog دارد ($hasAccess).
    // در غیر این‌صورت یک شناسهٔ مستعار پایدار نمایش داده می‌شود. این مخفی‌سازی سمت سرور است
    // (اطلاعات واقعی اصلاً در HTML قرار نمی‌گیرد) نه صرفاً پنهان‌سازی بصری با CSS.
    $showRealName = $isSelf || $hasAccess;
    $artistName   = $showRealName ? $profile->user->name : 'هنرمند #' . $profile->id;
    $fieldLabel  = $profile->field ?? '';
    $cityLabel   = $profile->city  ?? '';

    $metaDesc = $artistName;
    if ($fieldLabel) $metaDesc .= '، هنرمند ' . $fieldLabel;
    if ($cityLabel)  $metaDesc .= ' از ' . $cityLabel;
    $metaDesc .= '. نمونه‌کار، ریل و سوابق کاری در آوان.';
@endphp

@extends('layouts.app')

@section('title', $artistName . ($fieldLabel ? ' | ' . $fieldLabel : '') . ' | آوان')
@section('meta-description', \Illuminate\Support\Str::limit($metaDesc, 155))

@push('styles')
<style>
    /* ── Profile hero ── */
    .profile-hero {
        background: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2rem;
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .profile-avatar {
        width: 120px; height: 120px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 3px solid var(--color-accent);
    }
    .profile-avatar-placeholder {
        width: 120px; height: 120px;
        border-radius: 50%;
        background: var(--color-primary);
        display: flex; align-items: center; justify-content: center;
        font-family: 'YekanBakh', sans-serif;
        font-size: 2.5rem; font-weight: 800;
        color: var(--color-accent);
        flex-shrink: 0;
        border: 3px solid var(--color-accent);
    }
    .profile-meta { flex: 1; min-width: 200px; }
    .profile-name { font-size: 1.75rem; font-weight: 800; color: var(--color-primary); margin-bottom: .35rem; }
    .profile-field {
        display: inline-block;
        background: var(--color-accent);
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
        padding: .2rem .8rem;
        border-radius: 999px;
        margin-bottom: .75rem;
    }
    .profile-tags { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: .75rem; }
    .profile-tag {
        font-size: .84rem;
        color: var(--color-muted);
        background: #f5f0e8;
        border-radius: 999px;
        padding: .18rem .75rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }

    /* ── Section cards ── */
    .section-card {
        background: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }
    .section-title {
        font-family: 'YekanBakh', sans-serif;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 1.1rem;
        padding-bottom: .7rem;
        border-bottom: 1px solid #ede8dc;
        display: flex; align-items: center; gap: .5rem;
    }

    /* ── Reel player ── */
    .reel-video {
        width: 100%;
        border-radius: var(--radius);
        background: #000;
        max-height: 480px;
        display: block;
    }

    /* ── Gallery ── */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: .75rem;
    }
    .gallery-item {
        aspect-ratio: 1;
        border-radius: 6px;
        overflow: hidden;
        cursor: zoom-in;
        position: relative;
    }
    .gallery-item img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .25s ease;
    }
    .gallery-item:hover img { transform: scale(1.04); }

    /* ── Work history table ── */
    .wh-table { width: 100%; border-collapse: collapse; }
    .wh-table th {
        text-align: right;
        padding: .55rem .75rem;
        font-size: .84rem;
        font-family: 'YekanBakh', sans-serif;
        font-weight: 700;
        color: var(--color-primary);
        border-bottom: 2px solid #ede8dc;
    }
    .wh-table td {
        padding: .55rem .75rem;
        font-size: .88rem;
        border-bottom: 1px solid #f0ede8;
        vertical-align: middle;
    }
    .wh-table tbody tr:last-child td { border-bottom: none; }
    .wh-table tbody tr:hover td { background: #faf7f2; }
    .wh-year {
        font-family: 'YekanBakh', sans-serif;
        font-weight: 700;
        font-size: .83rem;
        background: #f0ede8;
        color: var(--color-primary);
        padding: .15rem .55rem;
        border-radius: 5px;
        display: inline-block;
    }

    /* ── Contact card ── */
    .contact-card {
        background: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        border: 2px solid var(--color-success);
    }
    .contact-card .section-title { border-bottom-color: #dde8d5; color: var(--color-success); }
    .contact-row {
        display: flex; align-items: center; gap: .75rem;
        padding: .6rem 0;
        border-bottom: 1px solid #f0ede8;
        font-size: .95rem;
    }
    .contact-row:last-child { border-bottom: none; }
    .contact-icon { font-size: 1.1rem; flex-shrink: 0; }
    .contact-label { color: var(--color-muted); font-size: .82rem; min-width: 60px; }
    .contact-value { font-weight: 600; color: var(--color-primary); direction: ltr; }

    /* ── Access CTA ── */
    .access-cta {
        background: linear-gradient(135deg, #1F2A44 0%, #2d3e60 100%);
        border-radius: var(--radius);
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .access-cta-text { font-size: .95rem; color: #c8d0e0; line-height: 1.7; }
    .access-cta-title { font-family: 'YekanBakh', sans-serif; font-weight: 700; font-size: 1.05rem; margin-bottom: .3rem; }

    /* ── Lightbox ── */
    #lightbox {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.88);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #lightbox.open { display: flex; }
    #lightbox img {
        max-width: 90vw; max-height: 90vh;
        border-radius: 6px;
        object-fit: contain;
    }
    #lightbox-close {
        position: absolute; top: 1.5rem; left: 1.5rem;
        background: rgba(255,255,255,.15); color: #fff;
        border: none; border-radius: 50%;
        width: 40px; height: 40px;
        font-size: 1.3rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s;
    }
    #lightbox-close:hover { background: rgba(255,255,255,.3); }

    /* ── Specialties tabs ── */
    .spec-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-bottom: 1.25rem;
        border-bottom: 2px solid #ede8dc;
        padding-bottom: .5rem;
    }
    .spec-tab-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: .45rem .9rem;
        border-radius: 6px 6px 0 0;
        font-family: 'YekanBakh', Tahoma, sans-serif;
        font-size: .88rem;
        font-weight: 600;
        color: var(--color-muted);
        position: relative;
        transition: color .15s, background .15s;
    }
    .spec-tab-btn:hover { color: var(--color-primary); background: #f5f0e8; }
    .spec-tab-btn.active { color: var(--color-accent); }
    .spec-tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        right: 0; left: 0;
        height: 2px;
        background: var(--color-accent);
        border-radius: 2px 2px 0 0;
    }
    .primary-dot {
        display: inline-block;
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--color-accent);
        vertical-align: middle;
        margin-right: .25rem;
        margin-bottom: .1rem;
    }
    .spec-panel { display: none; }
    .spec-panel.active { display: block; }
    .spec-attr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: .6rem 1.5rem;
        margin-bottom: 1.1rem;
    }
    .spec-attr-row { font-size: .88rem; }
    .spec-attr-key { color: var(--color-muted); font-size: .8rem; margin-bottom: .1rem; }
    .spec-attr-val { color: var(--color-primary); font-weight: 600; }
    .prod-only-badge {
        font-size: .72rem;
        background: #fef3cd;
        color: #7a5c00;
        border: 1px solid #f0dda0;
        padding: .1rem .45rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: .2rem;
    }
    .spec-photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: .6rem;
        margin-bottom: 1.1rem;
    }
    .spec-photo-item {
        aspect-ratio: 4/3;
        border-radius: 6px;
        overflow: hidden;
        cursor: zoom-in;
        position: relative;
    }
    .spec-photo-item img { width:100%; height:100%; object-fit:cover; transition: transform .25s; }
    .spec-photo-item:hover img { transform: scale(1.05); }
    .aparat-embed-wrap {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 8px;
        background: #000;
        margin-bottom: 1.1rem;
    }
    .aparat-embed-wrap iframe {
        position: absolute;
        top: 0; right: 0;
        width: 100%; height: 100%;
        border: none;
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .profile-hero { flex-direction: column; align-items: center; text-align: center; }
        .profile-tags { justify-content: center; }
        .access-cta { flex-direction: column; }
        .wh-table th:nth-child(3),
        .wh-table td:nth-child(3) { display: none; }
        .spec-attr-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container" style="padding: 2rem 1.5rem; max-width: 860px;">

    {{-- Hero --}}
    <div class="profile-hero">
        @if($profile->avatar)
            <img src="{{ $profile->avatar_url }}" alt="{{ $artistName }}" class="profile-avatar" data-aos="zoom-in" data-aos-duration="600">
        @else
            <div class="profile-avatar-placeholder" data-aos="zoom-in" data-aos-duration="600">{{ mb_substr($artistName, 0, 1) }}</div>
        @endif

        <div class="profile-meta" data-aos="fade-right" data-aos-duration="600">
            <h1 class="profile-name">{{ $artistName }}</h1>

            @if($fieldLabel)
                <span class="profile-field">{{ $fieldLabel }}</span>
            @endif

            <div class="profile-tags">
                @if($cityLabel)
                    <span class="profile-tag">📍 {{ $cityLabel }}</span>
                @endif
                @if($profile->years_experience)
                    <span class="profile-tag">⏱ {{ $profile->years_experience }} سال تجربه</span>
                @endif
                @if($profile->birth_year)
                    <span class="profile-tag">🗓 متولد {{ $profile->birth_year }}</span>
                @endif
                @if($histories->count())
                    <span class="profile-tag">🎬 {{ $histories->count() }} سابقه کاری</span>
                @endif
            </div>

            @if($isSelf)
                <a href="{{ route('artist.profile') }}" class="btn btn-outline btn-sm">✏️ ویرایش پروفایل</a>
            @endif
        </div>
    </div>

    {{-- Reel video --}}
    @if($reel)
    <div class="section-card" data-aos="fade-up" data-aos-once="true">
        <div class="section-title">🎬 ویدیوی ریل</div>
        <video controls class="reel-video" src="{{ $reel->url }}" preload="metadata">
            مرورگر شما پخش ویدیو را پشتیبانی نمی‌کند.
        </video>
        @if($reel->formatted_duration)
            <div style="margin-top:.5rem;font-size:.82rem;color:var(--color-muted)">
                مدت: {{ $reel->formatted_duration }}
            </div>
        @endif
    </div>
    @endif

    {{-- Biography --}}
    @if($profile->bio)
    <div class="section-card">
        <div class="section-title">👤 بیوگرافی</div>
        <p style="line-height:2;color:var(--color-text);white-space:pre-line">{{ $profile->bio }}</p>
    </div>
    @endif

    {{-- Specialties accordion --}}
    @if($specialties->count())
    @php
    $catIconMap = [
        'acting'=>'🎭','stunt'=>'🤸','directing'=>'🎬','writing'=>'✍️',
        'cinematography'=>'📷','lighting'=>'💡','sound'=>'🎙️','music'=>'🎵',
        'editing'=>'🎞️','vfx'=>'✨','production-design'=>'🏛️','costume'=>'👗',
        'makeup'=>'💄','photography'=>'📸','production-management'=>'📋',
        'casting'=>'👥','pr-marketing'=>'📣','animation'=>'🐲',
        'games'=>'🎮','crew'=>'🤝','coaching'=>'👨‍🏫','translation'=>'🌐',
    ];

    // Inline helper: return human-readable value from def + raw value
    $displayVal = function($value, $def) {
        if ($value === null || $value === '') return null;
        $type = $def->field_type;
        if ($type === 'boolean')    return $value ? 'بله' : 'خیر';
        if ($type === 'select') {
            $opt = collect($def->options ?? [])->firstWhere('value', $value);
            return $opt['label'] ?? $value;
        }
        if ($type === 'multiselect') {
            if (empty($value)) return null;
            return collect($value)->map(function($v) use ($def) {
                $o = collect($def->options ?? [])->firstWhere('value', $v);
                return $o['label'] ?? $v;
            })->join('، ');
        }
        return $value;
    };

    // Extract Aparat embed URL from raw aparat.com/v/{hash} URL
    $aparatEmbed = function(string $url): ?string {
        if (preg_match('/aparat\.com\/v\/([A-Za-z0-9]+)/i', $url, $m)) {
            return "https://www.aparat.com/video/video/embed/videohash/{$m[1]}/vt/frame";
        }
        return null;
    };
    @endphp
    <div class="section-card" data-aos="fade-left" data-aos-duration="600">
        <div class="section-title">🎯 تخصص‌ها</div>

        {{-- Tabs (hidden when only one specialty) --}}
        @if($specialties->count() > 1)
        <div class="spec-tabs" id="spec-tabs">
            @foreach($specialties as $i => $spec)
            <button class="spec-tab-btn {{ $i === 0 ? 'active' : '' }}"
                    onclick="switchSpecTab({{ $i }})" type="button">
                {{ $catIconMap[$spec->category->slug] ?? '🎯' }}
                {{ $spec->category->name_fa }}
                @if($spec->is_primary)<span class="primary-dot" title="تخصص اصلی"></span>@endif
            </button>
            @endforeach
        </div>
        @endif

        @foreach($specialties as $i => $spec)
        <div class="spec-panel {{ ($i === 0 || $specialties->count() === 1) ? 'active' : '' }}" data-spec-idx="{{ $i }}">

            {{-- Header when single specialty --}}
            @if($specialties->count() === 1)
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;flex-wrap:wrap">
                <span style="font-size:1.4rem">{{ $catIconMap[$spec->category->slug] ?? '🎯' }}</span>
                <strong style="font-size:1rem;color:var(--color-primary)">{{ $spec->category->name_fa }}</strong>
                @if($spec->is_primary)
                    <span style="font-size:.75rem;background:var(--color-accent);color:#fff;padding:.15rem .6rem;border-radius:999px">تخصص اصلی</span>
                @endif
            </div>
            @endif

            @if($spec->years_experience)
            <p style="font-size:.85rem;color:var(--color-muted);margin-bottom:.9rem">
                ⏱ {{ $spec->years_experience }} سال سابقه در این تخصص
            </p>
            @endif

            {{-- Attribute values --}}
            @php
            $defs    = $spec->category->effectiveAttributeDefinitions();
            $attrs   = $spec->attributes ?? [];
            $hasVals = false;
            @endphp
            @if($defs->count())
            <div class="spec-attr-grid">
                @foreach($defs as $def)
                @php
                $isProdOnly = $def->visibility === 'production_team_only';
                $val        = $attrs[$def->key] ?? null;
                $displayed  = $displayVal($val, $def);
                @endphp

                @if($isProdOnly && !($hasAccess || $isSelf))
                    {{-- Only show lock placeholder if field was explicitly set --}}
                    @if($val !== null && $val !== '' && $val !== [])
                    <div class="spec-attr-row">
                        <div class="spec-attr-key">{{ $def->label_fa }}</div>
                        <span class="prod-only-badge">🔒 فقط تیم تولید با دسترسی</span>
                    </div>
                    @php $hasVals = true; @endphp
                    @endif
                @elseif($displayed !== null && $displayed !== '')
                    <div class="spec-attr-row">
                        <div class="spec-attr-key">{{ $def->label_fa }}</div>
                        @if($def->field_type === 'file_link')
                            @php $embedUrl = $aparatEmbed((string)$val); @endphp
                            @if($embedUrl)
                                <div class="spec-attr-val" style="margin-top:.3rem">
                                    <a href="{{ $val }}" target="_blank" rel="noopener" style="color:var(--color-accent);font-size:.82rem">مشاهده در آپارات ↗</a>
                                </div>
                            @else
                                <div class="spec-attr-val">{{ $displayed }}</div>
                            @endif
                        @else
                            <div class="spec-attr-val">{{ $displayed }}</div>
                        @endif
                    </div>
                    @php $hasVals = true; @endphp
                @endif
                @endforeach
            </div>
            @endif

            {{-- Aparat video_link attributes (file_link field type — shown as embed) --}}
            @foreach($defs->where('field_type', 'file_link') as $def)
            @php
            $isProdOnly = $def->visibility === 'production_team_only';
            $rawUrl     = $attrs[$def->key] ?? null;
            $embedUrl   = $rawUrl ? $aparatEmbed((string)$rawUrl) : null;
            @endphp
            @if($embedUrl && ($hasAccess || $isSelf || !$isProdOnly))
            <div style="margin-bottom:1.1rem">
                <div style="font-size:.82rem;color:var(--color-muted);margin-bottom:.5rem">🎬 {{ $def->label_fa }}</div>
                <div class="aparat-embed-wrap" data-aos="fade-up" data-aos-once="true">
                    <iframe src="{{ $embedUrl }}" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            @endif
            @endforeach

            {{-- Specialty photos --}}
            @php $photos = $spec->media->where('type', 'photo'); @endphp
            @if($photos->count())
            <div style="font-size:.82rem;color:var(--color-muted);margin-bottom:.5rem">🖼 نمونه‌کار این تخصص</div>
            <div class="spec-photos-grid">
                @foreach($photos as $photo)
                <div class="spec-photo-item" data-src="{{ asset('uploads/' . $photo->file_path) }}">
                    <img src="{{ asset('uploads/' . $photo->file_path) }}"
                         alt="{{ $artistName }} — {{ $spec->category->name_fa }}" loading="lazy">
                </div>
                @endforeach
            </div>
            @endif

            {{-- Specialty video links (Aparat embeds) --}}
            @php $videoLinks = $spec->media->where('type', 'video_link'); @endphp
            @if($videoLinks->count())
            <div style="font-size:.82rem;color:var(--color-muted);margin-bottom:.5rem">🎬 ویدیوها</div>
            @foreach($videoLinks as $vl)
            @if($vl->aparat_embed_url)
            <div class="aparat-embed-wrap" data-aos="fade-up" data-aos-once="true">
                <iframe src="{{ $vl->aparat_embed_url }}" allowfullscreen loading="lazy"></iframe>
            </div>
            @endif
            @endforeach
            @endif

        </div>
        @endforeach
    </div>
    @endif

    {{-- Portfolio gallery --}}
    @if($images->count())
    <div class="section-card">
        <div class="section-title">
            🖼 نمونه‌کارها
            <span style="font-size:.8rem;font-weight:400;color:var(--color-muted);margin-right:.25rem">
                ({{ $images->count() }} تصویر)
            </span>
        </div>
        <div class="gallery-grid">
            @foreach($images as $img)
            <div class="gallery-item" data-src="{{ $img->url }}" data-caption="{{ $img->caption }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <img src="{{ $img->url }}" alt="{{ $img->caption ?: $artistName . ' نمونه‌کار' }}" loading="lazy">
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Work history --}}
    @if($histories->count())
    <div class="section-card">
        <div class="section-title">📋 سوابق کاری</div>
        <div style="overflow-x:auto">
            <table class="wh-table">
                <thead>
                    <tr>
                        <th>عنوان اثر</th>
                        <th>نقش</th>
                        <th>کارگردان</th>
                        <th>سال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $wh)
                    <tr>
                        <td style="font-weight:600">{{ $wh->title }}</td>
                        <td style="color:var(--color-muted)">{{ $wh->role }}</td>
                        <td style="color:var(--color-muted)">{{ $wh->director ?: '—' }}</td>
                        <td>
                            @if($wh->year)
                                <span class="wh-year">{{ $wh->year }}</span>
                            @else
                                <span style="color:var(--color-muted)">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Contact section --}}
    @if($hasAccess)
        {{-- Artist themselves OR production user who already unlocked --}}
        @if($profile->phone_contact || $profile->email_contact)
        <div class="contact-card">
            <div class="section-title">📞 اطلاعات تماس</div>
            @if($profile->phone_contact)
            <div class="contact-row">
                <span class="contact-icon">📱</span>
                <span class="contact-label">موبایل</span>
                <span class="contact-value">{{ $profile->phone_contact }}</span>
            </div>
            @endif
            @if($profile->email_contact)
            <div class="contact-row">
                <span class="contact-icon">📧</span>
                <span class="contact-label">ایمیل</span>
                <span class="contact-value">{{ $profile->email_contact }}</span>
            </div>
            @endif
        </div>
        @elseif($isSelf)
            {{-- Self but no contact info entered --}}
            <div class="section-card" style="border:1px dashed #d0cac0">
                <p class="text-sm" style="color:var(--color-muted);text-align:center">
                    اطلاعات تماس وارد نشده. از
                    <a href="{{ route('artist.profile') }}">ویرایش پروفایل</a>
                    اضافه کنید.
                </p>
            </div>
        @endif

    @elseif(auth()->check() && auth()->user()->isProduction())
        {{-- Production user — not yet unlocked --}}
        <div class="access-cta">
            <div>
                <div class="access-cta-title">🔒 اطلاعات تماس هنرمند</div>
                <div class="access-cta-text">
                    @if($canUnlock)
                        با استفاده از یک اعتبار دسترسی، اطلاعات تماس این هنرمند را مشاهده کنید.
                    @else
                        برای دیدن اطلاعات تماس، ابتدا بسته دسترسی تهیه کنید.
                    @endif
                </div>
            </div>
            @if($canUnlock)
                <form method="POST" action="{{ route('production.access.unlock') }}">
                    @csrf
                    <input type="hidden" name="artist_profile_id" value="{{ $profile->id }}">
                    <button type="submit" class="btn btn-accent">🔓 مشاهده اطلاعات تماس</button>
                </form>
            @else
                <a href="{{ route('production.access') }}" class="btn btn-accent">خرید دسترسی</a>
            @endif
        </div>

    @elseif(!auth()->check())
        {{-- Guest --}}
        <div class="section-card" style="text-align:center;padding:2rem">
            <div style="font-size:1.5rem;margin-bottom:.75rem">🔒</div>
            <p style="color:var(--color-muted);font-size:.95rem;line-height:1.8;margin-bottom:1.1rem">
                برای مشاهده اطلاعات تماس این هنرمند وارد حساب تیم تولید خود شوید.
            </p>
            <a href="{{ route('auth') }}" class="btn btn-accent btn-sm">ورود به حساب</a>
        </div>
    @endif

</div>

{{-- Lightbox --}}
<div id="lightbox" role="dialog" aria-modal="true" aria-label="نمایش تصویر">
    <button id="lightbox-close" aria-label="بستن">✕</button>
    <img id="lightbox-img" src="" alt="">
</div>
@endsection

@push('scripts')
<script>
function switchSpecTab(idx) {
    document.querySelectorAll('.spec-tab-btn').forEach(function(b, i) {
        b.classList.toggle('active', i === idx);
    });
    document.querySelectorAll('.spec-panel').forEach(function(p, i) {
        p.classList.toggle('active', i === idx);
    });
}

(function () {
    var lb      = document.getElementById('lightbox');
    var lbImg   = document.getElementById('lightbox-img');
    var lbClose = document.getElementById('lightbox-close');

    document.querySelectorAll('.gallery-item, .spec-photo-item').forEach(function (el) {
        el.addEventListener('click', function () {
            lbImg.src = el.dataset.src;
            lbImg.alt = el.dataset.caption || '';
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeLightbox() {
        lb.classList.remove('open');
        lbImg.src = '';
        document.body.style.overflow = '';
    }

    lbClose.addEventListener('click', closeLightbox);
    lb.addEventListener('click', function (e) {
        if (e.target === lb) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
</script>
@endpush
