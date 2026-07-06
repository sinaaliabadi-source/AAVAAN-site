<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ایمیل خود را بررسی کنید | آوان</title>
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
    .email-badge {
        display:block; text-align:center; font-weight:700; color:#1F2A44;
        background:#F6F1E7; border:1px solid #e6dcc6; border-radius:10px;
        padding:.7rem 1rem; margin:1rem 0; direction:ltr;
    }
    .alert { border-radius:10px; padding:.85rem 1.1rem; font-size:.9rem; margin:0 0 1rem; }
    .alert-success { background:#eaf5ea; border:1px solid #bfe0bf; color:#2d6a2d; }
    .alert-error { background:#fdecea; border:1px solid #f5c6cb; color:#a03027; }
    .actions { display:flex; gap:.7rem; justify-content:center; margin-top:1.2rem; flex-wrap:wrap; }
    .btn {
        display:inline-block; text-decoration:none; padding:.7rem 1.5rem; border-radius:9px;
        font-weight:700; font-size:.9rem; font-family:'YekanBakh',sans-serif; cursor:pointer; border:none;
    }
    .btn-gold { background:#C9A24B; color:#1F2A44; }
    .btn-ghost { background:#f3efe6; color:#333; border:1px solid #e0dbd0; }
    .hint { text-align:center; font-size:.83rem; color:#777; margin-top:1rem; }
    .hint a { color:#C9A24B; text-decoration:none; }
</style>
</head>
<body>
<div class="box">
    <div class="box-head">
        <div class="logo"><span>آ</span>وان</div>
        <div class="icon">📬</div>
    </div>
    <div class="box-body">
        <h1>ایمیل خود را بررسی کنید</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <p>
            سلام {{ auth()->user()->name }}،<br>
            یک لینک فعال‌سازی به ایمیل شما فرستادیم. برای فعال‌شدن حساب و دیده‌شدن در آوان، روی آن کلیک کنید.
        </p>

        @if(auth()->user()->email)
            <span class="email-badge">{{ auth()->user()->email }}</span>
        @endif

        <p style="font-size:.87rem;color:#777">
            ایمیلی دریافت نکردید؟ پوشهٔ اسپم را بررسی کنید یا لینک را دوباره برای خود بفرستید.
        </p>

        <div class="actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-gold">ارسال مجدد ایمیل</button>
            </form>
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="btn btn-ghost">خروج از حساب</button>
            </form>
        </div>

        <div class="hint">
            سؤالی دارید؟ با <a href="mailto:info@aavaan.com">info@aavaan.com</a> در تماس باشید.
        </div>
    </div>
</div>
</body>
</html>
