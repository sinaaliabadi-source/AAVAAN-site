@extends('emails.layouts.base')
@section('content')
<h2>حساب شما در آوان ساخته شد 🎬</h2>
<p>
    سلام {{ $user->name }}،<br>
    یک حساب کاربری در آوان برای شما ایجاد شده است. اطلاعات ورود شما به این شرح است:
</p>

<div class="url-fallback" style="direction:rtl;text-align:right;">
    <strong>ایمیل ورود:</strong> <span style="direction:ltr;display:inline-block;">{{ $user->email }}</span><br>
    <strong>رمز عبور:</strong> <span style="direction:ltr;display:inline-block;">{{ $plainPassword }}</span>
</div>

<p style="font-size:.85rem;color:#666;">
    به‌محض ورود، پیشنهاد می‌کنیم رمز عبور خود را از بخش تنظیمات تغییر دهید.
</p>

<div class="btn-wrap">
    <a href="{{ route('auth') }}" class="btn">ورود به آوان</a>
</div>

<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    اگر انتظار این ایمیل را نداشتید، از طریق <a href="mailto:info@aavaan.com" style="color:#C9A24B;">info@aavaan.com</a> با ما تماس بگیرید.
</p>
@endsection
