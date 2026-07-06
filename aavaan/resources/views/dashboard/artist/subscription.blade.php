@extends('layouts.dashboard')
@section('title', 'اشتراک هنرمند')
@section('sidebar-nav')
<a href="{{ route('artist.dashboard') }}">خانه</a>
<a href="{{ route('artist.profile') }}">پروفایل و نمونه‌کار</a>
<a href="{{ route('artist.subscription') }}" class="active">اشتراک</a>
@endsection
@php
    $planLabels = ['monthly' => 'ماهانه', 'yearly' => 'سالانه', 'festival' => 'جشنوارهٔ افتتاح'];
@endphp
@section('content')
<h1 style="margin-bottom:1.5rem">مدیریت اشتراک</h1>

@if($festivalActive)
<div style="margin-bottom:1.5rem">
    <x-festival-banner
        title="جشنوارهٔ آغاز — عضویت رایگان شما 🎉"
        message="به مناسبت آغاز به کار آوان، عضویت شما تا پایان تابستان رایگان است. نمونه‌کارهایتان را کامل کنید تا در نتایج جستجوی تیم‌های تولید دیده شوید." />
</div>
@endif

@if($subscription)
<div class="card" style="border:2px solid var(--color-success);margin-bottom:1.5rem">
    <h3 style="color:var(--color-success)">اشتراک شما فعال است</h3>
    <p style="margin-top:.5rem;font-size:.9rem">
        نوع: {{ $planLabels[$subscription->plan] ?? $subscription->plan }}
        @if($subscription->plan === 'festival')<span class="festival-badge">رایگان</span>@endif
    </p>
    <p style="font-size:.9rem">تاریخ انقضا: {{ $subscription->expires_at->format('Y/m/d') }}</p>
    @if($subscription->plan === 'festival')
    <p style="font-size:.85rem;color:var(--color-muted);margin-top:.4rem">اشتراک جشنواره تا پایان تابستان معتبر است؛ پس از آن برای ادامهٔ حضور می‌توانید یکی از پلن‌ها را تهیه کنید.</p>
    @endif
</div>
@endif

<div class="card">
    @if($festivalActive)
    <div style="margin-bottom:1.25rem;padding:1rem;border:1px dashed #ecdfbf;border-radius:10px;background:#faf6ec">
        <p style="font-size:.9rem;color:#6a5a2e;margin:0">
            <span class="festival-badge">جشنواره</span>
            در دورهٔ جشنواره نیازی به پرداخت نیست. اگر مایل‌اید از همین حالا اشتراک بلندمدت (پس از جشنواره) تهیه کنید، می‌توانید از گزینه‌های زیر استفاده کنید.
        </p>
    </div>
    <details style="margin-bottom:.5rem">
        <summary style="cursor:pointer;font-weight:700;color:var(--color-primary)">مشاهدهٔ پلن‌های پرداختی</summary>
        <div style="margin-top:1rem">
    @endif
    <h2 style="margin-bottom:1.5rem">خرید / تمدید اشتراک</h2>
    <form action="{{ route('artist.subscription.pay') }}" method="POST">
        @csrf
        <div class="grid-2">
            <label style="border:2px solid #e5e7eb;border-radius:var(--radius);padding:1.25rem;cursor:pointer;display:block">
                <input type="radio" name="plan" value="monthly" style="margin-left:.5rem" required> ماهانه
                <div style="font-size:1.4rem;font-weight:800;color:var(--color-primary);margin-top:.5rem">{{ number_format($prices['monthly_price']) }} تومان</div>
            </label>
            <label style="border:2px solid var(--color-accent);border-radius:var(--radius);padding:1.25rem;cursor:pointer;display:block;background:#fffbf2">
                <input type="radio" name="plan" value="yearly" style="margin-left:.5rem"> سالانه (صرفه‌جویی بیشتر)
                <div style="font-size:1.4rem;font-weight:800;color:var(--color-primary);margin-top:.5rem">{{ number_format($prices['yearly_price']) }} تومان</div>
            </label>
        </div>
        <button type="submit" class="btn btn-accent" style="margin-top:1.5rem">پرداخت از طریق درگاه</button>
    </form>
    @if($festivalActive)
        </div>
    </details>
    @endif
</div>

@if($history->count())
<div class="card">
    <h3 style="margin-bottom:1rem">تاریخچه پرداخت‌ها</h3>
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb">
                <th style="text-align:right;padding:.4rem">نوع</th>
                <th style="text-align:right;padding:.4rem">مبلغ</th>
                <th style="text-align:right;padding:.4rem">وضعیت</th>
                <th style="text-align:right;padding:.4rem">تاریخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $h)
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:.4rem">{{ $planLabels[$h->plan] ?? $h->plan }}</td>
                <td style="padding:.4rem">{{ $h->payment ? number_format($h->payment->amount) : '—' }}</td>
                <td style="padding:.4rem"><span style="color:{{ $h->payment?->status === 'paid' ? 'var(--color-success)' : '#dc3545' }}">{{ $h->payment?->status ?? '—' }}</span></td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $h->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
