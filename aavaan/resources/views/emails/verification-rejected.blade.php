@extends('emails.layouts.base')
@section('content')
<h2>نتیجهٔ بررسی تأیید تخصص</h2>
<p>
    سلام {{ $verification->user->name }}،<br>
    درخواست تأیید تخصص «<strong>{{ $categoryName }}</strong>» شما بررسی شد، اما در حال حاضر تأیید نشد.
</p>
@if($reason)
<div class="url-fallback" style="direction:rtl;text-align:right;">
    <strong>دلیل بررسی:</strong><br>
    {{ $reason }}
</div>
@endif
<p>
    می‌توانید پس از رفع موارد بالا، از پروفایل خود دوباره درخواست تأیید ارسال کنید.
</p>
<div class="btn-wrap">
    <a href="{{ route('artist.profile') }}" class="btn">ویرایش و ارسال مجدد</a>
</div>
@endsection
