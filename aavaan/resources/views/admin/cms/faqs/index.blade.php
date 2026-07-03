@extends('admin.layouts.app')
@section('title', 'سوالات متداول')
@section('page-title', 'سوالات متداول')
@section('topbar-actions')
<a href="{{ route('admin.cms.faqs.create') }}" class="btn btn-primary btn-sm">+ سوال جدید</a>
@endsection
@section('content')
@forelse($groups as $category => $items)
<div class="card" style="margin-bottom:1.1rem">
    <div class="card-title">{{ $categories[$category] ?? $category }}</div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr><th style="width:60px">ترتیب</th><th>سوال</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
            @foreach($items as $faq)
            <tr>
                <td>{{ $faq->sort_order }}</td>
                <td>{{ \Illuminate\Support\Str::limit($faq->question, 70) }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.cms.faqs.reorder') }}" style="display:inline">@csrf
                        <input type="hidden" name="toggle_id" value="{{ $faq->id }}">
                        <button class="badge {{ $faq->is_published ? 'badge-success' : 'badge-muted' }}" style="border:none;cursor:pointer">
                            {{ $faq->is_published ? 'منتشرشده' : 'مخفی' }}
                        </button>
                    </form>
                </td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.cms.faqs.edit', $faq) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                    <form method="POST" action="{{ route('admin.cms.faqs.destroy', $faq) }}" style="display:inline" onsubmit="return confirm('حذف سوال؟')">@csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm" style="color:#c0392b">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card"><p style="text-align:center;padding:2rem;color:var(--color-muted)">سوالی ثبت نشده است.</p></div>
@endforelse
@endsection
