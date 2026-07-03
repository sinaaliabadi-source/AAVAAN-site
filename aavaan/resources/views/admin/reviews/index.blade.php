@extends('admin.layouts.app')
@section('title', 'نظرات هنرمندان')
@section('page-title', 'نظرات هنرمندان')
@section('content')

<div class="card" style="margin-bottom:1rem;">
    <form method="GET" action="{{ route('admin.reviews.index') }}"
          style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
        <div class="form-group" style="margin:0;">
            <label>امتیاز</label>
            <select name="rating" class="form-control">
                <option value="">همه</option>
                @for($r = 5; $r >= 1; $r--)
                    <option value="{{ $r }}" {{ (string) request('rating') === (string) $r ? 'selected' : '' }}>{{ $r }} ستاره</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="">همه</option>
                <option value="visible" {{ request('status') === 'visible' ? 'selected' : '' }}>نمایش</option>
                <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>مخفی</option>
            </select>
        </div>
        <div style="display:flex;gap:.5rem;">
            <button class="btn btn-primary btn-sm">فیلتر</button>
            @if(request()->hasAny(['rating', 'status']))
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost btn-sm">پاک کردن</a>
            @endif
        </div>
    </form>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table class="table">
            <thead><tr>
                <th>تاریخ</th><th>امتیاز</th><th>خلاصه نظر</th><th>هنرمند</th><th>وضعیت</th><th>عملیات</th>
            </tr></thead>
            <tbody>
            @forelse($reviews as $review)
            <tr>
                <td style="white-space:nowrap;">{{ $review->created_at->format('Y/m/d') }}</td>
                <td style="white-space:nowrap;color:#f0b429;">
                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                </td>
                <td>{{ $review->comment ? \Illuminate\Support\Str::limit($review->comment, 70) : '—' }}</td>
                <td>
                    @if($review->artist && $review->artist->artistProfile)
                        <a href="{{ route('profile.show', $review->artist->artistProfile->username) }}" target="_blank">
                            {{ $review->artist->name }}
                        </a>
                    @else
                        هنرمند #{{ $review->artist_user_id }}
                    @endif
                </td>
                <td>
                    @if($review->is_visible)
                        <span class="badge badge-success">نمایش</span>
                    @else
                        <span class="badge badge-muted">مخفی</span>
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    <form method="POST" action="{{ route('admin.reviews.toggle', $review->id) }}" style="display:inline;">@csrf
                        <button class="btn btn-ghost btn-sm">{{ $review->is_visible ? 'مخفی کردن' : 'نمایش' }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-muted);">نظری ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem;">{{ $reviews->links() }}</div>
</div>
@endsection
