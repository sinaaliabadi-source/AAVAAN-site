@extends('emails.layouts.base')
@section('content')
<h2>حساب شما تأیید شد ✅</h2>
<p>
    سلام {{ $user->name }}،<br>
    خبر خوب! حساب تیم تولید شما در آوان تأیید شد و اکنون به داشبورد و کست‌یاب دسترسی کامل دارید.
</p>
<p>
    می‌توانید هنرمندان و عوامل متخصص را بر اساس ویژگی‌ها جستجو و فیلتر کنید و فهرست کست پروژهٔ بعدی‌تان را بسازید.
</p>
<div class="btn-wrap">
    <a href="{{ route('production.dashboard') }}" class="btn">ورود به داشبورد</a>
</div>
<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    سؤالی دارید؟ با <a href="mailto:info@aavaan.com" style="color:#C9A24B;">info@aavaan.com</a> در تماس باشید.
</p>
@endsection
