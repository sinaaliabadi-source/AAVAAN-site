@extends('admin.layouts.app')
@section('title', 'گزارش دسترسی تولید')
@section('page-title', 'گزارش دسترسی‌های تیم تولید')

@php use App\Helpers\JalaliHelper; @endphp

@section('topbar-actions')
<a href="{{ route('admin.reports.production-access.export', request()->query()) }}" class="btn btn-outline btn-sm">📥 Export CSV</a>
@endsection

@section('content')

{{-- ── Filters ─── --}}
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;">
        <div class="form-group" style="margin:0;">
            <label>از تاریخ پرداخت</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" style="width:155px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>تا تاریخ پرداخت</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" style="width:155px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت پرداخت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="paid"     {{ request('status','paid')=='paid'     ?'selected':'' }}>پرداخت‌شده</option>
                <option value="pending"  {{ request('status')=='pending'  ?'selected':'' }}>در انتظار</option>
                <option value="failed"   {{ request('status')=='failed'   ?'selected':'' }}>ناموفق</option>
                <option value="refunded" {{ request('status')=='refunded' ?'selected':'' }}>بازگشت</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.reports.production-access') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
</div>

{{-- ── Stats ─── --}}
<div class="grid-3" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">💰</div>
        <div>
            <div class="stat-value" style="font-size:1.2rem;">{{ number_format($totalRevenue) }}</div>
            <div class="stat-label">مجموع درآمد دسترسی (تومان)</div>
        </div>
    </div>

    <div class="card" style="margin:0;padding:1.2rem;">
        <div class="card-title" style="font-size:.88rem;margin-bottom:.75rem;padding-bottom:.5rem;">🏆 پرفعال‌ترین تیم‌های تولید</div>
        @forelse($topProducers as $i => $p)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.3rem 0;border-bottom:1px solid #f0ede8;font-size:.85rem;">
            <span>{{ $i+1 }}. {{ $p->user?->name ?? '—' }}</span>
            <span class="badge badge-info">{{ $p->access_count }} دسترسی</span>
        </div>
        @empty
        <div class="text-muted" style="font-size:.85rem;">داده‌ای وجود ندارد</div>
        @endforelse
    </div>

    <div class="card" style="margin:0;padding:1.2rem;">
        <div class="card-title" style="font-size:.88rem;margin-bottom:.75rem;padding-bottom:.5rem;">🎭 پربازدیدترین هنرمندان</div>
        @forelse($topArtists as $i => $a)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.3rem 0;border-bottom:1px solid #f0ede8;font-size:.85rem;">
            <span>{{ $i+1 }}. {{ $a->artistProfile?->user?->name ?? '—' }}</span>
            <span class="badge badge-warning">{{ $a->access_count }} بازدید</span>
        </div>
        @empty
        <div class="text-muted" style="font-size:.85rem;">داده‌ای وجود ندارد</div>
        @endforelse
    </div>
</div>

{{-- ── Table ─── --}}
<div class="card">
    <div class="card-title">📋 لیست دسترسی‌ها ({{ $accesses->total() }} مورد)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام تیم تولید</th>
                    <th>ایمیل</th>
                    <th>نوع دسترسی</th>
                    <th>استفاده</th>
                    <th>مبلغ</th>
                    <th>وضعیت پرداخت</th>
                    <th>تاریخ پرداخت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accesses as $a)
                @php
                    $typeMap = ['single' => 'تکی', 'bundle_5' => 'بسته ۵', 'bundle_10' => 'بسته ۱۰'];
                    $payStatus = $a->payment?->status;
                    $payBadge = match($payStatus) {
                        'paid'     => '<span class="badge badge-success">پرداخت‌شده</span>',
                        'pending'  => '<span class="badge badge-warning">در انتظار</span>',
                        'failed'   => '<span class="badge badge-danger">ناموفق</span>',
                        'refunded' => '<span class="badge badge-muted">بازگشت</span>',
                        default    => '<span class="badge badge-muted">—</span>',
                    };
                @endphp
                <tr>
                    <td class="text-muted">{{ $a->id }}</td>
                    <td><strong>{{ $a->user?->name }}</strong></td>
                    <td class="text-muted" style="font-size:.82rem;">{{ $a->user?->email }}</td>
                    <td><span class="badge badge-info">{{ $typeMap[$a->access_type] ?? $a->access_type }}</span></td>
                    <td>{{ $a->used_count }} / {{ $a->bundle_size }}</td>
                    <td>{{ $a->payment ? number_format($a->payment->amount) . ' تومان' : '—' }}</td>
                    <td>{!! $payBadge !!}</td>
                    <td>{{ $a->payment?->paid_at ? JalaliHelper::toDate($a->payment->paid_at) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-muted);">موردی یافت نشد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($accesses->hasPages())
    <div style="margin-top:1rem;">{{ $accesses->links() }}</div>
    @endif
</div>

@endsection
