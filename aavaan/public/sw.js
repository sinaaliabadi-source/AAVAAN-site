/*
 * Service Worker آوان
 * استراتژی ساده و دستی (بدون هیچ ابزار جانبی مثل vite-plugin-pwa).
 *
 * خلاصه:
 *  - ناوبری (navigate): network-first با fallback به کش و در نهایت offline.html
 *  - فایل‌های استاتیک (/build/, /images/, /fonts/): cache-first + به‌روزرسانی در پس‌زمینه (stale-while-revalidate)
 *  - مسیرهای حساس (داشبورد، ادمین، احراز هویت، بازگشت پرداخت): هرگز کش نمی‌شوند و همیشه از شبکه می‌آیند
 *  - فقط درخواست‌های GET مدیریت می‌شوند؛ POST و بقیه‌ی متدها کاملاً رها می‌شوند
 */

// نام کش نسخه‌دار؛ با هر تغییر مهم، شماره‌ی نسخه را بالا ببرید تا کش قدیمی پاک شود.
const CACHE_NAME = 'aavaan-cache-v1';

// صفحه‌ی آفلاین و آیکن‌های اصلی که هنگام نصب پیش‌ذخیره (precache) می‌شوند.
const PRECACHE_URLS = [
    '/offline.html',
    '/images/pwa/icon-192.png',
    '/images/pwa/icon-512.png',
    '/images/pwa/icon-maskable-512.png',
    '/images/pwa/apple-touch-icon.png',
    '/images/pwa/favicon.ico',
];

// مسیرهایی که هرگز نباید کش شوند (صفحات لاگین‌شده و پرداخت).
// دلیل: محتوای این صفحات شخصی/حساس است و کش‌شدن آن‌ها می‌تواند خطرناک باشد.
const NEVER_CACHE_PREFIXES = [
    '/dashboard',
    '/admin',
    '/auth',
];

// الگوهای مسیرِ بازگشت پرداخت (payment callback) که نباید کش شوند.
const NEVER_CACHE_PATTERNS = [
    /\/payment\/(callback|verify|return)/i,
    /\/callback/i,
];

// پیشوندهای فایل‌های استاتیک که با استراتژی cache-first مدیریت می‌شوند.
const STATIC_PREFIXES = [
    '/build/',
    '/images/',
    '/fonts/',
];

// ---------------------------------------------------------------------------
// install: پیش‌ذخیره‌ی صفحه‌ی آفلاین و آیکن‌ها
// ---------------------------------------------------------------------------
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            // بلافاصله فعال شود و منتظر بسته‌شدن تب‌های قبلی نماند.
            .then(() => self.skipWaiting())
    );
});

// ---------------------------------------------------------------------------
// activate: پاک‌کردن کش‌های قدیمی با نامِ متفاوت و در اختیار گرفتن کلاینت‌ها
// ---------------------------------------------------------------------------
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// ---------------------------------------------------------------------------
// توابع کمکی
// ---------------------------------------------------------------------------

// آیا این مسیر جزو مسیرهای حساسی است که هرگز نباید کش شود؟
function isNeverCache(pathname) {
    if (NEVER_CACHE_PREFIXES.some((prefix) => pathname.startsWith(prefix))) {
        return true;
    }
    return NEVER_CACHE_PATTERNS.some((pattern) => pattern.test(pathname));
}

// آیا این مسیر یک فایل استاتیک است؟
function isStaticAsset(pathname) {
    return STATIC_PREFIXES.some((prefix) => pathname.startsWith(prefix));
}

// استراتژی ناوبری: اول شبکه، بعد کش، در نهایت صفحه‌ی آفلاین.
async function handleNavigation(request) {
    try {
        const networkResponse = await fetch(request);
        return networkResponse;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        return caches.match('/offline.html');
    }
}

// استراتژی فایل استاتیک: اول کش (سریع)، و به‌روزرسانی در پس‌زمینه (stale-while-revalidate).
async function handleStatic(request) {
    const cache = await caches.open(CACHE_NAME);
    const cached = await cache.match(request);

    const networkFetch = fetch(request)
        .then((networkResponse) => {
            // فقط پاسخ‌های موفق و معتبر را کش می‌کنیم.
            if (networkResponse && networkResponse.ok) {
                cache.put(request, networkResponse.clone());
            }
            return networkResponse;
        })
        .catch(() => null);

    // اگر در کش بود، همان را برگردان و در پس‌زمینه تازه‌سازی کن؛ وگرنه منتظر شبکه بمان.
    return cached || networkFetch;
}

// ---------------------------------------------------------------------------
// fetch: مسیریابی درخواست‌ها بر اساس استراتژی‌های بالا
// ---------------------------------------------------------------------------
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // فقط درخواست‌های GET را مدیریت می‌کنیم؛ POST و بقیه را کاملاً رها می‌کنیم.
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // فقط درخواست‌های هم‌مبدأ (same-origin) را مدیریت می‌کنیم.
    if (url.origin !== self.location.origin) {
        return;
    }

    // مسیرهای حساس: همیشه مستقیم از شبکه، بدون هیچ کشی.
    if (isNeverCache(url.pathname)) {
        return;
    }

    // درخواست‌های ناوبری (باز کردن صفحه): network-first.
    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(request));
        return;
    }

    // فایل‌های استاتیک: cache-first (stale-while-revalidate).
    if (isStaticAsset(url.pathname)) {
        event.respondWith(handleStatic(request));
        return;
    }

    // بقیه‌ی درخواست‌های GET: به‌صورت پیش‌فرض از شبکه (بدون کش‌کردن اجباری).
});
