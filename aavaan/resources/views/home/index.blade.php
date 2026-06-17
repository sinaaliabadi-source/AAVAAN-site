@extends('layouts.app')
@section('title', 'خانه')
@section('content')
<section style="background: var(--color-primary); color: #fff; padding: 5rem 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.5rem; color: var(--color-accent); margin-bottom: 1rem;">آوان</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">پلتفرم تخصصی کاستینگ هنرمندان ایران</p>
        <a href="{{ route('artists') }}" class="btn btn-accent" style="margin-left: 1rem;">مشاهده هنرمندان</a>
        <a href="{{ route('auth') }}" class="btn btn-primary">ثبت‌نام رایگان</a>
    </div>
</section>
<section style="padding: 4rem 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 2rem;">هنرمندان برجسته</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
            @forelse($featuredArtists as $artist)
            <div style="background: #fff; border-radius: 8px; padding: 1rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 0.5rem;">
                <h3 style="font-size: 1rem;">{{ $artist->user->name }}</h3>
                <p style="color: var(--color-accent); font-size: 0.9rem;">{{ $artist->field }}</p>
                <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary" style="font-size: 0.85rem; margin-top: 0.5rem;">مشاهده پروفایل</a>
            </div>
            @empty
            <p style="text-align: center; grid-column: 1/-1; color: var(--color-muted);">هنوز هنرمندی ثبت نشده است.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
