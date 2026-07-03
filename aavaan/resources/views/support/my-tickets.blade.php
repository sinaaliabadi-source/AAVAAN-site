@extends('layouts.app')
@section('title', 'تیکت‌های من')

@push('styles')
<style>
    .mt-wrap { max-width:900px; margin:0 auto; padding:2.5rem 1.5rem; }
    .mt-wrap h1 { font-size:1.6rem; color:var(--color-primary); margin-bottom:1.25rem; }
    .mt-table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #ece6da; border-radius:var(--radius); overflow:hidden; }
    .mt-table th { text-align:right; font-size:.82rem; color:var(--color-primary); padding:.7rem .9rem; border-bottom:2px solid #ede8dc; background:#faf7f2; }
    .mt-table td { padding:.7rem .9rem; font-size:.88rem; border-bottom:1px solid #f0ede8; }
    .st { color:#fff; font-weight:600; font-size:.75rem; padding:.15rem .6rem; border-radius:99px; }
    .st-open { background:#c0392b; } .st-in_progress { background:#e08600; }
    .st-waiting_user { background:#2980b9; } .st-resolved { background:#27852f; } .st-closed { background:#6b7280; }
    @media (max-width:640px){ .mt-table th:nth-child(3), .mt-table td:nth-child(3){display:none;} }
</style>
@endpush

@section('content')
<div class="mt-wrap">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
        <h1>تیکت‌های من</h1>
        <a href="{{ route('support.create') }}" class="btn btn-accent btn-sm">➕ تیکت جدید</a>
    </div>

    @if($tickets->count())
    <table class="mt-table">
        <thead>
            <tr>
                <th>شماره</th>
                <th>موضوع</th>
                <th>دپارتمان</th>
                <th>آخرین به‌روزرسانی</th>
                <th>وضعیت</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $t)
            <tr>
                <td style="direction:ltr">{{ $t->ticket_number }}</td>
                <td>{{ \Illuminate\Support\Str::limit($t->subject, 40) }}</td>
                <td>{{ $t->department_label }}</td>
                <td>{{ $t->updated_at->diffForHumans() }}</td>
                <td><span class="st st-{{ $t->status }}">{{ $t->status_label }}</span></td>
                <td><a href="{{ route('support.show', $t->ticket_number) }}" class="btn btn-ghost btn-sm">مشاهده</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:1.25rem">{{ $tickets->links() }}</div>
    @else
    <div style="background:#fff;border:1px solid #ece6da;border-radius:var(--radius);padding:3rem;text-align:center;color:var(--color-muted)">
        <div style="font-size:2rem;margin-bottom:.5rem">🎫</div>
        <p>هنوز تیکتی ثبت نکرده‌اید.</p>
        <a href="{{ route('support.create') }}" class="btn btn-accent btn-sm" style="margin-top:1rem">ثبت اولین تیکت</a>
    </div>
    @endif
</div>
@endsection
