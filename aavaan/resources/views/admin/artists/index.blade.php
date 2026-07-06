@extends('admin.layouts.app')
@section('title', 'مدیریت هنرمندان')
@section('page-title', 'مدیریت هنرمندان')
@section('content')
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="نام هنری، نام، ایمیل...">
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>فعال</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>غیرفعال</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>تیک آبی</label>
            <select name="blue_tick" class="form-control">
                <option value="">همه</option>
                <option value="yes" {{ request('blue_tick')=='yes'?'selected':'' }}>دارای تیک آبی</option>
                <option value="no" {{ request('blue_tick')=='no'?'selected':'' }}>بدون تیک آبی</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>شهر</label>
            <select name="city" class="form-control">
                <option value="">همه شهرها</option>
                @foreach($cities as $city)
                <option value="{{ $city }}" {{ request('city')==$city?'selected':'' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.artists.index') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>نام هنری</th><th>نام کاربر</th><th>شهر</th><th>وضعیت</th><th>تیک آبی</th><th>بازدید</th><th>تاریخ</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($artists as $artist)
            <tr>
                <td>{{ $artist->id }}</td>
                <td>{{ $artist->username ?: '—' }}</td>
                <td>{{ $artist->user?->name }}</td>
                <td>{{ $artist->city ?: '—' }}</td>
                <td>
                    @if($artist->is_active) <span class="badge badge-success">فعال</span>
                    @else <span class="badge badge-muted">غیرفعال</span>
                    @endif
                </td>
                <td>
                    @if($artist->has_blue_tick)
                        <span title="دارای تیک آبی آوان"><x-blue-tick :size="18" /></span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ number_format($artist->profile_views) }}</td>
                <td>{{ $artist->created_at->format('Y/m/d') }}</td>
                <td style="white-space:nowrap;">
                    @if($artist->username)
                    <a href="{{ route('profile.show', $artist->username) }}" target="_blank" class="btn btn-ghost btn-sm">پروفایل</a>
                    @endif
                    <a href="{{ route('admin.artists.edit', $artist->id) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--color-muted);">هنرمندی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $artists->links() }}</div>
</div>
@endsection
