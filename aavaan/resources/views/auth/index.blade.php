@extends('layouts.app')
@section('title', 'ورود / ثبت‌نام — آوان')

@push('styles')
<style>
.auth-wrap { max-width:480px; margin:4rem auto; padding:0 1rem; }
.auth-tabs { display:flex; border-bottom:2px solid #e5e7eb; margin-bottom:2rem; }
.auth-tab { flex:1; padding:.7rem; text-align:center; cursor:pointer; font-family:'YekanBakh',sans-serif; font-weight:600; color:var(--color-muted); border-bottom:3px solid transparent; margin-bottom:-2px; transition:all .2s; }
.auth-tab.active { color:var(--color-primary); border-color:var(--color-accent); }
.role-cards { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; }
.role-card { padding:1.25rem; border:2px solid #e5e7eb; border-radius:var(--radius); text-align:center; cursor:pointer; transition:all .2s; }
.role-card.selected { border-color:var(--color-accent); background:#fffbf2; }
.role-card h4 { font-size:.95rem; margin-top:.5rem; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="card">
        <h1 style="text-align:center;margin-bottom:1.5rem;font-size:1.5rem">به آوان خوش آمدید</h1>

        @if($errors->any())
            <div class="alert alert-error">
                <ul style="padding-right:1.2rem">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="auth-tabs">
            <div class="auth-tab active" onclick="showTab('login',this)">ورود</div>
            <div class="auth-tab" onclick="showTab('register',this)">ثبت‌نام</div>
        </div>

        <div id="tab-login">
            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>ایمیل یا شماره موبایل</label>
                    <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>رمز عبور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.85rem">
                        <input type="checkbox" name="remember"> مرا به خاطر بسپار
                    </label>
                    <a href="{{ route('auth.forgot') }}" style="font-size:.85rem;color:var(--color-accent)">فراموشی رمز</a>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">ورود</button>
            </form>
        </div>

        <div id="tab-register" style="display:none">
            <form action="{{ route('auth.register') }}" method="POST">
                @csrf
                <p style="font-size:.88rem;color:var(--color-muted);margin-bottom:1rem">نوع حساب خود را انتخاب کنید:</p>
                <div class="role-cards">
                    <label class="role-card {{ old('role') === 'artist' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="artist" style="display:none" {{ old('role') === 'artist' ? 'checked' : '' }} required>
                        <div style="font-size:2rem">🎭</div>
                        <h4>من هنرمند هستم</h4>
                    </label>
                    <label class="role-card {{ old('role') === 'production' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="production" style="display:none" {{ old('role') === 'production' ? 'checked' : '' }}>
                        <div style="font-size:2rem">🎬</div>
                        <h4>تیم تولید</h4>
                    </label>
                </div>
                <div class="form-group">
                    <label>نام کامل</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>ایمیل</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label>شماره موبایل</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="09xxxxxxxxx">
                </div>
                <div id="field-group" class="form-group">
                    <label>رشته‌ی هنری</label>
                    <select name="field" class="form-control">
                        <option value="">انتخاب کنید...</option>
                        @foreach(config('aavaan.artistic_fields') as $f)
                            <option value="{{ $f }}" {{ old('field') === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>رمز عبور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>تکرار رمز عبور</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%">ثبت‌نام</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tab, el) {
    document.getElementById('tab-login').style.display = tab === 'login' ? '' : 'none';
    document.getElementById('tab-register').style.display = tab === 'register' ? '' : 'none';
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

@if(old('role') || old('name'))
    showTab('register', document.querySelectorAll('.auth-tab')[1]);
@endif

document.querySelectorAll('.role-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        const isArtist = this.querySelector('input').value === 'artist';
        document.getElementById('field-group').style.display = isArtist ? '' : 'none';
    });
});

const urlRole = new URLSearchParams(location.search).get('role');
if (urlRole === 'artist' || urlRole === 'production') {
    showTab('register', document.querySelectorAll('.auth-tab')[1]);
    document.querySelectorAll('.role-card').forEach(card => {
        if (card.querySelector('input').value === urlRole) card.click();
    });
}
</script>
@endpush
@endsection
