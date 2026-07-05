@extends('admin.layouts.app')
@section('title', 'تأیید تخصص')
@section('page-title', 'تأیید تخصص')
@section('content')
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem;">
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="pending" {{ request('status','pending')=='pending'?'selected':'' }}>در انتظار</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>تأییدشده</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>ردشده</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>نوع</label>
            <select name="type" class="form-control">
                <option value="">همه</option>
                <option value="specialty" {{ request('type')=='specialty'?'selected':'' }}>تخصص</option>
                <option value="phone" {{ request('type')=='phone'?'selected':'' }}>تلفن</option>
                <option value="resume" {{ request('type')=='resume'?'selected':'' }}>رزومه</option>
                <option value="professional" {{ request('type')=='professional'?'selected':'' }}>حرفه‌ای</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
    </form>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>#</th><th>هنرمند</th><th>نوع</th><th>تخصص</th><th>وضعیت</th><th>یادداشت</th><th>تاریخ درخواست</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($verifications as $v)
            <tr>
                <td>{{ $v->id }}</td>
                <td>{{ $v->user?->name }}</td>
                <td>
                    @php $types=['phone'=>'تلفن','resume'=>'رزومه','professional'=>'حرفه‌ای','specialty'=>'تخصص']; @endphp
                    {{ $types[$v->type] ?? $v->type }}
                </td>
                <td>{{ $v->artistSpecialty?->category?->name_fa ?? '—' }}</td>
                <td>
                    @if($v->status==='approved') <span class="badge badge-success">تأییدشده</span>
                    @elseif($v->status==='rejected') <span class="badge badge-danger">ردشده</span>
                    @else <span class="badge badge-warning">در انتظار</span>
                    @endif
                </td>
                <td>{{ $v->notes ? \Str::limit($v->notes, 50) : '—' }}</td>
                <td>{{ $v->created_at->format('Y/m/d') }}</td>
                <td>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;">
                        <a href="{{ route('admin.verifications.show', $v->id) }}" class="btn btn-primary btn-sm">بررسی</a>
                        @if($v->status === 'pending')
                        <form method="POST" action="{{ route('admin.verifications.approve', $v->id) }}" style="display:inline;">@csrf
                            <button class="btn btn-outline btn-sm" style="color:var(--color-success);border-color:var(--color-success);">تأیید سریع</button>
                        </form>
                        @else
                        <span class="text-muted text-sm">{{ $v->reviewed_at?->format('Y/m/d') }}</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-muted);">موردی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $verifications->links() }}</div>
</div>
@endsection
