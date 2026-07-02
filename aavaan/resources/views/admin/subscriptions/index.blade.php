@extends('admin.layouts.app')
@section('title', 'اشتراک‌ها')
@section('page-title', 'مدیریت اشتراک‌ها')
@section('content')
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="نام یا ایمیل هنرمند...">
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>فعال</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>منقضی</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>در انتظار</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>پلن</label>
            <select name="plan" class="form-control">
                <option value="">همه</option>
                <option value="monthly" {{ request('plan')=='monthly'?'selected':'' }}>ماهانه</option>
                <option value="yearly" {{ request('plan')=='yearly'?'selected':'' }}>سالانه</option>
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
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-ghost">پاک</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>هنرمند</th><th>پلن</th><th>شروع</th><th>پایان</th><th>وضعیت</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($subscriptions as $sub)
            <tr>
                <td>{{ $sub->id }}</td>
                <td>{{ $sub->user?->name }}</td>
                <td>{{ $sub->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}</td>
                <td>{{ $sub->starts_at?->format('Y/m/d') ?: '—' }}</td>
                <td>{{ $sub->expires_at?->format('Y/m/d') ?: '—' }}</td>
                <td>
                    @if($sub->status==='active' && $sub->expires_at > now()) <span class="badge badge-success">فعال</span>
                    @elseif($sub->status==='pending') <span class="badge badge-warning">در انتظار</span>
                    @else <span class="badge badge-muted">منقضی</span>
                    @endif
                </td>
                <td><a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-ghost btn-sm">جزئیات</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-muted);">اشتراکی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $subscriptions->links() }}</div>
</div>
@endsection
