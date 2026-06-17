@extends('layouts.app')
@section('title', $profile->user->name . ' — آوان')
@section('content')
<div class="container" style="padding:3rem 0;max-width:900px">
    <div class="card" style="display:flex;gap:2rem;align-items:flex-start;flex-wrap:wrap;margin-bottom:1.5rem">
        <img src="{{ $profile->avatar_url }}" alt="{{ $profile->user->name }}"
             style="width:120px;height:120px;object-fit:cover;border-radius:50%;flex-shrink:0">
        <div style="flex:1">
            <h1 style="font-size:1.6rem">{{ $profile->user->name }}</h1>
            <p style="color:var(--color-accent);font-weight:600;margin:.25rem 0">{{ $profile->field }}</p>
            @if($profile->city) <p style="color:var(--color-muted);font-size:.9rem">📍 {{ $profile->city }}</p> @endif
            @if($profile->years_experience) <p style="color:var(--color-muted);font-size:.9rem">{{ $profile->years_experience }} سال تجربه</p> @endif
            @if($profile->bio) <p style="margin-top:1rem;line-height:1.8">{{ $profile->bio }}</p> @endif
        </div>
        @if(auth()->check() && auth()->user()->isProduction() && !$hasAccess)
        <form action="{{ route('production.access.unlock') }}" method="POST">
            @csrf
            <input type="hidden" name="artist_profile_id" value="{{ $profile->id }}">
            <button type="submit" class="btn btn-accent">🔓 دسترسی به اطلاعات تماس</button>
        </form>
        @endif
    </div>

    @if($profile->reel_video)
    <div class="card" style="margin-bottom:1.5rem">
        <h2 style="margin-bottom:1rem">ویدیوی نمونه‌کار</h2>
        @if($profile->reel_is_external)
            <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:var(--radius)">
                <iframe src="{{ $profile->reel_url }}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe>
            </div>
        @else
            <video controls style="width:100%;border-radius:var(--radius)" src="{{ $profile->reel_url }}"></video>
        @endif
    </div>
    @endif

    @if($profile->portfolioItems->count())
    <div class="card" style="margin-bottom:1.5rem">
        <h2 style="margin-bottom:1rem">نمونه‌کارها</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem">
            @foreach($profile->portfolioItems as $item)
                @if($item->type === 'image')
                    <img src="{{ $item->url }}" alt="{{ $item->caption }}" style="width:100%;height:140px;object-fit:cover;border-radius:var(--radius)">
                @else
                    <a href="{{ $item->url }}" target="_blank" style="display:flex;background:#f0f0f0;height:140px;border-radius:var(--radius);align-items:center;justify-content:center;color:var(--color-primary)">▶ ویدیو</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    @if($profile->workHistories->count())
    <div class="card" style="margin-bottom:1.5rem">
        <h2 style="margin-bottom:1rem">سوابق کاری</h2>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="border-bottom:2px solid #e5e7eb">
                    <th style="text-align:right;padding:.5rem;font-size:.9rem">عنوان</th>
                    <th style="text-align:right;padding:.5rem;font-size:.9rem">نقش</th>
                    <th style="text-align:right;padding:.5rem;font-size:.9rem">کارگردان</th>
                    <th style="text-align:right;padding:.5rem;font-size:.9rem">سال</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profile->workHistories as $wh)
                <tr style="border-bottom:1px solid #f0f0f0">
                    <td style="padding:.5rem;font-size:.9rem">{{ $wh->title }}</td>
                    <td style="padding:.5rem;font-size:.9rem;color:var(--color-muted)">{{ $wh->role }}</td>
                    <td style="padding:.5rem;font-size:.9rem;color:var(--color-muted)">{{ $wh->director ?? '—' }}</td>
                    <td style="padding:.5rem;font-size:.9rem;color:var(--color-muted)">{{ $wh->year ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($hasAccess)
    <div class="card" style="border:2px solid var(--color-success)">
        <h2 style="margin-bottom:1rem;color:var(--color-success)">اطلاعات تماس</h2>
        @if($profile->email_contact) <p>📧 {{ $profile->email_contact }}</p> @endif
        @if($profile->phone_contact) <p>📞 {{ $profile->phone_contact }}</p> @endif
        @if(!$profile->email_contact && !$profile->phone_contact)
            <p style="color:var(--color-muted)">هنرمند اطلاعات تماس ثبت نکرده است.</p>
        @endif
    </div>
    @elseif(!auth()->check())
    <div class="alert" style="background:#e8f4fd;color:#0c5460">برای مشاهده اطلاعات تماس، <a href="{{ route('auth') }}">وارد شوید</a>.</div>
    @endif
</div>
@endsection
