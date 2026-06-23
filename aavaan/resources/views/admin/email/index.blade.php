@extends('admin.layouts.app')
@section('title', 'ارسال ایمیل')
@section('page-title', 'ارسال ایمیل به کاربران')

@section('content')

<div class="grid-2">

    {{-- ── Compose Form ── --}}
    <div class="card" style="grid-column: 1 / 2;">
        <div class="card-title">✉️ ارسال ایمیل جدید</div>

        <div x-data="{ recipientType: '{{ old('recipient_type', 'all') }}' }">
            <form method="POST" action="{{ route('admin.email.send') }}">
                @csrf

                {{-- Recipient type --}}
                <div class="form-group">
                    <label>گیرنده</label>
                    <select name="recipient_type"
                            class="form-control @error('recipient_type') is-invalid @enderror"
                            x-model="recipientType">
                        <option value="all">همه کاربران (هنرمندان + تیم‌های تولید)</option>
                        <option value="artists">فقط هنرمندان ({{ number_format($artistCount) }} نفر)</option>
                        <option value="production">فقط تیم‌های تولید ({{ number_format($productionCount) }} نفر)</option>
                        <option value="specific">ایمیل مشخص</option>
                    </select>
                    @error('recipient_type')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                {{-- Specific email (shown only when recipient_type === specific) --}}
                <div class="form-group" x-show="recipientType === 'specific'" x-cloak>
                    <label>آدرس ایمیل</label>
                    <input type="email" name="specific_email"
                           class="form-control @error('specific_email') is-invalid @enderror"
                           value="{{ old('specific_email') }}"
                           placeholder="example@email.com" dir="ltr">
                    @error('specific_email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                {{-- Subject --}}
                <div class="form-group">
                    <label>موضوع ایمیل</label>
                    <input type="text" name="subject"
                           class="form-control @error('subject') is-invalid @enderror"
                           value="{{ old('subject') }}"
                           placeholder="مثال: اطلاعیه مهم آوان"
                           maxlength="150">
                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                {{-- Body --}}
                <div class="form-group">
                    <label>متن ایمیل</label>
                    <textarea name="body"
                              class="form-control @error('body') is-invalid @enderror"
                              rows="10"
                              placeholder="متن پیام را اینجا بنویسید..."
                              maxlength="5000">{{ old('body') }}</textarea>
                    <span class="form-hint">از Enter برای پاراگراف جدید استفاده کنید. حداکثر ۵۰۰۰ کاراکتر.</span>
                    @error('body')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div style="display:flex;gap:.75rem;align-items:center;margin-top:.5rem;">
                    <button type="submit" class="btn btn-primary"
                            onclick="return confirm('ایمیل برای گیرندگان انتخاب‌شده ارسال شود؟')">
                        📤 ارسال ایمیل
                    </button>
                    <span class="text-muted text-sm">این عمل قابل بازگشت نیست.</span>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Stats & Info ── --}}
    <div style="display:flex;flex-direction:column;gap:1.2rem;">

        <div class="card">
            <div class="card-title">📊 آمار کاربران</div>
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem .85rem;background:#f6f1e7;border-radius:7px;">
                    <span style="font-size:.88rem;">🎭 هنرمندان</span>
                    <strong style="color:var(--color-primary);">{{ number_format($artistCount) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem .85rem;background:#f6f1e7;border-radius:7px;">
                    <span style="font-size:.88rem;">🎬 تیم‌های تولید</span>
                    <strong style="color:var(--color-primary);">{{ number_format($productionCount) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem .85rem;background:#1F2A44;border-radius:7px;">
                    <span style="font-size:.88rem;color:#fff;">مجموع</span>
                    <strong style="color:var(--color-accent);">{{ number_format($totalCount) }}</strong>
                </div>
            </div>
            <p class="form-hint" style="margin-top:.85rem;">
                * کاربرانی که فقط شماره موبایل دارند و ایمیل ثبت نکرده‌اند، ایمیل دریافت نمی‌کنند.
            </p>
        </div>

        <div class="card">
            <div class="card-title">💡 راهنما</div>
            <ul style="padding-right:1.2rem;font-size:.84rem;color:var(--color-muted);line-height:2;">
                <li>ایمیل‌ها با قالب برندینگ آوان ارسال می‌شوند.</li>
                <li>از آدرس <code>no-reply@aavaan.com</code> ارسال می‌شود.</li>
                <li>برای ارسال انبوه صبر کنید تا عملیات کامل شود.</li>
                <li>تمام ارسال‌ها در لاگ فعالیت ثبت می‌شوند.</li>
            </ul>
        </div>

        <div class="card">
            <div class="card-title">⚙️ تنظیمات ایمیل (فعلی)</div>
            <div style="font-size:.82rem;line-height:2;color:var(--color-muted);">
                <div><strong>Mailer:</strong> <code>{{ config('mail.default') }}</code></div>
                <div><strong>Host:</strong> <code>{{ config('mail.mailers.smtp.host', '—') }}</code></div>
                <div><strong>Port:</strong> <code>{{ config('mail.mailers.smtp.port', '—') }}</code></div>
                <div><strong>From:</strong> <code>{{ config('mail.from.address') }}</code></div>
            </div>
            <p class="form-hint" style="margin-top:.7rem;">
                برای تغییر تنظیمات SMTP، فایل <code>.env</code> سرور را ویرایش کنید.
            </p>
        </div>

    </div>

</div>

@endsection
