@extends('layouts.dashboard')
@section('title', 'داشبورد تیم تولید')
@section('page-title', 'داشبورد')

@section('topbar-actions')
    <a href="{{ route('production.search') }}" class="btn btn-accent btn-sm">🔍 جستجوی هنرمند جدید</a>
@endsection

@section('content')

{{-- کارت میان‌بر برجستهٔ کست‌یاب --}}
<a href="{{ route('production.search') }}"
   style="display:flex;align-items:center;gap:1.1rem;background:linear-gradient(120deg,var(--color-primary) 0%,#33456b 100%);color:#fff;border-radius:var(--radius);padding:1.4rem 1.6rem;margin-bottom:1.5rem;text-decoration:none;box-shadow:0 4px 18px rgba(31,42,68,.18);transition:transform .18s,box-shadow .18s"
   onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 26px rgba(31,42,68,.26)'"
   onmouseout="this.style.transform='';this.style.boxShadow='0 4px 18px rgba(31,42,68,.18)'">
    <div style="flex-shrink:0;width:56px;height:56px;border-radius:14px;background:rgba(201,162,75,.18);display:flex;align-items:center;justify-content:center;font-size:1.8rem">🎯</div>
    <div style="flex:1">
        <div style="font-family:'YekanBakh',sans-serif;font-weight:800;font-size:1.15rem;margin-bottom:.25rem">
            کست‌یاب — یافتن هنرمند بر اساس ویژگی‌ها
        </div>
        <div style="font-size:.87rem;color:#c8d0e0;line-height:1.7">
            بدون دیدن هویت، با فیلتر جنسیت، سن، قد، وزن، لهجه و هر ویژگی تخصصی، کست موردنظرتان را پیدا کنید.
        </div>
    </div>
    <span style="flex-shrink:0;background:var(--color-accent);color:var(--color-primary);font-weight:700;font-family:'YekanBakh',sans-serif;padding:.55rem 1.1rem;border-radius:8px;font-size:.9rem">
        شروع جستجو ←
    </span>
</a>

{{-- Onboarding banner — only when no paid access yet --}}
@if(!$hasPaidAccess)
<div style="background:linear-gradient(135deg,var(--color-primary) 0%,#2d3e60 100%);color:#fff;border-radius:var(--radius);padding:1.5rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <div>
        <div style="font-family:'YekanBakh',sans-serif;font-size:1.1rem;font-weight:700;margin-bottom:.4rem">
            خوش آمدید، {{ $user->name }} 👋
        </div>
        <div style="font-size:.87rem;color:#c8d0e0;line-height:1.7;max-width:500px">
            برای مشاهده پروفایل کامل هنرمندان، اطلاعات تماس و ویدیوی ریل، یک بسته دسترسی تهیه کنید.
        </div>
    </div>
    <a href="{{ route('production.access') }}" class="btn btn-accent">خرید دسترسی</a>
</div>
@endif

<div class="grid-2">

    {{-- Credits status --}}
    <div class="card">
        <div class="card-title">💳 وضعیت اعتبار</div>
        @if($access && $access->remainingCredits() > 0)
            <div style="margin-bottom:.6rem">
                <span class="badge badge-success">● فعال</span>
            </div>
            <div style="font-family:'YekanBakh',sans-serif;font-size:2.2rem;font-weight:800;color:var(--color-primary);line-height:1;margin-bottom:.25rem">
                {{ $access->remainingCredits() }}
            </div>
            <div class="text-sm text-muted">دسترسی باقی‌مانده از {{ $access->bundle_size }}</div>
            @if($access->expires_at)
            <div class="text-sm text-muted" style="margin-top:.4rem">
                اعتبار تا: <strong>{{ $access->expires_at->format('Y/m/d') }}</strong>
            </div>
            @endif
            <a href="{{ route('production.access') }}" class="btn btn-outline btn-sm" style="margin-top:.9rem">افزایش اعتبار</a>
        @elseif($hasPaidAccess)
            <div style="margin-bottom:.75rem">
                <span class="badge badge-warning">اعتبار تمام شده</span>
            </div>
            <p class="text-sm text-muted" style="margin-bottom:1rem;line-height:1.7">
                تمام اعتبارهای شما استفاده شده. برای دسترسی به هنرمندان جدید، بسته جدید بخرید.
            </p>
            <a href="{{ route('production.access') }}" class="btn btn-accent btn-sm">خرید اعتبار جدید</a>
        @else
            <div style="margin-bottom:.75rem">
                <span class="badge badge-danger">● بدون اعتبار</span>
            </div>
            <p class="text-sm text-muted" style="margin-bottom:1rem;line-height:1.7">
                هنوز هیچ بسته دسترسی‌ای خریداری نکرده‌اید.
            </p>
            <a href="{{ route('production.access') }}" class="btn btn-accent btn-sm">خرید اولین بسته</a>
        @endif
    </div>

    {{-- Unlocked artists --}}
    <div class="card">
        <div class="card-title">🎬 هنرمندان باز شده</div>
        <div style="font-family:'YekanBakh',sans-serif;font-size:2.2rem;font-weight:800;color:var(--color-primary);line-height:1;margin-bottom:.25rem">
            {{ $totalUnlocked }}
        </div>
        <div class="text-sm text-muted" style="margin-bottom:1rem">هنرمند در فهرست شما</div>
        @if($totalUnlocked > 0)
            <a href="{{ route('production.saved') }}" class="btn btn-outline btn-sm">مشاهده فهرست کامل</a>
        @else
            <p class="text-sm text-muted">
                هنوز هیچ هنرمندی باز نکرده‌اید.<br>
                از جستجو شروع کنید.
            </p>
        @endif
    </div>

</div>

{{-- Quick actions --}}
<div class="card">
    <div class="card-title">⚡ دسترسی سریع</div>
    <div style="display:flex;flex-wrap:wrap;gap:.65rem">
        <a href="{{ route('production.search') }}" class="btn btn-ghost btn-sm">🔍 جستجوی هنرمند</a>
        @if($totalUnlocked > 0)
            <a href="{{ route('production.saved') }}" class="btn btn-ghost btn-sm">📋 فهرست من</a>
        @endif
        <a href="{{ route('production.access') }}" class="btn btn-ghost btn-sm">💳 مدیریت دسترسی</a>
    </div>
</div>

{{-- Recent unlocked --}}
@if($recentLogs->count())
<div class="card">
    <div class="card-title">🕓 آخرین دسترسی‌ها</div>
    <div style="display:flex;flex-direction:column;gap:.4rem">
        @foreach($recentLogs as $log)
        @php $ap = $log->artistProfile; @endphp
        @if($ap)
        @php $hasProfileLink = !empty($ap->username); @endphp
        {{-- اگر پروفایل username نداشته باشد، ردیف به لینک مرده تبدیل نمی‌شود؛ به‌جای آن غیرقابل‌کلیک می‌ماند. --}}
        <{{ $hasProfileLink ? 'a' : 'div' }}
           @if($hasProfileLink) href="{{ route('profile.show', $ap->username) }}" @endif
           style="display:flex;align-items:center;gap:.85rem;padding:.65rem .85rem;background:#faf7f2;border-radius:8px;text-decoration:none;color:inherit;transition:background .15s"
           @if($hasProfileLink) onmouseover="this.style.background='#f0ede6'" onmouseout="this.style.background='#faf7f2'" @endif>
            <div style="width:38px;height:38px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.95rem;color:var(--color-accent)">
                @if($ap->avatar)
                    <img src="{{ $ap->avatar_url }}" style="width:100%;height:100%;object-fit:cover" alt="">
                @else
                    {{ mb_substr($ap->user->name, 0, 1) }}
                @endif
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:.88rem;color:var(--color-primary)">{{ $ap->user->name }}</div>
                <div class="text-sm text-muted">{{ $ap->field }}{{ $ap->city ? ' · ' . $ap->city : '' }}</div>
            </div>
            @if($hasProfileLink)
                <div class="text-sm text-muted" style="white-space:nowrap">{{ $log->accessed_at->diffForHumans() }}</div>
            @else
                <div class="text-sm text-muted" style="white-space:nowrap">پروفایل در دسترس نیست</div>
            @endif
        </{{ $hasProfileLink ? 'a' : 'div' }}>
        @endif
        @endforeach
    </div>
    @if($totalUnlocked > 5)
    <div style="margin-top:.85rem;padding-top:.85rem;border-top:1px solid #f0ede8">
        <a href="{{ route('production.saved') }}" class="btn btn-ghost btn-sm">مشاهده همه ({{ $totalUnlocked }})</a>
    </div>
    @endif
</div>
@endif

@endsection
