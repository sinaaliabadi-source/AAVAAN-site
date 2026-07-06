@extends('layouts.app')
@section('title', 'پرداخت موفق — آوان')

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
        border: 1px solid #e8ede8;
    }
    .payment-result-icon {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: #ecf5ec;
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
    .ref-box {
        background: #f5f0e8;
        border-radius: 10px;
        padding: .85rem 1.2rem;
        font-family: 'YekanBakh', sans-serif;
        font-size: .95rem;
        color: var(--color-primary);
        margin-bottom: 1.8rem;
        border: 1px solid #e2d9c8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .ref-label { font-size: .78rem; color: var(--color-muted); }
    .ref-value { font-weight: 800; font-size: 1.1rem; letter-spacing: .04em; }
    .payment-actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="payment-result-wrap">
    <div class="payment-result-card">
        <div class="payment-result-icon">✅</div>

        <div class="payment-result-title">پرداخت با موفقیت انجام شد</div>

        <div class="payment-result-sub">
            @if(session('context'))
                {{ session('context') }} فعال شد.<br>
            @endif
            از اعتماد شما متشکریم.
        </div>

        @if(session('ref_id'))
        <div class="ref-box">
            <span class="ref-label">کد پیگیری</span>
            <span class="ref-value">{{ session('ref_id') }}</span>
        </div>
        @endif

        <div class="payment-actions">
            @auth
                @if(auth()->user()->isArtist())
                    <a href="{{ route('artist.subscription') }}" class="btn btn-primary">مشاهده اشتراک</a>
                @elseif(auth()->user()->isProduction())
                    <a href="{{ route('production.search') }}" class="btn btn-primary">جستجوی هنرمند</a>
                @endif
            @endauth
            <a href="{{ route('home') }}" class="btn btn-ghost">بازگشت به خانه</a>
        </div>
    </div>
</div>
@endsection
