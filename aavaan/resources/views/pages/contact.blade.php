@extends('layouts.app')
@section('title', 'تماس با ما — آوان')
@section('meta-description', 'سوال، پیشنهاد یا گزارش مشکل دارید؟ از طریق فرم تماس با تیم آوان در ارتباط باشید.')

@push('styles')
<style>
.contact-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
    padding: 4rem 0 3.5rem;
    text-align: center;
}
.contact-hero-title {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: clamp(1.6rem, 3.5vw, 2.4rem);
    font-weight: 800;
    color: var(--color-accent);
    margin-bottom: .5rem;
}
.contact-hero-sub { color: #c8d0e0; font-size: .95rem; }

.contact-body { padding: 4rem 0 5rem; }
.contact-layout { display: grid; grid-template-columns: 1fr 380px; gap: 2.5rem; align-items: start; }

.contact-form-card { background: #fff; border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow); border: 1px solid #ede8dc; }
.contact-form-title { font-family: 'YekanBakh'; font-size: 1.1rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1.5rem; }

.info-card { background: var(--color-primary); border-radius: var(--radius); padding: 1.75rem; color: #fff; }
.info-card-title { font-family: 'YekanBakh'; font-size: 1rem; font-weight: 700; color: var(--color-accent); margin-bottom: 1.25rem; }
.info-item { display: flex; align-items: flex-start; gap: .75rem; margin-bottom: 1.1rem; }
.info-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: .1rem; }
.info-label { font-size: .78rem; color: #8899bb; margin-bottom: .18rem; }
.info-value { font-size: .9rem; color: #e0e8f5; line-height: 1.55; }
.info-note { background: rgba(255,255,255,.08); border-radius: 8px; padding: .85rem 1rem; font-size: .85rem; color: #c8d0e0; line-height: 1.75; margin-top: 1.25rem; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

@media (max-width: 900px) { .contact-layout { grid-template-columns: 1fr; } }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<section class="contact-hero">
    <div class="container">
        <h1 class="contact-hero-title">تماس با ما</h1>
        <p class="contact-hero-sub">سوال، پیشنهاد یا گزارش مشکل دارید؟ پیامتان را برایمان بفرستید.</p>
    </div>
</section>

<section class="contact-body">
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success" style="max-width:760px;margin:0 auto 2rem;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error" style="max-width:760px;margin:0 auto 2rem;">{{ $errors->first() }}</div>
        @endif

        <div class="contact-layout">

            {{-- Form --}}
            <div class="contact-form-card">
                <div class="contact-form-title">ارسال پیام</div>
                <form method="POST" action="{{ route('contact.send') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>نام <span style="color:#c0392b">*</span></label>
                            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" required placeholder="نام و نام خانوادگی">
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>ایمیل <span style="color:#c0392b">*</span></label>
                            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required placeholder="example@email.com">
                            @error('email')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>شماره تماس</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="۰۹۱۲۰۰۰۰۰۰۰ (اختیاری)">
                        </div>
                        <div class="form-group">
                            <label>موضوع <span style="color:#c0392b">*</span></label>
                            <input type="text" name="subject" class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" value="{{ old('subject') }}" required placeholder="موضوع پیام شما">
                            @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label>پیام <span style="color:#c0392b">*</span></label>
                        <textarea name="message" class="form-control {{ $errors->has('message') ? 'is-invalid' : '' }}" rows="6" required placeholder="پیام خود را اینجا بنویسید...">{{ old('message') }}</textarea>
                        @error('message')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">ارسال پیام</button>
                </form>
            </div>

            {{-- Info --}}
            <div>
                <div class="info-card">
                    <div class="info-card-title">اطلاعات تماس</div>
                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div>
                            <div class="info-label">ایمیل پشتیبانی</div>
                            <div class="info-value">info@aavaan.com</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">🕐</div>
                        <div>
                            <div class="info-label">زمان پاسخگویی</div>
                            <div class="info-value">معمولاً ظرف ۲۴ ساعت پاسخ می‌دهیم</div>
                        </div>
                    </div>
                    <div class="info-note">
                        💡 شاید پاسخ سوالتان در
                        <a href="{{ route('faq') }}" style="color:var(--color-accent)">سوالات متداول</a>
                        باشد — نگاهی بیندازید.
                    </div>
                </div>

                <div style="background:#fff;border-radius:var(--radius);padding:1.5rem;margin-top:1.25rem;border:1px solid #ede8dc;">
                    <div style="font-family:'YekanBakh';font-weight:700;color:var(--color-primary);margin-bottom:.75rem;font-size:.95rem;">موضوعات رایج</div>
                    <ul style="list-style:none;display:flex;flex-direction:column;gap:.5rem;">
                        <li style="font-size:.87rem;color:var(--color-muted);">● سوال درباره اشتراک هنرمندان</li>
                        <li style="font-size:.87rem;color:var(--color-muted);">● سوال درباره بسته‌های دسترسی</li>
                        <li style="font-size:.87rem;color:var(--color-muted);">● مشکل فنی در آپلود یا پروفایل</li>
                        <li style="font-size:.87rem;color:var(--color-muted);">● گزارش محتوای نامناسب</li>
                        <li style="font-size:.87rem;color:var(--color-muted);">● پیشنهاد و انتقاد سازنده</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
