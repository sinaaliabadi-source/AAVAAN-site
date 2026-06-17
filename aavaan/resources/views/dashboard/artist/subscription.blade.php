@extends('layouts.dashboard')
@section('title', 'اشتراک هنرمند')
@section('sidebar-nav')
<a href="{{ route('artist.dashboard') }}">خانه</a>
<a href="{{ route('artist.profile') }}">پروفایل و نمونه‌کار</a>
<a href="{{ route('artist.subscription') }}" class="active">اشتراک</a>
@endsection
@section('content')
<h1 style="margin-bottom:1.5rem">مدیریت اشتراک</h1>

@if($subscription)
<div class="card" style="border:2px solid var(--color-success);margin-bottom:1.5rem">
    <h3 style="color:var(--color-success)">اشتراک شما فعال است</h3>
    <p style="margin-top:.5rem;font-size:.9rem">نوع: {{ $subscription->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}</p>
    <p style="font-size:.9rem">تاریخ انقضا: {{ $subscription->expires_at->format('Y/m/d') }}</p>
</div>
@endif

<div class="card">
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
                <td style="padding:.4rem">{{ $h->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}</td>
                <td style="padding:.4rem">{{ number_format($h->amount) }}</td>
                <td style="padding:.4rem"><span style="color:{{ $h->payment_status === 'paid' ? 'var(--color-success)' : '#dc3545' }}">{{ $h->payment_status }}</span></td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $h->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
