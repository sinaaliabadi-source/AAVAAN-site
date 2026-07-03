@extends('admin.layouts.app')
@section('title', 'همه تیکت‌ها')
@section('page-title', 'همه تیکت‌ها')

@push('styles')
<style>
    .st { color:#fff; font-weight:600; font-size:.74rem; padding:.15rem .6rem; border-radius:99px; white-space:nowrap; }
    .st-open { background:#c0392b; } .st-in_progress { background:#e08600; }
    .st-waiting_user { background:#2980b9; } .st-resolved { background:#27852f; } .st-closed { background:#6b7280; }
    .pr { font-size:.74rem; padding:.1rem .5rem; border-radius:5px; }
    .pr-urgent { background:#fdecec; color:#c0392b; } .pr-high { background:#fef1e0; color:#b06a00; }
    .pr-normal { background:#eef1f5; color:#556; } .pr-low { background:#f0f0f0; color:#888; }
</style>
@endpush

@section('content')

<div class="card" style="margin-bottom:1rem">
    <form method="GET" action="{{ route('admin.support.tickets') }}"
          style="display:flex;flex-wrap:wrap;gap:.7rem;align-items:end">
        <div class="form-group" style="margin:0;min-width:150px;flex:1">
            <label>جستجو</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="شماره یا موضوع">
        </div>
        <div class="form-group" style="margin:0">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                @foreach(['open'=>'باز','in_progress'=>'در حال بررسی','waiting_user'=>'در انتظار کاربر','resolved'=>'حل شده','closed'=>'بسته شده'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>دپارتمان</label>
            <select name="department" class="form-control">
                <option value="">همه</option>
                @foreach(['technical'=>'فنی','billing'=>'مالی','casting'=>'کستینگ','honarbaz'=>'هنرباز','general'=>'عمومی'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('department')===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>اولویت</label>
            <select name="priority" class="form-control">
                <option value="">همه</option>
                @foreach(['urgent'=>'فوری','high'=>'زیاد','normal'=>'معمولی','low'=>'کم'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('priority')===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>مسئول</label>
            <select name="assigned_to" class="form-control">
                <option value="">همه</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ (string)request('assigned_to')===(string)$a->id?'selected':'' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>مرتب‌سازی</label>
            <select name="sort" class="form-control">
                <option value="newest" {{ request('sort')==='newest'?'selected':'' }}>جدیدترین</option>
                <option value="oldest" {{ request('sort')==='oldest'?'selected':'' }}>قدیمی‌ترین</option>
                <option value="last_reply" {{ request('sort')==='last_reply'?'selected':'' }}>آخرین پیام</option>
            </select>
        </div>
        <div style="display:flex;gap:.4rem">
            <button class="btn btn-primary btn-sm">فیلتر</button>
            @if(request()->hasAny(['search','status','department','priority','assigned_to','sort']))
                <a href="{{ route('admin.support.tickets') }}" class="btn btn-ghost btn-sm">✕</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>شماره</th><th>موضوع</th><th>دپارتمان</th><th>اولویت</th><th>درخواست‌کننده</th><th>مسئول</th><th>به‌روزرسانی</th><th>وضعیت</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($tickets as $t)
            <tr>
                <td style="direction:ltr">{{ $t->ticket_number }}</td>
                <td>{{ \Illuminate\Support\Str::limit($t->subject, 30) }}</td>
                <td>{{ $t->department_label }}</td>
                <td><span class="pr pr-{{ $t->priority }}">{{ $t->priority_label }}</span></td>
                <td>{{ $t->requester_name }}</td>
                <td>{{ $t->assignedAdmin?->name ?? '—' }}</td>
                <td style="white-space:nowrap">{{ $t->updated_at->diffForHumans() }}</td>
                <td><span class="st st-{{ $t->status }}">{{ $t->status_label }}</span></td>
                <td><a href="{{ route('admin.support.show', $t->ticket_number) }}" class="btn btn-ghost btn-sm">مشاهده</a></td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:2rem;color:var(--color-muted)">تیکتی یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $tickets->links() }}</div>
</div>

@endsection
