@extends('layouts.app')
@section('title', 'دسترسی غیرمجاز — آوان')

@push('styles')
<style>
.error-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 4rem 1rem;
}
.error-code {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: clamp(5rem, 18vw, 9rem);
    font-weight: 900;
    color: var(--color-accent);
    line-height: 1;
    margin-bottom: .25rem;
    letter-spacing: -.04em;
    text-shadow: 0 4px 24px rgba(201,162,75,.25);
}
.error-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    font-weight: 700;
    color: var(--color-primary);
    margin-bottom: .75rem;
}
.error-desc {
    color: var(--color-muted);
    font-size: .95rem;
    line-height: 1.85;
    max-width: 400px;
    margin: 0 auto 2.25rem;
}
.error-divider {
    width: 60px;
    height: 3px;
    background: var(--color-accent);
    border-radius: 2px;
    margin: 1rem auto 1.5rem;
}
.error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<section class="error-page">
    <div>
        <div class="error-code">۴۰۳</div>
        <div class="error-divider"></div>
        <h1 class="error-title">دسترسی مجاز نیست</h1>
        <p class="error-desc">
            شما مجوز دسترسی به این بخش را ندارید.
            در صورت نیاز وارد حساب کاربری خود شوید یا با پشتیبانی تماس بگیرید.
        </p>
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">بازگشت به خانه</a>
            @guest
                <a href="{{ route('auth') }}" class="btn btn-accent btn-lg">ورود به حساب</a>
            @endguest
        </div>
    </div>
</section>
@endsection
