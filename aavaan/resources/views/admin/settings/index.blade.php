@extends('admin.layouts.app')

@section('title', 'تنظیمات عمومی')
@section('page-title', 'تنظیمات عمومی')

@section('content')

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="grid-2">

        {{-- Contact Info --}}
        <div class="card">
            <div class="card-title">📞 اطلاعات تماس</div>

            <div class="form-group">
                <label>شماره تماس</label>
                <input type="text" name="contact_phone" class="form-control"
                       value="{{ old('contact_phone', $settings['contact_phone']) }}"
                       placeholder="مثال: ۰۲۱-۱۲۳۴۵۶۷۸">
                @error('contact_phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>ایمیل تماس</label>
                <input type="email" name="contact_email" class="form-control" dir="ltr"
                       value="{{ old('contact_email', $settings['contact_email']) }}"
                       placeholder="info@aavaan.ir">
                @error('contact_email')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>آدرس</label>
                <textarea name="contact_address" class="form-control" rows="2"
                          placeholder="آدرس دفتر">{{ old('contact_address', $settings['contact_address']) }}</textarea>
                @error('contact_address')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Social Media --}}
        <div class="card">
            <div class="card-title">📱 شبکه‌های اجتماعی</div>

            <div class="form-group">
                <label>اینستاگرام (آدرس کامل)</label>
                <input type="url" name="social_instagram" class="form-control" dir="ltr"
                       value="{{ old('social_instagram', $settings['social_instagram']) }}"
                       placeholder="https://instagram.com/aavaan">
                @error('social_instagram')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>تلگرام</label>
                <input type="text" name="social_telegram" class="form-control" dir="ltr"
                       value="{{ old('social_telegram', $settings['social_telegram']) }}"
                       placeholder="@aavaan یا https://t.me/aavaan">
                @error('social_telegram')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>لینکدین (آدرس کامل)</label>
                <input type="url" name="social_linkedin" class="form-control" dir="ltr"
                       value="{{ old('social_linkedin', $settings['social_linkedin']) }}"
                       placeholder="https://linkedin.com/company/aavaan">
                @error('social_linkedin')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>توییتر (آدرس کامل)</label>
                <input type="url" name="social_twitter" class="form-control" dir="ltr"
                       value="{{ old('social_twitter', $settings['social_twitter']) }}"
                       placeholder="https://twitter.com/aavaan">
                @error('social_twitter')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- SEO --}}
        <div class="card">
            <div class="card-title">🔍 سئوی پیش‌فرض</div>

            <div class="form-group">
                <label>عنوان پیش‌فرض (تگ title)</label>
                <input type="text" name="seo_default_title" class="form-control"
                       value="{{ old('seo_default_title', $settings['seo_default_title']) }}"
                       maxlength="80"
                       placeholder="آوان — پلتفرم تخصصی کاستینگ هنرمندان ایران">
                <span class="form-hint">حداکثر ۸۰ کاراکتر</span>
                @error('seo_default_title')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>توضیح پیش‌فرض (meta description)</label>
                <textarea name="seo_default_description" class="form-control" rows="3"
                          maxlength="200"
                          placeholder="توضیح مختصر پلتفرم آوان...">{{ old('seo_default_description', $settings['seo_default_description']) }}</textarea>
                <span class="form-hint">حداکثر ۲۰۰ کاراکتر</span>
                @error('seo_default_description')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Platform Config --}}
        <div class="card">
            <div class="card-title">⚙️ تنظیمات پلتفرم</div>

            <div class="form-group">
                <label>نرخ کارمزد پلتفرم (%)</label>
                <input type="number" name="platform_commission_rate" class="form-control" dir="ltr"
                       value="{{ old('platform_commission_rate', $settings['platform_commission_rate']) }}"
                       min="0" max="100" step="0.5" placeholder="0">
                <span class="form-hint">
                    این عدد فقط ذخیره می‌شود و در فازهای آینده برای محاسبات کمیسیون استفاده خواهد شد.
                </span>
                @error('platform_commission_rate')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="alert alert-warning" style="margin-top:1rem; font-size:.82rem;">
                ⚠️ کلیدهای درگاه پرداخت (زرین‌پال) از طریق این صفحه قابل تغییر نیستند و فقط در فایل
                <code>.env</code> سرور نگهداری می‌شوند.
            </div>
        </div>

    </div>

    <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
        <button type="submit" class="btn btn-primary">💾 ذخیره تنظیمات</button>
    </div>
</form>

@endsection
