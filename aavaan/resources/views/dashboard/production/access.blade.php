@extends('layouts.dashboard')
@section('title', 'خرید دسترسی')
@section('sidebar-nav')
<a href="{{ route('production.dashboard') }}">خانه</a>
<a href="{{ route('production.search') }}">جستجوی هنرمند</a>
<a href="{{ route('production.saved') }}">فهرست‌های من</a>
<a href="{{ route('production.access') }}" class="active">خرید دسترسی</a>
@endsection
@section('content')
<h1 style="margin-bottom:1.5rem">خرید دسترسی</h1>

@if($festivalActive)
<div style="margin-bottom:1.5rem">
    <x-festival-banner
        title="جشنوارهٔ آغاز — دسترسی رایگان"
        message="به مناسبت آغاز به کار آوان، تا پایان تابستان باز کردن پروفایل هنرمندان برای تیم‌های تولید تأییدشده رایگان است. تعرفه‌ها را می‌توانید ببینید؛ پس از جشنواره اعمال می‌شوند." />
</div>
@endif

<div class="card">
    <p style="color:var(--color-muted);margin-bottom:1.5rem;font-size:.9rem">با خرید دسترسی، می‌توانید اطلاعات کامل هنرمندان انتخابی را مشاهده کنید.</p>
    @if($festivalActive)
    <p style="margin-bottom:1.25rem;font-size:.86rem;color:#6a5a2e;background:#faf6ec;border:1px solid #ecdfbf;border-radius:8px;padding:.6rem .8rem">
        <span class="festival-badge">جشنواره</span>
        در حال حاضر نیازی به خرید نیست — باز کردن پروفایل‌ها رایگان است. تعرفه‌های زیر پس از پایان جشنواره فعال می‌شوند.
    </p>
    @endif
    <form action="{{ route('production.access.buy') }}" method="POST">
        @csrf
        <div class="grid-3" style="margin-bottom:1.5rem">
            @foreach([
                ['single','تک‌دسترسی','۱ هنرمند',$prices['single_price']],
                ['bundle_5','بسته ۵تایی','۵ هنرمند',$prices['bundle_5_price']],
                ['bundle_10','بسته ۱۰تایی','۱۰ هنرمند',$prices['bundle_10_price']],
            ] as [$type,$label,$desc,$price])
            <label style="border:2px solid #e5e7eb;border-radius:var(--radius);padding:1.25rem;cursor:pointer;display:block;transition:border-color .2s">
                <input type="radio" name="access_type" value="{{ $type }}" style="margin-left:.5rem" required>
                <strong>{{ $label }}</strong>
                <div style="font-size:.85rem;color:var(--color-muted);margin:.3rem 0">{{ $desc }}</div>
                <div style="font-size:1.3rem;font-weight:800;color:var(--color-primary)">{{ number_format($price) }} تومان</div>
            </label>
            @endforeach
        </div>
        <button type="submit" class="btn btn-accent">پرداخت از طریق درگاه</button>
    </form>
</div>

@if($accesses->count())
<div class="card">
    <h3 style="margin-bottom:1rem">دسترسی‌های فعال</h3>
    <table style="width:100%;border-collapse:collapse;font-size:.88rem">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb">
                <th style="text-align:right;padding:.4rem">نوع</th>
                <th style="text-align:right;padding:.4rem">استفاده شده</th>
                <th style="text-align:right;padding:.4rem">باقی‌مانده</th>
                <th style="text-align:right;padding:.4rem">تاریخ خرید</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accesses as $acc)
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:.4rem">{{ $acc->access_type }}</td>
                <td style="padding:.4rem">{{ $acc->used_count }}/{{ $acc->bundle_size }}</td>
                <td style="padding:.4rem;color:var(--color-success)">{{ $acc->remainingCredits() }}</td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $acc->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
