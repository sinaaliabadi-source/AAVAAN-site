@extends('emails.layouts.base')
@section('content')
<h2>به آوان خوش آمدید 🎬</h2>
<p>
    سلام {{ $notifiable->name }}،<br>
    خوشحالیم که به آوان پیوستید. تنها یک قدم تا آغاز مانده است.
</p>
<p>
    برای فعال‌سازی حساب و دیده‌شدن در میان تیم‌های تولید، روی دکمهٔ زیر کلیک کنید:
</p>
<div class="btn-wrap">
    <a href="{{ $verifyUrl }}" class="btn" style="background:#C9A24B;color:#1F2A44 !important;">فعال‌سازی حساب</a>
</div>
<p class="url-fallback">
    {{ $verifyUrl }}
</p>
<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    این لینک تا ۶۰ دقیقه معتبر است.<br>
    اگر شما این حساب را نساخته‌اید، این ایمیل را نادیده بگیرید.
</p>
@endsection
