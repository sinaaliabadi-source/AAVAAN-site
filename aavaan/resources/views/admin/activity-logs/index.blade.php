@extends('admin.layouts.app')

@section('title', 'لاگ فعالیت ادمین')
@section('page-title', 'لاگ فعالیت ادمین')

@section('content')

<div class="card">
    @if($logs->isEmpty())
        <div class="placeholder-section">
            <div class="ph-icon">📋</div>
            <h3>هنوز اقدامی ثبت نشده</h3>
            <p>اقدامات مدیران در این صفحه نمایش داده می‌شود.</p>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>مدیر</th>
                    <th>اقدام</th>
                    <th>توضیح</th>
                    <th>موضوع</th>
                    <th>IP</th>
                    <th>زمان</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="text-muted" style="font-size:.8rem;">{{ $log->id }}</td>
                    <td>
                        <span style="font-weight:600;">{{ $log->admin?->name ?? '—' }}</span>
                    </td>
                    <td>
                        <span class="badge badge-info" style="font-family:monospace; font-size:.75rem;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="max-width:280px; color:var(--color-text);">
                        {{ $log->description ?: '—' }}
                    </td>
                    <td class="text-muted text-sm">
                        @if($log->subject_type && $log->subject_id)
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:.8rem; direction:ltr; text-align:left;">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                    <td class="text-muted text-sm" style="white-space:nowrap;" title="{{ $log->created_at }}">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:1.25rem; display:flex; justify-content:center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection
