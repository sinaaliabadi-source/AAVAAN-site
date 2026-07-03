@extends('admin.layouts.app')
@section('title', 'برچسب‌ها')
@section('page-title', 'برچسب‌ها')
@section('content')
<div style="display:grid;grid-template-columns:300px 1fr;gap:1.25rem;align-items:start">
    <div class="card">
        <div class="card-title">برچسب جدید</div>
        <form method="POST" action="{{ route('admin.cms.tags.store') }}">
            @csrf
            <div class="form-group">
                <label>نام برچسب</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button class="btn btn-primary btn-sm">افزودن</button>
        </form>
    </div>

    <div class="card">
        <div style="overflow-x:auto">
            <table class="table">
                <thead><tr><th>نام</th><th>نامک</th><th>مقالات</th><th></th></tr></thead>
                <tbody>
                @forelse($tags as $tag)
                <tr>
                    <td style="font-weight:600">{{ $tag->name }}</td>
                    <td style="direction:ltr">{{ $tag->slug }}</td>
                    <td>{{ $tag->posts_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.cms.tags.destroy', $tag->id) }}" onsubmit="return confirm('حذف برچسب؟')">@csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm" style="color:#c0392b">حذف</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--color-muted)">برچسبی ثبت نشده.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem">{{ $tags->links() }}</div>
    </div>
</div>
@endsection
