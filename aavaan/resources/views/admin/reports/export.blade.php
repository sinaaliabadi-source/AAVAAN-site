@extends('admin.layouts.app')
@section('title', 'خروجی سراسری')
@section('page-title', 'خروجی سراسری گزارش‌ها')

@section('content')

<div class="card" style="max-width:600px;">
    <div class="card-title">📥 دانلود گزارش</div>

    <form method="GET" action="{{ route('admin.reports.export.download') }}">
        <div class="form-group">
            <label>نوع گزارش</label>
            <div style="display:flex;flex-direction:column;gap:.5rem;margin-top:.4rem;">
                <label style="font-weight:400;display:flex;align-items:center;gap:.5rem;">
                    <input type="radio" name="type" value="payments" required {{ old('type')=='payments'?'checked':'' }}> پرداخت‌های کامل
                </label>
                <label style="font-weight:400;display:flex;align-items:center;gap:.5rem;">
                    <input type="radio" name="type" value="subscriptions" {{ old('type')=='subscriptions'?'checked':'' }}> اشتراک‌ها
                </label>
                <label style="font-weight:400;display:flex;align-items:center;gap:.5rem;">
                    <input type="radio" name="type" value="production" {{ old('type')=='production'?'checked':'' }}> دسترسی تولید
                </label>
                <label style="font-weight:400;display:flex;align-items:center;gap:.5rem;">
                    <input type="radio" name="type" value="users" {{ old('type')=='users'?'checked':'' }}> کاربران
                </label>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>از تاریخ</label>
                <input type="date" name="from" value="{{ old('from') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>تا تاریخ</label>
                <input type="date" name="to" value="{{ old('to') }}" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>فرمت</label>
            <div style="display:flex;align-items:center;gap:.5rem;margin-top:.3rem;">
                <input type="radio" name="format" value="csv" checked> CSV (UTF-8 BOM — سازگار با Excel)
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            📥 دانلود گزارش
        </button>
    </form>
</div>

@endsection
