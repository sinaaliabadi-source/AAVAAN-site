@extends('admin.layouts.app')
@section('title', 'گزارش رشد کاربران')
@section('page-title', 'گزارش رشد کاربران')

@php use App\Helpers\JalaliHelper; @endphp

@push('styles')
<style>
.chart-container { position: relative; min-height: 240px; }
.chart-spinner { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--color-muted);opacity:.5; }
</style>
@endpush

@section('content')

{{-- ── Date filter ─── --}}
<div class="card">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;">
        <div class="form-group" style="margin:0;">
            <label>از تاریخ</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control" style="width:155px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label>تا تاریخ</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control" style="width:155px;">
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.reports.users') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>
</div>

{{-- ── Line chart ─── --}}
<div class="card">
    <div class="card-title">📈 ثبت‌نام روزانه در بازه انتخابی</div>
    <div class="chart-container">
        <div class="chart-spinner" id="usersSpinner">⏳</div>
        <canvas id="usersChart" style="display:none;"></canvas>
    </div>
</div>

{{-- ── Monthly table ─── --}}
<div class="card">
    <div class="card-title">📅 ثبت‌نام ماه‌به‌ماه (۱۲ ماه گذشته)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ماه</th>
                    <th>هنرمند جدید</th>
                    <th>تیم تولید جدید</th>
                    <th>جمع</th>
                    <th>رشد نسبت به ماه قبل</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyUsers as $row)
                <tr>
                    <td><strong>{{ JalaliHelper::toMonthYear($row['date']) }}</strong></td>
                    <td>{{ $row['artists'] ?: '—' }}</td>
                    <td>{{ $row['production'] ?: '—' }}</td>
                    <td><strong>{{ $row['total'] ?: '—' }}</strong></td>
                    <td>
                        @if($row['growth'] === null)
                            <span class="text-muted">—</span>
                        @elseif($row['growth'] >= 0)
                            <span style="color:var(--color-success);font-weight:600;">▲ {{ $row['growth'] }}٪</span>
                        @else
                            <span style="color:var(--color-danger);font-weight:600;">▼ {{ abs($row['growth']) }}٪</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── New users list ─── --}}
<div class="card">
    <div class="card-title">👤 کاربران جدید در بازه انتخابی ({{ $newUsers->total() }} مورد)</div>
    <div style="overflow-x:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>نقش</th>
                    <th>تاریخ ثبت‌نام</th>
                    <th>وضعیت اشتراک</th>
                </tr>
            </thead>
            <tbody>
                @forelse($newUsers as $u)
                @php
                    $roleMap = ['artist' => 'هنرمند', 'production' => 'تیم تولید', 'admin' => 'مدیر'];
                @endphp
                <tr>
                    <td class="text-muted">{{ $u->id }}</td>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td class="text-muted" style="font-size:.82rem;">{{ $u->email }}</td>
                    <td>
                        <span class="badge {{ $u->role==='artist'?'badge-info':'badge-warning' }}">
                            {{ $roleMap[$u->role] ?? $u->role }}
                        </span>
                    </td>
                    <td>{{ JalaliHelper::toDate($u->created_at) }}</td>
                    <td>
                        @if($u->role === 'artist')
                            @php $hasSub = $u->subscriptions()->where('status','active')->where('expires_at','>',now())->exists(); @endphp
                            @if($hasSub)
                                <span class="badge badge-success">فعال</span>
                            @else
                                <span class="badge badge-muted">ندارد</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-muted);">موردی یافت نشد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($newUsers->hasPages())
    <div style="margin-top:1rem;">{{ $newUsers->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const from = '{{ $from->toDateString() }}';
    const to   = '{{ $to->toDateString() }}';
    fetch('{{ route("admin.reports.chart-data") }}?type=users&from=' + from + '&to=' + to)
        .then(r => r.json())
        .then(data => {
            document.getElementById('usersSpinner').style.display = 'none';
            const canvas = document.getElementById('usersChart');
            canvas.style.display = 'block';
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'هنرمندان',
                            data: data.artists,
                            borderColor: '#C9A24B',
                            backgroundColor: 'rgba(201,162,75,.1)',
                            tension: 0.3, fill: true,
                            pointRadius: data.labels.length > 40 ? 0 : 3,
                        },
                        {
                            label: 'تیم‌های تولید',
                            data: data.production,
                            borderColor: '#1F2A44',
                            backgroundColor: 'rgba(31,42,68,.08)',
                            tension: 0.3, fill: true,
                            pointRadius: data.labels.length > 40 ? 0 : 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { ticks: { maxTicksLimit: 14, font: { family: 'IRANSansX, Tahoma, sans-serif' } } },
                        y: { ticks: { stepSize: 1 } }
                    }
                }
            });
        });
})();
</script>
@endpush
