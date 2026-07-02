@extends('admin.layouts.app')
@section('title', 'داشبورد هنرباز')
@section('page-title', '🎭 داشبورد هنرباز')

@section('content')

<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">📝</div>
        <div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <div class="stat-label">کل ثبت‌نام‌ها</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">⏳</div>
        <div>
            <div class="stat-value">{{ number_format($stats['pending']) }}</div>
            <div class="stat-label">در انتظار بررسی</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">✅</div>
        <div>
            <div class="stat-value">{{ number_format($stats['approved']) }}</div>
            <div class="stat-label">تأییدشده</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🗳️</div>
        <div>
            <div class="stat-value">{{ number_format($stats['votes_verified']) }}</div>
            <div class="stat-label">آرای تأییدشده</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:1.5rem;align-items:start">
    {{-- نمودار ثبت‌نام روزانه --}}
    <div class="card">
        <div class="card-title">ثبت‌نام روزانه (۱۴ روز اخیر)</div>
        <canvas id="dailyChart" height="140"></canvas>
    </div>

    {{-- توزیع استانی --}}
    <div class="card">
        <div class="card-title">توزیع استانی</div>
        <div style="overflow-x:auto">
            <table class="table">
                <thead><tr><th>استان</th><th>تعداد</th></tr></thead>
                <tbody>
                @forelse($provinceDist as $province => $count)
                    <tr><td>{{ $province }}</td><td>{{ number_format($count) }}</td></tr>
                @empty
                    <tr><td colspan="2">داده‌ای موجود نیست.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Top شرکت‌کنندگان --}}
<div class="card">
    <div class="card-title">۵ شرکت‌کننده برتر از نظر رأی</div>
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr><th>#</th><th>نام</th><th>استان</th><th>رشته</th><th>رأی</th></tr></thead>
            <tbody>
            @forelse($topContestants as $i => $c)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $c->full_name }}</td>
                    <td>{{ $c->province }}</td>
                    <td>{{ $c->talent_type }}</td>
                    <td><span class="badge badge-info">{{ number_format($c->votes_count) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5">هنوز شرکت‌کننده تأییدشده‌ای وجود ندارد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const hbDaily = @json($dailyChart);
    const ctx = document.getElementById('dailyChart');
    if (ctx && window.Chart) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: hbDaily.map(d => d.date.slice(5)),
                datasets: [{
                    label: 'ثبت‌نام',
                    data: hbDaily.map(d => d.count),
                    borderColor: '#C9A24B',
                    backgroundColor: 'rgba(201,162,75,.15)',
                    fill: true,
                    tension: .3,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
</script>
@endpush
