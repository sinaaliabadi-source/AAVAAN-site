@extends('emails.layouts.base')
@section('content')
<h2>نتیجهٔ بررسی حساب شما</h2>
<p>
    سلام {{ $user->name }}،<br>
    متأسفانه پس از بررسی، حساب تیم تولید شما در آوان در حال حاضر تأیید نشد.
</p>
@if($reason)
<div class="url-fallback" style="direction:rtl;text-align:right;">
    <strong>دلیل بررسی:</strong><br>
    {{ $reason }}
</div>
@endif
<p>
    در صورتی که فکر می‌کنید اشتباهی رخ داده یا مایل به بررسی مجدد هستید، با پشتیبانی در تماس باشید.
</p>
<div class="btn-wrap">
    <a href="mailto:info@aavaan.com" class="btn">تماس با پشتیبانی</a>
</div>
@endsection
