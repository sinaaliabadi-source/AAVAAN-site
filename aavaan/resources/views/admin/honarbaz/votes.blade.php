@extends('admin.layouts.app')
@section('title', 'آرای هنرباز')
@section('page-title', '🎭 آرای هنرباز')

@section('content')
<div class="card">
    <form method="GET" style="display:flex;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem">
        <div class="form-group" style="margin:0">
            <label>وضعیت تأیید</label>
            <select name="verified" class="form-control" onchange="this.form.submit()">
                <option value="">همه</option>
                <option value="1" @selected(($filters['verified'] ?? '')==='1')>تأییدشده</option>
                <option value="0" @selected(($filters['verified'] ?? '')==='0')>در انتظار</option>
            </select>
        </div>
        <a href="{{ route('admin.honarbaz.votes') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>

    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>#</th><th>شرکت‌کننده</th><th>شماره رأی‌دهنده</th><th>IP</th><th>وضعیت</th><th>تاریخ</th>
            </tr></thead>
            <tbody>
            @forelse($votes as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->registration?->full_name ?? '—' }}</td>
                    <td style="direction:ltr;text-align:left">{{ $v->voter_phone ?? '—' }}</td>
                    <td style="direction:ltr;text-align:left">{{ $v->voter_ip }}</td>
                    <td>
                        @if($v->phone_verified)
                            <span class="badge badge-success">تأییدشده</span>
                        @else
                            <span class="badge badge-warning">در انتظار</span>
                        @endif
                    </td>
                    <td>{{ $v->created_at->format('Y/m/d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center">رأیی ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem">
        {{ $votes->links() }}
    </div>
</div>
@endsection
