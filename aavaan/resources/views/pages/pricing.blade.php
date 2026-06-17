@extends('layouts.app')
@section('title', 'تعرفه‌ها')
@section('content')
<div class="container" style="padding: 3rem 1rem;">
    <h1 style="text-align:center; margin-bottom: 2rem;">تعرفه‌ها</h1>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 700px; margin: 0 auto;">
        <div style="background:#fff; border-radius:8px; padding:2rem; text-align:center;">
            <h2>اشتراک ماهانه هنرمند</h2>
            <p style="font-size:2rem; color:var(--color-accent); margin:1rem 0;">{{ number_format($prices['artist_subscription']['monthly_price']) }} تومان</p>
            <a href="{{ route('auth') }}" class="btn btn-primary">شروع کنید</a>
        </div>
        <div style="background:#fff; border-radius:8px; padding:2rem; text-align:center; border: 2px solid var(--color-accent);">
            <h2>اشتراک سالانه هنرمند</h2>
            <p style="font-size:2rem; color:var(--color-accent); margin:1rem 0;">{{ number_format($prices['artist_subscription']['yearly_price']) }} تومان</p>
            <a href="{{ route('auth') }}" class="btn btn-accent">شروع کنید</a>
        </div>
    </div>
</div>
@endsection
