@extends('layouts.dashboard')
@section('title', 'فهرست‌های من')
@section('sidebar-nav')
<a href="{{ route('production.dashboard') }}">خانه</a>
<a href="{{ route('production.search') }}">جستجوی هنرمند</a>
<a href="{{ route('production.saved') }}" class="active">فهرست‌های من</a>
<a href="{{ route('production.access') }}">خرید دسترسی</a>
@endsection
@section('content')
<h1 style="margin-bottom:1.5rem">هنرمندان باز شده</h1>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem">
    @forelse($savedArtists as $log)
    @php $savedUsername = $log->artistProfile?->username; @endphp
    {{-- بدون username، کارت لینک مرده نمی‌سازد؛ به‌جای آن غیرقابل‌کلیک با متن راهنما نمایش داده می‌شود. --}}
    <{{ $savedUsername ? 'a' : 'div' }}
        @if($savedUsername) href="{{ route('profile.show', $savedUsername) }}" @endif
        class="card" style="display:block">
        <img src="{{ $log->artistProfile?->avatar_url }}" style="width:100%;height:150px;object-fit:cover;border-radius:var(--radius);margin-bottom:.75rem">
        <h4>{{ $log->artistProfile?->user->name }}</h4>
        <p style="font-size:.85rem;color:var(--color-muted)">{{ $log->artistProfile?->field }}</p>
        @unless($savedUsername)
            <p style="font-size:.8rem;color:#c0392b;margin-top:.35rem">پروفایل در دسترس نیست</p>
        @endunless
    </{{ $savedUsername ? 'a' : 'div' }}>
    @empty
    <p style="color:var(--color-muted)">هنوز هیچ هنرمندی باز نکرده‌اید.</p>
    @endforelse
</div>
<div style="margin-top:1.5rem">{{ $savedArtists->links() }}</div>
@endsection
