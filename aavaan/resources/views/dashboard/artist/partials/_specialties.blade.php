{{--
  Variables expected (passed from profile.blade.php or controller):
    $specialties         — Collection<ArtistSpecialty> with category.attributeDefinitions + media eager-loaded
    $categories          — Collection<SpecialtyCategory> (root, active, for "add new" dropdown)
    $usedCategoryIds     — Collection of category_id values already added by this user
    $definitionsByCategory — array keyed by category_id for Alpine.js
    $totalPhotos         — int, global photo count for this user across all specialties
--}}

<div class="card" id="specialties">
    <div class="card-title">
        🎭 تخصص‌های من
        <span class="badge badge-info" style="margin-right:.5rem;font-size:.75rem">
            {{ $specialties->count() }} تخصص
        </span>
        <span class="badge {{ $totalPhotos >= 30 ? 'badge-danger' : 'badge-success' }}"
              style="font-size:.75rem">
            📷 {{ $totalPhotos }}/30 عکس
        </span>
    </div>

    {{-- ─────────── Existing specialties ─────────── --}}
    @forelse($specialties->sortByDesc('is_primary') as $specialty)
    @php
        $spPhotos     = $specialty->media->where('type', 'photo');
        $spVideos     = $specialty->media->where('type', 'video_link');
        $attrDefs     = $specialty->category->effectiveAttributeDefinitions();
        $attrs        = $specialty->attributes ?? [];
    @endphp

    {{-- کارت تخصص؛ برای هنر اصلی، حاشیهٔ طلایی دودی #C9A24B --}}
    <div x-data="{ open: false }"
         style="border:1px solid {{ $specialty->is_primary ? '#C9A24B' : '#ede8dc' }};border-radius:8px;margin-bottom:1rem;overflow:hidden{{ $specialty->is_primary ? ';box-shadow:0 0 0 1px #C9A24B33' : '' }}">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.85rem 1rem;background:{{ $specialty->is_primary ? '#faf6ec' : '#faf7f2' }};flex-wrap:wrap;gap:.5rem">
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                <strong style="font-family:'YekanBakh',sans-serif;color:var(--color-primary)">
                    {{ $specialty->category->name_fa }}
                </strong>
                @if($specialty->is_primary)
                    <span class="badge" style="background:#C9A24B;color:#fff">⭐ اصلی</span>
                @endif
                @if($specialty->years_experience)
                    <span class="text-sm text-muted">{{ $specialty->years_experience }} سال تجربه</span>
                @endif
                @if($spPhotos->count())
                    <span class="badge badge-info">{{ $spPhotos->count() }} عکس</span>
                @endif
                @if($spVideos->count())
                    <span class="badge badge-info">{{ $spVideos->count() }} ویدیو</span>
                @endif
            </div>

            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                @if(!$specialty->is_primary)
                <form action="{{ route('artist.specialties.primary', $specialty->id) }}" method="POST"
                      style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" title="تعیین به‌عنوان تخصص اصلی">
                        ⭐ تعیین اصلی
                    </button>
                </form>
                @endif
                <button @click="open = !open" class="btn btn-outline btn-sm">
                    <span x-text="open ? '✕ بستن' : '✎ ویرایش'">✎ ویرایش</span>
                </button>
                <form action="{{ route('artist.specialties.destroy', $specialty->id) }}" method="POST"
                      style="display:inline"
                      onsubmit="return confirm('این تخصص و تمام عکس‌ها و ویدیوهای آن حذف شود؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </div>
        </div>

        {{-- Collapsible edit form (server-side rendered, static fields) --}}
        <div x-show="open" x-cloak style="padding:1.25rem;border-top:1px solid #ede8dc">
            <form action="{{ route('artist.specialties.update', $specialty->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="category_id" value="{{ $specialty->category_id }}">

                <div class="grid-2" style="margin-bottom:1rem">
                    <div class="form-group">
                        <label>سال‌های تجربه در این رشته</label>
                        <input type="number" name="years_experience" class="form-control"
                               value="{{ $specialty->years_experience }}" min="0" max="60" dir="ltr">
                    </div>
                    <div class="form-group" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
                        <input type="checkbox" name="is_primary" value="1"
                               id="primary-{{ $specialty->id }}"
                               {{ $specialty->is_primary ? 'checked' : '' }}>
                        <label for="primary-{{ $specialty->id }}" style="margin:0;font-weight:400;cursor:pointer">
                            تخصص اصلی من
                        </label>
                    </div>
                </div>

                {{-- Static attribute fields (rendered server-side for existing specialty) --}}
                @foreach($attrDefs as $def)
                @php
                    $val = $attrs[$def->key] ?? null;
                    $isPrivate = $def->visibility === 'production_team_only';
                @endphp
                <div class="form-group">
                    <label>
                        {{ $def->label_fa }}
                        @if($def->is_required)<span class="req">*</span>@endif
                        @if($isPrivate)
                            <span style="color:var(--color-accent);font-size:.74rem;font-weight:400">
                                🔒 فقط تیم تولید با دسترسی
                            </span>
                        @endif
                    </label>

                    @if($def->field_type === 'text')
                        <input type="text" name="attributes[{{ $def->key }}]" class="form-control"
                               value="{{ $val }}" {{ $def->is_required ? 'required' : '' }}>

                    @elseif($def->field_type === 'textarea')
                        <textarea name="attributes[{{ $def->key }}]" class="form-control"
                                  rows="3" {{ $def->is_required ? 'required' : '' }}>{{ $val }}</textarea>

                    @elseif($def->field_type === 'number')
                        <div style="display:flex;align-items:center;gap:.5rem">
                            <input type="number" name="attributes[{{ $def->key }}]" class="form-control"
                                   value="{{ $val }}" {{ $def->is_required ? 'required' : '' }} dir="ltr"
                                   style="flex:1">
                            @if($def->unit)
                                <span class="text-sm text-muted" style="white-space:nowrap">{{ $def->unit }}</span>
                            @endif
                        </div>

                    @elseif($def->field_type === 'url')
                        <input type="text" name="attributes[{{ $def->key }}]" class="form-control"
                               value="{{ $val }}" dir="ltr" placeholder="https://"
                               {{ $def->is_required ? 'required' : '' }}>

                    @elseif($def->field_type === 'file_link')
                        <input type="text" name="attributes[{{ $def->key }}]" class="form-control"
                               value="{{ $val }}" dir="ltr"
                               placeholder="https://www.aparat.com/v/..."
                               {{ $def->is_required ? 'required' : '' }}>
                        <span class="form-hint">فقط لینک آپارات: https://www.aparat.com/v/...</span>

                    @elseif($def->field_type === 'date')
                        <input type="date" name="attributes[{{ $def->key }}]" class="form-control"
                               value="{{ $val }}" dir="ltr"
                               {{ $def->is_required ? 'required' : '' }}>

                    @elseif($def->field_type === 'boolean')
                        <label style="display:flex;align-items:center;gap:.5rem;font-weight:400;cursor:pointer">
                            <input type="checkbox" name="attributes[{{ $def->key }}]" value="1"
                                   {{ $val ? 'checked' : '' }}>
                            <span>بله</span>
                        </label>

                    @elseif($def->field_type === 'select')
                        <select name="attributes[{{ $def->key }}]" class="form-control"
                                {{ $def->is_required ? 'required' : '' }}>
                            <option value="">انتخاب کنید…</option>
                            @foreach($def->options ?? [] as $opt)
                                <option value="{{ $opt['value'] }}"
                                        {{ $val === $opt['value'] ? 'selected' : '' }}>
                                    {{ $opt['label'] }}
                                </option>
                            @endforeach
                        </select>

                    @elseif($def->field_type === 'multiselect')
                        <div style="display:flex;flex-wrap:wrap;gap:.4rem .75rem;padding:.4rem 0">
                            @foreach($def->options ?? [] as $opt)
                                @php $checked = is_array($val) && in_array($opt['value'], $val, true); @endphp
                                <label style="display:flex;align-items:center;gap:.3rem;font-weight:400;cursor:pointer;font-size:.85rem">
                                    <input type="checkbox" name="attributes[{{ $def->key }}][]"
                                           value="{{ $opt['value'] }}" {{ $checked ? 'checked' : '' }}>
                                    {{ $opt['label'] }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            </form>
        </div>

        {{-- Media section (always visible when expanded or as mini preview) --}}
        <div style="padding:.75rem 1rem;background:#fff;border-top:1px solid #f5f2ed">

            {{-- Photos grid --}}
            @if($spPhotos->count())
            <div style="margin-bottom:.75rem">
                <div style="font-size:.8rem;font-weight:600;color:var(--color-muted);margin-bottom:.5rem">
                    عکس‌ها ({{ $spPhotos->count() }})
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                    @foreach($spPhotos as $photo)
                    <div style="position:relative;width:80px;height:80px;border-radius:6px;overflow:hidden;background:#f0ede8;flex-shrink:0">
                        <img src="{{ asset('uploads/' . $photo->file_path) }}"
                             alt="{{ $photo->title }}"
                             style="width:100%;height:100%;object-fit:cover">
                        <form action="{{ route('artist.specialties.media.delete', $photo->id) }}"
                              method="POST"
                              style="position:absolute;top:2px;left:2px"
                              onsubmit="return confirm('این عکس حذف شود؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:rgba(192,57,43,.9);color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:.65rem;display:flex;align-items:center;justify-content:center;padding:0"
                                    title="حذف">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upload photo --}}
            @if($totalPhotos < 30)
            <form action="{{ route('artist.specialties.media.upload', $specialty->id) }}" method="POST"
                  enctype="multipart/form-data"
                  style="display:flex;align-items:flex-end;gap:.5rem;flex-wrap:wrap;margin-bottom:.75rem">
                @csrf
                <input type="hidden" name="type" value="photo">
                <div class="form-group" style="flex:1;min-width:180px;margin-bottom:0">
                    <label style="font-size:.8rem">افزودن عکس (Headshot / Full Body / گالری)</label>
                    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp"
                           style="font-size:.8rem">
                </div>
                <div class="form-group" style="flex:1;min-width:150px;margin-bottom:0">
                    <label style="font-size:.8rem">عنوان (اختیاری)</label>
                    <input type="text" name="title" class="form-control" placeholder="مثلاً: Headshot"
                           style="font-size:.8rem">
                </div>
                <button type="submit" class="btn btn-outline btn-sm">📤 آپلود عکس</button>
            </form>
            <span class="form-hint" style="font-size:.75rem">
                عکس‌ها تا ۲۰۰۰px و ۱ مگابایت فشرده‌سازی می‌شوند | سقف کلی: {{ $totalPhotos }}/30
            </span>
            @else
            <p class="text-sm text-muted" style="margin-bottom:.75rem">
                به سقف ۳۰ عکس رسیده‌اید. برای افزودن عکس، ابتدا یک عکس قبلی را حذف کنید.
            </p>
            @endif

            {{-- Video links --}}
            @if($spVideos->count())
            <div style="margin-top:.5rem">
                <div style="font-size:.8rem;font-weight:600;color:var(--color-muted);margin-bottom:.4rem">
                    لینک‌های ویدیو آپارات ({{ $spVideos->count() }})
                </div>
                @foreach($spVideos as $vid)
                <div style="display:flex;align-items:center;justify-content:space-between;background:#faf7f2;border-radius:6px;padding:.4rem .6rem;margin-bottom:.3rem;gap:.5rem;flex-wrap:wrap">
                    <div>
                        @if($vid->title)
                            <span class="text-sm" style="font-weight:600">{{ $vid->title }}</span>
                            <span class="text-muted" style="font-size:.78rem"> — </span>
                        @endif
                        <a href="{{ $vid->external_url }}" target="_blank" rel="noopener"
                           style="font-size:.8rem;color:var(--color-accent);direction:ltr;display:inline-block">
                            {{ Str::limit($vid->external_url, 50) }}
                        </a>
                    </div>
                    <form action="{{ route('artist.specialties.media.delete', $vid->id) }}"
                          method="POST"
                          onsubmit="return confirm('این لینک ویدیو حذف شود؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">✕</button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Add video link --}}
            <form action="{{ route('artist.specialties.media.upload', $specialty->id) }}" method="POST"
                  style="display:flex;align-items:flex-end;gap:.5rem;flex-wrap:wrap;margin-top:.5rem">
                @csrf
                <input type="hidden" name="type" value="video_link">
                <div class="form-group" style="flex:2;min-width:220px;margin-bottom:0">
                    <label style="font-size:.8rem">لینک ویدیو آپارات (Showreel، Self Tape…)</label>
                    <input type="text" name="external_url" class="form-control"
                           placeholder="https://www.aparat.com/v/..."
                           dir="ltr" style="font-size:.8rem">
                </div>
                <div class="form-group" style="flex:1;min-width:130px;margin-bottom:0">
                    <label style="font-size:.8rem">عنوان (اختیاری)</label>
                    <input type="text" name="title" class="form-control"
                           placeholder="مثلاً: Showreel ۱۴۰۳" style="font-size:.8rem">
                </div>
                <button type="submit" class="btn btn-outline btn-sm">+ افزودن ویدیو</button>
            </form>
            <span class="form-hint" style="font-size:.75rem">فقط لینک آپارات مجاز است — هیچ فایل ویدیویی آپلود نمی‌شود</span>

        </div>{{-- /media --}}
    </div>{{-- /specialty card --}}
    @empty
    <div style="text-align:center;padding:2rem;color:var(--color-muted);background:#faf7f2;border-radius:8px;border:2px dashed #ddd8ce;margin-bottom:1rem">
        <div style="font-size:2rem;margin-bottom:.5rem">🎭</div>
        <div class="text-sm">هنوز تخصصی به پروفایل اضافه نشده</div>
        <div class="text-sm" style="margin-top:.25rem">با افزودن تخصص، فیلدهای اختصاصی رشته شما نمایش می‌یابد</div>
    </div>
    @endforelse

    <hr class="divider">

    {{-- ─────────── Add new specialty (Alpine.js dynamic form) ─────────── --}}
    <div x-data='{
        show: false,
        categoryId: "",
        attributes: {},
        allDefs: @json($definitionsByCategory),
        get currentDefs() {
            return this.categoryId ? (this.allDefs[this.categoryId] ?? []) : [];
        },
        setCategory(id) {
            this.categoryId = id;
            this.attributes = {};
        },
        toggleMulti(key, value, checked) {
            if (!Array.isArray(this.attributes[key])) this.attributes[key] = [];
            if (checked) {
                if (!this.attributes[key].includes(value)) this.attributes[key].push(value);
            } else {
                this.attributes[key] = this.attributes[key].filter(v => v !== value);
            }
        }
    }'>
        <button @click="show = !show" class="btn btn-accent" type="button">
            <span x-text="show ? '✕ انصراف' : '＋ افزودن تخصص جدید'">＋ افزودن تخصص جدید</span>
        </button>

        <div x-show="show" x-cloak
             style="margin-top:1rem;background:#faf7f2;border-radius:8px;padding:1.25rem;border:1px solid #ede8dc">

            <div style="font-weight:700;font-family:'YekanBakh',sans-serif;font-size:.9rem;color:var(--color-primary);margin-bottom:1rem">
                ＋ افزودن تخصص جدید
            </div>

            <form action="{{ route('artist.specialties.store') }}" method="POST">
                @csrf

                {{-- Category selector — leaf categories grouped by parent --}}
                <div class="form-group">
                    <label>دسته‌بندی تخصص <span class="req">*</span></label>
                    <select name="category_id" class="form-control" required
                            @change="setCategory($event.target.value)">
                        <option value="">انتخاب کنید…</option>
                        @foreach($categories->groupBy('parent_id') as $parentId => $children)
                        <optgroup label="{{ $children->first()->parent->name_fa }}">
                            @foreach($children as $cat)
                                @if(!$usedCategoryIds->contains($cat->id))
                                <option value="{{ $cat->id }}">{{ $cat->name_fa }}</option>
                                @else
                                <option value="{{ $cat->id }}" disabled style="color:#aaa">
                                    {{ $cat->name_fa }} (قبلاً اضافه شده)
                                </option>
                                @endif
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Base fields --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label>سال‌های تجربه</label>
                        <input type="number" name="years_experience" class="form-control"
                               min="0" max="60" dir="ltr" placeholder="۰">
                    </div>
                    <div class="form-group" style="display:flex;align-items:center;gap:.5rem;padding-top:1.5rem">
                        <input type="checkbox" name="is_primary" value="1" id="new-specialty-primary">
                        <label for="new-specialty-primary" style="margin:0;font-weight:400;cursor:pointer">
                            تخصص اصلی من
                        </label>
                    </div>
                </div>

                {{-- Dynamic attribute fields rendered by Alpine.js --}}
                <div x-show="categoryId" x-cloak>
                    <div style="border-top:1px solid #ede8dc;margin:.75rem 0 1rem"></div>
                    <div style="font-size:.82rem;font-weight:600;color:var(--color-muted);margin-bottom:.75rem">
                        فیلدهای اختصاصی این رشته
                    </div>

                    <template x-for="def in currentDefs" :key="def.key">
                        <div class="form-group">
                            <label>
                                <span x-text="def.label_fa + (def.is_required ? ' *' : '')"></span>
                                <template x-if="def.visibility === 'production_team_only'">
                                    <span style="color:var(--color-accent);font-size:.74rem;font-weight:400">
                                        🔒 فقط تیم تولید با دسترسی
                                    </span>
                                </template>
                            </label>

                            {{-- text / url --}}
                            <template x-if="def.field_type === 'text' || def.field_type === 'url'">
                                <input type="text"
                                       :name="'attributes[' + def.key + ']'"
                                       class="form-control"
                                       :required="def.is_required"
                                       x-model="attributes[def.key]">
                            </template>

                            {{-- number (با واحد اختیاری کنار فیلد) --}}
                            <template x-if="def.field_type === 'number'">
                                <div style="display:flex;align-items:center;gap:.5rem">
                                    <input type="number"
                                           :name="'attributes[' + def.key + ']'"
                                           class="form-control"
                                           :required="def.is_required"
                                           x-model="attributes[def.key]"
                                           dir="ltr"
                                           style="flex:1">
                                    <template x-if="def.unit">
                                        <span class="text-sm text-muted" style="white-space:nowrap" x-text="def.unit"></span>
                                    </template>
                                </div>
                            </template>

                            {{-- textarea --}}
                            <template x-if="def.field_type === 'textarea'">
                                <textarea :name="'attributes[' + def.key + ']'"
                                          class="form-control"
                                          :required="def.is_required"
                                          x-model="attributes[def.key]"
                                          rows="3"></textarea>
                            </template>

                            {{-- date --}}
                            <template x-if="def.field_type === 'date'">
                                <input type="date"
                                       :name="'attributes[' + def.key + ']'"
                                       class="form-control"
                                       :required="def.is_required"
                                       x-model="attributes[def.key]"
                                       dir="ltr">
                            </template>

                            {{-- file_link (Aparat only) --}}
                            <template x-if="def.field_type === 'file_link'">
                                <div>
                                    <input type="text"
                                           :name="'attributes[' + def.key + ']'"
                                           class="form-control"
                                           :required="def.is_required"
                                           x-model="attributes[def.key]"
                                           dir="ltr"
                                           placeholder="https://www.aparat.com/v/...">
                                    <span class="form-hint">فقط لینک آپارات مجاز است</span>
                                </div>
                            </template>

                            {{-- boolean (checkbox) --}}
                            <template x-if="def.field_type === 'boolean'">
                                <label style="display:flex;align-items:center;gap:.5rem;font-weight:400;cursor:pointer">
                                    <input type="checkbox"
                                           :name="'attributes[' + def.key + ']'"
                                           value="1"
                                           :checked="!!attributes[def.key]"
                                           @change="attributes[def.key] = $event.target.checked">
                                    <span>بله</span>
                                </label>
                            </template>

                            {{-- select --}}
                            <template x-if="def.field_type === 'select'">
                                <select :name="'attributes[' + def.key + ']'"
                                        class="form-control"
                                        :required="def.is_required"
                                        x-model="attributes[def.key]">
                                    <option value="">انتخاب کنید…</option>
                                    <template x-for="opt in (def.options || [])" :key="opt.value">
                                        <option :value="opt.value" x-text="opt.label"></option>
                                    </template>
                                </select>
                            </template>

                            {{-- multiselect (checkboxes) --}}
                            <template x-if="def.field_type === 'multiselect'">
                                <div style="display:flex;flex-wrap:wrap;gap:.4rem .75rem;padding:.4rem 0">
                                    <template x-for="opt in (def.options || [])" :key="opt.value">
                                        <label style="display:flex;align-items:center;gap:.3rem;font-weight:400;cursor:pointer;font-size:.85rem">
                                            <input type="checkbox"
                                                   :name="'attributes[' + def.key + '][]'"
                                                   :value="opt.value"
                                                   :checked="Array.isArray(attributes[def.key]) && attributes[def.key].includes(opt.value)"
                                                   @change="toggleMulti(def.key, opt.value, $event.target.checked)">
                                            <span x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div style="margin-top:1rem;display:flex;gap:.5rem">
                    <button type="submit" class="btn btn-primary"
                            :disabled="!categoryId">افزودن تخصص</button>
                    <button type="button" @click="show = false; categoryId = ''; attributes = {}"
                            class="btn btn-ghost">انصراف</button>
                </div>
            </form>
        </div>
    </div>
</div>
