@extends('admin.layouts.app')
@section('title', 'ویرایش کد تخفیف')
@section('page-title', 'ویرایش کد: ' . $code->code)
@section('content')
<nav style="font-size:.83rem;color:var(--color-muted);margin-bottom:1.2rem;">
    <a href="{{ route('admin.discounts.index') }}">کدهای تخفیف</a> ← ویرایش
</nav>
<div class="grid-2">
<div class="card">
    <div class="card-title">ویرایش کد تخفیف</div>
    <form method="POST" action="{{ route('admin.discounts.update', $code->id) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label>کد تخفیف</label>
            <div style="display:flex;gap:.5rem;">
                <input type="text" name="code" id="codeInput" value="{{ old('code', $code->code) }}"
                    class="form-control" required style="direction:ltr;letter-spacing:.1em;">
                <button type="button" class="btn btn-ghost btn-sm" onclick="generateCode()">تولید</button>
            </div>
            @error('code')<span class="form-error">{{ $message }}</span>@enderror
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>نوع</label>
                <select name="type" class="form-control" required>
                    <option value="percent" {{ $code->type=='percent'?'selected':'' }}>درصدی (%)</option>
                    <option value="fixed" {{ $code->type=='fixed'?'selected':'' }}>مبلغ ثابت</option>
                </select>
            </div>
            <div class="form-group">
                <label>مقدار</label>
                <input type="number" name="value" value="{{ old('value', $code->value) }}" class="form-control" required min="0" step="0.01">
            </div>
        </div>
        <div class="form-group">
            <label>حداکثر استفاده (خالی = نامحدود)</label>
            <input type="number" name="max_uses" value="{{ old('max_uses', $code->max_uses) }}" class="form-control" min="1">
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>معتبر از تاریخ</label>
                <input type="date" name="valid_from" value="{{ old('valid_from', $code->valid_from?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="form-group">
                <label>معتبر تا تاریخ</label>
                <input type="date" name="valid_until" value="{{ old('valid_until', $code->valid_until?->format('Y-m-d')) }}" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $code->is_active?'checked':'' }}> فعال
            </label>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-ghost">بازگشت</a>
        </div>
    </form>
</div>
<div class="card">
    <div class="card-title">آمار و وضعیت</div>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>تعداد استفاده:</strong> {{ $code->used_count }} / {{ $code->max_uses ?? '∞' }}</p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>وضعیت فعلی:</strong>
        @if(!$code->is_active) <span class="badge badge-muted">غیرفعال</span>
        @elseif($code->isValid()) <span class="badge badge-success">فعال و معتبر</span>
        @else <span class="badge badge-warning">منقضی/تمام‌شده</span>
        @endif
    </p>
    <p class="text-sm" style="margin-bottom:.5rem;"><strong>ایجادکننده:</strong> {{ $code->creator?->name }}</p>
    <p class="text-sm" style="margin-bottom:1rem;"><strong>تاریخ ایجاد:</strong> {{ $code->created_at->format('Y/m/d') }}</p>
    <hr class="divider">
    <p class="text-sm text-muted" style="margin-bottom:.75rem;">تغییر سریع وضعیت:</p>
    <form method="POST" action="{{ route('admin.discounts.toggle', $code->id) }}">@csrf
        <button class="btn btn-sm {{ $code->is_active ? 'btn-ghost' : 'btn-outline' }}">
            {{ $code->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
        </button>
    </form>
</div>
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
