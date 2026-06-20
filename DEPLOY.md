# راهنمای دیپلوی آوان روی هاست اشتراکی (cPanel / PHP 8.3)

> پیش‌نیاز: PHP 8.3+، MySQL 5.7+ یا MariaDB 10.3+، phpMyAdmin، دسترسی به File Manager یا FTP

---

## ۱. آپلود فایل‌ها

ساختار پوشه پروژه روی سرور باید به این شکل باشد:

```
/home/USERNAME/              ← ریشه‌ی حساب cPanel شما
├── public_html/             ← دایرکتوری public هاست (ریشه‌ی دامنه)
│   ├── index.php            ← از public/ پروژه
│   ├── favicon.svg
│   ├── images/
│   ├── fonts/
│   ├── uploads/
│   └── ...
└── aavaan/                  ← پوشه‌ی اصلی پروژه (خارج از public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── ...
```

### گام ۱ — آپلود پوشه‌ی اصلی

محتوای پوشه‌ی `aavaan/` را (به‌جز خود پوشه‌ی `public/`) به
`/home/USERNAME/aavaan/` آپلود کنید.  
**پوشه‌های حذف‌شدنی قبل از آپلود (کاهش حجم):**
- `node_modules/` (وجود ندارد)
- `.git/`
- `tests/`

### گام ۲ — آپلود پوشه‌ی public

محتوای `aavaan/public/` را مستقیماً داخل `public_html/` آپلود کنید.

### گام ۳ — اصلاح مسیرها در index.php

فایل `public_html/index.php` را باز کرده و **دو خط** زیر را ویرایش کنید:

```php
// قبل از اصلاح:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// بعد از اصلاح:
require __DIR__.'/../aavaan/vendor/autoload.php';
$app = require_once __DIR__.'/../aavaan/bootstrap/app.php';
```

> **توضیح:** `__DIR__` در `public_html/index.php` برابر `/home/USERNAME/public_html` است.
> مسیر `/../aavaan/` یعنی یک سطح بالاتر رفته و وارد پوشه‌ی aavaan شو.

---

## ۲. ساخت دیتابیس MySQL در cPanel

1. وارد cPanel شوید → **MySQL Databases**
2. یک دیتابیس جدید بسازید، مثلاً: `USERNAME_aavaan`
3. یک کاربر MySQL بسازید، مثلاً: `USERNAME_avuser` با رمز قوی
4. کاربر را به دیتابیس اضافه کرده و **All Privileges** بدهید
5. این اطلاعات را یادداشت کنید:
   - **DB_DATABASE**: `USERNAME_aavaan`
   - **DB_USERNAME**: `USERNAME_avuser`
   - **DB_PASSWORD**: رمزی که انتخاب کردید
   - **DB_HOST**: معمولاً `localhost`

---

## ۳. ایمپورت اسکیمای دیتابیس

1. در cPanel وارد **phpMyAdmin** شوید
2. دیتابیس `USERNAME_aavaan` را از لیست سمت چپ انتخاب کنید
3. روی تب **Import** کلیک کنید
4. فایل `schema.sql` (موجود در ریشه‌ی مخزن) را انتخاب کنید
5. **Format: SQL** باشد، سپس **Go** را بزنید

> این اسکیما تمام جداول لازم را می‌سازد و جدول `migrations` را هم پر می‌کند
> تا بعداً نیازی به `php artisan migrate` نباشد.

---

## ۴. پیکربندی فایل .env

فایل `/home/USERNAME/aavaan/.env` را از روی `.env.example` بسازید
و مقادیر زیر را تنظیم کنید:

```ini
APP_NAME="آوان"
APP_ENV=production
APP_KEY=                          # ← در گام ۵ پر می‌شود
APP_DEBUG=false
APP_URL=https://aavaan.com        # ← دامنه‌ی واقعی شما

LOG_CHANNEL=stack
LOG_LEVEL=error                   # ← در production فقط خطاها لاگ شوند

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=USERNAME_aavaan
DB_USERNAME=USERNAME_avuser
DB_PASSWORD=YOUR_STRONG_PASSWORD

FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=mail.aavaan.com
MAIL_PORT=465
MAIL_USERNAME=info@aavaan.com
MAIL_PASSWORD=YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@aavaan.com
MAIL_FROM_NAME="آوان"

SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

# Payment Gateway
PAYMENT_DRIVER=zarinpal
ZARINPAL_MERCHANT_ID=YOUR-REAL-MERCHANT-ID   # ← از پنل زرین‌پال
ZARINPAL_SANDBOX=false                        # ← false در production!

# Subscription Prices (in Tomans)
SUBSCRIPTION_MONTHLY_PRICE=150000
SUBSCRIPTION_YEARLY_PRICE=1500000

# Production Access Prices (in Tomans)
ACCESS_SINGLE_PRICE=200000
ACCESS_BUNDLE_5_PRICE=850000
ACCESS_BUNDLE_10_PRICE=1500000
```

---

## ۵. تولید APP_KEY

چون دسترسی SSH نداریم، کلید را به یکی از روش‌های زیر بسازید:

**روش الف — ابزار آنلاین (پیشنهادی):**
در مرورگر باز کنید: `https://generate-random.org/laravel-key-generator`  
یک کلید `base64:...` دریافت کرده و در `.env` قرار دهید.

**روش ب — در محیط local (قبل از آپلود):**
```bash
php artisan key:generate --show
```
خروجی `base64:...` را کپی کرده و در `.env` سرور قرار دهید.

> **مهم:** کلید را هرگز در مخزن git قرار ندهید.

---

## ۶. مجوزهای پوشه‌ها (Permissions)

در File Manager یا FTP مجوزهای زیر را تنظیم کنید:

| مسیر | Permission |
|------|-----------|
| `aavaan/storage/` و تمام زیرپوشه‌ها | `775` |
| `aavaan/bootstrap/cache/` | `775` |
| `public_html/uploads/` | `775` |

در cPanel File Manager: راست‌کلیک → Permissions → عدد را وارد کنید.

---

## ۷. بهینه‌سازی Autoload (اختیاری)

اگر هاست شما از SSH محدود پشتیبانی می‌کند:
```bash
cd ~/aavaan && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

اگر SSH ندارید، این مرحله را رد کنید — سایت بدون cache هم کار می‌کند.

---

## ۸. چک‌لیست نهایی قبل از روشن کردن سایت

### محیط و پیکربندی
- [ ] `APP_DEBUG=false` در .env سرور
- [ ] `APP_ENV=production` در .env سرور
- [ ] `APP_KEY` پر شده (شروع با `base64:`)
- [ ] `APP_URL` با دامنه‌ی واقعی تنظیم شده (با `https://`)
- [ ] اطلاعات دیتابیس صحیح است و اسکیما ایمپورت شده
- [ ] `ZARINPAL_SANDBOX=false` و `ZARINPAL_MERCHANT_ID` پر شده
- [ ] اطلاعات میل سرور تنظیم شده (`MAIL_PASSWORD`)
- [ ] دو خط `index.php` اصلاح شده‌اند

### فایل‌ها و مجوزها
- [ ] پوشه‌ی `vendor/` آپلود شده و `autoload.php` وجود دارد
- [ ] مجوز `storage/` و `bootstrap/cache/` روی `775` است
- [ ] پوشه‌ی `public_html/uploads/` وجود دارد و `775` است
- [ ] لوگو در `public_html/images/logo.webp` قرار داده شده
- [ ] فونت‌ها در `public_html/fonts/` آپلود شده‌اند

### تست فرم‌ها
- [ ] فرم ثبت‌نام هنرمند (ایمیل/موبایل + رمز) کار می‌کند
- [ ] فرم ورود با ایمیل و با موبایل کار می‌کند
- [ ] فرم «فراموشی رمز» ایمیل reset می‌فرستد
- [ ] فرم تماس (`/contact`) پیام را ذخیره می‌کند
- [ ] داشبورد هنرمند نمایش داده می‌شود

### تست پرداخت
- [ ] با `ZARINPAL_SANDBOX=true` یک خرید آزمایشی انجام دهید
- [ ] صفحه‌ی موفقیت (`/payment/success`) نمایش درستی دارد
- [ ] صفحه‌ی شکست (`/payment/failed`) نمایش درستی دارد
- [ ] پس از موفقیت اشتراک، پروفایل هنرمند در جستجو ظاهر می‌شود
- [ ] پس از موفقیت خرید دسترسی، تیم تولید می‌تواند پروفایل باز کند
- [ ] وقتی سایت production شد: `ZARINPAL_SANDBOX=false` قرار دهید

### تست آپلود فایل
- [ ] آپلود آواتار (jpg/png، حداکثر ۵ مگ) از داشبورد هنرمند کار می‌کند
- [ ] آپلود تصویر پورتفولیو کار می‌کند
- [ ] آپلود ویدیوی ریل (mp4، حداکثر ۱۰۰ مگ) کار می‌کند
- [ ] حذف تصویر/ویدیو کار می‌کند

### تست UI/UX موبایل
- [ ] فونت‌های YekanBakh و IRANSansX روی موبایل بارگذاری می‌شوند
- [ ] RTL و چیدمان فارسی روی iOS Safari و Android Chrome درست است
- [ ] منوی همبرگر موبایل در هدر باز و بسته می‌شود
- [ ] صفحات اصلی (خانه، هنرمندان، تعرفه، درباره) روی موبایل responsive هستند
- [ ] فرم‌ها روی صفحه‌ی کوچک قابل استفاده هستند

### امنیت
- [ ] آدرس `https://` فعال است (SSL گواهی‌نامه نصب شده)
- [ ] صفحه‌ی `/up` پاسخ `200 OK` می‌دهد (health check لاراول)
- [ ] صفحه‌ی خطای ۴۰۴ با برند آوان نمایش داده می‌شود
- [ ] با وارد کردن آدرس اشتباه، stack trace نمایش نمی‌دهد (APP_DEBUG=false)

---

## یادداشت‌های مهم

> **بروزرسانی‌های آینده:** هر بار که فایل‌های جدید آپلود می‌کنید، اگر فقط
> view/config تغییر کرده و migration جدیدی ندارید، کافی است فایل‌ها را جایگزین
> کنید. برای migration جدید، دستور SQL معادل را در phpMyAdmin اجرا کنید یا از
> SSH محدود هاست استفاده نمایید.

> **storage/logs:** لاگ‌های خطا در `aavaan/storage/logs/laravel.log` ذخیره
> می‌شوند. این فایل را برای عیب‌یابی بررسی کنید.
