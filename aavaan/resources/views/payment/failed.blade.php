@extends('layouts.app')
@section('title', 'پرداخت ناموفق — آوان')

@push('styles')
<style>
    .payment-result-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1rem;
    }
    .payment-result-card {
        background: #fff;
        border-radius: 16px;
        padding: 3rem 2.5rem;
        text-align: center;
        max-width: 460px;
        width: 100%;
        box-shadow: 0 4px 32px rgba(31,42,68,.10);
        border: 1px solid #f0e0e0;
    }
    .payment-result-icon {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: #fdf0f0;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 1.4rem;
    }
    .payment-result-title {
        font-family: 'YekanBakh', sans-serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--color-primary);
        margin-bottom: .5rem;
    }
    .payment-result-sub {
        font-size: .9rem;
        color: var(--color-muted);
        line-height: 1.75;
        margin-bottom: 1.6rem;
    }
    .error-box {
        background: #fdf0f0;
        border-radius: 10px;
        padding: .85rem 1.2rem;
        font-size: .88rem;
        color: #9b3030;
        margin-bottom: 1.8rem;
        border: 1px solid #f5cece;
        text-align: right;
    }
    .payment-actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="payment-result-wrap">
    <div class="payment-result-card">
        <div class="payment-result-icon">❌</div>

        <div class="payment-result-title">پرداخت ناموفق یا لغو شد</div>

        <div class="payment-result-sub">
            مبلغی از حساب شما کسر نشده است.<br>
            در صورت تمایل می‌توانید دوباره تلاش کنید.
        </div>

        @if(session('error'))
        <div class="error-box">{{ session('error') }}</div>
        @endif

        <div class="payment-actions">
            @auth
                @if(auth()->user()->isArtist())
                    <a href="{{ route('artist.subscription') }}" class="btn btn-primary">تلاش مجدد</a>
                @elseif(auth()->user()->isProduction())
                    <a href="{{ route('production.access') }}" class="btn btn-primary">تلاش مجدد</a>
                @endif
            @endauth
            <a href="{{ route('home') }}" class="btn btn-ghost">بازگشت به خانه</a>
        </div>
    </div>
</div>
@endsection
