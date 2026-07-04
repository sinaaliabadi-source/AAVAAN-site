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

## دیپلوی ویژگی‌های تخصص (Artist Specialty Attributes)

> روی سرور همهٔ دستورات artisan با `php8.5` اجرا می‌شوند.

پس از آپلود این نسخه، این دستورات را به‌ترتیب اجرا کنید:

```bash
php8.5 artisan migrate --force
php8.5 artisan db:seed --class=SpecialtyAttributeDefinitionsSeeder --force
php8.5 artisan specialties:reindex
php8.5 artisan config:cache && php8.5 artisan route:cache && php8.5 artisan view:clear
```

توضیح:
- `migrate` ستون `gender` را به `artist_profiles`، ستون‌های `unit` و `is_filterable` را به
  `specialty_attribute_definitions` اضافه و جدول `artist_specialty_attribute_values` را می‌سازد.
- Seeder جدول تعریف‌ها را truncate و بازسازی می‌کند؛ **مقادیر کاربران دست‌نخورده می‌ماند**
  (منبع حقیقت ستون JSON `attributes` است). زیرشاخه‌ها هم حفظ می‌شوند و فیلد تکراری «زیرتخصص» برنمی‌گردد.
- `specialties:reindex` جدول ایندکس فیلترپذیر را برای همهٔ تخصص‌های موجود از روی JSON بازسازی می‌کند
  (تطبیق مقادیر با «کلید» تعریف انجام می‌شود، پس تغییر idها پس از seed مشکلی ایجاد نمی‌کند).

## دیپلوی «کست‌یاب» (Cast Finder)

این نسخه صفحهٔ `/dashboard/production/search` را به «کست‌یاب» با فیلتر کاملاً SQL ارتقا می‌دهد.
فقط یک migration سبک (ایندکس ترکیبی) دارد و نیازی به seed مجدد نیست:

```bash
cd /var/www/aavaan-dev
git fetch origin
git checkout claude/artist-specialties-schema-9gpn9m   # یا: git pull اگر روی همین برنچ هستید
cd aavaan
npm install && npm run build
php8.5 artisan migrate --force
php8.5 artisan config:cache && php8.5 artisan route:cache && php8.5 artisan view:clear
chown -R www-data:www-data /var/www/aavaan-dev
```

توضیح:
- `migrate` ایندکس ترکیبی `asav_sp_def_number_idx (artist_specialty_id, definition_id, value_number)`
  را برای بهینه‌کردن فیلتر بازهٔ عددیِ همبسته اضافه می‌کند.
- دستور `db:seed` و `specialties:reindex` **فقط** بعد از پرامپت قبلی لازم بودند؛ برای این نسخه لازم نیستند
  (مگر داده‌های موجود هنوز reindex نشده باشند).
- ممیزی مستقل SQL: `docs/schema-cast-finder.sql` (DDL ایندکس + ۵ نمونه‌کوئری فیلتر ترکیبی برای MariaDB).
