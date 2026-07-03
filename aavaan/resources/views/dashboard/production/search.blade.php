@extends('layouts.dashboard')
@section('title', 'جستجوی هنرمند')
@section('page-title', 'جستجوی هنرمند')

@push('styles')
<style>
    /* ── Filter form ── */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: .9rem;
        align-items: end;
    }

    /* ── Paywall banner ── */
    .paywall-banner {
        background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%);
        color: #fff;
        border-radius: var(--radius);
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .paywall-banner-title {
        font-family: 'YekanBakh', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: .3rem;
    }
    .paywall-banner-desc { font-size: .85rem; color: #c8d0e0; line-height: 1.7; }
    .paywall-features {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-top: .6rem;
    }
    .paywall-feature {
        font-size: .78rem;
        background: rgba(255,255,255,.12);
        padding: .18rem .65rem;
        border-radius: 999px;
        color: #e0e8f5;
    }

    /* ── Artist cards grid ── */
    .artists-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 1.1rem;
    }

    /* ── Full card (access granted) ── */
    .artist-card {
        position: relative;
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid #ede8dc;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(31,42,68,.06);
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s, transform .2s;
    }
    .artist-card:hover { box-shadow: 0 4px 16px rgba(31,42,68,.12); transform: translateY(-2px); }
    .artist-card-overlay {
        position: absolute;
        inset: 0;
        border-radius: var(--radius);
        background: linear-gradient(160deg, rgba(201,162,75,.14), rgba(31,42,68,.06));
        opacity: 0;
        pointer-events: none;
        z-index: 1;
    }
    .artist-card-img {
        width: 100%; height: 160px;
        object-fit: cover;
        display: block;
        flex-shrink: 0;
    }
    .artist-card-img-placeholder {
        width: 100%; height: 160px;
        background: var(--color-primary);
        display: flex; align-items: center; justify-content: center;
        font-family: 'YekanBakh', sans-serif;
        font-size: 3rem; font-weight: 800;
        color: var(--color-accent);
        flex-shrink: 0;
    }
    .artist-card-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
    .artist-card-name {
        font-family: 'YekanBakh', sans-serif;
        font-size: .95rem; font-weight: 700;
        color: var(--color-primary);
        margin-bottom: .3rem;
    }
    .artist-card-field {
        display: inline-block;
        background: #f5f0e8;
        color: var(--color-primary);
        font-size: .75rem;
        font-weight: 600;
        padding: .15rem .6rem;
        border-radius: 999px;
        margin-bottom: .4rem;
    }
    .artist-card-meta { font-size: .8rem; color: var(--color-muted); margin-bottom: .65rem; flex: 1; }
    .artist-card-meta span + span::before { content: ' · '; }
    .artist-card-actions { margin-top: auto; }

    /* ── Locked card (paywall) ── */
    .artist-card-locked .artist-card-img,
    .artist-card-locked .artist-card-img-placeholder { filter: brightness(.92); }
    .locked-bars { margin: .5rem 0 .7rem; }
    .locked-bar {
        height: 9px;
        background: linear-gradient(90deg, #e8e3d8, #d5cfc4, #e8e3d8);
        border-radius: 6px;
        margin-bottom: .4rem;
        filter: blur(2px);
    }
    .locked-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .78rem;
        color: var(--color-muted);
        background: #f0ede8;
        border-radius: 999px;
        padding: .22rem .75rem;
        border: 1px solid #ddd9d0;
    }

    /* ── Credits badge ── */
    .credits-bar {
        background: #ecf5ec;
        border: 1px solid #c3dfc3;
        border-radius: 8px;
        padding: .6rem 1rem;
        margin-bottom: 1.1rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .87rem;
        color: #2d5a2d;
    }
    .credits-bar strong { font-family: 'YekanBakh', sans-serif; font-weight: 800; font-size: 1.05rem; }

    /* ── Results header ── */
    .results-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .results-count { font-size: .85rem; color: var(--color-muted); }

    /* ── Pagination ── */
    .pagination-wrap { margin-top: 1.5rem; }
    .pagination-wrap nav { display: flex; justify-content: center; }

    @media (max-width: 680px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
        .artists-grid { grid-template-columns: 1fr 1fr; }
        .paywall-banner { flex-direction: column; }
        .artist-card-img, .artist-card-img-placeholder { height: 130px; }
    }
    @media (max-width: 420px) {
        .filter-grid { grid-template-columns: 1fr; }
        .artists-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Inject large JSON payloads separately to avoid HTML-attribute encoding issues --}}
<script>
window.__productionSearchDefs  = @json($definitionsByCategory);
window.__productionSearchSaved = @json((array) request('attr', []));
</script>

{{-- Filter form --}}
<div class="card" style="margin-bottom:1.35rem"
     x-data="{
         catId: {{ (int) request('category_id', 0) }} || '',
         allDefs: window.__productionSearchDefs,
         savedAttr: window.__productionSearchSaved,
         searchableTypes: ['number','select','multiselect','boolean'],
         get defs() {
             if (!this.catId) return [];
             return (this.allDefs[this.catId] ?? [])
                 .filter(d => this.searchableTypes.includes(d.field_type));
         },
         numVal(key, side) {
             var a = this.savedAttr[key];
             return (a && a[side]) ? a[side] : '';
         },
         selectVal(key) { return this.savedAttr[key] || ''; },
         isChecked(key, val) {
             var a = this.savedAttr[key];
             return Array.isArray(a) && a.indexOf(val) !== -1;
         }
     }">
    <div class="card-title">🔍 فیلتر جستجو</div>
    <form method="GET" action="{{ route('production.search') }}">
        <div class="filter-grid">
            <div class="form-group" style="margin:0">
                <label>رشته هنری</label>
                <x-art-fields-select name="field"
                    :selected="request('field')"
                    placeholder="همه رشته‌ها" />
            </div>
            <div class="form-group" style="margin:0">
                <label>دسته تخصصی</label>
                <select name="category_id" class="form-control"
                        @change="setCategory($event.target.value)">
                    <option value="">همه تخصص‌ها</option>
                    @foreach($categories->groupBy('parent_id') as $parentId => $children)
                    <optgroup label="{{ $children->first()->parent->name_fa }}">
                        @foreach($children as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name_fa }}
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>شهر</label>
                <input type="text" name="city" class="form-control" value="{{ request('city') }}" placeholder="مثال: تهران">
            </div>
            <div class="form-group" style="margin:0">
                <label>سن از</label>
                <input type="number" name="age_min" class="form-control" value="{{ request('age_min') }}" min="15" max="80" placeholder="مثال: ۲۵">
            </div>
            <div class="form-group" style="margin:0">
                <label>سن تا</label>
                <input type="number" name="age_max" class="form-control" value="{{ request('age_max') }}" min="15" max="80" placeholder="مثال: ۴۵">
            </div>
            <div class="form-group" style="margin:0">
                <label>حداقل سابقه (سال)</label>
                <input type="number" name="experience_min" class="form-control" value="{{ request('experience_min') }}" min="0" max="50" placeholder="مثال: ۵">
            </div>
            <div class="form-group" style="margin:0">
                <label>کلیدواژه</label>
                <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="بیوگرافی، کلمات کلیدی ...">
            </div>
            <div style="display:flex;gap:.5rem;align-items:center">
                <button type="submit" class="btn btn-primary btn-sm" style="flex:1">جستجو</button>
                @if(request()->hasAny(['field','city','age_min','age_max','experience_min','keyword','category_id','attr']))
                    <a href="{{ route('production.search') }}" class="btn btn-ghost btn-sm" title="پاک کردن فیلترها">✕</a>
                @endif
            </div>
        </div>

        {{-- Dynamic specialty attribute filters --}}
        <div x-show="defs.length > 0" x-cloak
             style="margin-top:1.1rem;padding-top:1.1rem;border-top:1px solid #ede8dc">
            <div style="font-size:.82rem;font-weight:600;color:var(--color-primary);margin-bottom:.85rem">
                🎯 فیلترهای تخصصی
            </div>
            <div class="filter-grid">
                <template x-for="def in defs" :key="def.key">
                    <div class="form-group" style="margin:0">
                        <label x-text="def.label_fa"></label>

                        {{-- Number: range min/max --}}
                        <template x-if="def.field_type === 'number'">
                            <div style="display:flex;gap:.4rem;align-items:center">
                                <input type="number" :name="'attr['+def.key+'][min]'"
                                       class="form-control" placeholder="از"
                                       :value="numVal(def.key, 'min')"
                                       style="min-width:0" dir="ltr">
                                <span style="color:var(--color-muted);font-size:.8rem">تا</span>
                                <input type="number" :name="'attr['+def.key+'][max]'"
                                       class="form-control" placeholder="تا"
                                       :value="numVal(def.key, 'max')"
                                       style="min-width:0" dir="ltr">
                            </div>
                        </template>

                        {{-- Select --}}
                        <template x-if="def.field_type === 'select'">
                            <select :name="'attr['+def.key+']'" class="form-control">
                                <option value="">همه</option>
                                <template x-for="opt in (def.options || [])" :key="opt.value">
                                    <option :value="opt.value"
                                            :selected="selectVal(def.key) === opt.value"
                                            x-text="opt.label"></option>
                                </template>
                            </select>
                        </template>

                        {{-- Multiselect --}}
                        <template x-if="def.field_type === 'multiselect'">
                            <div style="display:flex;flex-direction:column;gap:.3rem;max-height:140px;overflow-y:auto;border:1.5px solid #d5cfc4;border-radius:7px;padding:.4rem .6rem;background:#fdfaf6">
                                <template x-for="opt in (def.options || [])" :key="opt.value">
                                    <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;font-size:.82rem">
                                        <input type="checkbox"
                                               :name="'attr['+def.key+'][]'"
                                               :value="opt.value"
                                               :checked="isChecked(def.key, opt.value)">
                                        <span x-text="opt.label"></span>
                                    </label>
                                </template>
                            </div>
                        </template>

                        {{-- Boolean --}}
                        <template x-if="def.field_type === 'boolean'">
                            <select :name="'attr['+def.key+']'" class="form-control">
                                <option value="">فرقی نمی‌کند</option>
                                <option value="1" :selected="selectVal(def.key) === '1'">بله</option>
                                <option value="0" :selected="selectVal(def.key) === '0'">خیر</option>
                            </select>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </form>
</div>

{{-- Paywall banner — shown when user has no paid access --}}
@if(!$hasPaidAccess)
<div class="paywall-banner">
    <div>
        <div class="paywall-banner-title">🔒 برای مشاهده جزئیات کامل هنرمندان، دسترسی بخرید</div>
        <div class="paywall-banner-desc">پس از خرید، تمام نتایج به‌صورت کامل و بدون محدودیت نمایش داده می‌شوند.</div>
        <div class="paywall-features">
            <span class="paywall-feature">📍 شهر و موقعیت</span>
            <span class="paywall-feature">⏱ سابقه و تجربه</span>
            <span class="paywall-feature">🎬 ویدیوی ریل</span>
            <span class="paywall-feature">📞 اطلاعات تماس</span>
            <span class="paywall-feature">📋 سوابق کاری</span>
        </div>
    </div>
    <a href="{{ route('production.access') }}" class="btn btn-accent" style="white-space:nowrap">دسترسی به فهرست کامل</a>
</div>
@else
    {{-- Credits info for users who have access --}}
    @if($access && $access->remainingCredits() > 0)
    <div class="credits-bar">
        <span>💳</span>
        <strong>{{ $access->remainingCredits() }}</strong>
        <span>اعتبار باقی‌مانده — با هر «باز کردن» یک اعتبار استفاده می‌شود.</span>
        <a href="{{ route('production.access') }}" class="btn btn-ghost btn-sm" style="margin-right:auto">افزایش اعتبار</a>
    </div>
    @elseif(!$access)
    <div class="credits-bar" style="background:#fff3cd;border-color:#f0dda0;color:#7a5c00">
        <span>⚠️</span>
        <span>اعتبارهای شما تمام شده. می‌توانید نتایج را ببینید اما برای باز کردن هنرمند جدید نیاز به خرید اعتبار دارید.</span>
        <a href="{{ route('production.access') }}" class="btn btn-accent btn-sm" style="margin-right:auto">خرید اعتبار</a>
    </div>
    @endif
@endif

{{-- Results header --}}
<div class="results-header">
    <span class="results-count">
        {{ $artists->total() }} هنرمند یافت شد
        @if(request()->hasAny(['field','city','age_min','age_max','experience_min','keyword']))
            — نتایج فیلتر شده
        @endif
    </span>
    @if($hasPaidAccess)
        <span class="text-sm text-muted">{{ count($unlockedIds) }} هنرمند باز شده</span>
    @endif
</div>

{{-- Artist cards --}}
<div class="artists-grid">
    @forelse($artists as $artist)
    @php
        $unlocked = in_array($artist->id, $unlockedIds);
        // کستینگ ناشناس: نام واقعی فقط پس از باز کردن پروفایل (unlock) نمایش داده می‌شود؛
        // پیش از آن یک شناسهٔ مستعار پایدار جای نام و حرف اول قرار می‌گیرد (مخفی‌سازی سمت سرور).
        $cardName = $unlocked ? $artist->user->name : 'هنرمند #' . $artist->id;
    @endphp

    @if($hasPaidAccess)
        {{-- ── FULL CARD (user has paid) ── --}}
        <div class="artist-card" data-animate="artist-card">
            <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
            @if($artist->avatar)
                <img src="{{ $artist->avatar_url }}" alt="{{ $cardName }}" class="artist-card-img" data-card-img>
            @else
                <div class="artist-card-img-placeholder">{{ mb_substr($cardName, 0, 1) }}</div>
            @endif
            <div class="artist-card-body">
                <div class="artist-card-name">{{ $cardName }}</div>
                <span class="artist-card-field">{{ $artist->field }}</span>
                <div class="artist-card-meta">
                    @if($artist->city)<span>📍 {{ $artist->city }}</span>@endif
                    @if($artist->years_experience)<span>{{ $artist->years_experience }} سال تجربه</span>@endif
                    @if($artist->birth_year)<span>متولد {{ $artist->birth_year }}</span>@endif
                </div>
                <div class="artist-card-actions">
                    @if($unlocked)
                        <a href="{{ route('profile.show', $artist->username) }}"
                           class="btn btn-primary btn-sm btn-block">مشاهده پروفایل کامل</a>
                    @elseif($access && $access->remainingCredits() > 0)
                        <form action="{{ route('production.access.unlock') }}" method="POST">
                            @csrf
                            <input type="hidden" name="artist_profile_id" value="{{ $artist->id }}">
                            <button type="submit" class="btn btn-accent btn-sm btn-block">🔓 باز کردن (۱ اعتبار)</button>
                        </form>
                    @else
                        <a href="{{ route('production.access') }}"
                           class="btn btn-outline btn-sm btn-block">خرید اعتبار برای باز کردن</a>
                    @endif
                </div>
            </div>
        </div>

    @else
        {{-- ── LIMITED CARD (paywall active) ── --}}
        <div class="artist-card artist-card-locked" data-animate="artist-card">
            <span class="artist-card-overlay" data-card-overlay aria-hidden="true"></span>
            @if($artist->avatar)
                <img src="{{ $artist->avatar_url }}" alt="" class="artist-card-img" data-card-img>
            @else
                <div class="artist-card-img-placeholder">{{ mb_substr($cardName, 0, 1) }}</div>
            @endif
            <div class="artist-card-body">
                <div class="artist-card-name">{{ $cardName }}</div>
                <span class="artist-card-field">{{ $artist->field }}</span>
                <div class="locked-bars">
                    <div class="locked-bar" style="width:75%"></div>
                    <div class="locked-bar" style="width:55%"></div>
                </div>
                <div class="artist-card-actions">
                    <span class="locked-badge">🔒 اطلاعات محدود</span>
                </div>
            </div>
        </div>
    @endif

    @empty
    <div style="grid-column:1/-1;text-align:center;padding:3rem 1rem;color:var(--color-muted)">
        <div style="font-size:2rem;margin-bottom:.75rem">🔍</div>
        <p>هنرمندی با این مشخصات یافت نشد.</p>
        @if(request()->hasAny(['field','city','age_min','age_max','experience_min','keyword']))
            <a href="{{ route('production.search') }}" class="btn btn-ghost btn-sm" style="margin-top:.75rem">پاک کردن فیلترها</a>
        @endif
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($artists->hasPages())
<div class="pagination-wrap">
    {{ $artists->links() }}
</div>
@endif

@endsection
