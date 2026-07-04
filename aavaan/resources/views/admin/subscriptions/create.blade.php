@extends('admin.layouts.app')
@section('title', 'اشتراک دستی')
@section('page-title', 'ثبت اشتراک دستی')
@section('content')

<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.subscriptions.index') }}">اشتراک‌ها</a> ← اشتراک دستی
</nav>

<div class="card" style="max-width:640px;"
     x-data="{
        q: '',
        results: [],
        selected: null,
        open: false,
        loading: false,
        timer: null,
        search() {
            clearTimeout(this.timer);
            const term = this.q.trim();
            if (term.length < 2) { this.results = []; this.open = false; return; }
            this.timer = setTimeout(() => {
                this.loading = true;
                fetch('{{ route('admin.api.artist-search') }}?q=' + encodeURIComponent(term), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => { this.results = data; this.open = true; this.loading = false; })
                .catch(() => { this.loading = false; });
            }, 250);
        },
        pick(item) {
            this.selected = item;
            this.q = item.name + ' — ' + (item.email || '');
            this.open = false;
        },
        clearPick() { this.selected = null; this.q = ''; this.results = []; }
     }">
    <form method="POST" action="{{ route('admin.subscriptions.store') }}">
        @csrf

        {{-- انتخاب هنرمند با autocomplete --}}
        <div class="form-group" style="position:relative;">
            <label>هنرمند <span style="color:#c0392b">*</span></label>
            <template x-if="selected">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <span class="badge badge-info" x-text="selected.name + ' — ' + (selected.email || '')"></span>
                    <button type="button" class="btn btn-ghost btn-sm" @click="clearPick()">تغییر</button>
                </div>
            </template>
            <div x-show="!selected">
                <input type="text" class="form-control" x-model="q" @input="search()" @focus="open = results.length > 0"
                       placeholder="نام یا ایمیل هنرمند را بنویسید…" autocomplete="off">
                <div x-show="open && results.length > 0" x-cloak
                     style="position:absolute;z-index:20;left:0;right:0;background:#fff;border:1px solid #e0dbd0;border-radius:8px;margin-top:.25rem;box-shadow:0 4px 16px rgba(31,42,68,.12);max-height:240px;overflow-y:auto;">
                    <template x-for="item in results" :key="item.id">
                        <div @click="pick(item)"
                             style="padding:.55rem .8rem;cursor:pointer;border-bottom:1px solid #f4f0e8;"
                             onmouseover="this.style.background='#faf7f2'" onmouseout="this.style.background='#fff'">
                            <div style="font-weight:600;font-size:.88rem;" x-text="item.name"></div>
                            <div style="font-size:.78rem;color:#888;direction:ltr;text-align:right;" x-text="item.email"></div>
                            <template x-if="item.has_active">
                                <span class="badge badge-success" style="font-size:.68rem;">اشتراک فعال دارد</span>
                            </template>
                        </div>
                    </template>
                </div>
                <div x-show="loading" class="text-sm text-muted" style="margin-top:.3rem;">در حال جستجو…</div>
            </div>
            <input type="hidden" name="user_id" :value="selected ? selected.id : ''">
            @error('user_id')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        {{-- هشدار اشتراک فعال + انتخاب رفتار --}}
        <div x-show="selected && selected.has_active" x-cloak
             style="background:#faf6ec;border:1px solid #e6d09a;border-radius:8px;padding:.85rem 1rem;margin-bottom:1rem;">
            <div style="color:#8a6d1f;font-weight:600;margin-bottom:.5rem;">⚠️ این هنرمند اشتراک فعال دارد. چه کنیم؟</div>
            <label style="display:flex;align-items:center;gap:.5rem;margin:0 0 .4rem;cursor:pointer;font-weight:400;">
                <input type="radio" name="active_action" value="renew" checked>
                تمدید از انتهای اشتراک فعلی (اشتراک فعلی حفظ می‌شود)
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;margin:0;cursor:pointer;font-weight:400;">
                <input type="radio" name="active_action" value="replace">
                جایگزینی از امروز (اشتراک فعلی منقضی می‌شود)
            </label>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>پلن <span style="color:#c0392b">*</span></label>
                <select name="plan" class="form-control" required>
                    <option value="monthly" {{ old('plan')==='monthly'?'selected':'' }}>ماهانه</option>
                    <option value="yearly" {{ old('plan')==='yearly'?'selected':'' }}>سالانه</option>
                </select>
                @error('plan')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>مبلغ (تومان)</label>
                <input type="number" name="amount" class="form-control" min="0" value="{{ old('amount', 0) }}" dir="ltr">
                <span class="form-hint">پیش‌فرض ۰ (هدیه/جبران).</span>
            </div>
            <div class="form-group">
                <label>تاریخ شروع</label>
                <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', now()->format('Y-m-d')) }}" dir="ltr">
                <span class="form-hint">در حالت «تمدید»، شروع خودکار از انتهای اشتراک فعلی است.</span>
            </div>
            <div class="form-group">
                <label>تاریخ انقضا</label>
                <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}" dir="ltr">
                <span class="form-hint">خالی بگذارید تا خودکار از پلن محاسبه شود.</span>
                @error('expires_at')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>یادداشت ادمین</label>
            <input type="text" name="admin_note" class="form-control" value="{{ old('admin_note') }}" placeholder="مثلاً: هدیه به مناسبت…">
        </div>

        <div style="display:flex;gap:.6rem;margin-top:1rem;">
            <button type="submit" class="btn btn-primary" x-bind:disabled="!selected">ثبت اشتراک دستی</button>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-ghost">انصراف</a>
        </div>
    </form>
</div>

@endsection
