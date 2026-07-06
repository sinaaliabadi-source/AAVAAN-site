@extends('layouts.app')

@section('title', 'ثبت‌نام در هنرباز — آوان')
@section('meta-description', 'فرم ثبت‌نام رایگان در برنامه استعدادیابی هنرباز؛ ویژه کودکان و نوجوانان.')

@push('styles')
<style>
    .hb-reg-wrap { max-width: 720px; margin: 2.5rem auto 4rem; }
    .hb-reg-head { text-align: center; margin-bottom: 1.8rem; }
    .hb-reg-head h1 { font-size: 2rem; }
    .hb-reg-head p { color: var(--color-muted); }

    .hb-progress { display: flex; align-items: center; margin-bottom: 2rem; }
    .hb-progress .node {
        flex: 0 0 auto; width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; background: #e5e0d3; color: var(--color-muted);
        transition: background .3s, color .3s;
    }
    .hb-progress .node.done, .hb-progress .node.current {
        background: var(--color-accent); color: var(--color-primary);
    }
    .hb-progress .bar { flex: 1 1 auto; height: 4px; background: #e5e0d3; margin: 0 .5rem; border-radius: 4px; }
    .hb-progress .bar.done { background: var(--color-accent); }
    .hb-step-labels { display: flex; justify-content: space-between; font-size: .8rem; color: var(--color-muted); margin-bottom: 1.5rem; }

    .hb-form-card { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow); }
    .hb-form-nav { display: flex; justify-content: space-between; margin-top: 1.5rem; gap: 1rem; }
    .hb-hint { font-size: .82rem; color: var(--color-muted); margin-top: .3rem; }
    .hb-terms { display: flex; gap: .5rem; align-items: flex-start; font-size: .9rem; }
    .hb-terms input { margin-top: .35rem; }
    .field-err { color: #c0392b; font-size: .82rem; margin-top: .25rem; display: block; }
</style>
@endpush

@section('content')
<div class="container hb-reg-wrap">
    <div class="hb-reg-head">
        <h1>🎭 ثبت‌نام در هنرباز</h1>
        <p>ثبت‌نام رایگان است — ویژه کودکان و نوجوانان متولد ۱۳۸۰ تا ۱۴۱۰</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>لطفاً خطاهای زیر را برطرف کنید:</strong>
            <ul style="margin:.4rem 1.2rem 0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        x-data="honarbazRegister()"
        x-init="init()"
    >
        {{-- نوار پیشرفت --}}
        <div class="hb-progress">
            <div class="node" :class="{ 'done': step > 1, 'current': step === 1 }">۱</div>
            <div class="bar" :class="{ 'done': step > 1 }"></div>
            <div class="node" :class="{ 'done': step > 2, 'current': step === 2 }">۲</div>
            <div class="bar" :class="{ 'done': step > 2 }"></div>
            <div class="node" :class="{ 'current': step === 3 }">۳</div>
        </div>
        <div class="hb-step-labels">
            <span>اطلاعات کودک</span>
            <span>استعداد</span>
            <span>اطلاعات والدین</span>
        </div>

        <form method="POST" action="{{ route('honarbaz.register.submit') }}" @submit="onSubmit($event)">
            @csrf
            <div class="hb-form-card">

                {{-- ===== گام ۱ ===== --}}
                <div x-show="step === 1" x-cloak>
                    <h2 style="margin-bottom:1.2rem">گام ۱ — اطلاعات کودک/نوجوان</h2>

                    <div class="form-group">
                        <label>نام و نام خانوادگی *</label>
                        <input type="text" name="full_name" class="form-control" x-model="form.full_name" value="{{ old('full_name') }}" required>
                        <span class="field-err" x-show="err.full_name" x-text="err.full_name"></span>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>سال تولد * (۱۳۸۰ تا ۱۴۱۰)</label>
                            <input type="number" name="birth_year" class="form-control" x-model="form.birth_year" value="{{ old('birth_year') }}" min="1380" max="1410" placeholder="مثلاً ۱۳۹۵" required>
                            <span class="field-err" x-show="err.birth_year" x-text="err.birth_year"></span>
                        </div>
                        <div class="form-group">
                            <label>جنسیت *</label>
                            <select name="gender" class="form-control" x-model="form.gender" required>
                                <option value="">انتخاب کنید</option>
                                <option value="male" @selected(old('gender')==='male')>پسر</option>
                                <option value="female" @selected(old('gender')==='female')>دختر</option>
                            </select>
                            <span class="field-err" x-show="err.gender" x-text="err.gender"></span>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>استان *</label>
                            <select name="province" class="form-control" x-model="form.province" required>
                                <option value="">انتخاب کنید</option>
                                @foreach($provinces as $p)
                                    <option value="{{ $p }}" @selected(old('province')===$p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            <span class="field-err" x-show="err.province" x-text="err.province"></span>
                        </div>
                        <div class="form-group">
                            <label>شهر *</label>
                            <input type="text" name="city" class="form-control" x-model="form.city" value="{{ old('city') }}" required>
                            <span class="field-err" x-show="err.city" x-text="err.city"></span>
                        </div>
                    </div>
                </div>

                {{-- ===== گام ۲ ===== --}}
                <div x-show="step === 2" x-cloak>
                    <h2 style="margin-bottom:1.2rem">گام ۲ — استعداد</h2>

                    <div class="form-group">
                        <label>رشته هنری *</label>
                        <select name="talent_type" class="form-control" x-model="form.talent_type" required>
                            <option value="">انتخاب کنید</option>
                            @foreach($talentTypes as $t)
                                <option value="{{ $t }}" @selected(old('talent_type')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        <span class="field-err" x-show="err.talent_type" x-text="err.talent_type"></span>
                    </div>

                    <div class="form-group">
                        <label>توضیح استعداد * (حداقل ۵۰ کاراکتر)</label>
                        <textarea name="talent_description" class="form-control" rows="5" x-model="form.talent_description" required>{{ old('talent_description') }}</textarea>
                        <div class="hb-hint">
                            <span x-text="form.talent_description.length"></span> / ۵۰ کاراکتر
                        </div>
                        <span class="field-err" x-show="err.talent_description" x-text="err.talent_description"></span>
                    </div>

                    <div class="form-group">
                        <label>لینک ویدیو آپارات (اختیاری)</label>
                        <input type="url" name="video_url" class="form-control" x-model="form.video_url" value="{{ old('video_url') }}" placeholder="https://aparat.com/v/...">
                        <div class="hb-hint">
                            💡 چطور ویدیو بگذاریم؟ ویدیوی کوتاهی از استعداد کودک را در <a href="https://www.aparat.com" target="_blank" rel="noopener">آپارات</a> بارگذاری کنید و لینک آن را اینجا وارد کنید.
                        </div>
                        <span class="field-err" x-show="err.video_url" x-text="err.video_url"></span>
                    </div>
                </div>

                {{-- ===== گام ۳ ===== --}}
                <div x-show="step === 3" x-cloak>
                    <h2 style="margin-bottom:1.2rem">گام ۳ — اطلاعات والدین</h2>

                    <div class="form-group">
                        <label>نام والد/سرپرست *</label>
                        <input type="text" name="guardian_name" class="form-control" x-model="form.guardian_name" value="{{ old('guardian_name') }}" required>
                        <span class="field-err" x-show="err.guardian_name" x-text="err.guardian_name"></span>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>شماره تلفن والد *</label>
                            <input type="tel" name="guardian_phone" class="form-control" x-model="form.guardian_phone" value="{{ old('guardian_phone') }}" placeholder="۰۹..." required>
                            <span class="field-err" x-show="err.guardian_phone" x-text="err.guardian_phone"></span>
                        </div>
                        <div class="form-group">
                            <label>شماره تلفن کودک/تماس *</label>
                            <input type="tel" name="phone" class="form-control" x-model="form.phone" value="{{ old('phone') }}" placeholder="۰۹..." required>
                            <span class="field-err" x-show="err.phone" x-text="err.phone"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ایمیل (اختیاری)</label>
                        <input type="email" name="email" class="form-control" x-model="form.email" value="{{ old('email') }}">
                        <span class="field-err" x-show="err.email" x-text="err.email"></span>
                    </div>

                    <div class="form-group">
                        <label class="hb-terms">
                            <input type="checkbox" name="terms" value="1" x-model="form.terms" required>
                            <span>قوانین و شرایط شرکت در برنامه هنرباز را می‌پذیرم و اطلاعات واردشده صحیح است. *</span>
                        </label>
                        <span class="field-err" x-show="err.terms" x-text="err.terms"></span>
                    </div>
                </div>

                {{-- ناوبری --}}
                <div class="hb-form-nav">
                    <button type="button" class="btn btn-outline" x-show="step > 1" @click="prev()">→ قبلی</button>
                    <span x-show="step === 1"></span>
                    <button type="button" class="btn btn-primary" x-show="step < 3" @click="next()">بعدی ←</button>
                    <button type="submit" class="btn btn-accent" x-show="step === 3">ثبت نهایی ✓</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function honarbazRegister() {
    return {
        step: 1,
        form: {
            full_name: @json(old('full_name', '')),
            birth_year: @json(old('birth_year', '')),
            gender: @json(old('gender', '')),
            province: @json(old('province', '')),
            city: @json(old('city', '')),
            talent_type: @json(old('talent_type', '')),
            talent_description: @json(old('talent_description', '')),
            video_url: @json(old('video_url', '')),
            guardian_name: @json(old('guardian_name', '')),
            guardian_phone: @json(old('guardian_phone', '')),
            email: @json(old('email', '')),
            terms: false,
        },
        err: {},
        init() {
            // اگر خطای سروری وجود داشت، روی گام مربوطه بمان.
            @if($errors->has('talent_type') || $errors->has('talent_description') || $errors->has('video_url'))
                this.step = 2;
            @elseif($errors->has('guardian_name') || $errors->has('guardian_phone') || $errors->has('email') || $errors->has('terms') || $errors->has('phone'))
                this.step = 3;
            @endif
        },
        phoneOk(v) { return /^09[0-9]{9}$/.test(this.toEn(v || '')); },
        toEn(s) {
            return (s + '').replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
                             .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
        },
        validateStep() {
            this.err = {};
            if (this.step === 1) {
                if (!this.form.full_name.trim()) this.err.full_name = 'نام و نام خانوادگی الزامی است.';
                const y = parseInt(this.toEn(this.form.birth_year));
                if (!y || y < 1380 || y > 1410) this.err.birth_year = 'سال تولد باید بین ۱۳۸۰ تا ۱۴۱۰ باشد.';
                if (!this.form.gender) this.err.gender = 'انتخاب جنسیت الزامی است.';
                if (!this.form.province) this.err.province = 'انتخاب استان الزامی است.';
                if (!this.form.city.trim()) this.err.city = 'وارد کردن شهر الزامی است.';
            } else if (this.step === 2) {
                if (!this.form.talent_type) this.err.talent_type = 'انتخاب رشته هنری الزامی است.';
                if (this.form.talent_description.trim().length < 50) this.err.talent_description = 'توضیح استعداد باید حداقل ۵۰ کاراکتر باشد.';
                if (this.form.video_url && !/^https:\/\/(www\.)?aparat\.com\/v\/[A-Za-z0-9]+/.test(this.form.video_url))
                    this.err.video_url = 'لینک باید یک آدرس معتبر آپارات باشد.';
            } else if (this.step === 3) {
                if (!this.form.guardian_name.trim()) this.err.guardian_name = 'نام والد الزامی است.';
                if (!this.phoneOk(this.form.guardian_phone)) this.err.guardian_phone = 'شماره تلفن والد معتبر نیست.';
                if (!this.phoneOk(this.form.phone)) this.err.phone = 'شماره تلفن معتبر نیست.';
                if (!this.form.terms) this.err.terms = 'پذیرش قوانین الزامی است.';
            }
            return Object.keys(this.err).length === 0;
        },
        next() { if (this.validateStep()) this.step++; },
        prev() { if (this.step > 1) this.step--; },
        onSubmit(e) {
            if (!this.validateStep()) { e.preventDefault(); }
        },
    };
}
</script>
@endpush
