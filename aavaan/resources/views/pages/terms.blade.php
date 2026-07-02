@extends('layouts.app')
@section('title', 'شرایط استفاده — آوان')
@section('meta-description', 'شرایط و قوانین استفاده از پلتفرم کاستینگ آوان — حقوق و تکالیف هنرمندان و تیم‌های تولید')

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
.legal-content ul { margin: .5rem 0 1rem 0; padding-right: 1.25rem; }
.legal-content ul li { font-size:.9rem; color:#444; line-height:1.85; margin-bottom:.35rem; }
.legal-date { font-size:.82rem; color:var(--color-muted); margin-top:2.5rem; padding-top:1rem; border-top:1px solid #ede8dc; }

@media (max-width: 820px) { .legal-layout { grid-template-columns: 1fr; } .legal-nav { position:static; } }
</style>
@endpush

@section('content')

<section class="legal-hero">
    <div class="container">
        <h1 class="legal-hero-title">شرایط استفاده از آوان</h1>
        <p class="legal-hero-sub">لطفاً پیش از استفاده از خدمات، این شرایط را با دقت مطالعه کنید</p>
    </div>
</section>

<section class="legal-body">
    <div class="container">
        <div class="legal-layout">

            {{-- فهرست مطالب --}}
            <nav class="legal-nav">
                <div class="legal-nav-title">فهرست مطالب</div>
                <a href="#acceptance">پذیرش شرایط</a>
                <a href="#services">خدمات آوان</a>
                <a href="#account">حساب کاربری</a>
                <a href="#artist-subscription">اشتراک هنرمندان</a>
                <a href="#production-access">دسترسی تیم تولید</a>
                <a href="#content">محتوای کاربران</a>
                <a href="#privacy">حریم خصوصی</a>
                <a href="#ip">مالکیت معنوی</a>
                <a href="#liability">محدودیت مسئولیت</a>
                <a href="#changes">تغییرات</a>
            </nav>

            {{-- محتوا --}}
            <div class="legal-content">

                <h2 id="acceptance">۱. پذیرش شرایط</h2>
                <p>با استفاده از وب‌سایت آوان (aavaan.com)، ثبت‌نام در آن یا بهره‌گیری از خدمات آن، شرایط زیر را می‌پذیرید. در صورت عدم موافقت با این شرایط، لطفاً از خدمات آوان استفاده نکنید.</p>

                <h2 id="services">۲. خدمات آوان</h2>
                <p>آوان یک پلتفرم آنلاین است که هنرمندان را به تیم‌های تولید سینما، تئاتر، موسیقی و سایر حوزه‌های هنری متصل می‌کند. آوان:</p>
                <ul>
                    <li>واسطه‌ای برای نمایش پروفایل و نمونه‌کار هنرمندان است</li>
                    <li>ابزار جستجو و فیلتر برای تیم‌های تولید فراهم می‌کند</li>
                    <li>کارگزار هنری نیست و مسئولیت قراردادهای بین هنرمند و تیم تولید را بر عهده ندارد</li>
                    <li>هیچ تضمینی برای استخدام یا همکاری نمی‌دهد</li>
                </ul>

                <h2 id="account">۳. حساب کاربری</h2>
                <p>هر کاربر مسئول حفاظت از اطلاعات ورود (ایمیل/موبایل و رمز عبور) خود است. انتقال حساب به دیگری مجاز نیست. کاربران باید اطلاعات صحیح و به‌روز ارائه دهند. آوان حق دارد در صورت تخلف، حساب کاربری را تعلیق یا حذف کند.</p>

                <h2 id="artist-subscription">۴. اشتراک هنرمندان</h2>
                <p>هنرمندان می‌توانند اشتراک ماهانه یا سالانه تهیه کنند. موارد زیر قابل توجه است:</p>
                <ul>
                    <li>پرداخت از طریق درگاه امن زرین‌پال انجام می‌شود</li>
                    <li>اشتراک به‌صورت خودکار تمدید نمی‌شود — کاربر باید قبل از پایان دوره تمدید کند</li>
                    <li>در صورت لغو اشتراک، پروفایل از نتایج جستجو حذف می‌شود اما اطلاعات حذف نمی‌شود</li>
                    <li>بازگشت وجه در صورتی امکان‌پذیر است که از اشتراک استفاده نشده باشد و ظرف ۴۸ ساعت از خرید درخواست داده شود</li>
                </ul>

                <h2 id="production-access">۵. دسترسی تیم‌های تولید</h2>
                <p>تیم‌های تولید می‌توانند بسته‌های دسترسی (تکی، ۵ عددی یا ۱۰ عددی) تهیه کنند. شرایط:</p>
                <ul>
                    <li>اعتبارها پس از خرید فعال و بدون تاریخ انقضا هستند</li>
                    <li>استفاده از اطلاعات هنرمندان فقط برای پروژه‌های هنری واقعی مجاز است</li>
                    <li>فروش مجدد، اشتراک‌گذاری یا انتشار عمومی اطلاعات هنرمندان ممنوع است</li>
                    <li>اعتبارهای استفاده‌شده قابل بازگشت نیستند</li>
                </ul>

                <h2 id="content">۶. محتوای کاربران</h2>
                <p>هنرمندان مسئول صحت و اصالت تمام اطلاعاتی هستند که در پروفایل خود منتشر می‌کنند. محتوای توهین‌آمیز، گمراه‌کننده، دارای حق مؤلف دیگران یا مغایر قوانین جمهوری اسلامی ایران مجاز نیست. آوان حق دارد محتوای نامناسب را بدون اطلاع قبلی حذف کند.</p>

                <h2 id="privacy">۷. حریم خصوصی</h2>
                <p>جمع‌آوری و استفاده از اطلاعات شخصی کاربران طبق <a href="{{ route('privacy') }}" style="color:var(--color-accent)">سیاست حریم خصوصی</a> آوان انجام می‌شود. با پذیرش این شرایط، با سیاست حریم خصوصی نیز موافقت می‌کنید.</p>

                <h2 id="ip">۸. مالکیت معنوی</h2>
                <p>محتوای آپلود‌شده توسط هنرمندان (تصاویر، ویدیوها، متن بیوگرافی) متعلق به خود هنرمند است. آوان صرفاً مجوز نمایش در پلتفرم را دارد. طراحی، لوگو، کدنویسی و سایر محتوای آوان متعلق به تیم آوان است و کپی‌برداری بدون اجازه ممنوع است.</p>

                <h2 id="liability">۹. محدودیت مسئولیت</h2>
                <p>آوان تضمین استخدام، همکاری یا موفقیت در کاستینگ نمی‌دهد. آوان مسئول خسارات ناشی از تصمیمات تیم‌های تولید در انتخاب یا عدم انتخاب هنرمندان نیست. آوان خدمات را «همان‌طور که هست» (as-is) ارائه می‌دهد و مسئولیتی برای وقفه‌های موقت سرویس ندارد.</p>

                <h2 id="changes">۱۰. تغییرات در شرایط</h2>
                <p>آوان می‌تواند این شرایط را تغییر دهد. تغییرات از طریق ایمیل یا اطلاع‌رسانی در سایت اعلام می‌شود. ادامه استفاده از خدمات پس از اطلاع‌رسانی به معنای پذیرش تغییرات است.</p>

                <div class="legal-date">این شرایط آخرین بار در {{ date('Y/m/d') }} به‌روزرسانی شده است. برای سوال: <a href="{{ route('contact') }}" style="color:var(--color-accent)">تماس با ما</a></div>
            </div>
        </div>
    </div>
</section>

@endsection
