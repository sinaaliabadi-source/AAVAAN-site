@extends('layouts.app')
@section('title', 'بازیابی رمز عبور')

@section('content')
<div style="min-height:calc(100vh - 64px);display:flex;align-items:flex-start;justify-content:center;padding:4rem 1rem;background:radial-gradient(ellipse at 60% 0%,#e8e0ce 0%,var(--color-bg) 60%);">
    <div style="width:100%;max-width:440px;">

        <div style="text-align:center;margin-bottom:1.5rem;">
            <a href="{{ route('home') }}" style="font-family:'YekanBakh',Tahoma,sans-serif;font-size:1.8rem;font-weight:800;color:var(--color-primary);text-decoration:none;">
                <span style="color:var(--color-accent)">آ</span>وان
            </a>
        </div>

        <div style="background:#fff;border-radius:14px;box-shadow:0 4px 32px rgba(31,42,68,.13);padding:2.5rem 2rem;">
            <h2 style="margin-bottom:.5rem;font-size:1.3rem;">بازیابی رمز عبور</h2>
            <p style="color:var(--color-muted);font-size:.9rem;margin-bottom:1.8rem;line-height:1.7">
                ایمیل حساب خود را وارد کنید. لینک تنظیم رمز جدید برایتان ارسال می‌شود.
            </p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('auth.forgot.send') }}" novalidate>
                @csrf
                <div class="form-group">
                    <label for="forgot-email">آدرس ایمیل</label>
                    <input type="email" id="forgot-email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" autocomplete="email"
                           placeholder="example@email.com" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="padding:.7rem;font-size:.97rem;margin-top:.5rem">
                    ارسال لینک بازیابی
                </button>
            </form>

            @if(!config('mail.mailers.smtp.host') || config('mail.mailers.smtp.host') === 'smtp.mailgun.org')
            <div class="alert alert-warning" style="margin-top:1.5rem;font-size:.84rem">
                <strong>توجه:</strong> سرویس ایمیل در محیط جاری پیکربندی نشده است.
                برای فعال‌سازی، تنظیمات SMTP را در فایل <code>.env</code> وارد کنید.
            </div>
            @endif
        </div>

        <p style="text-align:center;margin-top:1.1rem;font-size:.87rem;">
            <a href="{{ route('auth') }}" style="color:var(--color-muted)">← بازگشت به ورود</a>
        </p>
    </div>
</div>
@endsection
