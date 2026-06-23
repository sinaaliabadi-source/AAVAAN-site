@extends('admin.layouts.app')
@section('title', 'داشبورد گزارش‌ها')
@section('page-title', 'داشبورد گزارش‌ها')

@php use App\Helpers\JalaliHelper; @endphp

@push('styles')
<style>
.report-filter-bar {
    background: #fff;
    border: 1px solid #ede8dc;
    border-radius: var(--radius);
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    align-items: flex-end;
}
.shortcut-btns { display: flex; gap: .4rem; flex-wrap: wrap; }
.shortcut-btn {
    padding: .3rem .7rem;
    border-radius: 6px;
    border: 1.5px solid #d5cfc4;
    background: #faf7f2;
    cursor: pointer;
    font-family: inherit;
    font-size: .8rem;
    color: var(--color-text);
    transition: all .15s;
}
.shortcut-btn:hover { border-color: var(--color-accent); color: var(--color-accent); }
.change-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .75rem;
    font-weight: 600;
    padding: .15rem .5rem;
    border-radius: 20px;
    margin-top: .3rem;
}
.change-up   { background: #e5f0e5; color: #2d5a2d; }
.change-down { background: #fde8e8; color: #b91c1c; }
.change-neutral { background: #f0ede6; color: var(--color-muted); }
.chart-container { position: relative; min-height: 260px; }
.chart-spinner {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--color-muted);
    opacity: .5;
}
</style>
@endpush

@section('content')

{{-- ── Date filter ─── --}}
<div class="report-filter-bar">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;" id="filterForm">
        <div class="form-group" style="margin:0;">
            <label>از تاریخ</label>
            <input type="date" name="from" id="inp_from" value="{{ $from->toDateString() }}" class="form-control" style="width:160px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>تا تاریخ</label>
            <input type="date" name="to" id="inp_to" value="{{ $to->toDateString() }}" class="form-control" style="width:160px;">
        </div>
        <button type="submit" class="btn btn-primary">اعمال فیلتر</button>
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <span style="font-size:.75rem;color:var(--color-muted);">میانبر</span>
            <div class="shortcut-btns">
                <button type="button" class="shortcut-btn" data-range="today">امروز</button>
                <button type="button" class="shortcut-btn" data-range="week">این هفته</button>
                <button type="button" class="shortcut-btn" data-range="month">این ماه</button>
                <button type="button" class="shortcut-btn" data-range="last_month">ماه گذشته</button>
                <button type="button" class="shortcut-btn" data-range="year">این سال</button>
            </div>
        </div>
    </form>
</div>

{{-- ── Summary cards ─── --}}
@php
    function pctBadge($current, $prev) {
        if ($prev == 0) return '<span class="change-badge change-neutral">—</span>';
        $pct = round(($current - $prev) / $prev * 100, 1);
        $cls = $pct >= 0 ? 'change-up' : 'change-down';
        $icon = $pct >= 0 ? '▲' : '▼';
        return "<span class=\"change-badge {$cls}\">{$icon} {$pct}٪</span>";
    }
@endphp

<div class="grid-4" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent" style="font-size:1.4rem;">💰</div>
        <div>
            <div class="stat-value">{{ number_format($totalRevenue) }}</div>
            <div class="stat-label">مجموع درآمد (تومان)</div>
            {!! pctBadge($totalRevenue, $prevTotalRevenue) !!}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary" style="font-size:1.4rem;">⭐</div>
        <div>
            <div class="stat-value">{{ number_format($subRevenue) }}</div>
            <div class="stat-label">اشتراک هنرمندان (تومان)</div>
            {!! pctBadge($subRevenue, $prevSubRevenue) !!}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info" style="font-size:1.4rem;">🎬</div>
        <div>
            <div class="stat-value">{{ number_format($prodRevenue) }}</div>
            <div class="stat-label">دسترسی تولید (تومان)</div>
            {!! pctBadge($prodRevenue, $prevProdRevenue) !!}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success" style="font-size:1.4rem;">👤</div>
        <div>
            <div class="stat-value">{{ number_format($newUsers) }}</div>
            <div class="stat-label">کاربر جدید</div>
            {!! pctBadge($newUsers, $prevNewUsers) !!}
        </div>
    </div>
</div>

{{-- ── Charts ─── --}}
<div class="grid-2" style="margin-bottom:1.5rem;">
    <div class="card" style="margin:0;">
        <div class="card-title">📊 درآمد روزانه / هفتگی</div>
        <div class="chart-container">
            <div class="chart-spinner" id="revSpinner">⏳</div>
            <canvas id="revenueChart" style="display:none;"></canvas>
        </div>
    </div>
    <div class="card" style="margin:0;">
        <div class="card-title">🍩 ترکیب درآمد</div>
        <div class="chart-container" style="min-height:220px;display:flex;align-items:center;justify-content:center;">
            <div class="chart-spinner" id="doughnutSpinner">⏳</div>
            <canvas id="doughnutChart" style="max-width:240px;max-height:240px;display:none;"></canvas>
        </div>
    </div>
</div>

{{-- ── Monthly revenue table ─── --}}
<div class="card">
    <div class="card-title">📅 درآمد ۱۲ ماه گذشته</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ماه</th>
                    <th>درآمد اشتراک</th>
                    <th>درآمد دسترسی</th>
                    <th>جمع</th>
                    <th>تعداد اشتراک</th>
                    <th>تعداد دسترسی</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRevenue as $row)
                <tr>
                    <td><strong>{{ JalaliHelper::toMonthYear($row['date']) }}</strong></td>
                    <td>{{ $row['sub_revenue'] > 0 ? number_format($row['sub_revenue']) . ' تومان' : '—' }}</td>
                    <td>{{ $row['prod_revenue'] > 0 ? number_format($row['prod_revenue']) . ' تومان' : '—' }}</td>
                    <td><strong>{{ $row['total'] > 0 ? number_format($row['total']) . ' تومان' : '—' }}</strong></td>
                    <td>{{ $row['sub_count'] ?: '—' }}</td>
                    <td>{{ $row['prod_count'] ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const from = '{{ $from->toDateString() }}';
    const to   = '{{ $to->toDateString() }}';
    const base = '{{ route("admin.reports.chart-data") }}';

    function fetchChart(type) {
        return fetch(base + '?type=' + type + '&from=' + from + '&to=' + to)
            .then(r => r.json());
    }

    // Revenue line chart
    fetchChart('revenue').then(data => {
        document.getElementById('revSpinner').style.display = 'none';
        const canvas = document.getElementById('revenueChart');
        canvas.style.display = 'block';
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'اشتراک هنرمندان',
                        data: data.subscription,
                        borderColor: '#C9A24B',
                        backgroundColor: 'rgba(201,162,75,.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: data.labels.length > 40 ? 0 : 3,
                    },
                    {
                        label: 'دسترسی تولید',
                        data: data.production,
                        borderColor: '#1F2A44',
                        backgroundColor: 'rgba(31,42,68,.08)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: data.labels.length > 40 ? 0 : 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { ticks: { maxTicksLimit: 12, font: { family: 'IRANSansX, Tahoma, sans-serif' } } },
                    y: { ticks: { callback: v => v.toLocaleString('fa') + ' ت' } }
                }
            }
        });
    });

    // Doughnut chart
    fetchChart('doughnut').then(data => {
        document.getElementById('doughnutSpinner').style.display = 'none';
        const canvas = document.getElementById('doughnutChart');
        canvas.style.display = 'block';
        const total = data.subscription + data.production;
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['اشتراک هنرمندان', 'دسترسی تولید'],
                datasets: [{
                    data: [data.subscription, data.production],
                    backgroundColor: ['#C9A24B', '#1F2A44'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const v = ctx.raw;
                                const pct = total > 0 ? Math.round(v / total * 100) : 0;
                                return ' ' + v.toLocaleString('fa') + ' تومان (' + pct + '٪)';
                            }
                        }
                    }
                }
            }
        });
    });

    // Shortcut buttons
    document.querySelectorAll('.shortcut-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const today = new Date();
            let from, to;
            const fmt = d => d.toISOString().split('T')[0];
            switch (btn.dataset.range) {
                case 'today':
                    from = to = fmt(today); break;
                case 'week': {
                    const d = new Date(today);
                    d.setDate(d.getDate() - d.getDay());
                    from = fmt(d); to = fmt(today); break;
                }
                case 'month':
                    from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
                    to   = fmt(today); break;
                case 'last_month': {
                    const f = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const t = new Date(today.getFullYear(), today.getMonth(), 0);
                    from = fmt(f); to = fmt(t); break;
                }
                case 'year':
                    from = fmt(new Date(today.getFullYear(), 0, 1));
                    to   = fmt(today); break;
            }
            document.getElementById('inp_from').value = from;
            document.getElementById('inp_to').value   = to;
            document.getElementById('filterForm').submit();
        });
    });
})();
</script>
@endpush
