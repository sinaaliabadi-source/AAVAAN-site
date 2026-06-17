@extends('layouts.dashboard')
@section('title', 'جستجوی هنرمند')
@section('sidebar-nav')
<a href="{{ route('production.dashboard') }}">خانه</a>
<a href="{{ route('production.search') }}" class="active">جستجوی هنرمند</a>
<a href="{{ route('production.saved') }}">فهرست‌های من</a>
<a href="{{ route('production.access') }}">خرید دسترسی</a>
@endsection
@section('content')
<h1 style="margin-bottom:1.5rem">جستجوی هنرمند</h1>

<div class="card" style="margin-bottom:1.5rem">
    <form method="GET" action="{{ route('production.search') }}">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;align-items:end">
            <div class="form-group" style="margin:0">
                <label>رشته‌ی هنری</label>
                <select name="field" class="form-control">
                    <option value="">همه</option>
                    @foreach($fields as $f)
                        <option value="{{ $f }}" {{ request('field') === $f ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>شهر</label>
                <input type="text" name="city" class="form-control" value="{{ request('city') }}" placeholder="تهران">
            </div>
            <div class="form-group" style="margin:0">
                <label>حداقل تجربه (سال)</label>
                <input type="number" name="experience_min" class="form-control" value="{{ request('experience_min') }}" min="0">
            </div>
            <div class="form-group" style="margin:0">
                <label>کلیدواژه</label>
                <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}">
            </div>
            <button type="submit" class="btn btn-primary">جستجو</button>
        </div>
    </form>
</div>

@if($access)
<p style="font-size:.85rem;color:var(--color-success);margin-bottom:1rem">● {{ $access->remainingCredits() }} دسترسی باقی‌مانده</p>
@endif

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem">
    @forelse($artists as $artist)
    @php $unlocked = in_array($artist->id, $unlockedIds); @endphp
    <div class="card" style="position:relative">
        <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}"
             style="width:100%;height:160px;object-fit:cover;border-radius:var(--radius);margin-bottom:.75rem">
        <h4>{{ $artist->user->name }}</h4>
        <p style="font-size:.85rem;color:var(--color-muted)">{{ $artist->field }} @if($artist->city) · {{ $artist->city }} @endif</p>
        <p style="font-size:.82rem;color:var(--color-muted)">{{ $artist->years_experience }} سال تجربه</p>
        <div style="margin-top:.75rem">
            @if($unlocked)
                <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary" style="font-size:.82rem;padding:.4rem .8rem">مشاهده پروفایل</a>
            @elseif($access)
                <form action="{{ route('production.access.unlock') }}" method="POST">
                    @csrf
                    <input type="hidden" name="artist_profile_id" value="{{ $artist->id }}">
                    <button type="submit" class="btn btn-accent" style="font-size:.82rem;padding:.4rem .8rem">🔓 باز کردن</button>
                </form>
            @else
                <a href="{{ route('production.access') }}" class="btn btn-outline" style="font-size:.82rem;padding:.4rem .8rem">خرید دسترسی</a>
            @endif
        </div>
    </div>
    @empty
    <p style="color:var(--color-muted);grid-column:1/-1">هنرمندی با این مشخصات یافت نشد.</p>
    @endforelse
</div>
<div style="margin-top:1.5rem">{{ $artists->links() }}</div>
@endsection
