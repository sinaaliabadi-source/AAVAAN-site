@extends('admin.layouts.app')

@section('title', 'نمای کلی')
@section('page-title', 'نمای کلی')

@section('content')

{{-- ── در انتظار اقدام ── --}}
<div class="grid-2" style="margin-bottom:1.5rem">
    @php
        $pp = $stats['pending_production'];
        $pv = $stats['pending_verifications'];
    @endphp
    <a href="{{ route('admin.production.index', ['approval_status' => 'pending']) }}"
       class="stat-card" style="text-decoration:none;{{ $pp > 0 ? 'border:1.5px solid #C9A24B;background:#fdfaf1;' : '' }}">
        <div class="stat-icon" style="background:{{ $pp > 0 ? '#faf6ec' : '#f0ede6' }};color:{{ $pp > 0 ? '#C9A24B' : '#bbb' }}">🎬</div>
        <div>
            <div class="stat-value">{{ number_format($pp) }}</div>
            <div class="stat-label">تیم تولید در انتظار تأیید</div>
        </div>
    </a>
    <a href="{{ route('admin.verifications.index', ['status' => 'pending', 'type' => 'specialty']) }}"
       class="stat-card" style="text-decoration:none;{{ $pv > 0 ? 'border:1.5px solid #C9A24B;background:#fdfaf1;' : '' }}">
        <div class="stat-icon" style="background:{{ $pv > 0 ? '#faf6ec' : '#f0ede6' }};color:{{ $pv > 0 ? '#C9A24B' : '#bbb' }}">✔</div>
        <div>
            <div class="stat-value">{{ number_format($pv) }}</div>
            <div class="stat-label">درخواست تأیید تخصص</div>
        </div>
    </a>
</div>

{{-- ── کاربران ── --}}
<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">👥</div>
        <div><div class="stat-value">{{ number_format($stats['total_users']) }}</div><div class="stat-label">کل کاربران</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">🎭</div>
        <div><div class="stat-value">{{ number_format($stats['total_artists']) }}</div><div class="stat-label">هنرمندان</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🎬</div>
        <div><div class="stat-value">{{ number_format($stats['total_production']) }}</div><div class="stat-label">تیم‌های تولید</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">🆕</div>
        <div><div class="stat-value">{{ number_format($stats['new_users_today']) }}</div><div class="stat-label">کاربران جدید امروز</div></div>
    </div>
</div>

{{-- ── مالی ── --}}
<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">💰</div>
        <div><div class="stat-value" style="font-size:1.15rem">{{ number_format($stats['total_revenue']) }}</div><div class="stat-label">درآمد کل (تومان)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">📅</div>
        <div><div class="stat-value" style="font-size:1.15rem">{{ number_format($stats['revenue_this_month']) }}</div><div class="stat-label">درآمد ماه جاری</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">📈</div>
        <div><div class="stat-value" style="font-size:1.15rem">{{ number_format($stats['revenue_30d']) }}</div><div class="stat-label">درآمد ۳۰ روز اخیر</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">🧾</div>
        <div><div class="stat-value">{{ number_format($stats['paid_count_month']) }}</div><div class="stat-label">پرداخت موفق این ماه</div></div>
    </div>
</div>

{{-- ── اشتراک و اعتبار ── --}}
<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">⭐</div>
        <div><div class="stat-value">{{ number_format($stats['active_subscriptions']) }}</div><div class="stat-label">اشتراک فعال</div></div>
    </div>
    <div class="stat-card" style="{{ $stats['expiring_soon'] > 0 ? 'border:1.5px solid #e6d09a;background:#fdfaf1;' : '' }}">
        <div class="stat-icon" style="background:#faf6ec;color:#C9A24B">⏰</div>
        <div><div class="stat-value">{{ number_format($stats['expiring_soon']) }}</div><div class="stat-label">منقضی‌شونده در ۷ روز</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🔁</div>
        <div><div class="stat-value">{{ $stats['renewal_rate'] }}٪</div><div class="stat-label">نرخ تمدید (ماه)</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">🎟️</div>
        <div><div class="stat-value">{{ number_format($stats['credits_used']) }}/{{ number_format($stats['credits_sold']) }}</div><div class="stat-label">اعتبار مصرف‌شده/فروخته</div></div>
    </div>
</div>

{{-- ── محتوا ── --}}
<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🎯</div>
        <div><div class="stat-value">{{ number_format($stats['total_specialties']) }}</div><div class="stat-label">کل تخصص‌ها</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">✔</div>
        <div><div class="stat-value">{{ number_format($stats['verified_specialties']) }}</div><div class="stat-label">تخصص تأییدشده</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">🟢</div>
        <div><div class="stat-value">{{ number_format($stats['active_profiles']) }}</div><div class="stat-label">پروفایل فعال</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0ede6;color:#bbb">⚪</div>
        <div><div class="stat-value">{{ number_format($stats['inactive_profiles']) }}</div><div class="stat-label">پروفایل غیرفعال</div></div>
    </div>
</div>

{{-- ── صف اقدامات + آخرین اقدامات ── --}}
<div class="grid-2" style="margin-bottom:1.5rem">
    <div class="card">
        <div class="card-title">📌 صف اقدامات من</div>
        @if(empty($actionQueue))
            <p class="text-muted text-sm" style="text-align:center;padding:1.5rem 0;">همه‌چیز بررسی شده ✔</p>
        @else
            @foreach($actionQueue as $item)
            <a href="{{ $item['url'] }}" style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;padding:.55rem 0;border-bottom:1px solid #f0ede8;text-decoration:none;">
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <span style="font-size:1rem;">{{ $item['type'] === 'production' ? '🎬' : '✔' }}</span>
                    <div>
                        <div style="font-weight:600;color:var(--color-primary);font-size:.85rem;">{{ $item['label'] }}</div>
                        <div class="text-muted" style="font-size:.76rem;">{{ $item['sub'] }}</div>
                    </div>
                </div>
                <span class="text-muted" style="font-size:.74rem;white-space:nowrap;">{{ $item['at']->diffForHumans() }}</span>
            </a>
            @endforeach
        @endif
    </div>

    <div class="card">
        <div class="card-title">📋 آخرین اقدامات ادمین</div>
        @if($recentLogs->isEmpty())
            <p class="text-muted text-sm" style="text-align:center;padding:1.5rem 0;">هنوز اقدامی ثبت نشده است.</p>
        @else
            @foreach($recentLogs as $log)
            <div style="padding:.5rem 0;border-bottom:1px solid #f0ede8;display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem;">
                <div>
                    <div style="font-weight:600;color:var(--color-primary);font-size:.85rem;">{{ $log->description ?: $log->action }}</div>
                    <div class="text-muted" style="font-size:.78rem;">{{ $log->admin?->name }}</div>
                </div>
                <div class="text-muted" style="font-size:.75rem;white-space:nowrap;flex-shrink:0;">{{ $log->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
            <div style="margin-top:1rem;text-align:left;">
                <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-sm">مشاهده همه</a>
            </div>
        @endif
    </div>
</div>

{{-- ── نمودارها (Chart.js از endpoint JSON) ── --}}
<div class="grid-2" style="margin-bottom:1.5rem">
    <div class="card">
        <div class="card-title">📈 ثبت‌نام کاربران (۳۰ روز)</div>
        <canvas id="usersChart" height="140"></canvas>
        <p id="usersChartEmpty" class="text-muted text-sm" style="display:none;text-align:center;padding:1rem;">داده‌ای برای نمایش نیست.</p>
    </div>
    <div class="card">
        <div class="card-title">💰 درآمد روزانه (۳۰ روز)</div>
        <canvas id="revenueChart" height="140"></canvas>
        <p id="revenueChartEmpty" class="text-muted text-sm" style="display:none;text-align:center;padding:1rem;">داده‌ای برای نمایش نیست.</p>
    </div>
</div>

<div class="card">
    <div class="card-title">⚡ دسترسی سریع</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.65rem;">
        <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline btn-sm">✔ تأیید تخصص</a>
        <a href="{{ route('admin.production.index') }}" class="btn btn-outline btn-sm">🎬 تیم‌های تولید</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline btn-sm">📊 گزارش‌ها</a>
        <a href="{{ route('admin.settings') }}" class="btn btn-outline btn-sm">⚙️ تنظیمات</a>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost btn-sm">🌐 سایت</a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const to   = new Date();
        const from = new Date(); from.setDate(to.getDate() - 29);
        const fmt  = d => d.toISOString().slice(0, 10);
        const base = '{{ route('admin.reports.chart-data') }}';
        const qs   = 'from=' + fmt(from) + '&to=' + fmt(to);

        function lineChart(canvasId, emptyId, datasets, labels) {
            const total = datasets.reduce((s, ds) => s + ds.data.reduce((a, b) => a + (+b || 0), 0), 0);
            if (!labels.length || total === 0) {
                document.getElementById(canvasId).style.display = 'none';
                document.getElementById(emptyId).style.display = 'block';
                return;
            }
            new Chart(document.getElementById(canvasId), {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    plugins: { legend: { labels: { font: { family: 'Tahoma' } } } },
                    scales: { x: { ticks: { maxTicksLimit: 8 } } },
                    interaction: { intersect: false, mode: 'index' },
                    elements: { line: { tension: .3 }, point: { radius: 0 } }
                }
            });
        }

        fetch(base + '?type=users&' + qs, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => lineChart('usersChart', 'usersChartEmpty', [
                { label: 'هنرمند', data: d.artists || [], borderColor: '#C9A24B', backgroundColor: 'rgba(201,162,75,.12)', fill: true },
                { label: 'تیم تولید', data: d.production || [], borderColor: '#1F2A44', backgroundColor: 'rgba(31,42,68,.10)', fill: true }
            ], d.labels || []))
            .catch(() => {});

        fetch(base + '?type=revenue&' + qs, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => lineChart('revenueChart', 'revenueChartEmpty', [
                { label: 'اشتراک', data: d.subscription || [], borderColor: '#5C6F4F', backgroundColor: 'rgba(92,111,79,.12)', fill: true },
                { label: 'دسترسی تولید', data: d.production || [], borderColor: '#C9A24B', backgroundColor: 'rgba(201,162,75,.12)', fill: true }
            ], d.labels || []))
            .catch(() => {});
    })();
</script>
@endpush
