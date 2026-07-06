@extends('layouts.app')
@section('title', 'ثبت تیکت جدید — آوان')

@push('styles')
<style>
    .create-wrap { max-width:720px; margin:0 auto; padding:2.5rem 1.5rem; }
    .create-wrap h1 { font-size:1.6rem; color:var(--color-primary); margin-bottom:.3rem; }
    .steps-bar { display:flex; gap:.5rem; margin:1.5rem 0 2rem; }
    .step-dot { flex:1; height:6px; border-radius:99px; background:#e8e2d6; transition:background .2s; }
    .step-dot.active { background:var(--color-accent); }
    .dept-choose { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:.9rem; }
    .dept-opt { border:2px solid #ece6da; border-radius:var(--radius); padding:1.2rem; cursor:pointer; text-align:center; transition:border-color .15s, background .15s; }
    .dept-opt:hover { border-color:var(--color-accent); }
    .dept-opt.selected { border-color:var(--color-accent); background:#faf6ee; }
    .dept-opt .icon { font-size:1.7rem; display:block; margin-bottom:.4rem; }
    .dept-opt h3 { font-size:.95rem; color:var(--color-primary); margin-bottom:.25rem; }
    .dept-opt p { font-size:.78rem; color:var(--color-muted); line-height:1.6; }
    .preview-box { background:#faf7f2; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem; }
    .preview-row { display:flex; gap:.5rem; padding:.35rem 0; font-size:.9rem; border-bottom:1px solid #f0ede8; }
    .preview-row:last-child { border-bottom:none; }
    .preview-row .k { color:var(--color-muted); min-width:90px; }
    .nav-btns { display:flex; justify-content:space-between; margin-top:1.75rem; gap:.75rem; }
</style>
@endpush

@section('content')
<div class="create-wrap"
     x-data="{
        step: {{ $errors->any() ? 2 : 1 }},
        isGuest: {{ auth()->check() ? 'false' : 'true' }},
        department: @js(old('department', '')),
        departmentLabel: '',
        subject: @js(old('subject', '')),
        message: @js(old('message', '')),
        guestName: @js(old('guest_name', '')),
        guestEmail: @js(old('guest_email', '')),
        deps: @js($departments),
        pick(key) { this.department = key; this.departmentLabel = this.deps[key].label; },
        get canStep2() { return this.department !== ''; },
        get canStep3() { return this.subject.trim().length > 0 && this.message.trim().length >= 20; },
     }"
     x-init="if(department) departmentLabel = (deps[department]||{}).label || ''">

    <h1>ثبت تیکت جدید</h1>
    <p style="color:var(--color-muted)">در چند گام ساده درخواستتان را برای ما بفرستید.</p>

    <div class="steps-bar">
        <div class="step-dot" :class="{ 'active': step >= 1 }"></div>
        <div class="step-dot" :class="{ 'active': step >= 2 }"></div>
        <div class="step-dot" :class="{ 'active': step >= 3 }"></div>
        <div class="step-dot" :class="{ 'active': step >= 4 }"></div>
    </div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1.25rem">
        <ul style="margin:0;padding-right:1.2rem">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- گام ۱: دپارتمان --}}
        <div x-show="step === 1" x-cloak>
            <h2 style="font-size:1.1rem;color:var(--color-primary);margin-bottom:1rem">۱. موضوع درخواست به کدام بخش مربوط است؟</h2>
            <div class="dept-choose">
                @foreach($departments as $key => $dep)
                <label class="dept-opt" :class="{ 'selected': department === '{{ $key }}' }" @click="pick('{{ $key }}')">
                    <input type="radio" name="department" value="{{ $key }}" x-model="department" class="sr-only" style="display:none">
                    <span class="icon">{{ $dep['icon'] }}</span>
                    <h3>{{ $dep['label'] }}</h3>
                    <p>{{ $dep['desc'] }}</p>
                </label>
                @endforeach
            </div>
            <div class="nav-btns">
                <span></span>
                <button type="button" class="btn btn-primary" :disabled="!canStep2" @click="step = 2">ادامه ←</button>
            </div>
        </div>

        {{-- گام ۲: موضوع + پیام + پیوست --}}
        <div x-show="step === 2" x-cloak>
            <h2 style="font-size:1.1rem;color:var(--color-primary);margin-bottom:1rem">۲. جزئیات درخواست</h2>
            <div class="form-group">
                <label>موضوع <span style="color:#c0392b">*</span></label>
                <input type="text" name="subject" class="form-control" x-model="subject" maxlength="255" placeholder="خلاصه‌ای از موضوع">
            </div>
            <div class="form-group">
                <label>شرح کامل <span style="color:#c0392b">*</span> <span style="color:var(--color-muted);font-size:.8rem">(حداقل ۲۰ کاراکتر)</span></label>
                <textarea name="message" class="form-control" x-model="message" rows="6" maxlength="5000" placeholder="مشکل یا سوال خود را با جزئیات بنویسید..."></textarea>
                <span style="font-size:.78rem;color:var(--color-muted)" x-text="message.length + ' / 5000'"></span>
            </div>
            <div class="form-group">
                <label>پیوست <span style="color:var(--color-muted);font-size:.8rem">(اختیاری — jpg, png, pdf, zip تا ۵ مگابایت)</span></label>
                <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf,.zip">
            </div>
            <div class="nav-btns">
                <button type="button" class="btn btn-ghost" @click="step = 1">→ بازگشت</button>
                <button type="button" class="btn btn-primary" :disabled="!canStep3" @click="step = isGuest ? 3 : 4">ادامه ←</button>
            </div>
        </div>

        {{-- گام ۳: اطلاعات مهمان --}}
        <div x-show="step === 3" x-cloak>
            <h2 style="font-size:1.1rem;color:var(--color-primary);margin-bottom:1rem">۳. اطلاعات تماس شما</h2>
            <div class="form-group">
                <label>نام <span style="color:#c0392b">*</span></label>
                <input type="text" name="guest_name" class="form-control" x-model="guestName" maxlength="100">
            </div>
            <div class="form-group">
                <label>ایمیل <span style="color:#c0392b">*</span></label>
                <input type="email" name="guest_email" class="form-control" x-model="guestEmail" dir="ltr" placeholder="you@example.com">
                <span style="font-size:.78rem;color:var(--color-muted)">پاسخ‌ها و شماره پیگیری به این ایمیل ارسال می‌شود.</span>
            </div>
            <div class="nav-btns">
                <button type="button" class="btn btn-ghost" @click="step = 2">→ بازگشت</button>
                <button type="button" class="btn btn-primary" :disabled="guestName.trim() === '' || guestEmail.trim() === ''" @click="step = 4">پیش‌نمایش ←</button>
            </div>
        </div>

        {{-- گام ۴: پیش‌نمایش --}}
        <div x-show="step === 4" x-cloak>
            <h2 style="font-size:1.1rem;color:var(--color-primary);margin-bottom:1rem">پیش‌نمایش و ارسال</h2>
            <div class="preview-box">
                <div class="preview-row"><span class="k">دپارتمان</span><span x-text="departmentLabel"></span></div>
                <div class="preview-row"><span class="k">موضوع</span><span x-text="subject"></span></div>
                <div class="preview-row"><span class="k">شرح</span><span style="white-space:pre-line" x-text="message"></span></div>
                <template x-if="isGuest">
                    <div>
                        <div class="preview-row"><span class="k">نام</span><span x-text="guestName"></span></div>
                        <div class="preview-row"><span class="k">ایمیل</span><span x-text="guestEmail" dir="ltr"></span></div>
                    </div>
                </template>
                @auth
                <div class="preview-row"><span class="k">کاربر</span><span>{{ auth()->user()->name }}</span></div>
                @endauth
            </div>
            <div class="nav-btns">
                <button type="button" class="btn btn-ghost" @click="step = isGuest ? 3 : 2">→ بازگشت</button>
                <button type="submit" class="btn btn-accent">✔ ثبت تیکت</button>
            </div>
        </div>

    </form>
</div>
@endsection
