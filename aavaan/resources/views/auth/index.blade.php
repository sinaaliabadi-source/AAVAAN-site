@extends('layouts.app')
@section('title', 'ورود / ثبت‌نام')

@push('styles')
<style>
.auth-page {
    min-height: calc(100vh - 64px);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 3rem 1rem 4rem;
    background: radial-gradient(ellipse at 60% 0%, #e8e0ce 0%, var(--color-bg) 60%);
}
.auth-box { width: 100%; max-width: 500px; }
.auth-logo { text-align: center; margin-bottom: 1.5rem; }
.auth-logo a {
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-size: 2rem;
    font-weight: 800;
    color: var(--color-primary);
    text-decoration: none;
    letter-spacing: -.5px;
}
.auth-logo a span { color: var(--color-accent); }
.auth-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 32px rgba(31,42,68,.13); overflow: hidden; }
.auth-tabs { display: flex; border-bottom: 2px solid #f0ede6; }
.auth-tab {
    flex: 1;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    font-family: 'YekanBakh', Tahoma, sans-serif;
    font-weight: 700;
    font-size: 1rem;
    color: var(--color-muted);
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
    background: none;
    border-top: none;
    border-right: none;
    border-left: none;
}
.auth-tab.active { color: var(--color-primary); border-bottom-color: var(--color-accent); }
.auth-body { padding: 2rem; }
.login-pane, .register-pane { display: none; }
.login-pane.visible, .register-pane.visible { display: block; }
.role-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.role-card {
    border: 2px solid var(--color-border);
    border-radius: 10px;
    padding: 1.4rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all .22s;
    user-select: none;
}
.role-card:hover { border-color: var(--color-accent); background: #fffbf2; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(201,162,75,.18); }
.role-card.selected { border-color: var(--color-accent); background: #fffbf2; box-shadow: 0 0 0 4px rgba(201,162,75,.15); }
.role-card input[type=radio] { display: none; }
.role-icon { font-size: 2.4rem; margin-bottom: .5rem; line-height: 1; }
.role-label { font-family: 'YekanBakh', Tahoma, sans-serif; font-weight: 700; font-size: .95rem; color: var(--color-primary); }
.role-sub   { font-size: .78rem; color: var(--color-muted); margin-top: .25rem; }
.register-form-wrap { max-height: 0; overflow: hidden; transition: max-height .35s ease; }
.register-form-wrap.open { max-height: 900px; }
.divider { display: flex; align-items: center; gap: .75rem; margin: 1.2rem 0; color: var(--color-muted); font-size: .83rem; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--color-border); }
.pw-wrap { position: relative; }
.pw-wrap .pw-toggle {
    position: absolute;
    left: .75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--color-muted);
    font-size: .85rem;
    padding: 0;
    line-height: 1;
}
@media (max-width: 480px) {
    .auth-body { padding: 1.5rem; }
    .role-card { padding: 1rem .6rem; }
    .role-icon { font-size: 2rem; }
}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-box">

        <div class="auth-logo">
            <a href="{{ route('home') }}"><span>آ</span>وان</a>
            <p style="color:var(--color-muted);font-size:.9rem;margin-top:.35rem">پلتفرم تخصصی کاستینگ هنرمندان ایران</p>
        </div>

        <div class="auth-card">
            <div class="auth-tabs" role="tablist">
                <button class="auth-tab" id="tab-btn-login"    role="tab" onclick="switchTab('login')">ورود به حساب</button>
                <button class="auth-tab" id="tab-btn-register" role="tab" onclick="switchTab('register')">ثبت‌نام</button>
            </div>

            <div class="auth-body">

                @if($errors->any())
                <div class="alert alert-error">
                    <ul style="padding-right:1.2rem;margin:0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                {{-- LOGIN PANE --}}
                <div class="login-pane" id="pane-login">
                    <form method="POST" action="{{ route('auth.login') }}" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="login-email">ایمیل یا شماره موبایل</label>
                            <input type="text" id="login-email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" autocomplete="username"
                                   placeholder="example@email.com یا 09xxxxxxxxx" required>
                        </div>
                        <div class="form-group">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:.3rem">
                                <label for="login-password" style="margin:0">رمز عبور</label>
                                <a href="{{ route('auth.forgot') }}" style="font-size:.82rem;color:var(--color-muted);">فراموشی رمز؟</a>
                            </div>
                            <div class="pw-wrap">
                                <input type="password" id="login-password" name="password"
                                       class="form-control" autocomplete="current-password" required>
                                <button type="button" class="pw-toggle" onclick="togglePw('login-password',this)" tabindex="-1">👁</button>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.3rem">
                            <input type="checkbox" name="remember" id="remember"
                                   style="accent-color:var(--color-accent);width:15px;height:15px;">
                            <label for="remember" style="font-size:.87rem;color:var(--color-muted);cursor:pointer;margin:0;font-weight:400">مرا به خاطر بسپار</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">ورود به آوان</button>
                    </form>
                    <div class="divider">یا</div>
                    <p style="text-align:center;font-size:.9rem;color:var(--color-muted)">
                        حساب ندارید؟
                        <a href="#" onclick="switchTab('register');return false;" style="font-weight:700;color:var(--color-accent)">همین حالا ثبت‌نام کنید</a>
                    </p>
                </div>

                {{-- REGISTER PANE --}}
                <div class="register-pane" id="pane-register">
                    <form method="POST" action="{{ route('auth.register') }}" id="register-form" novalidate>
                        @csrf
                        <p style="font-size:.88rem;color:var(--color-muted);margin-bottom:.9rem;text-align:center">
                            نوع حساب خود را انتخاب کنید:
                        </p>
                        <div class="role-grid" id="role-grid">
                            <label class="role-card {{ old('role') === 'artist' ? 'selected' : '' }}" id="card-artist" onclick="selectRole('artist')">
                                <input type="radio" name="role" value="artist" {{ old('role') === 'artist' ? 'checked' : '' }}>
                                <div class="role-icon">🎭</div>
                                <div class="role-label">من هنرمند هستم</div>
                                <div class="role-sub">بازیگر، کارگردان، موسیقی‌دان و...</div>
                            </label>
                            <label class="role-card {{ old('role') === 'production' ? 'selected' : '' }}" id="card-production" onclick="selectRole('production')">
                                <input type="radio" name="role" value="production" {{ old('role') === 'production' ? 'checked' : '' }}>
                                <div class="role-icon">🎬</div>
                                <div class="role-label">تیم تولید هستم</div>
                                <div class="role-sub">کارگزار، تهیه‌کننده، کاستینگ...</div>
                            </label>
                        </div>

                        <div class="register-form-wrap" id="register-form-wrap">
                            <div class="form-group">
                                <label for="reg-name" id="name-label">نام کامل</label>
                                <input type="text" id="reg-name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" autocomplete="name" required>
                                @error('name')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="reg-email">ایمیل <span style="color:var(--color-muted);font-weight:400">(یا شماره موبایل)</span></label>
                                <input type="email" id="reg-email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" autocomplete="email" placeholder="example@email.com">
                                @error('email')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="reg-phone">شماره موبایل <span style="color:var(--color-muted);font-weight:400">(یا ایمیل)</span></label>
                                <input type="tel" id="reg-phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" autocomplete="tel" placeholder="09xxxxxxxxx">
                                @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                <span style="font-size:.78rem;color:var(--color-muted)">حداقل یکی از ایمیل یا موبایل الزامی است</span>
                            </div>
                            <div class="form-group" id="field-group" style="{{ old('role') === 'production' ? 'display:none' : '' }}">
                                <label for="reg-field">رشته هنری</label>
                                <select name="field" id="reg-field"
                                        class="form-control @error('field') is-invalid @enderror">
                                    <option value="">— انتخاب کنید —</option>
                                    @foreach(config('aavaan.artistic_fields') as $f)
                                        <option value="{{ $f }}" {{ old('field') === $f ? 'selected' : '' }}>{{ $f }}</option>
                                    @endforeach
                                </select>
                                @error('field')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="reg-password">رمز عبور</label>
                                <div class="pw-wrap">
                                    <input type="password" id="reg-password" name="password"
                                           class="form-control" autocomplete="new-password" required>
                                    <button type="button" class="pw-toggle" onclick="togglePw('reg-password',this)" tabindex="-1">👁</button>
                                </div>
                                <span style="font-size:.78rem;color:var(--color-muted)">حداقل ۸ کاراکتر</span>
                            </div>
                            <div class="form-group">
                                <label for="reg-password2">تکرار رمز عبور</label>
                                <div class="pw-wrap">
                                    <input type="password" id="reg-password2" name="password_confirmation"
                                           class="form-control" autocomplete="new-password" required>
                                    <button type="button" class="pw-toggle" onclick="togglePw('reg-password2',this)" tabindex="-1">👁</button>
                                </div>
                            </div>
                            <p style="font-size:.8rem;color:var(--color-muted);margin-bottom:1rem;line-height:1.6">
                                با ثبت‌نام، <a href="{{ route('terms') }}">شرایط استفاده</a> و <a href="{{ route('privacy') }}">حریم خصوصی</a> آوان را می‌پذیرم.
                            </p>
                            <button type="submit" class="btn btn-accent btn-block btn-lg">
                                ثبت‌نام در آوان
                            </button>
                        </div>
                    </form>

                    <div class="divider" id="reg-divider" style="{{ old('role') ? 'display:none' : '' }}">یا</div>
                    <p id="reg-login-link" style="text-align:center;font-size:.9rem;color:var(--color-muted);{{ old('role') ? 'display:none' : '' }}">
                        قبلاً ثبت‌نام کرده‌اید؟
                        <a href="#" onclick="switchTab('login');return false;" style="font-weight:700;color:var(--color-accent)">وارد شوید</a>
                    </p>
                </div>

            </div>
        </div>

        <p style="text-align:center;margin-top:1.2rem;font-size:.82rem;color:var(--color-muted)">
            <a href="{{ route('home') }}" style="color:var(--color-muted)">← بازگشت به صفحه اصلی</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    var isLogin = tab === 'login';
    document.getElementById('pane-login').classList.toggle('visible', isLogin);
    document.getElementById('pane-register').classList.toggle('visible', !isLogin);
    document.getElementById('tab-btn-login').classList.toggle('active', isLogin);
    document.getElementById('tab-btn-register').classList.toggle('active', !isLogin);
}

function selectRole(role) {
    document.querySelectorAll('#role-grid input[type=radio]').forEach(function(r) {
        r.checked = r.value === role;
    });
    document.getElementById('card-artist').classList.toggle('selected', role === 'artist');
    document.getElementById('card-production').classList.toggle('selected', role === 'production');
    var fg = document.getElementById('field-group');
    fg.style.display = role === 'artist' ? '' : 'none';
    if (role === 'artist') fg.querySelector('select').setAttribute('required', '');
    else fg.querySelector('select').removeAttribute('required');
    document.getElementById('name-label').textContent =
        role === 'artist' ? 'نام کامل' : 'نام شرکت / تیم تولید';
    document.getElementById('register-form-wrap').classList.add('open');
    document.getElementById('reg-divider').style.display = 'none';
    document.getElementById('reg-login-link').style.display = 'none';
}

function togglePw(inputId, btn) {
    var inp = document.getElementById(inputId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.textContent = inp.type === 'password' ? '👁' : '🙈';
}

(function init() {
    var oldRole   = '{{ old("role") }}';
    var oldName   = '{{ old("name") }}';
    var hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    var urlTab    = new URLSearchParams(location.search).get('tab');
    var urlRole   = new URLSearchParams(location.search).get('role');

    if (oldRole || oldName || hasErrors || urlTab === 'register') {
        switchTab('register');
    } else {
        switchTab('login');
    }

    var role = oldRole || urlRole;
    if (role === 'artist' || role === 'production') {
        selectRole(role);
    }
})();
</script>
@endpush
