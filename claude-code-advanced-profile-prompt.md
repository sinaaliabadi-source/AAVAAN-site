# پرامپت Claude Code — سیستم «پروفایل پیشرفته» و چندتخصصی آوان

این سند ادامه‌ی مستقیم `aavaan-site-structure.md` و `claude-code-build-prompts.md` است. هدف: افزودن سیستم دسته‌بندی تخصصی کامل، پروفایل چندتخصصی، فیلدهای پرمیوم، و مدیریت رسانه (عکس روی هاست / ویدیو فقط لینک آپارات).

> **توالی اجرا:** این کار را پس از تکمیل و تأیید daungrade به Laravel 10 / PHP 8.1 اجرا کن، نه قبل از آن. کدِ این فاز هم باید با PHP 8.1 و Laravel 10.x سازگار نوشته شود (از ویژگی‌های PHP 8.2+ مثل readonly classes یا DNF types استفاده نشود).

> **محدودیت هاست:** سرور SSH ندارد. هیچ `php artisan migrate` یا `php artisan db:seed` روی سرور اجرا نمی‌شود. تمام تغییرات اسکیما و داده‌های seed باید در نهایت به‌صورت **SQL خام** برای ایمپورت دستی از طریق phpMyAdmin تحویل داده شوند (طبق پایپ‌لاین فعلی: Claude Code محلی → Claude ادیتور/حسابرس → خروجی SQL + vendor zip → سینا آپلود FTP و ایمپورت phpMyAdmin).

---

## ۱. فهرست نهایی دسته‌بندی‌های تخصصی (۲۲ دسته)

این فهرست باید در یک جدول دیتابیس seed شود، نه هاردکد در Blade — هر جای سایت (صفحه اصلی، درباره، برای هنرمندان، فرم پروفایل، فیلتر جستجو) باید از همین منبع واحد بخواند.

| # | دسته | زیرشاخه‌ها |
|---|---|---|
| ۱ | بازیگری و اجرا | بازیگر سینما/تلویزیون، بازیگر تئاتر، بازیگر کودک، نقش مکمل/سیاهی‌لشکر، گوینده/دوبلور، مجری |
| ۲ | کاسکادوری و بدل | کاسکادور، بدل بازیگر، هماهنگ‌کننده صحنه‌های اکشن |
| ۳ | کارگردانی و دستیاری صحنه | کارگردان، دستیار اول کارگردان، دستیار دوم کارگردان، منشی صحنه (Continuity)، کارگردان کستینگ |
| ۴ | نویسندگی | فیلمنامه‌نویس، دیالوگ‌نویس، نویسنده داستان/طرح، نویسنده تیزر و تبلیغات |
| ۵ | تصویربرداری | مدیر فیلمبرداری، اپراتور دوربین، فوکوس‌پولر، اپراتور پهپاد/دکل |
| ۶ | نورپردازی | مدیر نور، گافر، دستیار نور |
| ۷ | صدا | صدابردار سرصحنه، بوم اپراتور، طراح صدا، میکس، مسترینگ |
| ۸ | موسیقی | آهنگساز، تنظیم‌کننده، نوازنده، رهبر ارکستر، خواننده |
| ۹ | تدوین و پساتولید | تدوینگر تصویر، تدوینگر صدا، رنگ‌گردان، موشن‌گرافیست |
| ۱۰ | جلوه‌های ویژه و CGI | آرتیست جلوه‌های دیجیتال، متخصص Virtual Production، متخصص Motion Capture |
| ۱۱ | طراحی صحنه | طراح صحنه، سازنده دکور، مدیر ساخت |
| ۱۲ | طراحی لباس | طراح لباس، خیاط/دوزنده اختصاصی، مدیر کارگاه لباس |
| ۱۳ | گریم | گریمور زیبایی، گریم کاراکتر، گریم پروتز/SFX، طراح مو |
| ۱۴ | عکاسی | عکاس تبلیغاتی، عکاس صنعتی، عکاس پشت‌صحنه، عکاس پرتره، عکاس مد |
| ۱۵ | تهیه و مدیریت تولید | تهیه‌کننده، تهیه‌کننده اجرایی، مدیر تولید، مدیر لوکیشن، برنامه‌ریز |
| ۱۶ | کستینگ | کارگردان کستینگ حرفه‌ای، کستینگ کودک، کستینگ تبلیغاتی |
| ۱۷ | روابط عمومی و بازاریابی | مدیر روابط عمومی، مدیر بازاریابی، مدیر شبکه‌های اجتماعی |
| ۱۸ | انیمیشن | انیماتور دو بعدی، انیماتور سه بعدی، انیماتور کاراکتر، موشن دیزاینر |
| ۱۹ | بازی‌های ویدیویی | هنرمند بازی، انیماتور بازی، صداپیشه بازی، طراح روایت |
| ۲۰ | عوامل صحنه و پشت‌صحنه | مدیر صحنه، مسئول تجهیزات، راننده تولید، مسئول حمل‌ونقل، مسئول تدارکات، مسئول لوکیشن |
| ۲۱ | آموزش و مربیگری | مربی بازیگری، مربی لهجه، مربی صدا، مربی حرکت |
| ۲۲ | ترجمه و زیرنویس | مترجم فیلمنامه، زیرنویس‌گذار، مترجم همزمان صحنه |

---

## ۲. فیلدهای عمومی پیشرفته (در سطح پروفایل پایه، نه داخل تخصص‌ها)

| گروه | فیلد | نوع | سطح نمایش |
|---|---|---|---|
| هویت حرفه‌ای | نام هنری | text | عمومی |
| | نام رسمی | text | **فقط تیم تولید با دسترسی پرداخت‌شده** |
| | شهر محل سکونت | select | عمومی |
| | آمادگی برای سفر کاری | boolean | عمومی |
| | آمادگی برای اقامت بلندمدت | boolean | عمومی |
| | وضعیت سربازی (فقط آقایان) | select | **فقط تیم تولید با دسترسی** |
| | وضعیت گذرنامه | select (دارد معتبر/منقضی/ندارد) | **فقط تیم تولید با دسترسی** |
| | امکان همکاری بین‌المللی | boolean | عمومی |
| اعتبار حرفه‌ای | تعداد پروژه‌های انجام‌شده | number | عمومی |
| | تعداد پروژه‌های منتشرشده | number | عمومی |
| | جوایز (repeater: عنوان، سال، رویداد) | json | عمومی |
| | عضویت در انجمن‌ها و صنوف (repeater) | json | عمومی |
| تأیید هویت | تأیید شماره موبایل | boolean (سیستمی) | عمومی (فقط badge) |
| | تأیید رزومه توسط آوان | enum (pending/approved/rejected) | عمومی (فقط badge) |
| | نشان Professional Verified | boolean (مشتق‌شده) | عمومی |
| در دسترس بودن | وضعیت | enum (آماده فوری/مشغول پروژه/آزاد از تاریخ) | عمومی |
| | تاریخ آزاد شدن | date (نمایش شرطی) | عمومی |
| | ظرفیت پروژه همزمان | number | عمومی |
| نرخ همکاری | بازه نرخ روزانه | number range، اختیاری، toggle نمایش/عدم‌نمایش توسط هنرمند | عمومی در صورت فعال‌سازی |
| لینک‌ها | IMDb / Instagram / LinkedIn / YouTube / Vimeo / وب‌سایت شخصی | url × 6 | عمومی |

**نکته‌ی حریم خصوصی:** فیلدهایی که سطح نمایش‌شان "فقط تیم تولید با دسترسی پرداخت‌شده" است باید در همان منطق فعلیِ "اطلاعات تماس فقط بعد از پرداخت" پیاده شوند (همان سیستم دسترسی موجود در `dashboard/production/search`)، نه یک مکانیزم جدید موازی.

---

## ۳. معماری دیتابیس پیشنهادی

به‌جای ساختن ۲۲ جدول مجزا (که با هر تغییر آینده نیاز به migration جدید دارد)، از یک مدل **schema-driven** استفاده شود — دقیقاً هم‌راستا با معماری ماژولار درگاه پرداخت که قبلاً پیاده شده:

```
specialty_categories
  id, slug, name_fa, parent_id (nullable, FK self), sort_order, icon, is_active

specialty_attribute_definitions
  id, category_id (FK), key, label_fa, field_type
      enum: text, textarea, number, boolean, select, multiselect, url, date, file_link
  options (json, nullable — برای select/multiselect)
  is_required (bool)
  is_premium (bool — آیا فقط مشترکین پلن بالاتر می‌توانند پر کنند)
  visibility (enum: public, production_team_only)
  sort_order

artist_specialties
  id, user_id (FK), category_id (FK), is_primary (bool),
  years_experience (nullable int), attributes (JSON — مقدار فیلدهای داینامیک),
  created_at, updated_at
  UNIQUE(user_id, category_id)

artist_specialty_media
  id, artist_specialty_id (FK), type (enum: photo, video_link, document),
  file_path (nullable — فقط برای photo), external_url (nullable — فقط برای video_link، باید آپارات باشد),
  title, sort_order

artist_profile_premium  (یا ستون‌های اضافه روی جدول پروفایل موجود)
  user_id, stage_name, legal_name, willing_to_travel, willing_long_stay,
  military_status, passport_status, international_collaboration,
  completed_projects_count, published_projects_count, awards (json),
  memberships (json), availability_status, available_from_date,
  concurrent_capacity, day_rate_min, day_rate_max, show_day_rate (bool),
  imdb_url, instagram_url, linkedin_url, youtube_url, vimeo_url, website_url

verifications
  id, user_id, type (enum: phone, resume, professional),
  status (enum: pending, approved, rejected), reviewed_at, notes

analytics_events
  id, artist_id, event_type (enum: view, save, invite, response),
  production_team_id (nullable), created_at
```

**چرا JSON روی `attributes` به‌جای جدول EAV کامل؟** چون MariaDB 10.6.24 از نوع JSON و توابع جستجوی آن پشتیبانی کامل دارد، حجم کوئری‌ها برای این مقیاس (چند هزار پروفایل) مشکلی ایجاد نمی‌کند، و افزودن فیلد جدید به یک دسته فقط یک ردیف جدید در `specialty_attribute_definitions` نیاز دارد — نه migration. اعتبارسنجی مقادیر JSON در لایه‌ی اپلیکیشن (Form Request) در برابر تعریف‌های همان دسته انجام می‌شود.

### دستورات migration (برای اجرای محلی توسط Claude Code)
```bash
php artisan make:migration create_specialty_categories_table
php artisan make:migration create_specialty_attribute_definitions_table
php artisan make:migration create_artist_specialties_table
php artisan make:migration create_artist_specialty_media_table
php artisan make:migration create_artist_profile_premium_table
php artisan make:migration create_verifications_table
php artisan make:migration create_analytics_events_table
php artisan make:seeder SpecialtyCategoriesSeeder
php artisan make:seeder SpecialtyAttributeDefinitionsSeeder
```

**الزام نهایی:** بعد از اجرای محلی موفق (`composer install` بدون `--ignore-platform-reqs`، migrate روی SQLite یا MySQL محلی)، خروجی نهایی باید شامل یک فایل `advanced-profile-schema.sql` باشد که تمام `CREATE TABLE` و `INSERT` های seed را به‌صورت خام و آماده‌ی ایمپورت در phpMyAdmin شامل شود — این کار طبق روال فعلی توسط Claude (در مرحله‌ی حسابرسی) انجام می‌شود، نه لزوماً خودِ Claude Code، اما باید مطمئن شوی migration‌ها به شکلی نوشته شده‌اند که این خروجی‌گیری ساده باشد (بدون از `DB::raw` با سینتکس غیرقابل‌انتقال).

---

## ۴. مدیریت رسانه — عکس روی هاست، ویدیو فقط لینک آپارات

- **عکس (Headshot، Full Body Shot، گالری نمونه‌کار):** آپلود واقعی روی سرور، با فشرده‌سازی/resize اجباری (حداکثر ۲۰۰۰px ضلع بزرگ، حداکثر حجم ۱ مگابایت بعد از فشرده‌سازی). برای جلوگیری از پر شدن فضای هاست، یک سقف کلی برای هر هنرمند تعریف شود (مثلاً حداکثر ۳۰ عکس در کل پروفایل، صرف‌نظر از تعداد تخصص‌ها) نه نامحدود.
- **ویدیو (Showreel، Self Tape، Video Introduction، دموی موسیقی و...):** هیچ آپلود فایل ویدیویی روی هاست مجاز نیست. فقط یک فیلد URL با اعتبارسنجی regex مطابق دامنه آپارات:
  ```
  ^https://(www\.)?aparat\.com/v/[A-Za-z0-9]+
  ```
  در نمایش، از iframe امبد آپارات استفاده شود (`https://www.aparat.com/video/video/embed/videohash/{code}/vt/frame`)، نه پلیر بومی.
- این منطق باید روی همه‌ی فیلدهای نوع `video_link` در `artist_specialty_media` و فیلد پرمیوم «Video Introduction» اعمال شود.

---

## ۵. ویژگی‌های پرمیوم (Phase بعدی — فقط طراحی داده، نه منطق کامل کسب‌وکار)

این فاز فقط **مدل داده و UI نمایش وضعیت** پرمیوم را می‌سازد؛ منطق کامل گیتینگ (قفل‌کردن واقعی فیلدها بر اساس پرداخت)، آزمون مهارت، توصیه‌نامه، تقویم در‌دسترس‌بودن، و الگوریتم Match Score به یک فاز جداگانه موکول می‌شود تا از حجم بیش‌ازحد این پرامپت جلوگیری شود.

فهرست ویژگی‌های پرمیومِ هدف نهایی (برای آگاهی Claude Code از مسیر آینده، فقط جدول lookup ساخته شود):

`professional_badge، featured_profile، unlimited_portfolio، video_introduction، availability_calendar، direct_contact، project_match_score، verified_credits، skill_assessments، recommendation_letters، featured_projects، advanced_analytics`

جدول `premium_features (id, slug, name_fa, description, tier_required)` ساخته شود و به پلن‌های اشتراک فعلی (ماهانه/سالانه) یک ستون `tier` اضافه شود تا بعداً قابل گسترش به چند سطح (پایه/حرفه‌ای/ویژه) باشد.

---

## ۶. صفحاتی که باید به‌روزرسانی شوند

| صفحه | تغییر |
|---|---|
| `/` صفحه اصلی | بخش «معرفی رشته‌های هنری» باید از `specialty_categories` بخواند. با ۲۲ دسته، **همه را در صفحه اصلی نچپان** — گروه‌بندی بصری (مثلاً «اجرا»، «فنی و تولید»، «خلق محتوا»، «مدیریت») با لینک «مشاهده همه‌ی رشته‌ها» بساز. |
| `/about` | تأکید بر چندرشته‌ای بودن؛ می‌تواند آمار تعداد دسته‌ها را داینامیک نمایش دهد. |
| `/artists` | فهرست کامل دسته‌ها (می‌تواند صفحه‌ی مستقل `/artists/categories` باشد). |
| `/dashboard/artist/profile` | فرم افزودن چند تخصص: انتخاب دسته → نمایش فرم داینامیک بر اساس `specialty_attribute_definitions` همان دسته → آپلود عکس / فیلد لینک آپارات → علامت‌گذاری یک تخصص به‌عنوان «اصلی». |
| `/profile/{username}` | نمایش هر تخصص به‌صورت تب یا بخش جدا، با فیلدهای مخصوص همان دسته و رعایت `visibility`. |
| `/dashboard/production/search` | فیلتر پویا: انتخاب دسته → فیلترهای اختصاصی همان دسته (مثلاً قد/وزن برای بازیگری، نرم‌افزار برای تدوین) ساخته‌شده از همان `specialty_attribute_definitions`. |
| `/pricing` | اگر سطح‌بندی پلن (tier) اضافه شد، جدول تعرفه باید ویژگی‌های پرمیوم هر سطح را نشان دهد (فقط نمایشی در این فاز). |

---

## ۷. خارج از محدوده‌ی این فاز

- منطق کامل قفل‌کردن/باز‌کردن فیلدهای پرمیوم بر اساس پرداخت واقعی
- آزمون مهارت (Skill Assessments)
- توصیه‌نامه (Recommendation Letters) و گردش‌کار تأیید آن
- الگوریتم Project Match Score
- تقویم در‌دسترس‌بودن تعاملی (Availability Calendar UI)
- پنل ادمین کامل برای تأیید رزومه (`verifications`) — در این فاز فقط جدول و یک مسیر محافظت‌شده‌ی ساده برای تغییر دستی وضعیت کافی است؛ تا زمان ساخت پنل کامل، سینا می‌تواند مستقیماً از phpMyAdmin وضعیت را تغییر دهد.
- پیام‌رسانی داخلی (طبق تصمیم قبلی، همچنان خارج از محدوده)

---

## ۸. متن سه پرامپت متوالی برای Claude Code

> این سه پرامپت را یکی‌یکی اجرا کن، دقیقاً مثل ۱۱ پرامپت قبلی. بعد از هرکدام، خروجی را برای حسابرسی به Claude (این چت) بده.

### پرامپت Phase 1 — مدل داده و Seed
```
در پروژه AAVAAN-site (برنچ فعلی پس از داون‌گرید PHP 8.1 / Laravel 10)، سیستم دسته‌بندی تخصصی و پروفایل چندتخصصی را اضافه کن:

۱. جداول زیر را با migration بساز (مطابق مستند پیوست‌شده advanced-profile-schema که جزئیات ستون‌ها در آن آمده):
   specialty_categories, specialty_attribute_definitions, artist_specialties,
   artist_specialty_media, artist_profile_premium, verifications, analytics_events

۲. Seeder بساز که ۲۲ دسته‌ی تخصصی و زیرشاخه‌هایشان (طبق جدول بخش ۱ مستند) را در specialty_categories
   و فیلدهای اختصاصی هر دسته (طبق فیلدهایی که قبلاً برای هرکدام تعریف شده) را در specialty_attribute_definitions وارد کند.

۳. مدل‌های Eloquent متناظر با روابط مناسب (User hasMany ArtistSpecialty، ArtistSpecialty belongsTo SpecialtyCategory و hasMany ArtistSpecialtyMedia) بساز.

۴. فیلد attributes روی artist_specialties از نوع JSON باشد و یک Form Request بنویس که مقدارهای ورودی را
   در برابر specialty_attribute_definitions همان category_id اعتبارسنجی کند (فیلدهای required، نوع داده، گزینه‌های select).

۵. کد باید کاملاً با PHP 8.1.30 و Laravel 10.x سازگار باشد — از composer install واقعی (بدون ignore-platform-reqs)
   مطمئن شو قبل از تحویل.

۶. در پایان، یک خروجی خلاصه از composer install و migrate محلی (روی sqlite) ارائه بده.
```

### پرامپت Phase 2 — فرم پروفایل هنرمند (چندتخصصی)
```
روی مدل داده‌ی Phase 1، رابط کاربری dashboard/artist/profile را گسترش بده:

۱. بخش «تخصص‌های من»: امکان افزودن چند تخصص از specialty_categories، با مشخص‌کردن یکی به‌عنوان «تخصص اصلی».
۲. برای هر تخصص انتخاب‌شده، فرم داینامیک بر اساس specialty_attribute_definitions همان دسته رندر شود
   (Alpine.js برای رفتار پویای فرم، بدون reload صفحه).
۳. آپلود عکس (Headshot / Full Body Shot / گالری) برای هر تخصص جداگانه، با resize/فشرده‌سازی سمت سرور
   (حداکثر ۲۰۰۰px ضلع بزرگ، حداکثر ۱ مگابایت بعد از فشرده‌سازی) و سقف کلی ۳۰ عکس در کل پروفایل کاربر.
۴. برای هر فیلد نوع video_link (Showreel، Self Tape و مشابه)، فقط یک input متنی برای لینک آپارات با
   اعتبارسنجی regex ^https://(www\.)?aparat\.com/v/[A-Za-z0-9]+ — هیچ آپلود فایل ویدیویی مجاز نیست.
۵. فرم فیلدهای عمومی پیشرفته (artist_profile_premium) را هم به همین صفحه یا یک تب جدا اضافه کن
   (نام هنری/رسمی، وضعیت سربازی، گذرنامه، در‌دسترس‌بودن، لینک‌های IMDb/اینستاگرام/...).
۶. فیلدهایی با visibility=production_team_only را در این فرم به‌وضوح به‌عنوان «فقط برای تیم تولید با دسترسی»
   علامت‌گذاری کن (UI hint).
```

### پرامپت Phase 3 — نمایش عمومی، جستجوی تیم تولید، و صفحات معرفی
```
۱. صفحه profile/{username} را برای نمایش چند تخصص بازطراحی کن (تب یا آکاردئون به‌ازای هر تخصص)،
   با رعایت visibility فیلدها (فیلدهای production_team_only فقط برای کاربر تیم تولید با دسترسی فعال نمایش داده شوند)
   و امبد ویدیوی آپارات به‌جای پلیر بومی.

۲. صفحه dashboard/production/search را به فیلتر پویا مجهز کن: انتخاب دسته‌ی تخصصی →
   نمایش فیلترهای اختصاصی همان دسته بر اساس specialty_attribute_definitions (مثلاً بازه‌ی قد/وزن برای بازیگری،
   نرم‌افزار برای تدوین، نوع کاسکادوری برای کاسکادوری).

۳. بخش «معرفی رشته‌های هنری» در صفحه اصلی (/) را از specialty_categories بخوان و در قالب گروه‌های بصری
   (اجرا / فنی و تولید / خلق محتوا / مدیریت) نمایش بده، با لینک «مشاهده همه‌ی رشته‌ها».

۴. صفحات /about و /artists را برای اشاره به فهرست کامل و تعداد دسته‌ها به‌روزرسانی کن.

۵. در پایان، گزارش بده کدام صفحات تغییر کردند و آیا composer install و یک migrate محلی موفق بوده‌اند.
```

---

## ۹. یادآوری برای مرحله‌ی حسابرسی (Claude، نه Claude Code)

بعد از هر فاز:
- کلون برنچ، `composer install` واقعی بدون `--ignore-platform-reqs`
- migrate محلی روی sqlite برای تست صحت migration‌ها
- تولید `advanced-profile-schema.sql` (یا افزودن تدریجی به همان فایل در هر فاز) برای ایمپورت در phpMyAdmin
- بررسی صریح دو باگ شناخته‌شده‌ی قبلی (`$h->amount` در subscription.blade.php و mismatch نام لوگو) که نباید در این فاز دوباره سر باز کنند یا نادیده گرفته شوند
