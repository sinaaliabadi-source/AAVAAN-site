@extends('admin.layouts.app')
@section('title', 'تیم‌های تولید')
@section('page-title', 'تیم‌های تولید')
@section('content')

@php
    $statusMeta = [
        'pending'  => ['label' => 'در انتظار تأیید', 'bg' => '#faf6ec', 'color' => '#8a6d1f', 'border' => '#e6d09a'],
        'approved' => ['label' => 'تأییدشده',        'bg' => '#eef3ee', 'color' => '#3f5a3f', 'border' => '#c3dfc3'],
        'rejected' => ['label' => 'ردشده',           'bg' => '#fdecea', 'color' => '#a03027', 'border' => '#f5c6cb'],
    ];
@endphp

{{-- شمارندهٔ وضعیت‌ها به‌صورت تب --}}
<div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:1rem;">
    <a href="{{ route('admin.production.index') }}"
       class="btn btn-sm {{ !request('approval_status') ? 'btn-primary' : 'btn-ghost' }}">همه</a>
    @foreach($statusMeta as $key => $meta)
    <a href="{{ route('admin.production.index', ['approval_status' => $key]) }}"
       class="btn btn-sm {{ request('approval_status') === $key ? 'btn-primary' : 'btn-ghost' }}">
        {{ $meta['label'] }}
        <span style="display:inline-block;background:{{ $meta['border'] }};color:{{ $meta['color'] }};border-radius:999px;padding:0 .4rem;font-size:.72rem;margin-right:.25rem;">
            {{ $statusCounts[$key] }}
        </span>
    </a>
    @endforeach
</div>

<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="نام یا ایمیل...">
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت تأیید</label>
            <select name="approval_status" class="form-control">
                <option value="">همه</option>
                <option value="pending" {{ request('approval_status')=='pending'?'selected':'' }}>در انتظار تأیید</option>
                <option value="approved" {{ request('approval_status')=='approved'?'selected':'' }}>تأییدشده</option>
                <option value="rejected" {{ request('approval_status')=='rejected'?'selected':'' }}>ردشده</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>از تاریخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div class="form-group" style="margin:0;">
            <label>تا تاریخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.production.index') }}" class="btn btn-ghost">پاک</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>نام</th><th>ایمیل</th><th>وضعیت</th><th>تعداد دسترسی‌ها</th><th>تاریخ ثبت‌نام</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($teams as $team)
            @php $meta = $statusMeta[$team->approval_status] ?? $statusMeta['approved']; @endphp
            <tr>
                <td>{{ $team->id }}</td>
                <td>{{ $team->name }}</td>
                <td style="direction:ltr;text-align:left;">{{ $team->email }}</td>
                <td>
                    <span style="display:inline-block;background:{{ $meta['bg'] }};color:{{ $meta['color'] }};border:1px solid {{ $meta['border'] }};border-radius:999px;padding:.12rem .6rem;font-size:.78rem;font-weight:600;">
                        {{ $meta['label'] }}
                    </span>
                </td>
                <td>{{ $team->productionAccesses->count() }}</td>
                <td>{{ $team->created_at->format('Y/m/d') }}</td>
                <td><a href="{{ route('admin.production.show', $team->id) }}" class="btn btn-ghost btn-sm">جزئیات</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-muted);">تیمی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $teams->links() }}</div>
</div>
@endsection
