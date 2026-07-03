@extends('admin.layouts.app')
@section('title', $category->exists ? 'ویرایش دسته' : 'دستهٔ جدید')
@section('page-title', $category->exists ? 'ویرایش دسته' : 'دستهٔ جدید')
@section('content')
@if($errors->any())<div class="alert alert-error"><ul style="margin:0;padding-right:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card" style="max-width:640px">
    <form method="POST" action="{{ $category->exists ? route('admin.cms.categories.update', $category) : route('admin.cms.categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <div class="form-group">
            <label>نام <span style="color:#c0392b">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>
        <div class="form-group">
            <label>نامک (اختیاری)</label>
            <input type="text" name="slug" class="form-control" dir="ltr" value="{{ old('slug', $category->slug) }}" placeholder="auto">
        </div>
        <div class="form-group">
            <label>توضیح</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $category->description) }}</textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem">
            <div class="form-group">
                <label>والد</label>
                <select name="parent_id" class="form-control">
                    <option value="">— بدون والد —</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ (string)old('parent_id',$category->parent_id)===(string)$p->id?'selected':'' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>رنگ (hex)</label>
                <input type="color" name="color" class="form-control" value="{{ old('color', $category->color ?: '#C9A24B') }}" style="height:40px">
            </div>
            <div class="form-group">
                <label>ترتیب</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            </div>
        </div>
        <button class="btn btn-primary">ذخیره</button>
        <a href="{{ route('admin.cms.categories.index') }}" class="btn btn-ghost">انصراف</a>
    </form>
</div>
@endsection
