@extends('admin.layouts.app')
@section('title', 'تیم‌های تولید')
@section('page-title', 'تیم‌های تولید')
@section('content')
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="نام یا ایمیل...">
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
                <th>#</th><th>نام</th><th>ایمیل</th><th>تعداد دسترسی‌ها</th><th>تاریخ ثبت‌نام</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($teams as $team)
            <tr>
                <td>{{ $team->id }}</td>
                <td>{{ $team->name }}</td>
                <td style="direction:ltr;text-align:left;">{{ $team->email }}</td>
                <td>{{ $team->productionAccesses->count() }}</td>
                <td>{{ $team->created_at->format('Y/m/d') }}</td>
                <td><a href="{{ route('admin.production.show', $team->id) }}" class="btn btn-ghost btn-sm">جزئیات</a></td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-muted);">تیمی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $teams->links() }}</div>
</div>
@endsection
