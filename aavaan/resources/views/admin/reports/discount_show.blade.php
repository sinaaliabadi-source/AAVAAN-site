@extends('admin.layouts.app')
@section('title', 'جزئیات کد تخفیف')
@section('page-title', 'جزئیات کد تخفیف: ' . $discount->code)

@php use App\Helpers\JalaliHelper; @endphp

@section('topbar-actions')
<a href="{{ route('admin.reports.discounts') }}" class="btn btn-ghost btn-sm">← برگشت</a>
@endsection

@section('content')

{{-- ── Code info card ─── --}}
<div class="card">
    <div class="grid-4">
        <div>
            <div class="text-muted" style="font-size:.78rem;">کد</div>
            <code style="font-size:1.1rem;background:#f0ede6;padding:.2rem .6rem;border-radius:5px;">{{ $discount->code }}</code>
        </div>
        <div>
            <div class="text-muted" style="font-size:.78rem;">نوع / مقدار</div>
            <div style="font-weight:700;">
                @if($discount->type === 'percent')
                    {{ $discount->value }}٪ تخفیف
                @else
                    {{ number_format($discount->value) }} تومان تخفیف
                @endif
            </div>
        </div>
        <div>
            <div class="text-muted" style="font-size:.78rem;">استفاده / سقف</div>
            <div style="font-weight:700;">
                {{ $discount->used_count }} / {{ $discount->max_uses ?? '∞' }}
            </div>
        </div>
        <div>
            <div class="text-muted" style="font-size:.78rem;">وضعیت</div>
            @if($discount->is_active)
                <span class="badge badge-success">فعال</span>
            @else
                <span class="badge badge-danger">غیرفعال</span>
            @endif
        </div>
    </div>
</div>

{{-- ── Uses table ─── --}}
<div class="card">
    <div class="card-title">📋 لیست استفاده‌ها ({{ $uses->total() }} مورد)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام کاربر</th>
                    <th>ایمیل</th>
                    <th>مبلغ اشتراک</th>
                    <th>نوع پلن</th>
                    <th>تاریخ استفاده</th>
                </tr>
            </thead>
            <tbody>
                @forelse($uses as $u)
                <tr>
                    <td class="text-muted">{{ $u->id }}</td>
                    <td><strong>{{ $u->user?->name }}</strong></td>
                    <td class="text-muted" style="font-size:.82rem;">{{ $u->user?->email }}</td>
                    <td>{{ $u->subscription?->payment ? number_format($u->subscription->payment->amount) . ' تومان' : '—' }}</td>
                    <td>
                        @if($u->subscription)
                            {{ $u->subscription->plan === 'monthly' ? 'ماهانه' : 'سالانه' }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ JalaliHelper::toDate($u->used_at) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-muted);">هنوز استفاده‌ای ثبت نشده</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($uses->hasPages())
    <div style="margin-top:1rem;">{{ $uses->links() }}</div>
    @endif
</div>

@endsection
