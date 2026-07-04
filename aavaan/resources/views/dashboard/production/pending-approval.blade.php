<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>در انتظار تأیید | آوان</title>
<style>
    @font-face { font-family:'YekanBakh'; src:url('{{ asset("fonts/YekanBakh-VF.ttf") }}') format('truetype'); font-display:swap; }
    @font-face { font-family:'IRANSansX'; src:url('{{ asset("fonts/IRANSansXV.woff2") }}') format('woff2'); font-display:swap; }
    * { box-sizing:border-box; }
    body {
        margin:0; min-height:100vh; direction:rtl;
        font-family:'IRANSansX','YekanBakh',Tahoma,sans-serif;
        background:#F6F1E7; color:#232323;
        display:flex; align-items:center; justify-content:center; padding:1.5rem;
    }
    .box {
        max-width:520px; width:100%; background:#fff; border-radius:16px;
        box-shadow:0 6px 30px rgba(31,42,68,.12); overflow:hidden;
    }
    .box-head { background:#1F2A44; padding:2rem; text-align:center; }
    .logo { font-family:'YekanBakh',sans-serif; font-size:2rem; font-weight:800; color:#C9A24B; }
    .logo span { color:#fff; }
    .icon { font-size:3rem; margin:1.2rem 0 .3rem; }
    .box-body { padding:2rem; line-height:2; }
    .box-body h1 { font-family:'YekanBakh',sans-serif; font-size:1.3rem; color:#1F2A44; margin:0 0 1rem; text-align:center; }
    .box-body p { color:#444; font-size:.95rem; margin:0 0 1rem; }
    .note {
        background:#F6F1E7; border:1px solid #e6dcc6; border-radius:10px;
        padding:1rem 1.2rem; font-size:.9rem; color:#5a5340; margin:1.2rem 0;
    }
    .note strong { color:#8a6d1f; }
    .reject-box {
        background:#fdecea; border:1px solid #f5c6cb; border-radius:10px;
        padding:1rem 1.2rem; margin:1.2rem 0;
    }
    .reject-box .title { color:#a03027; font-weight:700; margin-bottom:.4rem; }
    .reject-box .reason { color:#333; font-size:.9rem; white-space:pre-wrap; }
    .actions { display:flex; gap:.7rem; justify-content:center; margin-top:1.5rem; flex-wrap:wrap; }
    .btn {
        display:inline-block; text-decoration:none; padding:.7rem 1.5rem; border-radius:9px;
        font-weight:700; font-size:.9rem; font-family:'YekanBakh',sans-serif; cursor:pointer; border:none;
    }
    .btn-gold { background:#C9A24B; color:#1F2A44; }
    .btn-ghost { background:#f3efe6; color:#333; border:1px solid #e0dbd0; }
    .support { text-align:center; font-size:.85rem; color:#777; margin-top:1rem; }
    .support a { color:#C9A24B; text-decoration:none; }
</style>
</head>
<body>
<div class="box">
    <div class="box-head">
        <div class="logo"><span>آ</span>وان</div>
        <div class="icon">@if($user->approval_status === 'rejected') 🚫 @else ⏳ @endif</div>
    </div>
    <div class="box-body">
        @if($user->approval_status === 'rejected')
            <h1>حساب شما تأیید نشد</h1>
            <p>سلام {{ $user->name }}،<br>متأسفانه درخواست دسترسی تیم تولید شما در آوان تأیید نشد.</p>
            @if($user->rejection_reason)
            <div class="reject-box">
                <div class="title">دلیل بررسی:</div>
                <div class="reason">{{ $user->rejection_reason }}</div>
            </div>
            @endif
            <p class="support">
                در صورت نیاز به بررسی مجدد، با پشتیبانی تماس بگیرید:
                <a href="mailto:info@aavaan.com">info@aavaan.com</a>
            </p>
        @else
            <h1>حساب شما در انتظار بررسی است</h1>
            <p>سلام {{ $user->name }}،<br>از ثبت‌نام شما در آوان سپاسگزاریم. حساب تیم تولید شما ثبت شد و در حال بررسی توسط تیم آوان است.</p>
            <div class="note">
                ⏱ درخواست‌ها معمولاً ظرف <strong>۱ تا ۲ روز کاری</strong> بررسی می‌شوند.
                پس از تأیید، از طریق ایمیل مطلع می‌شوید و دسترسی کامل به داشبورد و کست‌یاب فعال خواهد شد.
            </div>
            <p class="support">
                سؤالی دارید؟ با پشتیبانی در تماس باشید:
                <a href="mailto:info@aavaan.com">info@aavaan.com</a>
            </p>
        @endif

        <div class="actions">
            <form method="POST" action="{{ route('auth.logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost">خروج از حساب</button>
            </form>
            <a href="{{ route('home') }}" class="btn btn-gold">بازگشت به صفحهٔ اصلی</a>
        </div>
    </div>
</div>
</body>
</html>
