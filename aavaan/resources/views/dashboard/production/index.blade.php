@extends('layouts.dashboard')
@section('title', 'پنل تیم تولید')
@section('sidebar-nav')
<a href="{{ route('production.dashboard') }}" class="active">خانه</a>
<a href="{{ route('production.search') }}">جستجوی هنرمند</a>
<a href="{{ route('production.saved') }}">فهرست‌های من</a>
<a href="{{ route('production.access') }}">خرید دسترسی</a>
@endsection
@section('content')
<div class="dash-header">
    <h1>خوش آمدید، {{ $user->name }}</h1>
</div>

<div class="grid-2">
    <div class="card">
        <h3 style="margin-bottom:.75rem">اعتبار دسترسی</h3>
        @if($access)
            <div style="font-size:1.8rem;font-weight:800;color:var(--color-primary)">{{ $access->remainingCredits() }}</div>
            <p style="font-size:.85rem;color:var(--color-muted)">دسترسی باقی‌مانده</p>
        @else
            <p style="color:var(--color-muted);font-size:.9rem">اعتباری ندارید.</p>
            <a href="{{ route('production.access') }}" class="btn btn-accent" style="margin-top:.75rem;font-size:.85rem">خرید دسترسی</a>
        @endif
    </div>
    <div class="card">
        <h3 style="margin-bottom:.75rem">شروع جستجو</h3>
        <p style="font-size:.9rem;color:var(--color-muted);margin-bottom:1rem">هنرمندان را بر اساس رشته، شهر و سابقه فیلتر کنید.</p>
        <a href="{{ route('production.search') }}" class="btn btn-primary">جستجوی هنرمند</a>
    </div>
</div>

@if($recentLogs->count())
<div class="card">
    <h3 style="margin-bottom:1rem">آخرین دسترسی‌ها</h3>
    <div style="display:flex;flex-direction:column;gap:.75rem">
        @foreach($recentLogs as $log)
        <a href="{{ route('profile.show', $log->artistProfile?->username ?? '#') }}" style="display:flex;align-items:center;gap:1rem;padding:.75rem;background:var(--color-bg);border-radius:var(--radius)">
            <img src="{{ $log->artistProfile?->avatar_url }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover">
            <div>
                <div style="font-weight:600;font-size:.9rem">{{ $log->artistProfile?->user->name }}</div>
                <div style="font-size:.8rem;color:var(--color-muted)">{{ $log->artistProfile?->field }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
