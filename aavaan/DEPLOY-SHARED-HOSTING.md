# راهنمای دیپلوی روی هاست اشتراکی

## ساختار فایل‌ها روی هاست

```
/home/youraccount/
├── public_html/          ← محتوای پوشه‌ی public/ اینجا می‌آید
│   ├── index.php         ← ویرایش می‌شود (مسیرهای ../aavaan را اصلاح کنید)
│   ├── .htaccess
│   ├── uploads/
│   └── ...
└── aavaan/               ← بقیه فایل‌های پروژه اینجا (بیرون از public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── routes/
    ├── resources/
    ├── vendor/
    └── ...
```

## مراحل آپلود

1. **آپلود پوشه اصلی**: تمام محتوای پروژه (به‌جز پوشه `public/`) را در `/home/youraccount/aavaan/` آپلود کنید.

2. **آپلود محتوای public**: محتوای داخل `public/` را در `/home/youraccount/public_html/` آپلود کنید.

3. **ویرایش `public_html/index.php`**: دو خط زیر را پیدا و ویرایش کنید:
   ```php
   // قبل از ویرایش:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   
   // بعد از ویرایش (مسیر به پوشه aavaan بدهید):
   require __DIR__.'/../aavaan/vendor/autoload.php';
   $app = require_once __DIR__.'/../aavaan/bootstrap/app.php';
   ```

4. **ساخت دیتابیس**: از phpMyAdmin یک دیتابیس MySQL بسازید.

5. **ساخت فایل .env**: فایل `.env.example` را کپی و نام آن را `.env` بگذارید. مقادیر DB_ و APP_KEY را پر کنید.

6. **APP_KEY**: در صورت دسترسی به Terminal، دستور `php artisan key:generate` را اجرا کنید.

7. **Migration**: در صورت دسترسی به Terminal یا SSH: `php artisan migrate`

8. **پوشه‌های uploads**: مطمئن شوید پوشه‌های زیر وجود دارند (chmod 755):
   - `public_html/uploads/avatars/`
   - `public_html/uploads/portfolios/`
   - `public_html/uploads/reels/`

## نکات مهم

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync` (حتماً)
- `FILESYSTEM_DISK=uploads`
