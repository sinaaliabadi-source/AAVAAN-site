@extends('admin.layouts.app')
@section('title', 'ثبت‌نام‌های هنرباز')
@section('page-title', '🎭 ثبت‌نام‌های هنرباز')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.6rem;margin-bottom:1rem">
        <div class="card-title" style="margin:0">فهرست ثبت‌نام‌ها</div>
        <a href="{{ route('admin.honarbaz.export', request()->query()) }}" class="btn btn-outline btn-sm">📥 خروجی CSV</a>
    </div>

    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:flex-end;margin-bottom:1.2rem">
        <div class="form-group" style="margin:0;flex:1;min-width:180px">
            <label>جستجو</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="نام، تلفن، شهر...">
        </div>
        <div class="form-group" style="margin:0">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="pending" @selected(($filters['status'] ?? '')==='pending')>در انتظار</option>
                <option value="approved" @selected(($filters['status'] ?? '')==='approved')>تأییدشده</option>
                <option value="rejected" @selected(($filters['status'] ?? '')==='rejected')>ردشده</option>
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>استان</label>
            <select name="province" class="form-control">
                <option value="">همه</option>
                @foreach($provinces as $p)
                    <option value="{{ $p }}" @selected(($filters['province'] ?? '')===$p)>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>رشته</label>
            <select name="talent_type" class="form-control">
                <option value="">همه</option>
                @foreach($talentTypes as $t)
                    <option value="{{ $t }}" @selected(($filters['talent_type'] ?? '')===$t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">فیلتر</button>
        <a href="{{ route('admin.honarbaz.registrations') }}" class="btn btn-ghost">پاک‌سازی</a>
    </form>

    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>#</th><th>نام</th><th>تلفن</th><th>شهر</th><th>استان</th>
                <th>رشته</th><th>رأی</th><th>تاریخ</th><th>وضعیت</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($registrations as $r)
                @php
                    $statusBadge = ['pending'=>'badge-warning','approved'=>'badge-success','rejected'=>'badge-danger'][$r->status] ?? 'badge-muted';
                    $statusLabel = ['pending'=>'در انتظار','approved'=>'تأییدشده','rejected'=>'ردشده'][$r->status] ?? $r->status;
                @endphp
                <tr x-data="{ open: false }">
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->full_name }}<br><small style="color:var(--color-muted)">والد: {{ $r->guardian_name }}</small></td>
                    <td style="direction:ltr;text-align:left">{{ $r->phone }}</td>
                    <td>{{ $r->city }}</td>
                    <td>{{ $r->province }}</td>
                    <td>{{ $r->talent_type }}</td>
                    <td><span class="badge badge-info">{{ number_format($r->votes_count) }}</span></td>
                    <td>{{ $r->created_at->format('Y/m/d') }}</td>
                    <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                    <td style="white-space:nowrap">
                        <button type="button" class="btn btn-ghost btn-sm" @click="open = !open">تغییر وضعیت</button>
                        <div x-show="open" x-cloak style="position:relative">
                            <form method="POST" action="{{ route('admin.honarbaz.status', $r->id) }}"
                                  style="position:absolute;left:0;top:.4rem;z-index:10;background:#fff;border:1px solid var(--color-border);border-radius:8px;padding:.9rem;box-shadow:var(--shadow);min-width:230px">
                                @csrf
                                <div class="form-group" style="margin-bottom:.6rem">
                                    <label>وضعیت</label>
                                    <select name="status" class="form-control">
                                        <option value="approved" @selected($r->status==='approved')>تأیید</option>
                                        <option value="rejected" @selected($r->status==='rejected')>رد</option>
                                        <option value="pending" @selected($r->status==='pending')>در انتظار</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom:.6rem">
                                    <label>یادداشت مدیر</label>
                                    <textarea name="admin_notes" class="form-control" rows="2">{{ $r->admin_notes }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">ذخیره</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center">ثبت‌نامی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem">
        {{ $registrations->links() }}
    </div>
</div>
@endsection
