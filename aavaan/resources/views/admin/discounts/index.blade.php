@extends('admin.layouts.app')
@section('title', 'کدهای تخفیف')
@section('page-title', 'کدهای تخفیف')
@section('topbar-actions')
<a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">+ کد جدید</a>
@endsection
@section('content')
<div class="card">
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>کد</th><th>نوع</th><th>مقدار</th><th>استفاده</th><th>انقضا</th><th>وضعیت</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($codes as $code)
            <tr>
                <td><strong style="direction:ltr;display:inline-block;">{{ $code->code }}</strong></td>
                <td>{{ $code->type === 'percent' ? 'درصدی' : 'مبلغ ثابت' }}</td>
                <td>{{ $code->type === 'percent' ? $code->value . '%' : number_format($code->value) . ' تومان' }}</td>
                <td>{{ $code->used_count }} / {{ $code->max_uses ?? '∞' }}</td>
                <td>{{ $code->valid_until ? $code->valid_until->format('Y/m/d') : '—' }}</td>
                <td>
                    @if($code->is_active && $code->isValid()) <span class="badge badge-success">فعال</span>
                    @elseif($code->is_active) <span class="badge badge-warning">منقضی/تمام</span>
                    @else <span class="badge badge-muted">غیرفعال</span>
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('admin.discounts.edit', $code->id) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                    <form method="POST" action="{{ route('admin.discounts.toggle', $code->id) }}" style="display:inline;">@csrf
                        <button class="btn btn-ghost btn-sm">{{ $code->is_active ? 'غیرفعال' : 'فعال' }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-muted);">کد تخفیفی ثبت نشده.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $codes->links() }}</div>
</div>
@endsection
