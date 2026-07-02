@extends('admin.layouts.app')

@section('title', 'نتایج جستجو')
@section('page-title', 'جستجوی سراسری')

@section('content')

<div class="card">
    <form method="GET" action="{{ route('admin.search') }}" style="display:flex; gap:.75rem; margin-bottom:1.5rem;">
        <input
            type="search"
            name="q"
            class="form-control"
            placeholder="نام، ایمیل، موبایل، شناسه تراکنش..."
            value="{{ $query }}"
            autofocus
            style="flex:1"
        >
        <button type="submit" class="btn btn-primary">🔍 جستجو</button>
    </form>

    @if($query)
        @if(empty($results))
            <div class="placeholder-section">
                <div class="ph-icon">🔎</div>
                <h3>نتیجه‌ای یافت نشد</h3>
                <p>عبارت «{{ $query }}» در هیچ بخشی پیدا نشد.</p>
            </div>
        @else
            @foreach($results as $section => $items)
                <div style="margin-bottom:1.75rem;">
                    <h4 style="
                        font-family:'YekanBakh',sans-serif;
                        font-size:.85rem;
                        color:var(--color-muted);
                        text-transform:uppercase;
                        letter-spacing:.04em;
                        margin-bottom:.75rem;
                        padding-bottom:.5rem;
                        border-bottom:1px solid #f0ede8;
                    ">
                        @if($section === 'users') 👤 کاربران
                        @elseif($section === 'payments') 💳 پرداخت‌ها
                        @else {{ $section }}
                        @endif
                        <span class="badge badge-muted" style="margin-right:.5rem; font-size:.7rem;">
                            {{ count($items) }} نتیجه
                        </span>
                    </h4>

                    <table class="table">
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>
                                    <a href="{{ $item['url'] }}" style="color:var(--color-primary); font-weight:600;">
                                        {{ $item['label'] }}
                                    </a>
                                </td>
                                <td style="text-align:left; width:120px;">
                                    <span class="badge badge-muted">{{ $item['meta'] }}</span>
                                </td>
                                <td style="width:80px; text-align:left;">
                                    <a href="{{ $item['url'] }}" class="btn btn-ghost btn-sm">مشاهده</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    @else
        <div class="placeholder-section">
            <div class="ph-icon">🔍</div>
            <h3>جستجوی سراسری</h3>
            <p>نام، ایمیل، موبایل کاربر یا شناسه تراکنش را وارد کنید.</p>
        </div>
    @endif
</div>

@endsection
