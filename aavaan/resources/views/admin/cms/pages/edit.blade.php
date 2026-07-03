@extends('admin.layouts.app')
@section('title', 'ویرایش صفحه')
@section('page-title', 'ویرایش صفحه: ' . $page->title)
@section('topbar-actions')
<a href="{{ route('admin.cms.pages.index') }}" class="btn btn-ghost btn-sm">→ فهرست صفحات</a>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
#page-editor { height:420px; background:#fff; }
.ql-editor { direction:rtl; text-align:right; font-family:inherit; }
.ql-toolbar { direction:ltr; }
</style>
@endpush

@section('content')
@if($errors->any())<div class="alert alert-error"><ul style="margin:0;padding-right:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.cms.pages.update', $page->slug) }}" onsubmit="document.getElementById('page-content').value = quill.root.innerHTML">
    @csrf @method('PUT')
    <div class="card" style="margin-bottom:1rem">
        <div class="form-group">
            <label>عنوان</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
        </div>
        <label style="display:flex;align-items:center;gap:.4rem;font-size:.88rem">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
            انتشار در سایت
        </label>
    </div>

    <div class="card" style="margin-bottom:1rem">
        <label>محتوا</label>
        <div id="page-editor"></div>
        <textarea name="content" id="page-content" style="display:none">{{ old('content', $page->content) }}</textarea>
    </div>

    <div class="card" style="margin-bottom:1rem">
        <div class="card-title">SEO</div>
        <div class="form-group">
            <label>عنوان متا</label>
            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}" maxlength="255">
        </div>
        <div class="form-group">
            <label>توضیح متا</label>
            <textarea name="meta_description" class="form-control" rows="2" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
        </div>
    </div>

    <button class="btn btn-primary">ذخیره صفحه</button>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function () {
        quill = new Quill('#page-editor', {
            theme: 'snow',
            modules: { toolbar: [[{ header: [2,3,4,false] }],['bold','italic','underline','blockquote'],[{list:'ordered'},{list:'bullet'}],['link'],['clean']] },
        });
        const existing = document.getElementById('page-content').value;
        if (existing) quill.clipboard.dangerouslyPasteHTML(existing);
    });
</script>
@endpush
