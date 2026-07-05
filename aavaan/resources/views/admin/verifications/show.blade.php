@extends('admin.layouts.app')
@section('title', 'بررسی تأیید تخصص')
@section('page-title', 'بررسی درخواست تأیید')
@section('content')

@php
    $statusMeta = [
        'pending'  => ['label' => 'در انتظار بررسی', 'class' => 'badge-warning'],
        'approved' => ['label' => 'تأییدشده',        'class' => 'badge-success'],
        'rejected' => ['label' => 'ردشده',           'class' => 'badge-danger'],
    ];
    $meta      = $statusMeta[$verification->status] ?? $statusMeta['pending'];
    $specialty = $verification->artistSpecialty;
    $profile   = $verification->user?->artistProfile;
    $defs      = $specialty?->category?->effectiveAttributeDefinitions() ?? collect();
    $attrs     = $specialty?->attributes ?? [];

    // نمایش خوانای مقدار یک ویژگی
    $display = function ($def, $val) {
        if ($val === null || $val === '' || $val === []) return null;
        return match ($def->field_type) {
            'boolean'     => $val ? 'بله' : 'خیر',
            'select'      => collect($def->options ?? [])->firstWhere('value', $val)['label'] ?? $val,
            'multiselect' => collect((array) $val)->map(fn($v) => collect($def->options ?? [])->firstWhere('value', $v)['label'] ?? $v)->join('، '),
            default       => $val,
        };
    };
@endphp

<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.verifications.index') }}">تأیید تخصص</a> ← بررسی #{{ $verification->id }}
</nav>

<div style="margin-bottom:1rem;">
    <span class="badge {{ $meta['class'] }}" style="font-size:.85rem;">{{ $meta['label'] }}</span>
    @if($verification->reviewed_at)
        <span class="text-sm text-muted">بررسی‌شده در {{ $verification->reviewed_at->format('Y/m/d H:i') }}</span>
    @endif
</div>

<div class="grid-2">
    {{-- اطلاعات هنرمند --}}
    <div class="card">
        <div class="card-title">👤 هنرمند</div>
        <p class="text-sm"><strong>نام:</strong> {{ $verification->user?->name }}</p>
        <p class="text-sm"><strong>ایمیل:</strong> <span style="direction:ltr;display:inline-block;">{{ $verification->user?->email }}</span></p>
        <p class="text-sm"><strong>شهر:</strong> {{ $profile?->city ?: '—' }}</p>
        <p class="text-sm"><strong>سابقه:</strong> {{ $profile?->years_experience ? $profile->years_experience.' سال' : '—' }}</p>
        @if($profile?->username)
        <a href="{{ route('profile.show', $profile->username) }}" target="_blank" class="btn btn-ghost btn-sm" style="margin-top:.5rem;">مشاهدهٔ پروفایل عمومی ↗</a>
        @endif
    </div>

    {{-- تخصص موضوع درخواست --}}
    <div class="card">
        <div class="card-title">🎯 تخصص</div>
        @if($specialty)
        <p class="text-sm">
            <strong>{{ $specialty->category?->name_fa }}</strong>
            @if($specialty->is_primary)<span class="badge" style="background:#C9A24B;color:#fff;">اصلی</span>@endif
        </p>
        @if($specialty->years_experience)
        <p class="text-sm text-muted">{{ $specialty->years_experience }} سال سابقه در این تخصص</p>
        @endif

        {{-- ویژگی‌های پرشده (از JSON با label فارسی) --}}
        <div style="margin-top:.6rem;">
            @php $shown = false; @endphp
            @foreach($defs as $def)
                @php $d = $display($def, $attrs[$def->key] ?? null); @endphp
                @if($d !== null && $d !== '')
                <div style="display:flex;justify-content:space-between;gap:.5rem;padding:.35rem 0;border-bottom:1px solid #f4f0e8;font-size:.84rem;">
                    <span class="text-muted">{{ $def->label_fa }}@if($def->unit) ({{ $def->unit }})@endif</span>
                    <span style="font-weight:600;">{{ $d }}</span>
                </div>
                @php $shown = true; @endphp
                @endif
            @endforeach
            @if(!$shown)<p class="text-muted text-sm">ویژگی‌ای ثبت نشده.</p>@endif
        </div>
        @else
        <p class="text-muted text-sm">تخصص مرتبط یافت نشد (احتمالاً حذف شده).</p>
        @endif
    </div>
</div>

{{-- توضیح هنرمند و لینک مدارک --}}
<div class="card">
    <div class="card-title">📝 توضیح و مدارک هنرمند</div>
    <p class="text-sm" style="white-space:pre-wrap;">{{ $verification->artist_note ?: '—' }}</p>
    @if(!empty($verification->evidence_links))
    <div style="margin-top:.6rem;">
        <div class="text-sm text-muted" style="margin-bottom:.3rem;">لینک‌های مدرک:</div>
        <ul style="margin:0;padding-inline-start:1.2rem;">
            @foreach($verification->evidence_links as $link)
            <li style="margin-bottom:.25rem;"><a href="{{ $link }}" target="_blank" rel="noopener" style="direction:ltr;display:inline-block;color:var(--color-accent);">{{ $link }}</a></li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

{{-- گالری رسانه‌های همان تخصص --}}
@php $media = $specialty?->media ?? collect(); $photos = $media->where('type','photo'); $videos = $media->where('type','video_link'); @endphp
@if($photos->count() || $videos->count())
<div class="card">
    <div class="card-title">🖼 رسانه‌های تخصص (شواهد خودکار)</div>
    @if($photos->count())
    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.6rem;">
        @foreach($photos as $ph)
        <a href="{{ asset('uploads/'.$ph->file_path) }}" target="_blank" style="width:90px;height:90px;border-radius:6px;overflow:hidden;background:#f0ede8;">
            <img src="{{ asset('uploads/'.$ph->file_path) }}" alt="نمونه‌کار" style="width:100%;height:100%;object-fit:cover;">
        </a>
        @endforeach
    </div>
    @endif
    @foreach($videos as $vid)
    <div class="text-sm"><a href="{{ $vid->external_url }}" target="_blank" rel="noopener" style="direction:ltr;display:inline-block;color:var(--color-accent);">🎬 {{ $vid->external_url }}</a></div>
    @endforeach
</div>
@endif

{{-- فرم تأیید/رد --}}
@if($verification->status === 'pending')
<div class="card" x-data="{ showReject: false }">
    <div class="card-title">تصمیم بررسی</div>
    <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.verifications.approve', $verification->id) }}" style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="text" name="notes" class="form-control" style="width:220px;" placeholder="یادداشت (اختیاری)">
            <button class="btn btn-primary" style="background:#5C6F4F;border-color:#5C6F4F;">✅ تأیید تخصص</button>
        </form>
        <button type="button" class="btn btn-ghost" style="color:#a03027;" @click="showReject = !showReject">❌ رد</button>
    </div>
    <div x-show="showReject" x-cloak style="margin-top:1rem;border-top:1px solid #f0ede8;padding-top:1rem;">
        <form method="POST" action="{{ route('admin.verifications.reject', $verification->id) }}">
            @csrf
            <div class="form-group">
                <label>دلیل رد <span style="color:#c0392b">*</span></label>
                <textarea name="notes" class="form-control" rows="3" required placeholder="دلیل رد که به هنرمند نمایش داده می‌شود…">{{ old('notes') }}</textarea>
                @error('notes')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <button class="btn" style="background:#a03027;color:#fff;">ثبت رد درخواست</button>
        </form>
    </div>
</div>
@else
<div class="card">
    <div class="card-title">نتیجهٔ بررسی</div>
    <p class="text-sm"><strong>وضعیت:</strong> <span class="badge {{ $meta['class'] }}">{{ $meta['label'] }}</span></p>
    @if($verification->notes)
    <p class="text-sm"><strong>یادداشت ادمین:</strong> {{ $verification->notes }}</p>
    @endif
</div>
@endif

{{-- تاریخچهٔ درخواست‌های قبلی همین تخصص --}}
@if($history->count())
<div class="card">
    <div class="card-title">🕘 تاریخچهٔ درخواست‌های این تخصص</div>
    <table class="table" style="font-size:.84rem;">
        <thead><tr><th>#</th><th>وضعیت</th><th>یادداشت</th><th>تاریخ</th></tr></thead>
        <tbody>
        @foreach($history as $h)
        <tr>
            <td>{{ $h->id }}</td>
            <td>
                @php $hm = $statusMeta[$h->status] ?? $statusMeta['pending']; @endphp
                <span class="badge {{ $hm['class'] }}">{{ $hm['label'] }}</span>
            </td>
            <td>{{ $h->notes ? \Str::limit($h->notes, 60) : '—' }}</td>
            <td>{{ $h->created_at->format('Y/m/d') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
