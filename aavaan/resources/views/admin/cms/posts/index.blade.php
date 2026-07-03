@extends('admin.layouts.app')
@section('title', 'مقالات بلاگ')
@section('page-title', 'مقالات بلاگ')
@section('topbar-actions')
<a href="{{ route('admin.cms.posts.create') }}" class="btn btn-primary btn-sm">+ مقالهٔ جدید</a>
@endsection

@push('styles')
<style>
.st { color:#fff; font-weight:600; font-size:.74rem; padding:.15rem .6rem; border-radius:99px; white-space:nowrap; }
.st-draft { background:#6b7280; } .st-published { background:#27852f; } .st-archived { background:#b06a00; }
</style>
@endpush

@section('content')
<div class="card" style="margin-bottom:1rem">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:.7rem;align-items:end">
        <div class="form-group" style="margin:0;flex:1;min-width:150px">
            <label>جستجو</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="عنوان یا slug">
        </div>
        <div class="form-group" style="margin:0">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                @foreach(['draft'=>'پیش‌نویس','published'=>'منتشرشده','archived'=>'بایگانی'] as $k=>$v)
                    <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0">
            <label>دسته</label>
            <select name="category" class="form-control">
                <option value="">همه</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ (string)request('category')===(string)$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary btn-sm">فیلتر</button>
    </form>
</div>

<div class="card">
    <div style="overflow-x:auto">
        <table class="table">
            <thead><tr>
                <th>عنوان</th><th>دسته</th><th>نویسنده</th><th>وضعیت</th><th>انتشار</th><th>بازدید</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($posts as $post)
            <tr>
                <td>
                    <div style="font-weight:600">{{ \Illuminate\Support\Str::limit($post->title, 45) }}</div>
                    @if($post->is_featured)<span style="font-size:.7rem;color:var(--color-accent)">★ ویژه</span>@endif
                </td>
                <td>{{ $post->category?->name ?? '—' }}</td>
                <td>{{ $post->author?->name ?? '—' }}</td>
                <td><span class="st st-{{ $post->status }}">{{ $post->status_label }}</span></td>
                <td style="white-space:nowrap">{{ $post->published_at?->format('Y/m/d') ?? '—' }}</td>
                <td>{{ number_format($post->view_count) }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.cms.posts.edit', $post) }}" class="btn btn-ghost btn-sm">ویرایش</a>
                    @if($post->status !== 'published')
                    <form method="POST" action="{{ route('admin.cms.posts.publish', $post) }}" style="display:inline">@csrf
                        <button class="btn btn-ghost btn-sm" style="color:#27852f">انتشار</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.cms.posts.unpublish', $post) }}" style="display:inline">@csrf
                        <button class="btn btn-ghost btn-sm">پیش‌نویس</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.cms.posts.destroy', $post) }}" style="display:inline" onsubmit="return confirm('حذف مقاله؟')">@csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm" style="color:#c0392b">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-muted)">مقاله‌ای یافت نشد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $posts->links() }}</div>
</div>
@endsection
