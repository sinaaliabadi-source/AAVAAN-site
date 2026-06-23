<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'آوان' }}</title>
<style>
  body { margin:0; padding:0; background:#f0ebe0; font-family:Tahoma,'IRANSansX',sans-serif; color:#232323; direction:rtl; }
  .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 16px rgba(31,42,68,.10); }
  .header { background:#1F2A44; padding:28px 36px 22px; text-align:center; }
  .header-logo { font-size:2rem; font-weight:800; color:#C9A24B; letter-spacing:-1px; }
  .header-logo span { color:#fff; }
  .header-tagline { color:rgba(255,255,255,.55); font-size:.82rem; margin-top:4px; }
  .body { padding:36px 36px 28px; line-height:2; }
  .body h2 { color:#1F2A44; font-size:1.15rem; margin-top:0; margin-bottom:12px; font-weight:700; }
  .body p { margin:0 0 16px; font-size:.9rem; color:#444; }
  .btn-wrap { text-align:center; margin:28px 0 20px; }
  .btn { display:inline-block; background:#1F2A44; color:#fff !important; text-decoration:none; padding:13px 36px; border-radius:8px; font-size:.93rem; font-weight:700; letter-spacing:.02em; }
  .btn:hover { background:#2a3a5c; }
  .url-fallback { background:#f6f1e7; border:1px solid #e0d8c8; border-radius:6px; padding:10px 14px; font-size:.78rem; color:#666; word-break:break-all; direction:ltr; text-align:left; margin:0 0 16px; }
  .divider { border:none; border-top:1px solid #eee; margin:20px 0; }
  .footer { background:#f6f1e7; padding:18px 36px; text-align:center; }
  .footer p { margin:0; font-size:.75rem; color:#999; line-height:1.8; }
  .footer a { color:#C9A24B; text-decoration:none; }
  @media (max-width:620px) {
    .wrap { margin:0; border-radius:0; }
    .body, .header, .footer { padding:20px; }
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo"><span>آ</span>وان</div>
    <div class="header-tagline">زمانش رسید.</div>
  </div>
  <div class="body">
    @yield('content')
  </div>
  <div class="footer">
    <p>
      این ایمیل به‌صورت خودکار از طرف پلتفرم آوان ارسال شده است.<br>
      در صورتی که این ایمیل را درخواست نکرده‌اید، آن را نادیده بگیرید.<br>
      <a href="{{ config('app.url') }}">aavaan.com</a>
      &nbsp;|&nbsp;
      <a href="mailto:info@aavaan.com">info@aavaan.com</a>
    </p>
  </div>
</div>
</body>
</html>
