@extends('layouts.app')
@section('title', 'تنظیم رمز عبور جدید')

@section('content')
<div style="min-height:calc(100vh - 64px);display:flex;align-items:flex-start;justify-content:center;padding:4rem 1rem;background:radial-gradient(ellipse at 60% 0%,#e8e0ce 0%,var(--color-bg) 60%);">
    <div style="width:100%;max-width:440px;">

        <div style="text-align:center;margin-bottom:1.5rem;">
            <a href="{{ route('home') }}" style="font-family:'YekanBakh',Tahoma,sans-serif;font-size:1.8rem;font-weight:800;color:var(--color-primary);text-decoration:none;">
                <span style="color:var(--color-accent)">آ</span>وان
            </a>
        </div>

        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 32px rgba(31,42,68,.13);padding:2.5rem 2rem;">
            <h2 style="margin-bottom:.5rem;font-size:1.3rem;">تنظیم رمز عبور جدید</h2>
            <p style="color:var(--color-muted);font-size:.9rem;margin-bottom:1.8rem;">رمز عبور جدید خود را انتخاب کنید.</p>

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('auth.reset.do') }}" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="reset-email">آدرس ایمیل</label>
                    <input type="email" id="reset-email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', request('email')) }}" autocomplete="email" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="reset-pw">رمز عبور جدید</label>
                    <div style="position:relative;">
                        <input type="password" id="reset-pw" name="password"
                               class="form-control" autocomplete="new-password" required>
                        <button type="button" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--color-muted);font-size:.85rem;" onclick="var i=document.getElementById('reset-pw');i.type=i.type==='password'?'text':'password';" tabindex="-1">👁</button>
                    </div>
                    <span style="font-size:.78rem;color:var(--color-muted)">حداقل ۸ کاراکتر</span>
                </div>
                <div class="form-group">
                    <label for="reset-pw2">تکرار رمز عبور جدید</label>
                    <input type="password" id="reset-pw2" name="password_confirmation"
                           class="form-control" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="padding:.7rem;font-size:.97rem;margin-top:.5rem">
                    تغییر رمز عبور
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
