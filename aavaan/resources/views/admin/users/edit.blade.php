@extends('admin.layouts.app')
@section('title', 'ویرایش کاربر')
@section('page-title', 'ویرایش کاربر: ' . $user->name)
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.users.index') }}">کاربران</a> ← ویرایش
</nav>
<div class="grid-2">
<div class="card">
    <div class="card-title">اطلاعات کاربر</div>
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label>نام</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>ایمیل</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required style="direction:ltr;">
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>موبایل</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
        </div>
        <div class="form-group">
            <label>نقش</label>
            <select name="role" class="form-control">
                <option value="artist" {{ $user->role=='artist'?'selected':'' }}>هنرمند</option>
                <option value="production" {{ $user->role=='production'?'selected':'' }}>تیم تولید</option>
                <option value="admin" {{ $user->role=='admin'?'selected':'' }}>ادمین</option>
            </select>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="hidden" name="is_banned" value="0">
                <input type="checkbox" name="is_banned" value="1" {{ $user->is_banned?'checked':'' }}>
                مسدود کردن حساب
            </label>
        </div>
        <div class="form-group">
            <label>یادداشت داخلی ادمین</label>
            <textarea name="admin_notes" class="form-control" rows="3">{{ old('admin_notes', $user->admin_notes) }}</textarea>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">بازگشت</a>
        </div>
    </form>
</div>
<div>
    <div class="card">
        <div class="card-title">اطلاعات حساب</div>
        <p class="text-sm" style="margin-bottom:.5rem;"><strong>شناسه:</strong> #{{ $user->id }}</p>
        <p class="text-sm" style="margin-bottom:.5rem;"><strong>تاریخ عضویت:</strong> {{ $user->created_at->format('Y/m/d H:i') }}</p>
        <p class="text-sm" style="margin-bottom:.5rem;"><strong>وضعیت:</strong>
            @if($user->trashed()) <span class="badge badge-muted">حذف‌شده</span>
            @elseif($user->is_banned) <span class="badge badge-danger">مسدود</span>
            @else <span class="badge badge-success">فعال</span>
            @endif
        </p>
    </div>

    <div class="card">
        <div class="card-title">بازنشانی رمز عبور</div>
        <p class="text-sm text-muted" style="margin-bottom:1rem;">لینک بازنشانی به ایمیل کاربر ارسال می‌شود.</p>
        <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}">@csrf
            <button type="submit" class="btn btn-outline btn-sm">ارسال لینک بازنشانی</button>
        </form>
    </div>

    @if(!$user->trashed())
    <div class="card" style="border-color:var(--color-danger);">
        <div class="card-title" style="color:var(--color-danger);">حذف کاربر</div>
        <p class="text-sm text-muted" style="margin-bottom:1rem;">حذف نرم — کاربر قابل بازیابی است. پروفایل هنرمند غیرفعال می‌شود.</p>
        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
            onsubmit="return confirm('آیا از حذف این کاربر مطمئنید؟')">@csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">حذف کاربر</button>
        </form>
    </div>
    @else
    <div class="card" style="border-color:var(--color-success);">
        <div class="card-title">بازیابی کاربر</div>
        <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">@csrf
            <button type="submit" class="btn btn-outline btn-sm">بازیابی کاربر</button>
        </form>
    </div>
    @endif
</div>
</div>
@endsection
