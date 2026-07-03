@extends('admin.layouts.app')
@section('title', 'دسته‌بندی‌های بلاگ')
@section('page-title', 'دسته‌بندی‌های بلاگ')
@section('topbar-actions')
<a href="{{ route('admin.cms.categories.create') }}" class="btn btn-primary btn-sm">+ دستهٔ جدید</a>
@endsection
@section('content')
<div class="card">
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr><th>نام</th><th>نامک</th><th>والد</th><th>رنگ</th><th>مقالات</th><th></th></tr></thead>
            <tbody>
            @forelse($categories as $cat)
            <tr>
                <td style="font-weight:600">{{ $cat->name }}</td>
                <td style="direction:ltr">{{ $cat->slug }}</td>
                <td>{{ $cat->parent?->name ?? '—' }}</td>
                <td>@if($cat->color)<span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:{{ $cat->color }};vertical-align:middle"></span> <span style="font-size:.8rem;direction:ltr">{{ $cat->color }}</span>@else — @endif</td>
                <td>{{ $cat->posts_count }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.cms.categories.edit', $cat) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                    <form method="POST" action="{{ route('admin.cms.categories.destroy', $cat) }}" style="display:inline" onsubmit="return confirm('حذف دسته؟')">@csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm" style="color:#c0392b">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-muted)">دسته‌ای ثبت نشده.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
