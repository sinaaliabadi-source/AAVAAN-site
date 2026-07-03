@extends('admin.layouts.app')
@section('title', $post->exists ? 'ویرایش مقاله' : 'مقالهٔ جدید')
@section('page-title', $post->exists ? 'ویرایش مقاله' : 'مقالهٔ جدید')
@section('topbar-actions')
<a href="{{ route('admin.cms.posts.index') }}" class="btn btn-ghost btn-sm">→ فهرست مقالات</a>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
.cms-editor-layout { display:grid; grid-template-columns:1fr 320px; gap:1.25rem; align-items:start; }
.editor-host { background:#fff; }
#editor-container { height:380px; background:#fff; }
.ql-editor { direction:rtl; text-align:right; font-family:inherit; font-size:.95rem; }
.ql-toolbar { direction:ltr; }
.side-card { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.1rem; margin-bottom:1rem; }
.side-card h3 { font-size:.9rem; color:var(--color-primary); margin-bottom:.75rem; padding-bottom:.5rem; border-bottom:1px solid #f0ede8; }
.tag-input-chip { display:inline-flex; align-items:center; gap:.3rem; background:#f5f0e8; color:var(--color-primary); border-radius:99px; padding:.15rem .6rem; font-size:.8rem; margin:.15rem; }
.tag-input-chip button { border:none; background:none; cursor:pointer; color:#c0392b; }
.cover-preview { width:100%; height:150px; object-fit:cover; border-radius:8px; border:1px solid #ece6da; margin-bottom:.5rem; }
.google-preview { border:1px solid #ece6da; border-radius:8px; padding:.75rem; margin-top:.6rem; }
.google-preview .g-title { color:#1a0dab; font-size:1rem; line-height:1.4; }
.google-preview .g-url { color:#006621; font-size:.8rem; }
.google-preview .g-desc { color:#545454; font-size:.82rem; line-height:1.6; }
@media (max-width:900px){ .cms-editor-layout{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
@if($errors->any())
<div class="alert alert-error"><ul style="margin:0;padding-right:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST"
      action="{{ $post->exists ? route('admin.cms.posts.update', $post) : route('admin.cms.posts.store') }}"
      x-data="postForm()" @submit="syncContent()">
    @csrf
    @if($post->exists) @method('PUT') @endif

    <div class="cms-editor-layout">
        {{-- ستون اصلی --}}
        <div>
            <div class="side-card">
                <div class="form-group">
                    <label>عنوان <span style="color:#c0392b">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>نامک (slug) <span style="color:var(--color-muted);font-size:.8rem">(اختیاری — خودکار ساخته می‌شود)</span></label>
                    <input type="text" name="slug" class="form-control" dir="ltr" value="{{ old('slug', $post->slug) }}" placeholder="auto">
                </div>
            </div>

            <div class="side-card">
                <label>محتوا <span style="color:#c0392b">*</span></label>
                <div class="editor-host">
                    <div id="editor-container"></div>
                </div>
                <textarea name="content" id="content-field" style="display:none">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="side-card">
                <label>خلاصه <span style="color:var(--color-muted);font-size:.8rem">(برای فهرست مقالات)</span></label>
                <textarea name="excerpt" class="form-control" rows="2" maxlength="1000">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
        </div>

        {{-- sidebar --}}
        <div>
            {{-- انتشار --}}
            <div class="side-card">
                <h3>انتشار</h3>
                <div class="form-group">
                    <label>وضعیت</label>
                    <select name="status" class="form-control" x-model="status">
                        <option value="draft">پیش‌نویس</option>
                        <option value="published">منتشرشده</option>
                        <option value="archived">بایگانی</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:.75rem">
                    <label>زمان انتشار</label>
                    <input type="datetime-local" name="published_at" class="form-control"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <label style="display:flex;align-items:center;gap:.4rem;font-size:.85rem;margin-bottom:1rem">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                    ★ مقالهٔ ویژه
                </label>
                <div style="display:flex;gap:.5rem">
                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1" @click="status='{{ old('status',$post->status) ?: 'draft' }}'">ذخیره</button>
                    <button type="submit" class="btn btn-accent btn-sm" style="flex:1" @click="status='published'">انتشار</button>
                </div>
            </div>

            {{-- تصویر cover --}}
            <div class="side-card" x-data="coverUpload()">
                <h3>تصویر شاخص</h3>
                <template x-if="cover">
                    <img :src="coverUrl" class="cover-preview" x-show="coverUrl">
                </template>
                <input type="hidden" name="cover_image" x-model="cover">
                <input type="file" accept="image/*" @change="upload($event)" class="form-control" style="font-size:.8rem">
                <p x-show="uploading" style="font-size:.8rem;color:var(--color-muted);margin-top:.4rem">در حال آپلود…</p>
                <template x-if="cover">
                    <button type="button" class="btn btn-ghost btn-sm" style="margin-top:.5rem;color:#c0392b" @click="cover=''; coverUrl=''">حذف تصویر</button>
                </template>
                <a href="{{ route('admin.cms.media.index') }}" target="_blank" style="display:block;margin-top:.5rem;font-size:.8rem;color:var(--color-accent)">کتابخانهٔ رسانه ↗</a>
            </div>

            {{-- دسته‌بندی --}}
            <div class="side-card">
                <h3>دسته‌بندی</h3>
                <select name="category_id" class="form-control">
                    <option value="">— بدون دسته —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ (string)old('category_id',$post->category_id)===(string)$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- تگ‌ها --}}
            <div class="side-card" x-data="tagInput()">
                <h3>برچسب‌ها</h3>
                <div>
                    <template x-for="(t,i) in tags" :key="i">
                        <span class="tag-input-chip">
                            <span x-text="t"></span>
                            <button type="button" @click="remove(i)">✕</button>
                            <input type="hidden" name="tags[]" :value="t">
                        </span>
                    </template>
                </div>
                <input type="text" class="form-control" style="margin-top:.5rem" placeholder="برچسب و Enter"
                       @keydown.enter.prevent="add($event.target)">
                <p style="font-size:.75rem;color:var(--color-muted);margin-top:.35rem">با Enter اضافه کنید</p>
            </div>

            {{-- SEO --}}
            <div class="side-card" x-data="{ showSeo: {{ (old('meta_title',$post->meta_title) || old('meta_description',$post->meta_description)) ? 'true':'false' }} }">
                <h3 style="display:flex;justify-content:space-between;align-items:center">
                    SEO
                    <button type="button" class="btn btn-ghost btn-sm" @click="showSeo=!showSeo" x-text="showSeo?'بستن':'نمایش'"></button>
                </h3>
                <div x-show="showSeo" x-cloak>
                    <div class="form-group">
                        <label>عنوان متا</label>
                        <input type="text" name="meta_title" class="form-control" maxlength="255"
                               value="{{ old('meta_title', $post->meta_title) }}"
                               x-data x-ref="mt" @input="$refs.gt.textContent = $event.target.value || '{{ $post->title }}'">
                    </div>
                    <div class="form-group">
                        <label>توضیح متا</label>
                        <textarea name="meta_description" class="form-control" rows="2" maxlength="500"
                                  @input="$refs.gd.textContent = $event.target.value">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>
                    <div class="google-preview">
                        <div class="g-title" x-ref="gt">{{ old('meta_title', $post->meta_title ?: $post->title) ?: 'عنوان مقاله' }}</div>
                        <div class="g-url">aavaan.com › blog › {{ $post->slug ?: '...' }}</div>
                        <div class="g-desc" x-ref="gd">{{ old('meta_description', $post->meta_description) ?: 'توضیح مقاله در نتایج جستجو اینجا نمایش داده می‌شود.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function () {
        quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'blockquote'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image', 'code-block'],
                    ['clean'],
                ],
            },
        });
        const existing = document.getElementById('content-field').value;
        if (existing) { quill.clipboard.dangerouslyPasteHTML(existing); }
    });

    function postForm() {
        return {
            status: @js(old('status', $post->status ?: 'draft')),
            syncContent() {
                document.getElementById('content-field').value = quill.root.innerHTML;
            },
        };
    }

    function tagInput() {
        return {
            tags: @js(old('tags', $post->exists ? $post->tags->pluck('name')->all() : [])),
            add(el) {
                const v = el.value.trim();
                if (v && !this.tags.includes(v)) { this.tags.push(v); }
                el.value = '';
            },
            remove(i) { this.tags.splice(i, 1); },
        };
    }

    function coverUpload() {
        return {
            cover: @js(old('cover_image', $post->cover_image ?? '')),
            coverUrl: @js($post->cover_url ?? ''),
            uploading: false,
            upload(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.uploading = true;
                const fd = new FormData();
                fd.append('file', file);
                fd.append('_token', '{{ csrf_token() }}');
                fetch('{{ route('admin.cms.media.upload') }}', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(d => { this.cover = d.url; this.coverUrl = d.url; this.uploading = false; })
                    .catch(() => { this.uploading = false; alert('آپلود ناموفق بود'); });
            },
        };
    }
</script>
@endpush
