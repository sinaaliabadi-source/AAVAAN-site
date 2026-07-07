{{-- تگ‌ها و اسکریپت مربوط به PWA (Progressive Web App) آوان --}}
{{-- این partial باید داخل <head> همه‌ی لِی‌اوت‌های اصلی include شود. --}}

{{-- مانیفست و رنگ نوار مرورگر --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1F2A44">

{{-- آیکن‌ها --}}
<link rel="apple-touch-icon" href="/images/pwa/apple-touch-icon.png">
<link rel="icon" href="/images/pwa/favicon.ico">

{{-- متاتگ‌های مخصوص iOS برای رفتار شبیه اپلیکیشن نصب‌شده --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="آوان">

{{-- ثبت Service Worker فقط در صورت پشتیبانی مرورگر --}}
<script>
    // فقط اگر مرورگر از Service Worker پشتیبانی کند، آن را ثبت می‌کنیم.
    if ('serviceWorker' in navigator) {
        // بعد از بارگذاری کامل صفحه ثبت می‌کنیم تا روی سرعت اولیه اثر نگذارد.
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (error) {
                // در صورت خطا فقط در کنسول لاگ می‌کنیم و تجربه‌ی کاربر مختل نمی‌شود.
                console.error('ثبت Service Worker ناموفق بود:', error);
            });
        });
    }
</script>
