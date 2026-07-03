@extends('admin.layouts.app')
@section('title', 'داشبورد پشتیبانی')
@section('page-title', 'داشبورد پشتیبانی')
@section('topbar-actions')
<a href="{{ route('admin.support.tickets') }}" class="btn btn-primary btn-sm">همه تیکت‌ها</a>
@endsection

@push('styles')
<style>
    .st { color:#fff; font-weight:600; font-size:.74rem; padding:.15rem .6rem; border-radius:99px; white-space:nowrap; }
    .st-open { background:#c0392b; } .st-in_progress { background:#e08600; }
    .st-waiting_user { background:#2980b9; } .st-resolved { background:#27852f; } .st-closed { background:#6b7280; }
</style>
@endpush

@section('content')

<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">🔴</div>
        <div><div class="stat-value">{{ number_format($stats['open']) }}</div><div class="stat-label">باز</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">🟠</div>
        <div><div class="stat-value">{{ number_format($stats['in_progress']) }}</div><div class="stat-label">در حال بررسی</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🔵</div>
        <div><div class="stat-value">{{ number_format($stats['waiting_user']) }}</div><div class="stat-label">در انتظار کاربر</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">✅</div>
        <div><div class="stat-value">{{ number_format($stats['resolved_today']) }}</div><div class="stat-label">حل‌شده امروز</div></div>
    </div>
</div>

<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fdecec">🚨</div>
        <div><div class="stat-value">{{ number_format($stats['urgent']) }}</div><div class="stat-label">فوری (باز)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">📥</div>
        <div><div class="stat-value">{{ number_format($stats['today']) }}</div><div class="stat-label">تیکت‌های امروز</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">⏱</div>
        <div><div class="stat-value">{{ $stats['avg_first_response_hours'] ?? '—' }}</div><div class="stat-label">میانگین اولین پاسخ (ساعت)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">🎫</div>
        <div><div class="stat-value">{{ number_format(($stats['open'] + $stats['in_progress'] + $stats['waiting_user'])) }}</div><div class="stat-label">در جریان</div></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:1.25rem;align-items:start">

    {{-- نمودار هفتگی --}}
    <div class="card">
        <div class="card-title">📈 تیکت‌های ۷ روز اخیر</div>
        <canvas id="weeklyChart" height="120"></canvas>
    </div>

    {{-- تیکت‌های فوری --}}
    <div class="card">
        <div class="card-title">🚨 تیکت‌های فوری</div>
        @forelse($urgentTickets as $t)
        <a href="{{ route('admin.support.show', $t->ticket_number) }}" style="display:flex;justify-content:space-between;gap:.5rem;padding:.55rem 0;border-bottom:1px solid #f0ede8;text-decoration:none;color:inherit">
            <span style="font-size:.85rem">{{ \Illuminate\Support\Str::limit($t->subject, 30) }}</span>
            <span class="st st-{{ $t->status }}">{{ $t->status_label }}</span>
        </a>
        @empty
        <p style="color:var(--color-muted);font-size:.85rem;padding:1rem 0;text-align:center">تیکت فوریِ بازی وجود ندارد.</p>
        @endforelse
    </div>
</div>

{{-- تیکت‌های اخیر --}}
<div class="card" style="margin-top:1.25rem">
    <div class="card-title">🕑 تیکت‌های اخیر</div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>شماره</th><th>موضوع</th><th>دپارتمان</th><th>درخواست‌کننده</th><th>مسئول</th><th>وضعیت</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($recentTickets as $t)
            <tr>
                <td style="direction:ltr">{{ $t->ticket_number }}</td>
                <td>{{ \Illuminate\Support\Str::limit($t->subject, 32) }}</td>
                <td>{{ $t->department_label }}</td>
                <td>{{ $t->requester_name }}</td>
                <td>{{ $t->assignedAdmin?->name ?? '—' }}</td>
                <td><span class="st st-{{ $t->status }}">{{ $t->status_label }}</span></td>
                <td><a href="{{ route('admin.support.show', $t->ticket_number) }}" class="btn btn-ghost btn-sm">مشاهده</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-muted)">تیکتی ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const weekly = @json($weekly);
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weekly.map(d => d.label),
            datasets: [{
                label: 'تیکت‌ها',
                data: weekly.map(d => d.count),
                backgroundColor: '#C9A24B',
                borderRadius: 6,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>
@endpush
