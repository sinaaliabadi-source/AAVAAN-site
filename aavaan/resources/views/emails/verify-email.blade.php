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
{{--
    دکمهٔ فعال‌سازی به‌صورت کامل inline استایل داده شده و به کلاس .btn وابسته نیست،
    چون Gmail بلوک <style> داخل <head> را حذف می‌کند و دکمه بدون استایل می‌شکند.
    مقدار href با {!! !!} خروجی خام داده می‌شود تا Blade علامت & را در URL امضاشده
    به &amp; تبدیل نکند؛ در غیر این‌صورت امضا خراب می‌شود و دکمه ۴۰۳ می‌گیرد.
    این مقدار همیشه یک URL امضاشدهٔ ساخته‌شده توسط خود لاراول است (نه ورودی کاربر).
--}}
<div class="btn-wrap" style="text-align:center;margin:28px 0 20px;">
    <a href="{!! $verifyUrl !!}"
       style="display:inline-block;background:#C9A24B;color:#1F2A44;
              text-decoration:none;padding:14px 40px;border-radius:8px;
              font-size:15px;font-weight:700;font-family:Tahoma,sans-serif;">
        فعال‌سازی حساب
    </a>
</div>
<p class="url-fallback" style="background:#f6f1e7;border:1px solid #e0d8c8;border-radius:6px;padding:10px 14px;font-size:.78rem;color:#666;word-break:break-all;direction:ltr;text-align:left;margin:0 0 16px;">
    {{ $verifyUrl }}
</p>
<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    این لینک تا ۶۰ دقیقه معتبر است.<br>
    اگر شما این حساب را نساخته‌اید، این ایمیل را نادیده بگیرید.
</p>
@endsection
