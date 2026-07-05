@extends('emails.layouts.base')
@section('content')
<h2>تخصص شما تأیید شد ✔</h2>
<p>
    سلام {{ $verification->user->name }}،<br>
    تخصص «<strong>{{ $categoryName }}</strong>» شما در آوان بررسی و <strong>تأیید</strong> شد.
</p>
<p>
    از این پس نشان «تخصص تأییدشده» کنار این تخصص در پروفایل عمومی و نتایج جستجوی تیم‌های تولید نمایش داده می‌شود
    و اعتماد کارفرمایان به مهارت شما بیشتر خواهد بود.
</p>
<div class="btn-wrap">
    <a href="{{ route('artist.profile') }}" class="btn">مشاهدهٔ پروفایل</a>
</div>
<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    سؤالی دارید؟ با <a href="mailto:info@aavaan.com" style="color:#C9A24B;">info@aavaan.com</a> در تماس باشید.
</p>
@endsection
