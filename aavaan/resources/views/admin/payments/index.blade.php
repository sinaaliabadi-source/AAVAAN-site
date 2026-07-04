@extends('admin.layouts.app')
@section('title', 'پرداخت‌ها')
@section('page-title', 'مدیریت پرداخت‌ها')
@section('topbar-actions')
<a href="{{ route('admin.payments.export', request()->query()) }}" class="btn btn-outline btn-sm">📥 خروجی CSV</a>
@endsection
@section('content')
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>پرداخت‌شده</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>در انتظار</option>
                <option value="failed" {{ request('status')=='failed'?'selected':'' }}>ناموفق</option>
                <option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>بازگشت</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>نوع</label>
            <select name="payable_type" class="form-control">
                <option value="">همه</option>
                <option value="subscription" {{ request('payable_type')=='subscription'?'selected':'' }}>اشتراک هنرمند</option>
                <option value="production" {{ request('payable_type')=='production'?'selected':'' }}>دسترسی تولید</option>
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
        <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>کاربر</th><th>مبلغ (تومان)</th><th>نوع</th><th>روش</th><th>وضعیت</th><th>کد مرجع</th><th>تاریخ</th>
            </tr></thead>
            <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>
                    <div>{{ $p->user?->name }}</div>
                    <div class="text-muted" style="font-size:.78rem;">{{ $p->user?->email }}</div>
                </td>
                <td>{{ number_format($p->amount) }}</td>
                <td>{{ str_contains($p->payable_type ?? '', 'Subscription') ? 'اشتراک' : 'دسترسی تولید' }}</td>
                <td>
                    @if($p->isManual())
                        <span class="badge" style="background:#e7e2f5;color:#5b4a8a;">دستی</span>
                    @else
                        <span class="text-sm text-muted">{{ $p->gateway }}</span>
                    @endif
                </td>
                <td>
                    @if($p->status==='paid') <span class="badge badge-success">پرداخت‌شده</span>
                    @elseif($p->status==='pending') <span class="badge badge-warning">در انتظار</span>
                    @elseif($p->status==='refunded') <span class="badge badge-info">بازگشت‌داده‌شده</span>
                    @else <span class="badge badge-danger">ناموفق</span>
                    @endif
                </td>
                <td style="direction:ltr;text-align:left;font-size:.8rem;">{{ $p->ref_id ?: '—' }}</td>
                <td>{{ $p->created_at->format('Y/m/d') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-muted);">پرداختی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $payments->links() }}</div>
</div>
@endsection
