@extends('admin.layouts.app')
@section('title', $faq->exists ? 'ویرایش سوال' : 'سوال جدید')
@section('page-title', $faq->exists ? 'ویرایش سوال' : 'سوال جدید')
@section('content')
@if($errors->any())<div class="alert alert-error"><ul style="margin:0;padding-right:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card" style="max-width:680px">
    <form method="POST" action="{{ $faq->exists ? route('admin.cms.faqs.update', $faq) : route('admin.cms.faqs.store') }}">
        @csrf
        @if($faq->exists) @method('PUT') @endif
        <div class="form-group">
            <label>سوال <span style="color:#c0392b">*</span></label>
            <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question) }}" maxlength="500" required>
        </div>
        <div class="form-group">
            <label>پاسخ <span style="color:#c0392b">*</span></label>
            <textarea name="answer" class="form-control" rows="5" maxlength="5000" required>{{ old('answer', $faq->answer) }}</textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group">
                <label>دسته</label>
                <select name="category" class="form-control">
                    @foreach($categories as $k=>$v)
                        <option value="{{ $k }}" {{ old('category',$faq->category)===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>ترتیب</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:.4rem;font-size:.88rem;margin-bottom:1rem">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $faq->exists ? $faq->is_published : true) ? 'checked' : '' }}>
            انتشار
        </label>
        <button class="btn btn-primary">ذخیره</button>
        <a href="{{ route('admin.cms.faqs.index') }}" class="btn btn-ghost">انصراف</a>
    </form>
</div>
@endsection
