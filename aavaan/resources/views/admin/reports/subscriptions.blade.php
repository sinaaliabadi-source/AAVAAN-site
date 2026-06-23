@extends('admin.layouts.app')
@section('title', 'گزارش اشتراک‌ها')
@section('page-title', 'گزارش اشتراک‌های هنرمندان')

@php use App\Helpers\JalaliHelper; @endphp

@section('topbar-actions')
<a href="{{ route('admin.reports.subscriptions.export', request()->query()) }}" class="btn btn-outline btn-sm">📥 Export CSV</a>
@endsection

@section('content')

{{-- ── Filters ─── --}}
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;">
        <div class="form-group" style="margin:0;">
            <label>از تاریخ شروع</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" style="width:155px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>تا تاریخ شروع</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" style="width:155px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>نوع پلن</label>
            <select name="plan" class="form-control">
                <option value="">همه</option>
                <option value="monthly" {{ request('plan')=='monthly'?'selected':'' }}>ماهانه</option>
                <option value="yearly"  {{ request('plan')=='yearly' ?'selected':'' }}>سالانه</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="active"  {{ request('status')=='active'  ?'selected':'' }}>فعال</option>
                <option value="expired" {{ request('status')=='expired' ?'selected':'' }}>منقضی</option>
                <option value="pending" {{ request('status')=='pending' ?'selected':'' }}>در انتظار</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.reports.subscriptions') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
</div>

{{-- ── Stats ─── --}}
<div class="grid-4" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">💰</div>
        <div>
            <div class="stat-value" style="font-size:1.2rem;">{{ number_format($totalRevenue) }}</div>
            <div class="stat-label">مجموع درآمد (تومان)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">📅</div>
        <div>
            <div class="stat-value" style="font-size:1.2rem;">{{ $monthlyCount }}</div>
            <div class="stat-label">اشتراک ماهانه</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">📆</div>
        <div>
            <div class="stat-value" style="font-size:1.2rem;">{{ $yearlyCount }}</div>
            <div class="stat-label">اشتراک سالانه</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">🔄</div>
        <div>
            <div class="stat-value" style="font-size:1.2rem;">{{ $renewalRate }}٪</div>
            <div class="stat-label">نرخ تمدید | میانگین {{ $avgDays }} روز</div>
        </div>
    </div>
</div>

{{-- ── Table ─── --}}
<div class="card">
    <div class="card-title">📋 لیست اشتراک‌ها ({{ $subscriptions->total() }} مورد)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام هنرمند</th>
                    <th>ایمیل</th>
                    <th>نوع پلن</th>
                    <th>مبلغ</th>
                    <th>کد تخفیف</th>
                    <th>شروع</th>
                    <th>پایان</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $s)
                @php
                    $statusBadge = match($s->status) {
                        'active'  => '<span class="badge badge-success">فعال</span>',
                        'expired' => '<span class="badge badge-danger">منقضی</span>',
                        default   => '<span class="badge badge-warning">در انتظار</span>',
                    };
                    $planLabel = $s->plan === 'monthly' ? 'ماهانه' : 'سالانه';
                @endphp
                <tr>
                    <td class="text-muted">{{ $s->id }}</td>
                    <td><strong>{{ $s->user?->name }}</strong></td>
                    <td class="text-muted" style="font-size:.82rem;">{{ $s->user?->email }}</td>
                    <td><span class="badge badge-info">{{ $planLabel }}</span></td>
                    <td>{{ $s->payment ? number_format($s->payment->amount) . ' تومان' : '—' }}</td>
                    <td>
                        @if($s->discountUse?->discountCode)
                            <code style="font-size:.8rem;background:#f0ede6;padding:.1rem .4rem;border-radius:4px;">{{ $s->discountUse->discountCode->code }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $s->starts_at ? JalaliHelper::toDate($s->starts_at) : '—' }}</td>
                    <td>{{ $s->expires_at ? JalaliHelper::toDate($s->expires_at) : '—' }}</td>
                    <td>{!! $statusBadge !!}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--color-muted);">موردی یافت نشد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subscriptions->hasPages())
    <div style="margin-top:1rem;">{{ $subscriptions->links() }}</div>
    @endif
</div>

@endsection
