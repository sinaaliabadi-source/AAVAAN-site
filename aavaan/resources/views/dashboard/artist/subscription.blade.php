@extends('layouts.dashboard')
@section('title', 'اشتراک هنرمند')
@section('sidebar-nav')
<a href="{{ route('artist.dashboard') }}">خانه</a>
<a href="{{ route('artist.profile') }}">پروفایل و نمونه‌کار</a>
<a href="{{ route('artist.subscription') }}" class="active">اشتراک</a>
@endsection
@php
    $planLabels = ['monthly' => 'ماهانه', 'yearly' => 'سالانه', 'festival' => 'جشنوارهٔ افتتاح'];
@endphp
@section('content')
<h1 style="margin-bottom:1.5rem">مدیریت اشتراک</h1>

@if($festivalActive)
<div style="margin-bottom:1.5rem">
    <x-festival-banner
        title="جشنوارهٔ آغاز — عضویت رایگان شما 🎉"
        message="به مناسبت آغاز به کار آوان، عضویت شما تا پایان تابستان رایگان است. نمونه‌کارهایتان را کامل کنید تا در نتایج جستجوی تیم‌های تولید دیده شوید." />
</div>
@endif

@if($subscription)
<div class="card" style="border:2px solid var(--color-success);margin-bottom:1.5rem">
    <h3 style="color:var(--color-success)">اشتراک شما فعال است</h3>
    <p style="margin-top:.5rem;font-size:.9rem">
        نوع: {{ $planLabels[$subscription->plan] ?? $subscription->plan }}
        @if($subscription->plan === 'festival')<span class="festival-badge">رایگان</span>@endif
    </p>
    <p style="font-size:.9rem">تاریخ انقضا: {{ $subscription->expires_at->format('Y/m/d') }}</p>
    @if($subscription->plan === 'festival')
    <p style="font-size:.85rem;color:var(--color-muted);margin-top:.4rem">اشتراک جشنواره تا پایان تابستان معتبر است؛ پس از آن برای ادامهٔ حضور می‌توانید یکی از پلن‌ها را تهیه کنید.</p>
    @endif
</div>
@endif

{{-- قیمت‌های اولیه به‌صورت JSON جدا (بدون قرار دادن @json داخل اتریبیوت x-data) --}}
<script type="application/json" id="sub-prices">@json(['monthly' => (int) $prices['monthly_price'], 'yearly' => (int) $prices['yearly_price']])</script>

<div class="card" x-data="discountBox()">

    {{-- ═══ بخش کد تخفیف / کد جشنواره (همیشه نمایش داده می‌شود) ═══ --}}
    <div class="discount-box">
        <label class="discount-box__label">کد تخفیف یا کد جشنواره دارید؟</label>
        <div class="discount-box__row">
            <input type="text" x-model="code" @keydown.enter.prevent="apply()"
                   :disabled="loading" class="form-control" placeholder="مثلاً: AAVAAN10" dir="ltr"
                   style="text-align:center;text-transform:uppercase">
            <button type="button" class="btn btn-primary" @click="apply()" :disabled="loading || !code.trim()">
                <span x-show="!loading">اعمال کد</span>
                <span x-show="loading">در حال بررسی…</span>
            </button>
        </div>
        <p x-show="message" x-cloak x-text="message"
           :style="applied ? 'color:#2d6a2d' : 'color:#a03027'"
           style="font-size:.85rem;margin:.6rem 0 0"></p>
    </div>

    @if($festivalActive)
    {{-- در دورهٔ جشنواره فرم و دکمهٔ پرداخت نمایش داده نمی‌شود؛ فقط پیام اطلاع‌رسانی. --}}
    <div style="margin:1.25rem 0;padding:1rem;border:1px dashed #ecdfbf;border-radius:10px;background:#faf6ec">
        <p style="font-size:.9rem;color:#6a5a2e;margin:0">
            <span class="festival-badge">جشنواره</span>
            در دوره جشنواره افتتاح نیازی به پرداخت نیست. اشتراک شما تا پایان جشنواره رایگان و فعال است.
        </p>
    </div>
    @else
    <h2 style="margin:1.25rem 0 1.5rem">خرید / تمدید اشتراک</h2>
    <form action="{{ route('artist.subscription.pay') }}" method="POST">
        @csrf
        {{-- کدِ اعمال‌شده تا مرحلهٔ پرداخت نگه داشته می‌شود --}}
        <input type="hidden" name="discount_code" :value="applied ? appliedCode : ''">
        <div class="grid-2">
            <label style="border:2px solid #e5e7eb;border-radius:var(--radius);padding:1.25rem;cursor:pointer;display:block">
                <input type="radio" name="plan" value="monthly" style="margin-left:.5rem" required> ماهانه
                <div style="font-size:1.4rem;font-weight:800;color:var(--color-primary);margin-top:.5rem">
                    <span :class="{ 'festival-price-old': applied }" x-text="fmt(plans.monthly.original)"></span>
                    <template x-if="applied"><span x-text="fmt(plans.monthly.final)" style="margin-inline-start:.4rem"></span></template>
                    تومان
                </div>
            </label>
            <label style="border:2px solid var(--color-accent);border-radius:var(--radius);padding:1.25rem;cursor:pointer;display:block;background:#fffbf2">
                <input type="radio" name="plan" value="yearly" style="margin-left:.5rem"> سالانه (صرفه‌جویی بیشتر)
                <div style="font-size:1.4rem;font-weight:800;color:var(--color-primary);margin-top:.5rem">
                    <span :class="{ 'festival-price-old': applied }" x-text="fmt(plans.yearly.original)"></span>
                    <template x-if="applied"><span x-text="fmt(plans.yearly.final)" style="margin-inline-start:.4rem"></span></template>
                    تومان
                </div>
            </label>
        </div>
        <button type="submit" class="btn btn-accent" style="margin-top:1.5rem">پرداخت از طریق درگاه</button>
    </form>
    @endif
</div>

@push('styles')
<style>
    .discount-box { padding:1.1rem 1.2rem; background:#f6f6f9; border:1px solid #e6e6ee; border-radius:12px; }
    .discount-box__label { display:block; font-weight:700; color:var(--color-primary); margin-bottom:.6rem; font-size:.92rem; }
    .discount-box__row { display:flex; gap:.5rem; align-items:stretch; flex-wrap:wrap; }
    .discount-box__row .form-control { flex:1; min-width:160px; }
    [x-cloak] { display:none !important; }
</style>
@endpush

@push('scripts')
<script>
    function discountBox() {
        return {
            code: '',
            appliedCode: '',
            applied: false,
            loading: false,
            message: '',
            plans: (function () {
                var base = JSON.parse(document.getElementById('sub-prices').textContent);
                return {
                    monthly: { original: base.monthly, final: base.monthly },
                    yearly:  { original: base.yearly,  final: base.yearly },
                };
            })(),
            fmt(n) {
                var s = Number(n).toLocaleString('en-US');
                return s.replace(/[0-9]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; });
            },
            apply() {
                var value = this.code.trim();
                if (!value || this.loading) return;
                this.loading = true;
                this.message = '';
                fetch(@js(route('artist.subscription.discount')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @js(csrf_token()),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: value }),
                })
                .then(function (r) { return r.json(); })
                .then((data) => {
                    this.loading = false;
                    this.message = data.message || '';
                    if (data.valid) {
                        this.applied = true;
                        this.appliedCode = data.code;
                        this.plans = data.plans;
                    } else {
                        this.applied = false;
                        this.appliedCode = '';
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.applied = false;
                    this.message = 'خطا در بررسی کد. دوباره تلاش کنید.';
                });
            },
        };
    }
</script>
@endpush

@if($history->count())
<div class="card">
    <h3 style="margin-bottom:1rem">تاریخچه پرداخت‌ها</h3>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:.88rem;min-width:420px">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb">
                <th style="text-align:right;padding:.4rem">نوع</th>
                <th style="text-align:right;padding:.4rem">مبلغ</th>
                <th style="text-align:right;padding:.4rem">وضعیت</th>
                <th style="text-align:right;padding:.4rem">تاریخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $h)
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:.4rem">{{ $planLabels[$h->plan] ?? $h->plan }}</td>
                <td style="padding:.4rem">{{ $h->payment ? number_format($h->payment->amount) : '—' }}</td>
                <td style="padding:.4rem"><span style="color:{{ $h->payment?->status === 'paid' ? 'var(--color-success)' : '#dc3545' }}">{{ $h->payment?->status ?? '—' }}</span></td>
                <td style="padding:.4rem;color:var(--color-muted)">{{ $h->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif
@endsection
