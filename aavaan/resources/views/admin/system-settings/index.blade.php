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
            <input type="text" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}"
                class="form-control" style="max-width:400px;">
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
