@extends('layouts.dashboard')
@section('title', 'داشبورد هنرمند')
@section('page-title', 'داشبورد')

@section('topbar-actions')
    <a href="{{ route('artist.profile') }}" class="btn btn-accent btn-sm">ویرایش پروفایل</a>
@endsection

@section('content')

@php
    $completeness = 0;
    $hints = [];
    if ($profile) {
        if ($profile->field)        { $completeness += 20; } else { $hints[] = 'رشته هنری'; }
        if ($profile->city)         { $completeness += 10; } else { $hints[] = 'شهر'; }
        if ($profile->bio)          { $completeness += 20; } else { $hints[] = 'بیوگرافی'; }
        if ($profile->avatar)       { $completeness += 20; } else { $hints[] = 'تصویر پروفایل'; }
        if ($profile->mainReel())   { $completeness += 15; } else { $hints[] = 'ویدیوی ریل'; }
        if ($profile->phone_contact || $profile->email_contact) { $completeness += 15; } else { $hints[] = 'اطلاعات تماس'; }
    } else {
        $hints = ['رشته هنری', 'شهر', 'بیوگرافی', 'تصویر پروفایل', 'ویدیوی ریل', 'اطلاعات تماس'];
    }
    $barColor = $completeness >= 80 ? 'var(--color-success)' : 'var(--color-accent)';
@endphp

@if($completeness < 60)
<div style="background:linear-gradient(135deg,var(--color-primary) 0%,#2d3e60 100%);color:#fff;border-radius:var(--radius);padding:1.5rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <div>
        <div style="font-family:'YekanBakh',sans-serif;font-size:1.1rem;font-weight:700;margin-bottom:.35rem">
            خوش آمدید، {{ $user->name }} 👋
        </div>
        <div style="font-size:.87rem;color:#c8d0e0;line-height:1.6">
            پروفایل شما هنوز تکمیل نشده. برای ظاهر شدن در جستجوها، آن را کامل کنید.
        </div>
    </div>
    <a href="{{ route('artist.profile') }}" class="btn btn-accent">تکمیل پروفایل</a>
</div>
@endif

<div class="grid-2">

    <div class="card">
        <div class="card-title">⭐ وضعیت اشتراک</div>
        @if($subscription && $subscription->isActive())
            <div style="margin-bottom:.75rem">
                <span class="badge badge-success">● فعال</span>
            </div>
            <p class="text-sm text-muted">
                پلن: <strong>{{ $subscription->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}</strong>
            </p>
            <p class="text-sm text-muted" style="margin-top:.3rem">
                اعتبار تا: <strong>{{ $subscription->expires_at->format('Y/m/d') }}</strong>
            </p>
        @else
            <div style="margin-bottom:.85rem">
                <span class="badge badge-danger">● غیرفعال</span>
            </div>
            <p class="text-sm text-muted" style="margin-bottom:1rem;line-height:1.7">
                برای ظاهر شدن در نتایج جستجوی تیم‌های تولید، اشتراک تهیه کنید.
            </p>
            <a href="{{ route('artist.subscription') }}" class="btn btn-accent btn-sm">خرید اشتراک</a>
        @endif
    </div>

    <div class="card">
        <div class="card-title">📊 تکمیل پروفایل</div>
        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:.5rem">
            <span class="text-sm text-muted">میزان تکمیل</span>
            <span style="font-family:'YekanBakh',sans-serif;font-weight:800;font-size:1.15rem;color:{{ $barColor }}">{{ $completeness }}٪</span>
        </div>
        <div style="background:#e8e3d8;border-radius:20px;height:8px;overflow:hidden;margin-bottom:.85rem">
            <div style="background:{{ $barColor }};height:100%;width:{{ $completeness }}%;border-radius:20px;transition:width .6s ease"></div>
        </div>
        @if(count($hints))
            <p class="text-sm text-muted">موارد ناقص: {{ implode('،‌ ', $hints) }}</p>
        @else
            <p class="text-sm" style="color:var(--color-success)">✓ پروفایل شما کامل است</p>
        @endif
        <a href="{{ route('artist.profile') }}" class="btn btn-outline btn-sm" style="margin-top:.9rem">ویرایش پروفایل</a>
    </div>

</div>

<div class="card">
    <div class="card-title">⚡ دسترسی سریع</div>
    <div style="display:flex;flex-wrap:wrap;gap:.65rem">
        <a href="{{ route('artist.profile') }}" class="btn btn-ghost btn-sm">✏️ ویرایش اطلاعات</a>
        <a href="{{ route('artist.profile') }}#portfolio" class="btn btn-ghost btn-sm">🖼 آپلود تصاویر گالری</a>
        <a href="{{ route('artist.profile') }}#reel" class="btn btn-ghost btn-sm">🎬 آپلود ویدیوی ریل</a>
        <a href="{{ route('artist.profile') }}#work-history" class="btn btn-ghost btn-sm">📋 افزودن سابقه کاری</a>
        <a href="{{ route('artist.subscription') }}" class="btn btn-ghost btn-sm">⭐ مدیریت اشتراک</a>
        @if($profile?->username)
            <a href="{{ route('profile.show', $profile->username) }}" target="_blank" class="btn btn-ghost btn-sm">🔗 مشاهده پروفایل عمومی</a>
        @endif
    </div>
</div>

@if($profile)
<div class="card">
    <div class="card-title">📈 آمار</div>
    <div class="grid-3">
        <div style="text-align:center;padding:1rem;background:#f9f6f0;border-radius:8px">
            <div style="font-family:'YekanBakh',sans-serif;font-size:1.8rem;font-weight:800;color:var(--color-primary)">{{ $profile->profile_views ?? 0 }}</div>
            <div class="text-sm text-muted" style="margin-top:.2rem">بازدید پروفایل</div>
        </div>
        <div style="text-align:center;padding:1rem;background:#f9f6f0;border-radius:8px">
            <div style="font-family:'YekanBakh',sans-serif;font-size:1.8rem;font-weight:800;color:var(--color-primary)">{{ $profile->portfolio_images_count ?? 0 }}</div>
            <div class="text-sm text-muted" style="margin-top:.2rem">تصویر نمونه‌کار</div>
        </div>
        <div style="text-align:center;padding:1rem;background:#f9f6f0;border-radius:8px">
            <div style="font-family:'YekanBakh',sans-serif;font-size:1.8rem;font-weight:800;color:var(--color-primary)">{{ $profile->work_histories_count ?? 0 }}</div>
            <div class="text-sm text-muted" style="margin-top:.2rem">سابقه کاری</div>
        </div>
    </div>
</div>
@endif

@endsection
