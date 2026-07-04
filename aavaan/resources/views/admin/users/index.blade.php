@extends('admin.layouts.app')
@section('title', 'مدیریت کاربران')
@section('page-title', 'مدیریت کاربران')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:1rem;">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">➕ افزودن کاربر</a>
</div>
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="نام، ایمیل، موبایل...">
        </div>
        <div class="form-group" style="margin:0;">
            <label>نقش</label>
            <select name="role" class="form-control">
                <option value="">همه</option>
                <option value="artist" {{ request('role')=='artist'?'selected':'' }}>هنرمند</option>
                <option value="production" {{ request('role')=='production'?'selected':'' }}>تولید</option>
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>ادمین</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>فعال</option>
                <option value="banned" {{ request('status')=='banned'?'selected':'' }}>مسدود</option>
                <option value="deleted" {{ request('status')=='deleted'?'selected':'' }}>حذف‌شده</option>
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
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>نام</th><th>ایمیل</th><th>موبایل</th><th>نقش</th><th>وضعیت</th><th>تاریخ عضویت</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td style="direction:ltr;text-align:left;">{{ $user->email }}</td>
                <td>{{ $user->phone ?: '—' }}</td>
                <td>
                    @php $roles=['artist'=>'هنرمند','production'=>'تولید','admin'=>'ادمین']; @endphp
                    <span class="badge badge-info">{{ $roles[$user->role] ?? $user->role }}</span>
                </td>
                <td>
                    @if($user->trashed()) <span class="badge badge-muted">حذف‌شده</span>
                    @elseif($user->is_banned) <span class="badge badge-danger">مسدود</span>
                    @else <span class="badge badge-success">فعال</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('Y/m/d') }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                    @if($user->trashed())
                        <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" style="display:inline;">@csrf
                            <button type="submit" class="btn btn-outline btn-sm">بازیابی</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;"
                            onsubmit="return confirm('آیا از حذف این کاربر مطمئنید؟')">@csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-muted);">کاربری یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $users->links() }}</div>
</div>
@endsection
