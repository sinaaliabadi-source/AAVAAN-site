@extends('admin.layouts.app')
@section('title', 'جزئیات اشتراک')
@section('page-title', 'جزئیات اشتراک #' . $subscription->id)
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.subscriptions.index') }}">اشتراک‌ها</a> ← جزئیات
</nav>
<div class="grid-2">
<div class="card">
    <div class="card-title">اطلاعات اشتراک</div>
    <p class="text-sm"><strong>شناسه:</strong> {{ $subscription->id }}</p>
    <p class="text-sm"><strong>هنرمند:</strong> {{ $subscription->user?->name }}</p>
    <p class="text-sm"><strong>ایمیل:</strong> {{ $subscription->user?->email }}</p>
    <p class="text-sm"><strong>پلن:</strong> {{ $subscription->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}</p>
    <p class="text-sm"><strong>وضعیت:</strong>
        @if($subscription->status==='active' && $subscription->expires_at > now())
            <span class="badge badge-success">فعال</span>
        @elseif($subscription->status==='pending')
            <span class="badge badge-warning">در انتظار</span>
        @else
            <span class="badge badge-muted">منقضی</span>
        @endif
    </p>
    <p class="text-sm"><strong>شروع:</strong> {{ $subscription->starts_at?->format('Y/m/d') ?: '—' }}</p>
    <p class="text-sm"><strong>پایان:</strong> {{ $subscription->expires_at?->format('Y/m/d') ?: '—' }}</p>
    <p class="text-sm"><strong>تاریخ ایجاد:</strong> {{ $subscription->created_at->format('Y/m/d H:i') }}</p>
</div>
<div>
    <div class="card">
        <div class="card-title">تمدید دستی</div>
        <form method="POST" action="{{ route('admin.subscriptions.extend', $subscription->id) }}">@csrf
            <div class="form-group">
                <label>تاریخ پایان جدید</label>
                <input type="date" name="expires_at" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">تمدید</button>
        </form>
    </div>
    @if($subscription->status === 'active')
    <div class="card" style="border-color:var(--color-danger);">
        <div class="card-title" style="color:var(--color-danger);">لغو اشتراک</div>
        <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription->id) }}"
            onsubmit="return confirm('آیا از لغو این اشتراک مطمئنید؟')">@csrf
            <button type="submit" class="btn btn-danger btn-sm">لغو اشتراک</button>
        </form>
    </div>
    @endif
</div>
</div>
@endsection
