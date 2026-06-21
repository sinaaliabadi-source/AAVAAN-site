@extends('admin.layouts.app')

@section('title', 'نمای کلی')
@section('page-title', 'نمای کلی')

@section('content')

<div class="grid-4" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">👥</div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">کل کاربران</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">🎭</div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_artists']) }}</div>
            <div class="stat-label">هنرمندان</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">🎬</div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_production']) }}</div>
            <div class="stat-label">تیم‌های تولید</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">⭐</div>
        <div>
            <div class="stat-value">{{ number_format($stats['active_subscriptions']) }}</div>
            <div class="stat-label">اشتراک فعال</div>
        </div>
    </div>
</div>

<div class="grid-3" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon stat-icon-accent">💰</div>
        <div>
            <div class="stat-value" style="font-size:1.3rem">
                {{ number_format($stats['revenue_this_month']) }}
            </div>
            <div class="stat-label">درآمد ماه جاری (ریال)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">🆕</div>
        <div>
            <div class="stat-value">{{ number_format($stats['new_users_today']) }}</div>
            <div class="stat-label">کاربران جدید امروز</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">📅</div>
        <div>
            <div class="stat-value">{{ number_format($stats['new_users_this_week']) }}</div>
            <div class="stat-label">کاربران جدید این هفته</div>
        </div>
    </div>
</div>

{{-- Placeholder cards for future phases --}}
<div class="card" style="border: 1.5px dashed #d5cfc4; background: #faf7f2;">
    <div class="card-title" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
        <span>⏳</span> آمارهای در دسترس در فازهای آینده
    </div>
    <div class="grid-3" style="margin-top:1.2rem; gap:.85rem;">
        @foreach([
            ['label' => 'پروژه‌های فعال', 'phase' => 'فاز ۸'],
            ['label' => 'درخواست‌های همکاری', 'phase' => 'فاز ۸'],
            ['label' => 'استخدام‌های موفق', 'phase' => 'فاز ۸'],
        ] as $item)
        <div style="
            background:#fff;
            border:1px solid #e5e0d4;
            border-radius:8px;
            padding:1rem 1.2rem;
            display:flex; align-items:center; gap:.75rem;
            opacity:.6;
        ">
            <div style="
                width:38px; height:38px; border-radius:8px;
                background:#f0ede6;
                display:flex; align-items:center; justify-content:center;
                font-size:.85rem; color:#aaa; flex-shrink:0;
            ">—</div>
            <div>
                <div style="font-size:.82rem; color:var(--color-muted);">{{ $item['label'] }}</div>
                <div style="font-size:.72rem; color:#bbb; margin-top:.1rem;">{{ $item['phase'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-title">📋 آخرین اقدامات ادمین</div>
        @php
            $recentLogs = \App\Models\AdminActivityLog::with('admin')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        @endphp
        @if($recentLogs->isEmpty())
            <p class="text-muted text-sm" style="text-align:center; padding:1.5rem 0;">
                هنوز اقدامی ثبت نشده است.
            </p>
        @else
            <div class="table" style="font-size:.84rem;">
                @foreach($recentLogs as $log)
                <div style="
                    padding:.5rem 0;
                    border-bottom:1px solid #f0ede8;
                    display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem;
                ">
                    <div>
                        <div style="font-weight:600; color:var(--color-primary);">{{ $log->description ?: $log->action }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $log->admin?->name }}</div>
                    </div>
                    <div class="text-muted" style="font-size:.75rem; white-space:nowrap; flex-shrink:0;">
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top:1rem; text-align:left;">
                <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-sm">مشاهده همه</a>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-title">⚡ دسترسی سریع</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.65rem;">
            <a href="{{ route('admin.settings') }}" class="btn btn-outline btn-sm">⚙️ تنظیمات</a>
            <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline btn-sm">📋 لاگ‌ها</a>
            <a href="{{ route('admin.search') }}" class="btn btn-outline btn-sm">🔍 جستجو</a>
            <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost btn-sm">🌐 سایت</a>
        </div>
    </div>
</div>

@endsection
