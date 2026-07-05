@extends('admin.layouts.app')
@section('title', 'ویرایش هنرمند')
@section('page-title', 'ویرایش هنرمند: ' . ($artist->username ?: $artist->user?->name))
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.artists.index') }}">هنرمندان</a> ← ویرایش
</nav>
<div class="grid-2">
<div class="card">
    <div class="card-title">تنظیمات ادمین</div>
    <form method="POST" action="{{ route('admin.artists.update', $artist->id) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $artist->is_active?'checked':'' }}>
                پروفایل فعال باشد
            </label>
            <span class="form-hint">غیرفعال‌سازی باعث پنهان‌شدن پروفایل از جستجوی عمومی می‌شود.</span>
        </div>
        <div class="form-group">
            <label>یادداشت داخلی ادمین</label>
            <textarea name="admin_notes" class="form-control" rows="4" placeholder="این یادداشت فقط برای ادمین‌ها قابل مشاهده است">{{ old('admin_notes', $artist->user?->admin_notes) }}</textarea>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary">ذخیره</button>
            <a href="{{ route('admin.artists.index') }}" class="btn btn-ghost">بازگشت</a>
        </div>
    </form>
</div>
<div class="card">
    <div class="card-title">اطلاعات پروفایل</div>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>نام هنری:</strong> {{ $artist->username ?: '—' }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>نام:</strong> {{ $artist->user?->name }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>ایمیل:</strong> {{ $artist->user?->email }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>شهر:</strong> {{ $artist->city ?: '—' }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>تجربه:</strong> {{ $artist->years_experience }} سال</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>بازدید پروفایل:</strong> {{ number_format($artist->profile_views) }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>تاریخ ثبت‌نام:</strong> {{ $artist->created_at->format('Y/m/d') }}</p>
    @if($artist->username)
    <div style="margin-top:1rem;">
        <a href="{{ route('profile.show', $artist->username) }}" target="_blank" class="btn btn-outline btn-sm">مشاهده پروفایل عمومی</a>
        <a href="{{ route('admin.users.edit', $artist->user_id) }}" class="btn btn-ghost btn-sm" style="margin-right:.5rem;">ویرایش حساب کاربری</a>
    </div>
    @endif
</div>

{{-- تخصص‌ها و وضعیت تأیید (فقط‌خواندنی) --}}
<div class="card">
    <div class="card-title">🎯 تخصص‌ها و وضعیت تأیید</div>
    @php
        $verMeta = [
            'pending'  => ['label' => 'در انتظار بررسی', 'class' => 'badge-warning'],
            'approved' => ['label' => 'تأییدشده',        'class' => 'badge-success'],
            'rejected' => ['label' => 'ردشده',           'class' => 'badge-danger'],
        ];
    @endphp
    @forelse($specialties as $sp)
    <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.55rem 0;border-bottom:1px solid #f0ede8;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <strong style="font-size:.9rem;">{{ $sp->category?->name_fa ?? '—' }}</strong>
            @if($sp->is_primary)<span class="badge" style="background:#C9A24B;color:#fff;">اصلی</span>@endif
            @php $v = $sp->latestVerification; @endphp
            @if($v)
                @php $m = $verMeta[$v->status] ?? $verMeta['pending']; @endphp
                <span class="badge {{ $m['class'] }}">{{ $m['label'] }}</span>
            @else
                <span class="text-muted text-sm">بدون درخواست تأیید</span>
            @endif
        </div>
        @if($sp->latestVerification)
        <a href="{{ route('admin.verifications.show', $sp->latestVerification->id) }}" class="btn btn-ghost btn-sm">بررسی</a>
        @endif
    </div>
    @empty
    <p class="text-muted text-sm">این هنرمند تخصصی ثبت نکرده است.</p>
    @endforelse
</div>
</div>
@endsection
