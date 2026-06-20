@extends('layouts.app')
@section('title', 'حریم خصوصی — آوان')
@section('meta-description', 'سیاست حریم خصوصی آوان — نحوه جمع‌آوری، استفاده و حفاظت از اطلاعات کاربران')

@push('styles')
<style>
.legal-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 3.5rem 0;
    text-align: center;
}
.legal-hero-title { font-family:'YekanBakh',Tahoma,sans-serif; font-size:clamp(1.5rem,3.5vw,2.2rem); font-weight:800; color:var(--color-accent); margin-bottom:.4rem; }
.legal-hero-sub { color:#c8d0e0; font-size:.88rem; }

.legal-body { padding: 3.5rem 0 5rem; }
.legal-layout { display: grid; grid-template-columns: 240px 1fr; gap: 3rem; align-items: start; }

.legal-nav { position: sticky; top: 80px; background:#fff; border-radius:var(--radius); padding:1.25rem; border:1px solid #ede8dc; }
.legal-nav-title { font-family:'YekanBakh'; font-weight:700; font-size:.9rem; color:var(--color-primary); margin-bottom:.75rem; }
.legal-nav a { display:block; font-size:.83rem; color:var(--color-muted); padding:.35rem 0; text-decoration:none; border-right:2px solid transparent; padding-right:.6rem; transition:color .15s, border-color .15s; }
.legal-nav a:hover { color:var(--color-accent); border-right-color:var(--color-accent); }

.legal-content h2 { font-family:'YekanBakh'; font-size:1.15rem; font-weight:700; color:var(--color-primary); margin:2.25rem 0 .75rem; padding-bottom:.5rem; border-bottom:1px solid #ede8dc; }
.legal-content h2:first-child { margin-top:0; }
.legal-content p { font-size:.9rem; color:#444; line-height:1.9; margin-bottom:.75rem; }
.legal-content ul { margin:.5rem 0 1rem 0; padding-right:1.25rem; }
.legal-content ul li { font-size:.9rem; color:#444; line-height:1.85; margin-bottom:.35rem; }

.highlight-box {
    background: rgba(92,111,79,.08);
    border: 1.5px solid rgba(92,111,79,.3);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    margin: 1rem 0;
    font-size: .9rem;
    color: #2d4a2d;
    line-height: 1.85;
}
.highlight-box strong { color: var(--color-success); }

.legal-date { font-size:.82rem; color:var(--color-muted); margin-top:2.5rem; padding-top:1rem; border-top:1px solid #ede8dc; }

@media (max-width: 820px) { .legal-layout { grid-template-columns: 1fr; } .legal-nav { position:static; } }
</style>
@endpush

@section('content')

<section class="legal-hero">
    <div class="container">
        <h1 class="legal-hero-title">حریم خصوصی</h1>
        <p class="legal-hero-sub">آوان به حفاظت از اطلاعات شخصی کاربران اهمیت می‌دهد</p>
    </div>
</section>

<section class="legal-body">
    <div class="container">
        <div class="legal-layout">

            {{-- فهرست مطالب --}}
            <nav class="legal-nav">
                <div class="legal-nav-title">فهرست مطالب</div>
                <a href="#intro">مقدمه</a>
                <a href="#collect">اطلاعات جمع‌آوری‌شده</a>
                <a href="#use">نحوه استفاده</a>
                <a href="#artist-protection">حفاظت از اطلاعات هنرمندان</a>
                <a href="#sharing">اشتراک‌گذاری</a>
                <a href="#cookies">کوکی‌ها</a>
                <a href="#rights">حقوق کاربران</a>
                <a href="#security">امنیت</a>
                <a href="#contact-privacy">تماس</a>
            </nav>

            {{-- محتوا --}}
            <div class="legal-content">

                <h2 id="intro">۱. مقدمه</h2>
                <p>این سیاست حریم خصوصی توضیح می‌دهد که آوان چه اطلاعاتی جمع‌آوری می‌کند، چگونه از آن‌ها استفاده می‌کند و چه حقوقی برای کاربران در رابطه با اطلاعاتشان وجود دارد. با استفاده از آوان، با این سیاست موافقت می‌کنید.</p>

                <h2 id="collect">۲. اطلاعاتی که جمع‌آوری می‌کنیم</h2>
                <p>آوان اطلاعات زیر را از کاربران جمع‌آوری می‌کند:</p>
                <ul>
                    <li><strong>اطلاعات هویتی:</strong> نام، آدرس ایمیل، شماره موبایل</li>
                    <li><strong>اطلاعات پروفایل هنرمند:</strong> رشته هنری، شهر، سابقه کاری، بیوگرافی، تصاویر و ویدیوها</li>
                    <li><strong>اطلاعات پرداخت:</strong> سوابق تراکنش (بدون ذخیره اطلاعات کارت بانکی)</li>
                    <li><strong>اطلاعات استفاده:</strong> صفحات بازدیدشده، جستجوها، زمان حضور در سایت</li>
                    <li><strong>اطلاعات فنی:</strong> آدرس IP، نوع مرورگر، دستگاه</li>
                </ul>

                <h2 id="use">۳. نحوه استفاده از اطلاعات</h2>
                <p>اطلاعات جمع‌آوری‌شده برای اهداف زیر استفاده می‌شود:</p>
                <ul>
                    <li>نمایش پروفایل هنرمندان در نتایج جستجو</li>
                    <li>مدیریت حساب کاربری و احراز هویت</li>
                    <li>پردازش پرداخت‌ها و صدور فاکتور</li>
                    <li>بهبود کیفیت خدمات و تجربه کاربری</li>
                    <li>ارسال اطلاعیه‌های مرتبط با حساب (مانند تمدید اشتراک)</li>
                    <li>پشتیبانی و پاسخ به درخواست‌های کاربران</li>
                </ul>

                <h2 id="artist-protection">۴. حفاظت از اطلاعات هنرمندان</h2>
                <div class="highlight-box">
                    <strong>مهم:</strong> اطلاعات تماس هنرمندان (شماره موبایل و آدرس ایمیل) به صورت عمومی نمایش داده نمی‌شود. این اطلاعات فقط برای تیم‌های تولیدی که اعتبار فعال خریداری کرده‌اند قابل مشاهده است. آوان متعهد است از این قانون سرسختانه پیروی کند.
                </div>
                <p>همچنین:</p>
                <ul>
                    <li>هنرمندان می‌توانند در هر زمان اطلاعات پروفایل خود را ویرایش یا حذف کنند</li>
                    <li>عکس و ویدیوی هنرمندان فقط در پلتفرم آوان استفاده می‌شود</li>
                    <li>اطلاعات هنرمندان به پلتفرم‌های دیگر یا جستجوگرها ارسال نمی‌شود</li>
                </ul>

                <h2 id="sharing">۵. اشتراک‌گذاری اطلاعات</h2>
                <p>آوان اطلاعات شخصی کاربران را نمی‌فروشد و جز در موارد زیر به اشخاص ثالث منتقل نمی‌کند:</p>
                <ul>
                    <li>درگاه پرداخت (زرین‌پال) برای پردازش تراکنش‌ها</li>
                    <li>الزامات قانونی (در صورت دستور مراجع قضایی)</li>
                    <li>با رضایت صریح کاربر</li>
                </ul>

                <h2 id="cookies">۶. کوکی‌ها</h2>
                <p>آوان از کوکی‌ها برای موارد زیر استفاده می‌کند:</p>
                <ul>
                    <li>حفظ وضعیت ورود (Login Session)</li>
                    <li>ذخیره تنظیمات کاربر</li>
                    <li>بهبود عملکرد و تجربه کاربری</li>
                </ul>
                <p>می‌توانید کوکی‌ها را در مرورگر خود غیرفعال کنید، اما برخی قابلیت‌های سایت ممکن است محدود شوند.</p>

                <h2 id="rights">۷. حقوق کاربران</h2>
                <p>هر کاربر حق دارد:</p>
                <ul>
                    <li>به اطلاعات شخصی خود دسترسی داشته باشد</li>
                    <li>اطلاعات نادرست را اصلاح کند</li>
                    <li>درخواست حذف حساب و اطلاعات خود را بدهد</li>
                    <li>از دریافت ایمیل‌های بازاریابی انصراف دهد</li>
                </ul>
                <p>برای اعمال این حقوق با آدرس <a href="mailto:privacy@aavaan.com" style="color:var(--color-accent)">privacy@aavaan.com</a> تماس بگیرید.</p>

                <h2 id="security">۸. امنیت</h2>
                <p>آوان از تدابیر امنیتی زیر برای حفاظت از اطلاعات استفاده می‌کند:</p>
                <ul>
                    <li>ارتباط رمزنگاری‌شده با پروتکل HTTPS</li>
                    <li>ذخیره رمزهای عبور با الگوریتم‌های هش امن (bcrypt)</li>
                    <li>دسترسی محدود کارکنان به اطلاعات کاربران</li>
                    <li>بررسی‌های امنیتی منظم</li>
                </ul>
                <p>هیچ سیستمی صد درصد امن نیست. در صورت مشاهده فعالیت مشکوک، فوری با ما تماس بگیرید.</p>

                <h2 id="contact-privacy">۹. تماس درباره حریم خصوصی</h2>
                <p>برای سوال، درخواست یا گزارش نگرانی درباره حریم خصوصی:</p>
                <ul>
                    <li>ایمیل: <a href="mailto:privacy@aavaan.com" style="color:var(--color-accent)">privacy@aavaan.com</a></li>
                    <li>فرم تماس: <a href="{{ route('contact') }}" style="color:var(--color-accent)">تماس با ما</a></li>
                </ul>

                <div class="legal-date">این سیاست آخرین بار در {{ date('Y/m/d') }} به‌روزرسانی شده است.</div>
            </div>
        </div>
    </div>
</section>

@endsection
