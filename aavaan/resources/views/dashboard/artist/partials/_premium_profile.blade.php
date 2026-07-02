{{--
    Variables expected:
        $premiumProfile  — ArtistProfilePremium|null
--}}
@php
    $pp = $premiumProfile;
    $awardsJson = json_encode($pp?->awards ?? [], JSON_UNESCAPED_UNICODE);
    $membershipsJson = json_encode(
        collect($pp?->memberships ?? [])->map(fn($m) => is_array($m) ? $m : ['name' => $m])->values()->all(),
        JSON_UNESCAPED_UNICODE
    );
@endphp

<div class="card" id="premium-profile">
    <div class="card-title">⭐ اطلاعات تکمیلی حرفه‌ای</div>

    @if($errors->has('stage_name') || $errors->has('legal_name') || $errors->has('imdb_url') ||
        $errors->has('instagram_url') || $errors->has('availability_status') || $errors->has('awards') ||
        $errors->has('memberships'))
        <div class="alert alert-error">✕ {{ $errors->first() }}</div>
    @endif

    <form action="{{ route('artist.profile-premium.update') }}" method="POST">
        @csrf

        {{-- نام‌ها --}}
        <div class="grid-2">
            <div class="form-group">
                <label>نام هنری</label>
                <input type="text" name="stage_name" class="form-control"
                       value="{{ old('stage_name', $pp?->stage_name) }}"
                       placeholder="نام مستعار یا هنری شما" maxlength="100">
                @error('stage_name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>
                    نام رسمی / قانونی
                    <span class="badge badge-warning" style="font-size:.7rem;vertical-align:middle;margin-right:.3rem">🔒 فقط تیم تولید با دسترسی</span>
                </label>
                <input type="text" name="legal_name" class="form-control"
                       value="{{ old('legal_name', $pp?->legal_name) }}"
                       placeholder="نام و نام‌خانوادگی کامل" maxlength="100">
                @error('legal_name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <hr class="divider">

        {{-- وضعیت‌های ملی --}}
        <div style="margin-bottom:.6rem">
            <strong class="text-sm" style="color:var(--color-primary)">اطلاعات رسمی</strong>
            <span class="badge badge-warning" style="font-size:.7rem;vertical-align:middle;margin-right:.4rem">🔒 فقط تیم تولید با دسترسی</span>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>وضعیت سربازی</label>
                <select name="military_status" class="form-control">
                    <option value="">انتخاب کنید…</option>
                    <option value="not_required" {{ old('military_status', $pp?->military_status) === 'not_required' ? 'selected' : '' }}>مشمول نیست</option>
                    <option value="completed"    {{ old('military_status', $pp?->military_status) === 'completed'    ? 'selected' : '' }}>خدمت انجام شده</option>
                    <option value="exempt"       {{ old('military_status', $pp?->military_status) === 'exempt'       ? 'selected' : '' }}>معاف</option>
                    <option value="active"       {{ old('military_status', $pp?->military_status) === 'active'       ? 'selected' : '' }}>در خدمت</option>
                </select>
                @error('military_status')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>وضعیت گذرنامه</label>
                <select name="passport_status" class="form-control">
                    <option value="">انتخاب کنید…</option>
                    <option value="valid"   {{ old('passport_status', $pp?->passport_status) === 'valid'   ? 'selected' : '' }}>گذرنامه معتبر دارم</option>
                    <option value="expired" {{ old('passport_status', $pp?->passport_status) === 'expired' ? 'selected' : '' }}>گذرنامه منقضی‌شده</option>
                    <option value="none"    {{ old('passport_status', $pp?->passport_status) === 'none'    ? 'selected' : '' }}>ندارم</option>
                </select>
                @error('passport_status')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- آمادگی‌ها --}}
        <div style="display:flex;flex-wrap:wrap;gap:1.5rem;margin-bottom:1.1rem">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.88rem">
                <input type="hidden" name="willing_to_travel" value="0">
                <input type="checkbox" name="willing_to_travel" value="1"
                       {{ old('willing_to_travel', $pp?->willing_to_travel) ? 'checked' : '' }}>
                آمادگی سفر به شهرهای دیگر
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.88rem">
                <input type="hidden" name="willing_long_stay" value="0">
                <input type="checkbox" name="willing_long_stay" value="1"
                       {{ old('willing_long_stay', $pp?->willing_long_stay) ? 'checked' : '' }}>
                آمادگی اقامت طولانی‌مدت
            </label>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.88rem">
                <input type="hidden" name="international_collaboration" value="0">
                <input type="checkbox" name="international_collaboration" value="1"
                       {{ old('international_collaboration', $pp?->international_collaboration) ? 'checked' : '' }}>
                تمایل به همکاری بین‌المللی
            </label>
        </div>

        <hr class="divider">

        {{-- آمار پروژه‌ها --}}
        <div class="grid-2">
            <div class="form-group">
                <label>تعداد پروژه‌های تکمیل‌شده</label>
                <input type="number" name="completed_projects_count" class="form-control" min="0"
                       value="{{ old('completed_projects_count', $pp?->completed_projects_count ?? 0) }}" dir="ltr">
                @error('completed_projects_count')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>تعداد پروژه‌های منتشرشده</label>
                <input type="number" name="published_projects_count" class="form-control" min="0"
                       value="{{ old('published_projects_count', $pp?->published_projects_count ?? 0) }}" dir="ltr">
                @error('published_projects_count')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <hr class="divider">

        {{-- دسترس‌پذیری --}}
        <div x-data="{
            status: '{{ old('availability_status', $pp?->availability_status ?? 'ready') }}'
        }">
            <div class="grid-2" style="align-items:end">
                <div class="form-group" style="margin-bottom:0">
                    <label>وضعیت دسترسی</label>
                    <select name="availability_status" class="form-control" x-model="status">
                        <option value="ready">آماده‌ام</option>
                        <option value="busy">مشغول هستم</option>
                        <option value="available_from">از تاریخ مشخص آماده‌ام</option>
                    </select>
                    @error('availability_status')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0" x-show="status === 'available_from'" x-cloak>
                    <label>از تاریخ (شمسی)</label>
                    <input type="text" name="available_from_date" class="form-control"
                           value="{{ old('available_from_date', $pp?->available_from_date?->format('Y-m-d')) }}"
                           placeholder="مثلاً: ۱۴۰۳-۰۷-۰۱" dir="ltr">
                    @error('available_from_date')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group" style="margin-top:1rem">
                <label>ظرفیت همزمان (تعداد پروژه)</label>
                <input type="number" name="concurrent_capacity" class="form-control" min="1" max="20"
                       value="{{ old('concurrent_capacity', $pp?->concurrent_capacity) }}"
                       placeholder="مثلاً: ۲" dir="ltr" style="max-width:160px">
                @error('concurrent_capacity')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <hr class="divider">

        {{-- دستمزد --}}
        <div>
            <div style="margin-bottom:.8rem">
                <strong class="text-sm" style="color:var(--color-primary)">دستمزد روزانه (تومان)</strong>
            </div>
            <div class="grid-2" style="margin-bottom:.75rem">
                <div class="form-group">
                    <label>حداقل</label>
                    <input type="number" name="day_rate_min" class="form-control" min="0" step="100000"
                           value="{{ old('day_rate_min', $pp?->day_rate_min) }}"
                           placeholder="مثلاً: 5000000" dir="ltr">
                    @error('day_rate_min')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>حداکثر</label>
                    <input type="number" name="day_rate_max" class="form-control" min="0" step="100000"
                           value="{{ old('day_rate_max', $pp?->day_rate_max) }}"
                           placeholder="مثلاً: 15000000" dir="ltr">
                    @error('day_rate_max')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.88rem">
                <input type="hidden" name="show_day_rate" value="0">
                <input type="checkbox" name="show_day_rate" value="1"
                       {{ old('show_day_rate', $pp?->show_day_rate) ? 'checked' : '' }}>
                نمایش بازه دستمزد در پروفایل عمومی
                <span class="badge badge-warning" style="font-size:.7rem;margin-right:.3rem">🔒 فقط تیم تولید با دسترسی</span>
            </label>
        </div>

        <hr class="divider">

        {{-- جوایز —  Alpine.js repeater --}}
        <div x-data="{
            awards: {{ $awardsJson }}.length ? {{ $awardsJson }} : [],
            addAward()  { this.awards.push({ title: '', year: '', event: '' }); },
            removeAward(i) { this.awards.splice(i, 1); }
        }">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem">
                <strong class="text-sm" style="color:var(--color-primary)">جوایز و افتخارات</strong>
                <button type="button" class="btn btn-ghost btn-sm" @click="addAward()">＋ افزودن جایزه</button>
            </div>

            <template x-if="awards.length === 0">
                <p class="text-sm text-muted" style="margin-bottom:.75rem">هنوز جایزه‌ای ثبت نشده.</p>
            </template>

            <template x-for="(award, i) in awards" :key="i">
                <div style="display:grid;grid-template-columns:1fr auto auto 30px;gap:.6rem;align-items:end;margin-bottom:.6rem">
                    <div>
                        <label class="text-sm">عنوان جایزه</label>
                        <input type="text" :name="'awards['+i+'][title]'" x-model="award.title"
                               class="form-control" placeholder="مثلاً: بهترین بازیگر" maxlength="200">
                    </div>
                    <div style="min-width:80px">
                        <label class="text-sm">سال</label>
                        <input type="number" :name="'awards['+i+'][year]'" x-model="award.year"
                               class="form-control" min="1300" max="1410" placeholder="۱۴۰۲" dir="ltr">
                    </div>
                    <div style="min-width:140px">
                        <label class="text-sm">رویداد / جشنواره</label>
                        <input type="text" :name="'awards['+i+'][event]'" x-model="award.event"
                               class="form-control" placeholder="فجر، ۲۰۲۳…" maxlength="200">
                    </div>
                    <div style="padding-bottom:.1rem">
                        <button type="button"
                                style="background:#fde8e8;color:#b91c1c;border:none;border-radius:6px;width:28px;height:36px;cursor:pointer;font-size:.9rem"
                                @click="removeAward(i)" title="حذف">✕</button>
                    </div>
                </div>
            </template>

            @error('awards')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <hr class="divider">

        {{-- عضویت‌ها — Alpine.js repeater --}}
        <div x-data="{
            memberships: {{ $membershipsJson }}.length ? {{ $membershipsJson }} : [],
            addMembership()     { this.memberships.push({ name: '' }); },
            removeMembership(i) { this.memberships.splice(i, 1); }
        }">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem">
                <strong class="text-sm" style="color:var(--color-primary)">عضویت‌ها و انجمن‌ها</strong>
                <button type="button" class="btn btn-ghost btn-sm" @click="addMembership()">＋ افزودن عضویت</button>
            </div>

            <template x-if="memberships.length === 0">
                <p class="text-sm text-muted" style="margin-bottom:.75rem">هنوز عضویتی ثبت نشده.</p>
            </template>

            <template x-for="(m, i) in memberships" :key="i">
                <div style="display:grid;grid-template-columns:1fr 30px;gap:.6rem;align-items:end;margin-bottom:.6rem">
                    <div>
                        <label class="text-sm">نام انجمن / صنف</label>
                        <input type="text" :name="'memberships['+i+'][name]'" x-model="m.name"
                               class="form-control" placeholder="مثلاً: خانه سینما" maxlength="200">
                    </div>
                    <div style="padding-bottom:.1rem">
                        <button type="button"
                                style="background:#fde8e8;color:#b91c1c;border:none;border-radius:6px;width:28px;height:36px;cursor:pointer;font-size:.9rem"
                                @click="removeMembership(i)" title="حذف">✕</button>
                    </div>
                </div>
            </template>

            @error('memberships')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <hr class="divider">

        {{-- لینک‌های شبکه‌های اجتماعی و پورتفولیو --}}
        <div style="margin-bottom:.85rem">
            <strong class="text-sm" style="color:var(--color-primary)">لینک‌های حرفه‌ای و شبکه‌های اجتماعی</strong>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>IMDb</label>
                <input type="url" name="imdb_url" class="form-control" dir="ltr"
                       value="{{ old('imdb_url', $pp?->imdb_url) }}"
                       placeholder="https://www.imdb.com/name/…" maxlength="500">
                @error('imdb_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>اینستاگرام</label>
                <input type="url" name="instagram_url" class="form-control" dir="ltr"
                       value="{{ old('instagram_url', $pp?->instagram_url) }}"
                       placeholder="https://instagram.com/…" maxlength="500">
                @error('instagram_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>لینکدین</label>
                <input type="url" name="linkedin_url" class="form-control" dir="ltr"
                       value="{{ old('linkedin_url', $pp?->linkedin_url) }}"
                       placeholder="https://linkedin.com/in/…" maxlength="500">
                @error('linkedin_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>یوتیوب</label>
                <input type="url" name="youtube_url" class="form-control" dir="ltr"
                       value="{{ old('youtube_url', $pp?->youtube_url) }}"
                       placeholder="https://youtube.com/…" maxlength="500">
                @error('youtube_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>ویمئو</label>
                <input type="url" name="vimeo_url" class="form-control" dir="ltr"
                       value="{{ old('vimeo_url', $pp?->vimeo_url) }}"
                       placeholder="https://vimeo.com/…" maxlength="500">
                @error('vimeo_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>وب‌سایت شخصی</label>
                <input type="url" name="website_url" class="form-control" dir="ltr"
                       value="{{ old('website_url', $pp?->website_url) }}"
                       placeholder="https://example.com" maxlength="500">
                @error('website_url')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:.5rem">
            <button type="submit" class="btn btn-primary">ذخیره اطلاعات حرفه‌ای</button>
        </div>
    </form>
</div>
