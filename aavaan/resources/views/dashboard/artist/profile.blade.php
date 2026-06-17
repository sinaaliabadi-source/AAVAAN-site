@extends('layouts.dashboard')
@section('title', 'ویرایش پروفایل')
@section('sidebar-nav')
<a href="{{ route('artist.dashboard') }}">خانه</a>
<a href="{{ route('artist.profile') }}" class="active">پروفایل و نمونه‌کار</a>
<a href="{{ route('artist.subscription') }}">اشتراک</a>
@endsection
@section('content')
<h1 style="margin-bottom:1.5rem">ویرایش پروفایل</h1>

<div class="card">
    <form action="{{ route('artist.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label>نام کاربری (نشانی پروفایل عمومی)</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $profile?->username) }}" placeholder="مثلاً: ali-ahmadi">
                @error('username') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>رشته‌ی هنری <span style="color:red">*</span></label>
                <select name="field" class="form-control" required>
                    @foreach(config('aavaan.artistic_fields') as $f)
                        <option value="{{ $f }}" {{ old('field', $profile?->field) === $f ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>شهر</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $profile?->city) }}">
            </div>
            <div class="form-group">
                <label>سال تولد (شمسی)</label>
                <input type="number" name="birth_year" class="form-control" value="{{ old('birth_year', $profile?->birth_year) }}" min="1300" max="1410">
            </div>
            <div class="form-group">
                <label>سال‌های تجربه</label>
                <input type="number" name="years_experience" class="form-control" value="{{ old('years_experience', $profile?->years_experience) }}" min="0">
            </div>
        </div>
        <div class="form-group">
            <label>بیوگرافی (حداکثر ۲۰۰۰ کاراکتر)</label>
            <textarea name="bio" class="form-control" rows="4" maxlength="2000">{{ old('bio', $profile?->bio) }}</textarea>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>شماره تماس (برای تیم‌های تولید)</label>
                <input type="tel" name="phone_contact" class="form-control" value="{{ old('phone_contact', $profile?->phone_contact) }}">
            </div>
            <div class="form-group">
                <label>ایمیل تماس (برای تیم‌های تولید)</label>
                <input type="email" name="email_contact" class="form-control" value="{{ old('email_contact', $profile?->email_contact) }}">
            </div>
        </div>
        <hr style="margin:1.5rem 0;border-color:#e5e7eb">
        <div class="grid-2">
            <div class="form-group">
                <label>تصویر پروفایل (حداکثر ۲ مگابایت)</label>
                @if($profile?->avatar)
                    <img src="{{ $profile->avatar_url }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:.5rem;display:block">
                @endif
                <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>
            <div class="form-group">
                <label>لینک ویدیوی ریل (یوتیوب / ویمئو)</label>
                <input type="url" name="reel_url" class="form-control" value="{{ old('reel_url', $profile?->reel_is_external ? $profile->reel_video : '') }}" placeholder="https://youtube.com/...">
                @if($profile?->reel_video && !$profile->reel_is_external)
                    <p style="font-size:.82rem;color:var(--color-success);margin-top:.3rem">✅ ویدیو آپلود شده</p>
                @endif
            </div>
        </div>
        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
    </form>
</div>

<div class="card">
    <h2 style="margin-bottom:1rem">نمونه‌کارها</h2>
    @if($profile?->portfolioItems->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.75rem;margin-bottom:1.5rem">
        @foreach($profile->portfolioItems as $item)
        <div style="position:relative">
            @if($item->type === 'image')
                <img src="{{ $item->url }}" style="width:100%;height:120px;object-fit:cover;border-radius:var(--radius)">
            @else
                <div style="background:#e5e7eb;height:120px;border-radius:var(--radius);display:flex;align-items:center;justify-content:center">▶ ویدیو</div>
            @endif
            <form action="{{ route('artist.portfolio.delete', $item->id) }}" method="POST" style="position:absolute;top:4px;left:4px">
                @csrf @method('DELETE')
                <button type="submit" style="background:#dc3545;color:#fff;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;font-size:.75rem" title="حذف">✕</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    @if(!$profile || $profile->portfolioItems->count() < 10)
    <form action="{{ route('artist.portfolio.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label>تصویر جدید</label>
                <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>
            <div class="form-group">
                <label>یا لینک ویدیو</label>
                <input type="url" name="video_url" class="form-control" placeholder="لینک یوتیوب یا ویمئو">
            </div>
            <div class="form-group">
                <label>توضیح (اختیاری)</label>
                <input type="text" name="caption" class="form-control" maxlength="200">
            </div>
        </div>
        <input type="hidden" name="type" id="portfolio-type" value="image">
        <button type="submit" class="btn btn-outline" onclick="setPortfolioType(event)">افزودن به نمونه‌کار</button>
    </form>
    @endif
</div>

<div class="card">
    <h2 style="margin-bottom:1rem">سوابق کاری</h2>
    @if($profile?->workHistories->count())
    <table style="width:100%;border-collapse:collapse;margin-bottom:1.5rem">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb;font-size:.85rem">
                <th style="text-align:right;padding:.4rem">عنوان</th>
                <th style="text-align:right;padding:.4rem">نقش</th>
                <th style="text-align:right;padding:.4rem">سال</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($profile->workHistories as $wh)
            <tr style="border-bottom:1px solid #f0f0f0;font-size:.88rem">
                <td style="padding:.4rem">{{ $wh->title }}</td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $wh->role }}</td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $wh->year ?? '—' }}</td>
                <td style="padding:.4rem">
                    <form action="{{ route('artist.work-history.delete', $wh->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding:.25rem .6rem;font-size:.78rem">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <form action="{{ route('artist.work-history.add') }}" method="POST">
        @csrf
        <div class="grid-2">
            <div class="form-group">
                <label>عنوان اثر/پروژه</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>نقش شما</label>
                <input type="text" name="role" class="form-control" required>
            </div>
            <div class="form-group">
                <label>نام کارگردان/کارگزار</label>
                <input type="text" name="director" class="form-control">
            </div>
            <div class="form-group">
                <label>سال (شمسی)</label>
                <input type="number" name="year" class="form-control" min="1300" max="1410">
            </div>
        </div>
        <button type="submit" class="btn btn-outline">افزودن سابقه</button>
    </form>
</div>

@push('scripts')
<script>
function setPortfolioType(e) {
    const videoUrl = document.querySelector('input[name="video_url"]').value;
    document.getElementById('portfolio-type').value = videoUrl ? 'video_link' : 'image';
}
</script>
@endpush
@endsection
