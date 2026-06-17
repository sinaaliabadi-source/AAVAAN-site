@extends('layouts.dashboard')
@section('title', 'پنل هنرمند')
@section('sidebar-nav')
<a href="{{ route('artist.dashboard') }}" class="active">خانه</a>
<a href="{{ route('artist.profile') }}">پروفایل و نمونه‌کار</a>
<a href="{{ route('artist.subscription') }}">اشتراک</a>
<a href="{{ route('profile.show', $profile?->username ?? '#') }}">پروفایل عمومی</a>
@endsection
@section('content')
<div class="dash-header">
    <h1>خوش آمدید، {{ $user->name }}</h1>
</div>

<div class="grid-2">
    <div class="card">
        <h3 style="margin-bottom:.75rem">وضعیت اشتراک</h3>
        @if($subscription)
            <span style="background:#d4edda;color:#155724;padding:.3rem .8rem;border-radius:20px;font-size:.85rem">● فعال</span>
            <p style="margin-top:.75rem;font-size:.9rem;color:var(--color-muted)">تا {{ $subscription->expires_at->format('Y/m/d') }} معتبر است</p>
        @else
            <span style="background:#f8d7da;color:#721c24;padding:.3rem .8rem;border-radius:20px;font-size:.85rem">● غیرفعال</span>
            <p style="margin-top:.75rem;font-size:.9rem;color:var(--color-muted)">برای فعال‌شدن در جستجو، اشتراک تهیه کنید.</p>
            <a href="{{ route('artist.subscription') }}" class="btn btn-accent" style="margin-top:1rem;font-size:.85rem">خرید اشتراک</a>
        @endif
    </div>
    <div class="card">
        <h3 style="margin-bottom:.75rem">تکمیل پروفایل</h3>
        @php
            $completeness = 0;
            if ($profile) {
                if ($profile->field) $completeness += 20;
                if ($profile->city) $completeness += 10;
                if ($profile->bio) $completeness += 20;
                if ($profile->avatar) $completeness += 20;
                if ($profile->reel_video) $completeness += 15;
                if ($profile->phone_contact || $profile->email_contact) $completeness += 15;
            }
        @endphp
        <div style="background:#e5e7eb;border-radius:20px;height:10px;overflow:hidden;margin-bottom:.75rem">
            <div style="background:var(--color-accent);height:100%;width:{{ $completeness }}%;border-radius:20px;transition:width .5s"></div>
        </div>
        <p style="font-size:.9rem;color:var(--color-muted)">{{ $completeness }}٪ تکمیل شده</p>
        <a href="{{ route('artist.profile') }}" class="btn btn-outline" style="margin-top:.75rem;font-size:.85rem">ویرایش پروفایل</a>
    </div>
</div>
@endsection
