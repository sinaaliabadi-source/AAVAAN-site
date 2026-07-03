@extends('admin.layouts.app')
@section('title', 'پاسخ‌های آماده')
@section('page-title', 'پاسخ‌های آماده')

@php
    $depOptions = ['technical'=>'فنی','billing'=>'مالی','casting'=>'کستینگ','honarbaz'=>'هنرباز','general'=>'عمومی'];
@endphp

@section('content')

{{-- فرم افزودن --}}
<div class="card" style="margin-bottom:1.25rem" x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <div class="card-title" style="margin:0;border:none">➕ پاسخ آمادهٔ جدید</div>
        <button class="btn btn-ghost btn-sm" @click="open = !open" x-text="open ? 'بستن' : 'افزودن'"></button>
    </div>
    <form method="POST" action="{{ route('admin.support.canned.store') }}" x-show="open" x-cloak style="margin-top:1rem">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:.9rem">
            <div class="form-group">
                <label>عنوان <span style="color:#c0392b">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="form-group">
                <label>دپارتمان</label>
                <select name="department" class="form-control">
                    <option value="">— عمومی —</option>
                    @foreach($depOptions as $k=>$v)
                        <option value="{{ $k }}" {{ old('department')===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>متن پاسخ <span style="color:#c0392b">*</span></label>
            <textarea name="content" class="form-control" rows="4" maxlength="5000" required>{{ old('content') }}</textarea>
        </div>
        <button class="btn btn-primary btn-sm">ذخیره</button>
    </form>
</div>

{{-- لیست --}}
<div class="card">
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>عنوان</th><th>دپارتمان</th><th>دفعات استفاده</th><th>سازنده</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($canned as $c)
            <tr x-data="{ edit: false }">
                <td>
                    <div>{{ $c->title }}</div>
                    <div style="font-size:.78rem;color:var(--color-muted);margin-top:.2rem">{{ \Illuminate\Support\Str::limit($c->content, 70) }}</div>
                </td>
                <td>{{ $depOptions[$c->department] ?? '—' }}</td>
                <td>{{ number_format($c->use_count) }}</td>
                <td>{{ $c->creator?->name ?? '—' }}</td>
                <td style="white-space:nowrap">
                    <button class="btn btn-ghost btn-sm" @click="edit = !edit">ویرایش</button>
                    <form method="POST" action="{{ route('admin.support.canned.destroy', $c->id) }}" style="display:inline" onsubmit="return confirm('این پاسخ حذف شود؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm" style="color:#c0392b">حذف</button>
                    </form>

                    {{-- فرم ویرایش (کشویی) --}}
                    <div x-show="edit" x-cloak style="margin-top:.6rem">
                        <form method="POST" action="{{ route('admin.support.canned.update', $c->id) }}" style="text-align:right">
                            @csrf @method('PUT')
                            <input type="text" name="title" class="form-control" value="{{ $c->title }}" required style="margin-bottom:.4rem">
                            <select name="department" class="form-control" style="margin-bottom:.4rem">
                                <option value="">— عمومی —</option>
                                @foreach($depOptions as $k=>$v)
                                    <option value="{{ $k }}" {{ $c->department===$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            <textarea name="content" class="form-control" rows="3" required style="margin-bottom:.4rem">{{ $c->content }}</textarea>
                            <button class="btn btn-primary btn-sm">ذخیره تغییرات</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-muted)">هنوز پاسخ آماده‌ای ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $canned->links() }}</div>
</div>

@endsection
