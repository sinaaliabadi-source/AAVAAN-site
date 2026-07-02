@extends('admin.layouts.app')
@section('title', 'ایجاد کد تخفیف')
@section('page-title', 'ایجاد کد تخفیف جدید')
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.discounts.index') }}">کدهای تخفیف</a> ← ایجاد جدید
</nav>
<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.discounts.store') }}">
        @csrf
        <div class="form-group">
            <label>کد تخفیف</label>
            <div style="display:flex;gap:.5rem;">
                <input type="text" name="code" id="codeInput" value="{{ old('code') }}" class="form-control"
                    required placeholder="مثال: SAVE20" style="direction:ltr;letter-spacing:.1em;">
                <button type="button" class="btn btn-ghost btn-sm" onclick="generateCode()">تولید خودکار</button>
            </div>
            @error('code')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>نوع تخفیف</label>
                <select name="type" class="form-control" required>
                    <option value="percent" {{ old('type')=='percent'?'selected':'' }}>درصدی (%)</option>
                    <option value="fixed" {{ old('type')=='fixed'?'selected':'' }}>مبلغ ثابت (تومان)</option>
                </select>
            </div>
            <div class="form-group">
                <label>مقدار</label>
                <input type="number" name="value" value="{{ old('value') }}" class="form-control" required min="0" step="0.01">
                @error('value')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-group">
            <label>حداکثر دفعات استفاده</label>
            <input type="number" name="max_uses" value="{{ old('max_uses') }}" class="form-control" min="1" placeholder="خالی بگذارید = نامحدود">
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>معتبر از تاریخ (اختیاری)</label>
                <input type="date" name="valid_from" value="{{ old('valid_from') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>معتبر تا تاریخ (اختیاری)</label>
                <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked>
                از همان ابتدا فعال باشد
            </label>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary">ایجاد کد تخفیف</button>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-ghost">انصراف</a>
        </div>
    </form>
</div>
@push('scripts')
<script>
function generateCode() {
    fetch('{{ route('admin.discounts.generate-code') }}')
        .then(r => r.json())
        .then(d => { document.getElementById('codeInput').value = d.code; });
}
</script>
@endpush
@endsection
