@extends('admin.layouts.app')
@section('title', 'جزئیات تیم تولید')
@section('page-title', 'تیم تولید: ' . $team->name)
@section('content')

@php
    $statusMeta = [
        'pending'  => ['label' => 'در انتظار تأیید', 'bg' => '#faf6ec', 'color' => '#8a6d1f', 'border' => '#e6d09a'],
        'approved' => ['label' => 'تأییدشده',        'bg' => '#eef3ee', 'color' => '#3f5a3f', 'border' => '#c3dfc3'],
        'rejected' => ['label' => 'ردشده',           'bg' => '#fdecea', 'color' => '#a03027', 'border' => '#f5c6cb'],
    ];
    $meta = $statusMeta[$team->approval_status] ?? $statusMeta['approved'];
@endphp

<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.production.index') }}">تیم‌های تولید</a> ← جزئیات
</nav>

{{-- نوار وضعیت + اقدامات تأیید/رد --}}
<div class="card" x-data="{ showReject: false }">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:.7rem;flex-wrap:wrap;">
            <span style="font-family:'YekanBakh',sans-serif;font-weight:700;">وضعیت تأیید:</span>
            <span style="display:inline-block;background:{{ $meta['bg'] }};color:{{ $meta['color'] }};border:1px solid {{ $meta['border'] }};border-radius:999px;padding:.15rem .75rem;font-size:.82rem;font-weight:700;">
                {{ $meta['label'] }}
            </span>
            @if($team->approval_status === 'approved' && $team->approved_at)
                <span class="text-sm text-muted">
                    در {{ $team->approved_at->format('Y/m/d') }}
                    @if($team->approvedBy) توسط {{ $team->approvedBy->name }} @endif
                </span>
            @endif
        </div>

        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            @if($team->approval_status !== 'approved')
            <form method="POST" action="{{ route('admin.production.approve', $team->id) }}" style="margin:0;"
                  onsubmit="return confirm('این تیم تولید تأیید شود؟')">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" style="background:#5C6F4F;border-color:#5C6F4F;">✅ تأیید</button>
            </form>
            @endif
            @if($team->approval_status !== 'rejected')
            <button type="button" class="btn btn-ghost btn-sm" @click="showReject = !showReject"
                    style="color:#a03027;">❌ رد</button>
            @endif
        </div>
    </div>

    @if($team->approval_status === 'rejected' && $team->rejection_reason)
    <div style="margin-top:1rem;background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:.85rem 1rem;">
        <strong style="color:#a03027;">دلیل رد:</strong>
        <div class="text-sm" style="white-space:pre-wrap;margin-top:.3rem;">{{ $team->rejection_reason }}</div>
    </div>
    @endif

    {{-- فرم رد با دلیل الزامی --}}
    <div x-show="showReject" x-cloak style="margin-top:1rem;border-top:1px solid #f0ede8;padding-top:1rem;">
        <form method="POST" action="{{ route('admin.production.reject', $team->id) }}">
            @csrf
            <div class="form-group">
                <label>دلیل رد <span style="color:#c0392b">*</span></label>
                <textarea name="rejection_reason" class="form-control" rows="3" required
                          placeholder="دلیل رد حساب که به کاربر نمایش داده می‌شود…">{{ old('rejection_reason') }}</textarea>
                @error('rejection_reason')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-sm" style="background:#a03027;color:#fff;">ثبت رد حساب</button>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-title">اطلاعات حساب</div>
        <p class="text-sm"><strong>نام:</strong> {{ $team->name }}</p>
        <p class="text-sm"><strong>ایمیل:</strong> <span style="direction:ltr;display:inline-block;">{{ $team->email }}</span></p>
        <p class="text-sm"><strong>موبایل:</strong> {{ $team->phone ?: '—' }}</p>
        <p class="text-sm"><strong>تاریخ ثبت‌نام:</strong> {{ $team->created_at->format('Y/m/d H:i') }}</p>
        <p class="text-sm"><strong>مجموع پرداخت:</strong> {{ number_format($totalPaid) }} تومان</p>
        <p class="text-sm"><strong>پروفایل‌های بازشده:</strong> {{ $unlockedCount }} هنرمند</p>
    </div>

    <div class="card">
        <div class="card-title">اعتبارهای فعلی</div>
        @forelse($accesses as $access)
        <div style="padding:.65rem 0;border-bottom:1px solid #f0ede8;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span class="text-sm">
                    <strong>{{ $access->access_type === 'manual' ? 'دستی' : $access->access_type }}</strong>
                    — {{ $access->bundle_size }} اعتبار
                    @if($access->payment?->isManual())
                        <span class="badge" style="background:#e7e2f5;color:#5b4a8a;">دستی</span>
                    @endif
                </span>
                <span class="text-sm text-muted">{{ $access->created_at->format('Y/m/d') }}</span>
            </div>
            <div class="text-sm text-muted">
                استفاده‌شده: {{ $access->used_count }} / {{ $access->bundle_size }}
                @if($access->expires_at) — انقضا: {{ $access->expires_at->format('Y/m/d') }} @endif
            </div>
            @if($access->admin_note)
            <div class="text-sm" style="color:#8a6d1f;">یادداشت: {{ $access->admin_note }}</div>
            @endif
        </div>
        @empty
        <p class="text-muted text-sm">هیچ اعتباری ثبت نشده.</p>
        @endforelse
    </div>
</div>

{{-- افزودن اعتبار دستی — فقط تیم‌های تأییدشده --}}
@if($team->approval_status === 'approved')
<div class="card">
    <div class="card-title">➕ افزودن اعتبار دستی</div>
    <form method="POST" action="{{ route('admin.production.add-credit', $team->id) }}">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label>تعداد اعتبار <span style="color:#c0392b">*</span></label>
                <input type="number" name="bundle_size" class="form-control" min="1" max="1000" value="{{ old('bundle_size', 1) }}" dir="ltr" required>
                @error('bundle_size')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>تاریخ انقضا (اختیاری)</label>
                <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}" dir="ltr">
                @error('expires_at')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>مبلغ (تومان)</label>
                <input type="number" name="amount" class="form-control" min="0" value="{{ old('amount', 0) }}" dir="ltr">
                <span class="form-hint">پیش‌فرض ۰ (هدیه/جبران). یک رکورد پرداخت دستی ثبت می‌شود.</span>
            </div>
            <div class="form-group">
                <label>یادداشت ادمین</label>
                <input type="text" name="admin_note" class="form-control" value="{{ old('admin_note') }}" placeholder="مثلاً: جبران خرابی سرویس">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">افزودن اعتبار</button>
    </form>
</div>
@endif

{{-- تاریخچهٔ پرداخت‌ها --}}
<div class="card">
    <div class="card-title">تاریخچهٔ پرداخت‌ها</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>مبلغ</th><th>وضعیت</th><th>روش</th><th>تاریخ</th>
            </tr></thead>
            <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ number_format($p->amount) }} تومان</td>
                <td>
                    <span class="badge {{ $p->status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                        {{ $p->status === 'paid' ? 'پرداخت‌شده' : $p->status }}
                    </span>
                </td>
                <td>
                    @if($p->isManual())
                        <span class="badge" style="background:#e7e2f5;color:#5b4a8a;">دستی</span>
                    @else
                        <span class="text-sm text-muted">{{ $p->gateway }}</span>
                    @endif
                </td>
                <td>{{ $p->created_at->format('Y/m/d H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--color-muted);">پرداختی ثبت نشده.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
