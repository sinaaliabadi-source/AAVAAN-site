@extends('admin.layouts.app')
@section('title', 'گزارش کدهای تخفیف')
@section('page-title', 'گزارش کدهای تخفیف')

@php use App\Helpers\JalaliHelper; @endphp

@section('content')

<div class="card">
    <div class="card-title">🎫 لیست کدهای تخفیف ({{ $discounts->total() }} مورد)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>کد</th>
                    <th>نوع</th>
                    <th>مقدار</th>
                    <th>استفاده / سقف</th>
                    <th>اعتبار تا</th>
                    <th>وضعیت</th>
                    <th>آخرین استفاده</th>
                    <th>جزئیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discounts as $d)
                @php
                    $maxStr = $d->max_uses !== null ? $d->max_uses : '∞';
                    $usedCount = $totals->get($d->id, 0);
                    $activeBadge = $d->is_active
                        ? '<span class="badge badge-success">فعال</span>'
                        : '<span class="badge badge-danger">غیرفعال</span>';
                @endphp
                <tr>
                    <td class="text-muted">{{ $d->id }}</td>
                    <td>
                        <code style="background:#f0ede6;padding:.15rem .5rem;border-radius:4px;font-size:.88rem;">{{ $d->code }}</code>
                    </td>
                    <td>{{ $d->type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}</td>
                    <td>
                        @if($d->type === 'percent')
                            {{ $d->value }}٪
                        @else
                            {{ number_format($d->value) }} تومان
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:600;">{{ $usedCount }}</span>
                        <span class="text-muted"> / {{ $maxStr }}</span>
                    </td>
                    <td>{{ $d->valid_until ? JalaliHelper::toDate($d->valid_until) : '—' }}</td>
                    <td>{!! $activeBadge !!}</td>
                    <td>
                        @php
                            $lastUse = $d->uses()->latest('used_at')->first();
                        @endphp
                        {{ $lastUse ? JalaliHelper::toDate($lastUse->used_at) : '—' }}
                    </td>
                    <td>
                        <a href="{{ route('admin.reports.discounts.show', $d->id) }}" class="btn btn-ghost btn-sm">جزئیات</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--color-muted);">موردی یافت نشد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($discounts->hasPages())
    <div style="margin-top:1rem;">{{ $discounts->links() }}</div>
    @endif
</div>

@endsection
