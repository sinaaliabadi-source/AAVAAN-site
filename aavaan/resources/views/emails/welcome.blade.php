@extends('emails.layouts.base')
@section('content')
<h2>به آوان خوش آمدید 🎬</h2>
<p>
    سلام {{ $user->name }}،<br>
    ثبت‌نام شما در آوان با موفقیت انجام شد.
</p>

@if($user->role === 'artist')
<p>
    حالا می‌توانید پروفایل هنری خود را تکمیل کنید، نمونه‌کارها و لینک ویدیوی آپاراتتان را اضافه کنید
    و در فهرست جستجوی تیم‌های تولید دیده شوید.
</p>
<div class="btn-wrap">
    <a href="{{ route('artist.profile') }}" class="btn">تکمیل پروفایل هنری</a>
</div>
@else
<p>
    اکنون می‌توانید هنرمندان و عوامل متخصص را جستجو و فیلتر کنید
    و فهرست کست پروژه‌ی بعدی‌تان را پیدا کنید.
</p>
<div class="btn-wrap">
    <a href="{{ route('production.dashboard') }}" class="btn">ورود به داشبورد</a>
</div>
@endif

<hr class="divider">
<p style="font-size:.82rem;color:#888;">
    اگر این حساب را شما نساختید، از طریق <a href="mailto:info@aavaan.com" style="color:#C9A24B;">info@aavaan.com</a> با ما تماس بگیرید.
</p>
@endsection
