@extends('admin.layouts.app')
@section('title', 'تنظیمات سیستم')
@section('page-title', 'تنظیمات قیمت‌گذاری و محدودیت‌ها')
@section('content')
@php
$groupLabels = ['pricing' => '💰 قیمت‌گذاری', 'limits' => '🔒 محدودیت‌ها', 'general' => '⚙️ عمومی'];
@endphp
<form method="POST" action="{{ route('admin.system-settings.update') }}">
    @csrf @method('PUT')
    @foreach($settings as $group => $items)
    <div class="card">
        <div class="card-title">{{ $groupLabels[$group] ?? $group }}</div>
        @foreach($items as $setting)
        <div class="form-group">
            <label>{{ $setting->label_fa }}</label>

            @if($setting->key === 'festival_active')
                {{-- بولی: چک‌باکس با مقدار پیش‌فرض 0 (چک‌باکس خاموش چیزی نمی‌فرستد) --}}
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input type="hidden" name="{{ $setting->key }}" value="0">
                    <input type="checkbox" name="{{ $setting->key }}" value="1"
                           {{ in_array((string) old($setting->key, $setting->value), ['1','true','on'], true) ? 'checked' : '' }}>
                    جشنواره فعال باشد (عضویت و دسترسی رایگان تا پایان تابستان)
                </label>
            @elseif($setting->key === 'festival_ends_at')
                <input type="date" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}"
                    class="form-control" style="max-width:400px;" dir="ltr">
            @else
                <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}"
                    class="form-control" style="max-width:400px;">
            @endif

            <span class="form-hint" style="direction:ltr;display:inline-block;">{{ $setting->key }}</span>
        </div>
        @endforeach
    </div>
    @endforeach
    <div style="margin-top:.5rem;">
        <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
    </div>
</form>
@endsection
