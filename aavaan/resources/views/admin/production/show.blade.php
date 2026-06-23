@extends('admin.layouts.app')
@section('title', 'جزئیات تیم تولید')
@section('page-title', 'تیم تولید: ' . $team->name)
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.production.index') }}">تیم‌های تولید</a> ← جزئیات
</nav>
<div class="grid-2">
<div class="card">
    <div class="card-title">اطلاعات حساب</div>
    <p class="text-sm"><strong>نام:</strong> {{ $team->name }}</p>
    <p class="text-sm"><strong>ایمیل:</strong> {{ $team->email }}</p>
    <p class="text-sm"><strong>موبایل:</strong> {{ $team->phone ?: '—' }}</p>
    <p class="text-sm"><strong>تاریخ ثبت‌نام:</strong> {{ $team->created_at->format('Y/m/d H:i') }}</p>
    <p class="text-sm"><strong>مجموع پرداخت:</strong> {{ number_format($totalPaid) }} تومان</p>
</div>
<div class="card">
    <div class="card-title">تاریخچه دسترسی‌ها</div>
    @forelse($accesses as $access)
    <div style="padding:.65rem 0;border-bottom:1px solid #f0ede8;">
        <div style="display:flex;justify-content:space-between;">
            <span class="text-sm"><strong>{{ $access->access_type }}</strong> — {{ $access->bundle_size }} بسته</span>
            <span class="text-sm text-muted">{{ $access->created_at->format('Y/m/d') }}</span>
        </div>
        <div class="text-sm text-muted">استفاده‌شده: {{ $access->used_count }} / {{ $access->bundle_size }}</div>
        @if($access->payment)
        <div class="text-sm">
            <span class="badge {{ $access->payment->status==='paid'?'badge-success':'badge-warning' }}">
                {{ $access->payment->status === 'paid' ? 'پرداخت‌شده' : $access->payment->status }}
            </span>
            {{ number_format($access->payment->amount) }} تومان
        </div>
        @endif
    </div>
    @empty
    <p class="text-muted text-sm">هیچ دسترسی‌ای ثبت نشده.</p>
    @endforelse
</div>
</div>
@endsection
