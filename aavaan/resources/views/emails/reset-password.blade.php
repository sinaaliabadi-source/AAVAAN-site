@extends('emails.layouts.base')
@section('content')
<h2>بازیابی رمز عبور</h2>
<p>
    سلام {{ $notifiable->name }}،<br>
    درخواست بازیابی رمز عبور برای حساب شما در آوان دریافت شد.
</p>
<p>
    برای تنظیم رمز عبور جدید روی دکمه زیر کلیک کنید:
</p>
<div class="btn-wrap">
    <a href="{{ $resetUrl }}" class="btn">تنظیم رمز عبور جدید</a>
</div>
<p class="url-fallback">
    {{ $resetUrl }}
</p>
<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    این لینک تا ۶۰ دقیقه معتبر است.<br>
    اگر درخواست بازیابی رمز نداده‌اید، کاری لازم نیست — حساب شما در امنیت کامل است.
</p>
@endsection
