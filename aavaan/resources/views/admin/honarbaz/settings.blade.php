@extends('admin.layouts.app')
@section('title', 'تنظیمات هنرباز')
@section('page-title', '🎭 تنظیمات هنرباز')

@section('content')
<div class="card" style="max-width:720px">
    <div class="card-title">تنظیمات برنامه</div>

    <form method="POST" action="{{ route('admin.honarbaz.settings.update') }}">
        @csrf

        <div class="form-group">
            <label>عنوان برنامه</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $program->title) }}" required>
        </div>

        <div class="form-group">
            <label>توضیحات</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $program->description) }}</textarea>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>تاریخ شروع</label>
                <input type="date" name="starts_at" class="form-control"
                       value="{{ old('starts_at', $program->starts_at?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>تاریخ پایان</label>
                <input type="date" name="ends_at" class="form-control"
                       value="{{ old('ends_at', $program->ends_at?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="form-group">
            <label>وضعیت برنامه</label>
            <select name="status" class="form-control">
                <option value="draft" @selected($program->status==='draft')>پیش‌نویس</option>
                <option value="active" @selected($program->status==='active')>فعال</option>
                <option value="closed" @selected($program->status==='closed')>بسته</option>
            </select>
        </div>

        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem">
                <input type="checkbox" name="registration_enabled" value="1"
                       @checked($program->meta['registration_enabled'] ?? true)>
                ثبت‌نام فعال باشد
            </label>
        </div>

        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem">
                <input type="checkbox" name="voting_enabled" value="1"
                       @checked($program->meta['voting_enabled'] ?? true)>
                رأی‌گیری فعال باشد
            </label>
        </div>

        <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
    </form>
</div>
@endsection
