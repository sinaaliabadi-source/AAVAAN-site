@extends('layouts.dashboard')
@section('title', 'ویرایش پروفایل')
@section('page-title', 'پروفایل و نمونه‌کار')

@section('topbar-actions')
    @if($profile?->username)
        <a href="{{ route('profile.show', $profile->username) }}" target="_blank" class="btn btn-ghost btn-sm">مشاهده پروفایل عمومی 🔗</a>
    @endif
@endsection

@section('content')

{{-- 1. اطلاعات پروفایل --}}
<div class="card" id="profile-info">
    <div class="card-title">👤 اطلاعات پروفایل</div>

    @if($errors->has('field') || $errors->has('username') || $errors->has('bio') || $errors->has('avatar') || $errors->has('email_contact'))
        <div class="alert alert-error">✕ {{ $errors->first() }}</div>
    @endif

    <form action="{{ route('artist.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="display:flex;align-items:flex-start;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
            <div style="flex-shrink:0">
                @if($profile?->avatar)
                    <img src="{{ $profile->avatar_url }}"
                         style="width:90px;height:90px;border-radius:50%;object-fit:cover;display:block;border:3px solid var(--color-accent)">
                @else
                    <div style="width:90px;height:90px;border-radius:50%;background:#e8e3d8;display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--color-muted)">👤</div>
                @endif
            </div>
            <div style="flex:1;min-width:220px">
                <div class="form-group" style="margin-bottom:.75rem">
                    <label>تصویر پروفایل
                        <span class="form-hint" style="display:inline;margin-right:.3rem">(jpg/png، حداکثر ۵ مگابایت)</span>
                    </label>
                    <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png">
                    @error('avatar')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>نام کاربری (آدرس پروفایل عمومی)</label>
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <span class="text-sm text-muted" style="white-space:nowrap">aavaan.com/profile/</span>
                        <input type="text" name="username" class="form-control"
                               value="{{ old('username', $profile?->username) }}"
                               placeholder="مثلاً: ali-ahmadi" dir="ltr">
                    </div>
                    <span class="form-hint">فقط حروف انگلیسی، اعداد، خط‌تیره و زیرخط</span>
                    @error('username')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <hr class="divider">

        <div class="grid-2">
            <div class="form-group">
                <label>رشته هنری <span class="req">*</span></label>
                <x-art-fields-select name="field" required
                    :selected="old('field', $profile?->field)"
                    placeholder="انتخاب کنید…" />
                @error('field')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>شهر</label>
                <input type="text" name="city" class="form-control"
                       value="{{ old('city', $profile?->city) }}" placeholder="مثلاً: تهران">
            </div>
            <div class="form-group">
                <label>سال تولد (شمسی)</label>
                <input type="number" name="birth_year" class="form-control"
                       value="{{ old('birth_year', $profile?->birth_year) }}"
                       min="1300" max="1410" placeholder="مثلاً: 1370" dir="ltr">
            </div>
            <div class="form-group">
                <label>سال‌های تجربه</label>
                <input type="number" name="years_experience" class="form-control"
                       value="{{ old('years_experience', $profile?->years_experience) }}"
                       min="0" max="60" placeholder="مثلاً: ۵" dir="ltr">
            </div>
        </div>

        <div class="form-group">
            <label>بیوگرافی</label>
            <textarea name="bio" class="form-control" rows="4" maxlength="1000"
                      placeholder="خودتان را معرفی کنید…">{{ old('bio', $profile?->bio) }}</textarea>
            <span class="form-hint">حداکثر ۱۰۰۰ کاراکتر</span>
            @error('bio')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <hr class="divider">

        <div style="margin-bottom:.6rem">
            <strong class="text-sm" style="color:var(--color-primary)">اطلاعات تماس</strong>
            <span class="form-hint" style="display:inline;margin-right:.4rem">— فقط برای تیم‌های تولید با دسترسی فعال نمایش داده می‌شود</span>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>شماره تماس</label>
                <input type="tel" name="phone_contact" class="form-control"
                       value="{{ old('phone_contact', $profile?->phone_contact) }}"
                       placeholder="۰۹۱۲ …" dir="ltr">
            </div>
            <div class="form-group">
                <label>ایمیل تماس</label>
                <input type="email" name="email_contact" class="form-control"
                       value="{{ old('email_contact', $profile?->email_contact) }}"
                       placeholder="example@mail.com" dir="ltr">
                @error('email_contact')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:.5rem">
            <button type="submit" class="btn btn-primary">ذخیره اطلاعات پروفایل</button>
        </div>
    </form>
</div>

{{-- 2. گالری تصاویر --}}
<div class="card" id="portfolio">
    <div class="card-title">
        🖼 گالری تصاویر
        @php $imgCount = $profile?->portfolioImages?->count() ?? 0; $maxItems = config('aavaan.upload.max_portfolio_items', 10); @endphp
        <span class="badge badge-info" style="margin-right:.5rem;font-size:.75rem">{{ $imgCount }} / {{ $maxItems }}</span>
    </div>

    @if($errors->has('images') || $errors->has('images.*'))
        <div class="alert alert-error">✕ {{ $errors->first('images') ?: $errors->first('images.*') }}</div>
    @endif

    @if($imgCount > 0)
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.75rem;margin-bottom:1.5rem">
        @foreach($profile->portfolioImages as $img)
        <div style="position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;background:#f0ede8">
            <img src="{{ $img->url }}" alt="{{ $img->caption }}"
                 style="width:100%;height:100%;object-fit:cover">
            @if($img->caption)
                <div style="position:absolute;bottom:0;right:0;left:0;background:rgba(0,0,0,.55);color:#fff;font-size:.72rem;padding:.3rem .5rem;line-height:1.3">{{ $img->caption }}</div>
            @endif
            <form action="{{ route('artist.portfolio.delete', $img->id) }}" method="POST"
                  style="position:absolute;top:4px;left:4px"
                  onsubmit="return confirm('این تصویر حذف شود؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="background:rgba(192,57,43,.9);color:#fff;border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;font-size:.75rem;display:flex;align-items:center;justify-content:center"
                        title="حذف تصویر">✕</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
        <div style="text-align:center;padding:2rem;color:var(--color-muted);background:#faf7f2;border-radius:8px;margin-bottom:1.25rem;border:2px dashed #ddd8ce">
            <div style="font-size:2rem;margin-bottom:.5rem">🖼</div>
            <div class="text-sm">هنوز تصویری آپلود نشده</div>
        </div>
    @endif

    @if($imgCount < $maxItems)
    <form action="{{ route('artist.portfolio.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid-2">
            <div class="form-group" style="grid-column: 1 / -1">
                <label>افزودن تصاویر جدید
                    <span class="form-hint" style="display:inline;margin-right:.3rem">(jpg/png، حداکثر ۵ مگابایت هر تصویر، چندانتخابی)</span>
                </label>
                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png" multiple
                       id="portfolio-input">
                @error('images')<span class="form-error">{{ $message }}</span>@enderror
                @error('images.*')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>توضیح (برای همه تصاویر این دسته)</label>
                <input type="text" name="caption" class="form-control"
                       placeholder="اختیاری" maxlength="200">
            </div>
            <div style="display:flex;align-items:flex-end">
                <button type="submit" class="btn btn-outline">📤 آپلود تصاویر</button>
            </div>
        </div>
        <p id="portfolio-file-count" class="form-hint" style="display:none"></p>
    </form>
    @else
        <p class="text-sm text-muted">حداکثر تعداد تصاویر ({{ $maxItems }}) آپلود شده است. برای افزودن تصویر جدید، یک تصویر قبلی را حذف کنید.</p>
    @endif
</div>

{{-- 3. ویدیوی ریل --}}
<div class="card" id="reel">
    <div class="card-title">🎬 ویدیوی ریل (نمونه‌کار ویدیویی)</div>

    @if($errors->has('reel'))
        <div class="alert alert-error">✕ {{ $errors->first('reel') }}</div>
    @endif

    @php $reel = $profile?->mainReel(); @endphp

    @if($reel)
        <div style="margin-bottom:1.25rem">
            <div style="background:#f0ede8;border-radius:8px;overflow:hidden;max-width:500px">
                <video controls style="width:100%;display:block;max-height:280px;background:#000"
                       src="{{ $reel->url }}">
                    مرورگر شما ویدیو را پشتیبانی نمی‌کند.
                </video>
            </div>
            <div style="margin-top:.75rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
                @if($reel->formatted_duration)
                    <span class="badge badge-info">⏱ {{ $reel->formatted_duration }}</span>
                @endif
                <form action="{{ route('artist.reel.delete', $reel->id) }}" method="POST"
                      onsubmit="return confirm('ویدیوی ریل حذف شود؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف ویدیو</button>
                </form>
            </div>
        </div>
        <hr class="divider">
        <p class="text-sm text-muted" style="margin-bottom:1rem">برای جایگزینی، ویدیوی جدید آپلود کنید:</p>
    @else
        <div style="text-align:center;padding:2rem;color:var(--color-muted);background:#faf7f2;border-radius:8px;margin-bottom:1.25rem;border:2px dashed #ddd8ce">
            <div style="font-size:2.5rem;margin-bottom:.5rem">🎬</div>
            <div class="text-sm">هنوز ویدیویی آپلود نشده</div>
            <div class="text-sm" style="margin-top:.3rem">ویدیوی ریل شما اولین چیزی است که تیم‌های تولید می‌بینند</div>
        </div>
    @endif

    <form action="{{ route('artist.reel.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap">
            <div class="form-group" style="flex:1;min-width:220px;margin-bottom:0">
                <label>فایل ویدیو
                    <span class="form-hint" style="display:inline;margin-right:.3rem">(mp4/mov/webm، حداکثر ۱۰۰ مگابایت، حداکثر ۹۰ ثانیه)</span>
                </label>
                <input type="file" name="reel" class="form-control" accept=".mp4,.mov,.webm" id="reel-input">
            </div>
            <div style="padding-bottom:.1rem">
                <button type="submit" class="btn btn-primary" id="reel-submit">📤 آپلود ویدیو</button>
            </div>
        </div>
        <p id="reel-file-info" class="form-hint" style="display:none;margin-top:.4rem"></p>
    </form>

    <div class="alert alert-warning" style="margin-top:1.2rem;margin-bottom:0">
        <span>⚠️</span>
        <div class="text-sm">
            محدودیت مدت‌زمان ویدیو (۹۰ ثانیه) در سمت سرور قابل بررسی خودکار نیست. لطفاً قبل از آپلود، مطمئن شوید ویدیو بیشتر از ۹۰ ثانیه نیست.
        </div>
    </div>
</div>

{{-- 5. تخصص‌های من --}}
@include('dashboard.artist.partials._specialties')

{{-- 6. اطلاعات تکمیلی حرفه‌ای --}}
@include('dashboard.artist.partials._premium_profile')

{{-- 4. سوابق کاری --}}
<div class="card" id="work-history">
    <div class="card-title">📋 سوابق کاری</div>

    @if($errors->has('title') || $errors->has('role'))
        <div class="alert alert-error">✕ {{ $errors->first('title') ?: $errors->first('role') }}</div>
    @endif

    @if($profile?->workHistories?->count())
    <div style="overflow-x:auto;margin-bottom:1.5rem">
        <table class="table">
            <thead>
                <tr>
                    <th>عنوان اثر / پروژه</th>
                    <th>نقش</th>
                    <th>کارگردان</th>
                    <th>سال</th>
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($profile->workHistories as $wh)
                <tr>
                    <td>
                        <strong>{{ $wh->title }}</strong>
                        @if($wh->description)
                            <div class="text-sm text-muted">{{ Str::limit($wh->description, 60) }}</div>
                        @endif
                    </td>
                    <td class="text-muted">{{ $wh->role }}</td>
                    <td class="text-muted">{{ $wh->director ?? '—' }}</td>
                    <td class="text-muted" style="white-space:nowrap">{{ $wh->year ?? '—' }}</td>
                    <td>
                        <form action="{{ route('artist.work-history.delete', $wh->id) }}" method="POST"
                              onsubmit="return confirm('این سابقه حذف شود؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="حذف">✕</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div style="text-align:center;padding:1.5rem;color:var(--color-muted);background:#faf7f2;border-radius:8px;margin-bottom:1.25rem;border:2px dashed #ddd8ce">
            <div class="text-sm">هنوز سابقه کاری ثبت نشده</div>
        </div>
    @endif

    <div style="background:#faf7f2;border-radius:8px;padding:1.25rem;border:1px solid #ede8dc">
        <div style="font-family:'YekanBakh',sans-serif;font-weight:700;font-size:.9rem;color:var(--color-primary);margin-bottom:1rem">
            ＋ افزودن سابقه جدید
        </div>
        <form action="{{ route('artist.work-history.add') }}" method="POST">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label>عنوان اثر / پروژه <span class="req">*</span></label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}" placeholder="مثلاً: سریال پایتخت" required>
                    @error('title')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>نقش شما <span class="req">*</span></label>
                    <input type="text" name="role" class="form-control"
                           value="{{ old('role') }}" placeholder="مثلاً: بازیگر نقش اول" required>
                    @error('role')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>نام کارگردان / مدیر پروژه</label>
                    <input type="text" name="director" class="form-control"
                           value="{{ old('director') }}" placeholder="اختیاری">
                </div>
                <div class="form-group">
                    <label>سال (شمسی)</label>
                    <input type="number" name="year" class="form-control"
                           value="{{ old('year') }}" min="1300" max="1410"
                           placeholder="مثلاً: ۱۴۰۱" dir="ltr">
                </div>
                <div class="form-group" style="grid-column: 1 / -1">
                    <label>توضیح مختصر</label>
                    <input type="text" name="description" class="form-control"
                           value="{{ old('description') }}" placeholder="اختیاری — حداکثر ۵۰۰ کاراکتر" maxlength="500">
                </div>
            </div>
            <button type="submit" class="btn btn-outline">افزودن سابقه</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    var portInput = document.getElementById('portfolio-input');
    var portCount = document.getElementById('portfolio-file-count');
    if (portInput && portCount) {
        portInput.addEventListener('change', function () {
            var n = this.files.length;
            if (n > 0) {
                portCount.textContent = n + ' فایل انتخاب شد';
                portCount.style.display = 'block';
            } else {
                portCount.style.display = 'none';
            }
        });
    }

    var reelInput = document.getElementById('reel-input');
    var reelInfo  = document.getElementById('reel-file-info');
    var reelBtn   = document.getElementById('reel-submit');
    if (reelInput && reelInfo) {
        reelInput.addEventListener('change', function () {
            var f = this.files[0];
            if (!f) { reelInfo.style.display = 'none'; return; }
            var mb = (f.size / 1048576).toFixed(1);
            reelInfo.style.display = 'block';
            if (f.size > 104857600) {
                reelInfo.textContent = 'حجم فایل (' + mb + ' MB) بیشتر از ۱۰۰ مگابایت است.';
                reelInfo.style.color = '#c0392b';
                if (reelBtn) reelBtn.disabled = true;
            } else {
                reelInfo.textContent = 'فایل انتخاب‌شده: ' + f.name + ' (' + mb + ' MB)';
                reelInfo.style.color = 'var(--color-muted)';
                if (reelBtn) reelBtn.disabled = false;
            }
        });
    }

    if (window.location.hash) {
        var el = document.querySelector(window.location.hash);
        if (el) setTimeout(function () { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 200);
    }
})();
</script>
@endpush
