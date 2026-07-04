@extends('admin.layouts.app')
@section('title', 'افزودن کاربر')
@section('page-title', 'افزودن کاربر جدید')
@section('content')

<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.users.index') }}">مدیریت کاربران</a> ← افزودن کاربر
</nav>

<div class="card" style="max-width:640px;"
     x-data="{
        role: '{{ old('role', 'artist') }}',
        adminConfirm: false,
        genPassword() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789@#%';
            let p = '';
            for (let i = 0; i < 12; i++) p += chars[Math.floor(Math.random() * chars.length)];
            this.$refs.pw.value = p;
            this.$refs.pwc.value = p;
            this.$refs.pw.type = 'text';
        }
     }">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>نام <span style="color:#c0392b">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>ایمیل <span style="color:#c0392b">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" dir="ltr" required>
                @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>شماره موبایل</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" dir="ltr" placeholder="۰۹...">
                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>نقش <span style="color:#c0392b">*</span></label>
                <select name="role" class="form-control" x-model="role">
                    <option value="artist">هنرمند</option>
                    <option value="production">تیم تولید</option>
                    <option value="admin">ادمین</option>
                </select>
                @error('role')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- فیلدهای پروفایل هنرمند --}}
        <div x-show="role === 'artist'" x-cloak>
            <div class="grid-2">
                <div class="form-group">
                    <label>رشتهٔ هنری <span style="color:#c0392b">*</span></label>
                    <input type="text" name="field" value="{{ old('field') }}" class="form-control"
                           placeholder="مثال: بازیگری و اجرا">
                    @error('field')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>شهر</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="مثال: تهران">
                </div>
            </div>
        </div>

        {{-- تأیید دوبارهٔ ساخت ادمین --}}
        <div x-show="role === 'admin'" x-cloak
             style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:.85rem 1rem;margin-bottom:1rem;">
            <label style="display:flex;align-items:center;gap:.5rem;margin:0;cursor:pointer;font-weight:600;color:#a03027;">
                <input type="checkbox" name="admin_confirm" value="1" x-model="adminConfirm">
                مطمئنم که می‌خواهم یک حساب <strong>ادمین</strong> با دسترسی کامل بسازم.
            </label>
            @error('admin_confirm')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- رمز عبور --}}
        <div class="grid-2">
            <div class="form-group">
                <label>رمز عبور <span style="color:#c0392b">*</span></label>
                <input type="password" name="password" x-ref="pw" class="form-control" dir="ltr" required>
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>تکرار رمز عبور <span style="color:#c0392b">*</span></label>
                <input type="password" name="password_confirmation" x-ref="pwc" class="form-control" dir="ltr" required>
            </div>
        </div>
        <div style="margin-bottom:1rem;">
            <button type="button" class="btn btn-ghost btn-sm" @click="genPassword()">🎲 تولید رمز تصادفی</button>
        </div>

        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;margin:0;cursor:pointer;font-weight:400;">
                <input type="checkbox" name="send_welcome" value="1" {{ old('send_welcome') ? 'checked' : '' }}>
                ارسال ایمیل خوش‌آمد حاوی رمز عبور به کاربر
            </label>
        </div>

        <div style="display:flex;gap:.6rem;margin-top:1.2rem;">
            <button type="submit" class="btn btn-primary"
                    x-bind:disabled="role === 'admin' && !adminConfirm">ساخت کاربر</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">انصراف</a>
        </div>
    </form>
</div>

@endsection
