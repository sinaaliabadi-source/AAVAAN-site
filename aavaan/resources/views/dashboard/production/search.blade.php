@extends('layouts.dashboard')
@section('title', 'کست‌یاب')
@section('page-title', 'کست‌یاب')

@push('styles')
<style>
    /* ── چیدمان دو ستونهٔ کست‌یاب (RTL) ── */
    .cf-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 1.4rem;
        align-items: start;
    }
    .cf-sidebar { position: sticky; top: 1rem; }
    .cf-results { min-width: 0; }

    /* ── کارت‌های فیلتر ── */
    .cf-card {
        background: #fff;
        border: 1px solid #ede8dc;
        border-radius: var(--radius);
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 4px rgba(31,42,68,.05);
    }
    .cf-card-title {
        font-family: 'YekanBakh', sans-serif;
        font-weight: 700;
        font-size: .9rem;
        color: var(--color-primary);
        margin-bottom: .85rem;
        display: flex; align-items: center; gap: .4rem;
    }
    .cf-sidebar-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: .9rem;
    }
    .cf-sidebar-head h2 {
        font-family: 'YekanBakh', sans-serif; font-size: 1rem;
        font-weight: 800; color: var(--color-primary); margin: 0;
    }
    .cf-count-badge {
        background: var(--color-accent); color: var(--color-primary);
        font-weight: 800; font-size: .78rem;
        border-radius: 999px; padding: .1rem .6rem;
        font-family: 'YekanBakh', sans-serif;
    }

    /* ── دکمه‌های جنسیت ── */
    .cf-gender-row { display: flex; gap: .4rem; }
    .cf-gender-btn {
        flex: 1; text-align: center; cursor: pointer;
        padding: .5rem .3rem; border: 1px solid #ddd8ce; border-radius: 8px;
        font-size: .82rem; font-weight: 500; color: var(--color-text);
        background: #fdfaf6; transition: all .15s; margin: 0; user-select: none;
    }
    .cf-gender-btn input { display: none; }
    .cf-gender-btn.is-on {
        border-color: var(--color-accent); background: #faf6ec;
        color: var(--color-primary); font-weight: 700;
    }

    .cf-range { display: flex; align-items: center; gap: .4rem; }
    .cf-range span { color: var(--color-muted); font-size: .8rem; }

    .cf-attr-unit { color: var(--color-muted); font-size: .78rem; white-space: nowrap; }
    .cf-multi-box {
        display: flex; flex-direction: column; gap: .3rem;
        max-height: 150px; overflow-y: auto;
        border: 1.5px solid #d5cfc4; border-radius: 7px;
        padding: .45rem .6rem; background: #fdfaf6;
    }
    .cf-multi-box label { display: flex; align-items: center; gap: .4rem; cursor: pointer; font-size: .82rem; margin: 0; }

    .cf-actions { display: flex; flex-direction: column; gap: .5rem; margin-top: .3rem; }

    /* ── نوار بالای نتایج ── */
    .cf-results-bar {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .75rem; margin-bottom: 1.1rem;
    }
    .cf-results-count { font-family: 'YekanBakh', sans-serif; font-weight: 700; font-size: 1rem; color: var(--color-primary); }
    .cf-credits {
        display: inline-flex; align-items: center; gap: .5rem;
        background: #eef3ee; border: 1px solid #cbdecb; color: #2d5a2d;
        border-radius: 999px; padding: .3rem .85rem; font-size: .82rem;
    }
    .cf-credits strong { font-family: 'YekanBakh', sans-serif; font-weight: 800; }
    .cf-credits.is-empty { background: #fff3cd; border-color: #f0dda0; color: #7a5c00; }

    /* ── گرید و کارت هنرمند ── */
    .cf-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.1rem;
    }
    .cf-artist-card {
        position: relative;
        background: #fff; border: 1px solid #ede8dc;
        border-radius: 14px; overflow: hidden;
        box-shadow: 0 1px 5px rgba(31,42,68,.06);
        display: flex; flex-direction: column;
        transition: box-shadow .2s, transform .2s;
    }
    .cf-artist-card:hover { box-shadow: 0 6px 20px rgba(31,42,68,.13); transform: translateY(-2px); }
    .cf-artist-card.is-unlocked { border-color: #b9cbb0; }

    .cf-card-img { width: 100%; height: 180px; object-fit: cover; display: block; background: #f0ede8; }
    .cf-card-img-ph {
        width: 100%; height: 180px; background: var(--color-primary);
        display: flex; align-items: center; justify-content: center;
        font-family: 'YekanBakh', sans-serif; font-size: 2.4rem; font-weight: 800;
        color: var(--color-accent);
    }
    .cf-card-badge-unlocked {
        position: absolute; top: .6rem; inset-inline-start: .6rem; z-index: 2;
        background: var(--color-success); color: #fff;
        font-size: .72rem; font-weight: 700; padding: .2rem .6rem; border-radius: 999px;
    }

    .cf-card-body { padding: .9rem 1rem 1rem; flex: 1; display: flex; flex-direction: column; }
    .cf-card-code {
        font-family: 'YekanBakh', sans-serif; font-weight: 700; font-size: .95rem;
        color: var(--color-primary); margin-bottom: .5rem;
    }

    .cf-chips { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .55rem; }
    .cf-chip {
        font-size: .72rem; font-weight: 600;
        background: #eef1f6; color: var(--color-primary);
        border-radius: 999px; padding: .12rem .55rem;
    }
    .cf-verified { color: #C9A24B; margin-inline-start: .15rem; }
    .cf-card-meta { font-size: .8rem; color: var(--color-muted); margin-bottom: .55rem; }
    .cf-card-meta span + span::before { content: ' · '; }

    .cf-attr-chips { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .7rem; }
    .cf-attr-chip {
        font-size: .71rem; color: #6a5a2e;
        background: #faf6ec; border: 1px solid #ecdfbf;
        border-radius: 999px; padding: .1rem .55rem;
    }
    .cf-attr-chip b { font-weight: 700; }

    .cf-card-actions { margin-top: auto; }

    /* ── دکمهٔ فیلتر موبایل ── */
    .cf-mobile-filter-btn { display: none; }

    .cf-empty {
        grid-column: 1 / -1; text-align: center;
        padding: 3rem 1rem; color: var(--color-muted);
        background: #fff; border: 2px dashed #e0dbd0; border-radius: var(--radius);
    }

    .pagination-wrap { margin-top: 1.6rem; }
    .pagination-wrap nav { display: flex; justify-content: center; }

    /* ── واکنش‌گرا ── */
    @media (max-width: 1024px) {
        .cf-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 820px) {
        .cf-layout { grid-template-columns: 1fr; }
        .cf-sidebar {
            position: static;
            display: none;
            margin-bottom: 1.2rem;
        }
        .cf-sidebar.is-open { display: block; }
        .cf-mobile-filter-btn {
            display: inline-flex; align-items: center; gap: .5rem;
            margin-bottom: 1rem; width: 100%; justify-content: center;
        }
    }
    @media (max-width: 560px) {
        .cf-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- تعریف‌های ویژگی و مقادیر ذخیره‌شده به‌صورت JSON جدا (بدون قرار دادن @json داخل اتریبیوت x-data) --}}
<script type="application/json" id="cf-defs">@json($definitionsByCategory)</script>
<script type="application/json" id="cf-saved-attr">@json((object) request('attr', []))</script>
<script>
    function castFinder() {
        return {
            mobileOpen: false,
            catId: @json((string) (request('category_id') ?? '')),
            gender: @json((string) (request('gender') ?? '')),
            allDefs: JSON.parse(document.getElementById('cf-defs').textContent),
            savedAttr: JSON.parse(document.getElementById('cf-saved-attr').textContent),
            get defs() {
                return this.catId ? (this.allDefs[this.catId] ?? []) : [];
            },
            numVal(key, side) {
                const a = this.savedAttr[key];
                return (a && a[side]) ? a[side] : '';
            },
            selVal(key) {
                const v = this.savedAttr[key];
                return (typeof v === 'string') ? v : '';
            },
            isChecked(key, val) {
                const a = this.savedAttr[key];
                return Array.isArray(a) && a.indexOf(val) !== -1;
            }
        };
    }
</script>

<div x-data="castFinder()">

    {{-- دکمهٔ فیلتر (فقط موبایل) --}}
    <button type="button" class="btn btn-outline cf-mobile-filter-btn" @click="mobileOpen = !mobileOpen">
        <span x-text="mobileOpen ? '✕ بستن فیلترها' : '⚙ فیلترها'">⚙ فیلترها</span>
        @if($activeFilterCount > 0)
            <span class="cf-count-badge">@faNum($activeFilterCount)</span>
        @endif
    </button>

    <div class="cf-layout">

        {{-- ═══════════ سایدبار فیلتر (ستون راست) ═══════════ --}}
        <aside class="cf-sidebar" :class="{ 'is-open': mobileOpen }">
            <form method="GET" action="{{ route('production.search') }}">
                <div class="cf-sidebar-head">
                    <h2>🎯 فیلترها</h2>
                    @if($activeFilterCount > 0)
                        <span class="cf-count-badge">@faNum($activeFilterCount) فعال</span>
                    @endif
                </div>

                {{-- کارت مشخصات پایه --}}
                <div class="cf-card">
                    <div class="cf-card-title">👤 مشخصات پایه</div>

                    <div class="form-group">
                        <label>جنسیت</label>
                        <div class="cf-gender-row">
                            <label class="cf-gender-btn" :class="{ 'is-on': gender === '' }">
                                <input type="radio" name="gender" value="" x-model="gender">
                                همه
                            </label>
                            <label class="cf-gender-btn" :class="{ 'is-on': gender === 'female' }">
                                <input type="radio" name="gender" value="female" x-model="gender">
                                زن
                            </label>
                            <label class="cf-gender-btn" :class="{ 'is-on': gender === 'male' }">
                                <input type="radio" name="gender" value="male" x-model="gender">
                                مرد
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>شهر</label>
                        <input type="text" name="city" class="form-control" value="{{ request('city') }}" placeholder="مثال: تهران">
                    </div>

                    <div class="form-group">
                        <label>بازهٔ سن</label>
                        <div class="cf-range">
                            <input type="number" name="age_min" class="form-control" value="{{ request('age_min') }}"
                                   min="1" max="99" placeholder="از" dir="ltr">
                            <span>تا</span>
                            <input type="number" name="age_max" class="form-control" value="{{ request('age_max') }}"
                                   min="1" max="99" placeholder="تا" dir="ltr">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>حداقل سابقه (سال)</label>
                        <input type="number" name="experience_min" class="form-control" value="{{ request('experience_min') }}"
                               min="0" max="60" placeholder="مثال: ۵" dir="ltr">
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label>کلیدواژه (در بیوگرافی)</label>
                        <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                               placeholder="کلمات کلیدی بیوگرافی…">
                    </div>
                </div>

                {{-- کارت هنر و تخصص --}}
                <div class="cf-card">
                    <div class="cf-card-title">🎭 هنر و تخصص</div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>دستهٔ تخصص</label>
                        <select name="category_id" class="form-control" x-model="catId">
                            <option value="">همهٔ تخصص‌ها</option>
                            @foreach($categories->groupBy('parent_id') as $parentId => $children)
                            <optgroup label="{{ $children->first()->parent->name_fa }}">
                                @foreach($children as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_fa }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        <span class="form-hint">با انتخاب دسته، فیلترهای تخصصی نمایش داده می‌شوند.</span>
                    </div>
                    <label style="display:flex;align-items:center;gap:.5rem;margin:.8rem 0 0;cursor:pointer;font-weight:500;font-size:.85rem;">
                        <input type="checkbox" name="verified_only" value="1" {{ request()->boolean('verified_only') ? 'checked' : '' }}>
                        <span>فقط تخصص‌های تأییدشده <span style="color:#C9A24B;">✔</span></span>
                    </label>
                </div>

                {{-- کارت ویژگی‌های تخصصی (پویا) --}}
                <div class="cf-card" x-show="defs.length > 0" x-cloak>
                    <div class="cf-card-title">🎚 ویژگی‌های تخصصی</div>

                    <template x-for="def in defs" :key="def.key">
                        <div class="form-group">
                            <label x-text="def.label_fa"></label>

                            {{-- عددی: بازهٔ min/max با نمایش واحد --}}
                            <template x-if="def.field_type === 'number'">
                                <div class="cf-range">
                                    <input type="number" :name="'attr['+def.key+'][min]'" class="form-control"
                                           placeholder="از" :value="numVal(def.key, 'min')" dir="ltr" style="min-width:0">
                                    <span>تا</span>
                                    <input type="number" :name="'attr['+def.key+'][max]'" class="form-control"
                                           placeholder="تا" :value="numVal(def.key, 'max')" dir="ltr" style="min-width:0">
                                    <span class="cf-attr-unit" x-text="def.unit" x-show="def.unit"></span>
                                </div>
                            </template>

                            {{-- انتخابی --}}
                            <template x-if="def.field_type === 'select'">
                                <select :name="'attr['+def.key+']'" class="form-control">
                                    <option value="">همه</option>
                                    <template x-for="opt in (def.options || [])" :key="opt.value">
                                        <option :value="opt.value" :selected="selVal(def.key) === opt.value" x-text="opt.label"></option>
                                    </template>
                                </select>
                            </template>

                            {{-- چندانتخابی: چک‌باکس اسکرول‌دار --}}
                            <template x-if="def.field_type === 'multiselect'">
                                <div class="cf-multi-box">
                                    <template x-for="opt in (def.options || [])" :key="opt.value">
                                        <label>
                                            <input type="checkbox" :name="'attr['+def.key+'][]'" :value="opt.value"
                                                   :checked="isChecked(def.key, opt.value)">
                                            <span x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            {{-- بولی --}}
                            <template x-if="def.field_type === 'boolean'">
                                <select :name="'attr['+def.key+']'" class="form-control">
                                    <option value="">فرقی نمی‌کند</option>
                                    <option value="1" :selected="selVal(def.key) === '1'">بله</option>
                                    <option value="0" :selected="selVal(def.key) === '0'">خیر</option>
                                </select>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- اکشن‌ها --}}
                <div class="cf-actions">
                    <button type="submit" class="btn btn-accent btn-block">🔎 جستجوی کست</button>
                    @if($activeFilterCount > 0)
                        <a href="{{ route('production.search') }}" class="btn btn-ghost btn-block">پاک کردن فیلترها</a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- ═══════════ ستون نتایج (چپ) ═══════════ --}}
        <section class="cf-results">

            <div class="cf-results-bar">
                <div class="cf-results-count">
                    @if($activeFilterCount > 0)
                        @faNum($artists->total()) هنرمند مطابق فیلتر شما
                    @else
                        @faNum($artists->total()) هنرمند فعال
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
                    @if($festivalActive)
                        <span class="cf-credits"><span class="festival-badge">جشنواره</span> مشاهدهٔ پروفایل‌ها رایگان است 🎉</span>
                    @elseif($access && $access->remainingCredits() > 0)
                        <span class="cf-credits">💳 <strong>@faNum($access->remainingCredits())</strong> اعتبار باقی‌مانده</span>
                        <a href="{{ route('production.access') }}" class="btn btn-primary btn-sm">خرید اعتبار</a>
                    @else
                        <span class="cf-credits is-empty">⚠️ بدون اعتبار</span>
                        <a href="{{ route('production.access') }}" class="btn btn-primary btn-sm">خرید اعتبار</a>
                    @endif
                </div>
            </div>

            <div class="cf-grid">
                @forelse($artists as $artist)
                @php
                    $unlocked = in_array($artist->id, $unlockedIds);
                    $isBlue   = (bool) $artist->has_blue_tick;
                    // هنرمند تیک‌آبی هویت عمومی دارد: نام واقعی و لینک پروفایل حتی پیش از unlock مجاز است.
                    $showReal = $unlocked || $isBlue;
                    $card     = $cardData[$artist->id] ?? ['specialty_chips' => [], 'public_attrs' => []];
                    $pseudo   = \App\Helpers\ArtistPseudonym::code($artist->id);
                    $age      = $artist->birth_year ? ($currentJalaliYear - (int) $artist->birth_year) : null;
                    $genderFa = $artist->gender === 'male' ? 'آقا' : ($artist->gender === 'female' ? 'خانم' : null);
                @endphp

                <div class="cf-artist-card {{ $unlocked ? 'is-unlocked' : '' }}" data-animate="artist-card">
                    @if($unlocked)
                        <span class="cf-card-badge-unlocked">✓ باز شده</span>
                    @endif

                    {{-- تصویر: کارت قفلِ ناشناس از route مستعار می‌آید؛ تیک‌آبی/باز‌شده تصویر واقعی --}}
                    @if($showReal)
                        @if($artist->avatar)
                            <img src="{{ $artist->avatar_url }}" alt="{{ $artist->user->name }}" class="cf-card-img" loading="lazy">
                        @else
                            <div class="cf-card-img-ph">{{ mb_substr($artist->user->name, 0, 1) }}</div>
                        @endif
                    @else
                        @if($artist->avatar)
                            <img src="{{ route('production.anon-avatar', \App\Helpers\ArtistPseudonym::hash($artist->id)) }}"
                                 alt="{{ $pseudo }}" class="cf-card-img" loading="lazy">
                        @else
                            <div class="cf-card-img-ph">آ</div>
                        @endif
                    @endif

                    <div class="cf-card-body">
                        @if($showReal)
                            <div class="cf-card-code" style="display:flex;align-items:center;gap:.3rem">
                                {{ $artist->user->name }}
                                @if($isBlue)<x-blue-tick :size="16" />@endif
                            </div>
                        @else
                            <div class="cf-card-code">{{ $pseudo }}</div>
                        @endif

                        {{-- چیپ تخصص‌ها + نشان تأیید (بدون افشای هویت) --}}
                        @if(!empty($card['specialty_chips']))
                        <div class="cf-chips">
                            @foreach($card['specialty_chips'] as $chip)
                                <span class="cf-chip">
                                    {{ $chip['name'] }}
                                    @if($chip['verified'])<span class="cf-verified" title="تخصص تأییدشده">✔</span>@endif
                                </span>
                            @endforeach
                        </div>
                        @endif

                        {{-- ردیف مشخصات مجاز --}}
                        <div class="cf-card-meta">
                            @if($artist->city)<span>📍 {{ $artist->city }}</span>@endif
                            @if($age)<span>@faNum($age) ساله</span>@endif
                            @if($genderFa)<span>{{ $genderFa }}</span>@endif
                            @if($artist->years_experience)<span>@faNum($artist->years_experience) سال سابقه</span>@endif
                        </div>

                        {{-- ویژگی‌های public تخصص match شده --}}
                        @if(!empty($card['public_attrs']))
                        <div class="cf-attr-chips">
                            @foreach($card['public_attrs'] as $attr)
                                <span class="cf-attr-chip">
                                    <b>{{ $attr['label'] }}:</b> {{ $attr['value'] }}@if($attr['unit']) {{ $attr['unit'] }}@endif
                                </span>
                            @endforeach
                        </div>
                        @endif

                        {{-- اکشن --}}
                        <div class="cf-card-actions" style="display:flex;flex-direction:column;gap:.4rem">
                            @if($unlocked)
                                @if(!empty($artist->username))
                                    <a href="{{ route('profile.show', $artist->username) }}" class="btn btn-primary btn-sm btn-block">
                                        مشاهده پروفایل کامل
                                    </a>
                                @else
                                    <button type="button" class="btn btn-primary btn-sm btn-block" disabled>پروفایل در دسترس نیست</button>
                                @endif
                            @else
                                {{-- تیک‌آبی: لینک پروفیل عمومی مجاز است (هویت عمومی)، اطلاعات تماس همچنان نیازمند unlock --}}
                                @if($isBlue)
                                    @if(!empty($artist->username))
                                        <a href="{{ route('profile.show', $artist->username) }}" class="btn btn-outline btn-sm btn-block">
                                            مشاهده پروفایل
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline btn-sm btn-block" disabled>پروفایل در دسترس نیست</button>
                                    @endif
                                @endif

                                @if($festivalActive)
                                    {{-- جشنواره: باز کردن رایگان برای تیم تأییدشده --}}
                                    <form action="{{ route('production.access.unlock') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="artist_profile_id" value="{{ $artist->id }}">
                                        <button type="submit" class="btn btn-accent btn-sm btn-block">مشاهده رایگان (جشنواره) 🔓</button>
                                    </form>
                                @elseif($access && $access->remainingCredits() > 0)
                                    <form action="{{ route('production.access.unlock') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="artist_profile_id" value="{{ $artist->id }}">
                                        <button type="submit" class="btn btn-accent btn-sm btn-block">🔓 باز کردن با ۱ اعتبار</button>
                                    </form>
                                @else
                                    <a href="{{ route('production.access') }}" class="btn btn-outline btn-sm btn-block">خرید اعتبار</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @empty
                <div class="cf-empty">
                    <div style="font-size:2.2rem;margin-bottom:.6rem">🔍</div>
                    <p>هنرمندی مطابق فیلترهای شما یافت نشد.</p>
                    @if($activeFilterCount > 0)
                        <a href="{{ route('production.search') }}" class="btn btn-ghost btn-sm" style="margin-top:.8rem">پاک کردن فیلترها</a>
                    @endif
                </div>
                @endforelse
            </div>

            @if($artists->hasPages())
            <div class="pagination-wrap">
                {{ $artists->links() }}
            </div>
            @endif

        </section>
    </div>
</div>

@endsection
