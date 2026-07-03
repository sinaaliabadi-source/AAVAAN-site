@extends('admin.layouts.app')
@section('title', 'صفحات سایت')
@section('page-title', 'صفحات سایت')
@section('content')
<div class="card">
    <p style="color:var(--color-muted);font-size:.88rem;margin-bottom:1rem">
        محتوای این صفحات از طریق CMS مدیریت می‌شود. صفحات <strong>شرایط استفاده</strong> و <strong>حریم خصوصی</strong>
        مستقیماً از این محتوا در سایت نمایش داده می‌شوند.
    </p>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr><th>عنوان</th><th>نامک</th><th>وضعیت</th><th>آخرین ویرایش</th><th></th></tr></thead>
            <tbody>
            @forelse($pages as $page)
            <tr>
                <td style="font-weight:600">{{ $page->title }}</td>
                <td style="direction:ltr">/{{ $page->slug }}</td>
                <td>@if($page->is_published)<span class="badge badge-success">منتشرشده</span>@else<span class="badge badge-muted">پیش‌نویس</span>@endif</td>
                <td style="white-space:nowrap">{{ $page->updated_at?->format('Y/m/d') }}</td>
                <td><a href="{{ route('admin.cms.pages.edit', $page->slug) }}" class="btn btn-ghost btn-sm">ویرایش</a></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--color-muted)">صفحه‌ای ثبت نشده.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
