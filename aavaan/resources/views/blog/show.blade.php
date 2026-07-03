@extends('layouts.app')
@section('title', ($post->meta_title ?: $post->title) . ' | آوان')
@section('meta-description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 155))

@push('styles')
<style>
    .article-wrap { max-width:1080px; margin:0 auto; padding:2rem 1.5rem; display:grid; grid-template-columns:1fr 280px; gap:2.5rem; align-items:start; }
    .breadcrumb { font-size:.82rem; color:var(--color-muted); margin-bottom:1rem; }
    .breadcrumb a { color:var(--color-muted); text-decoration:none; }
    .breadcrumb a:hover { color:var(--color-accent); }
    .article-cover { width:100%; max-height:420px; object-fit:cover; border-radius:var(--radius); margin-bottom:1.5rem; }
    .article-title { font-size:2rem; color:var(--color-primary); line-height:1.5; margin-bottom:.75rem; }
    .article-meta { display:flex; flex-wrap:wrap; gap:1rem; font-size:.83rem; color:var(--color-muted); padding-bottom:1.25rem; margin-bottom:1.5rem; border-bottom:1px solid #ece6da; }
    .article-cat { font-size:.75rem; font-weight:700; padding:.15rem .6rem; border-radius:99px; }
    .article-tags { margin-top:2rem; display:flex; flex-wrap:wrap; gap:.4rem; }
    .article-tags a { font-size:.78rem; background:#f5f0e8; color:var(--color-primary); border-radius:99px; padding:.15rem .65rem; text-decoration:none; }
    .author-box { margin-top:2rem; background:#faf7f2; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem; display:flex; gap:1rem; align-items:center; }
    .author-avatar { width:48px; height:48px; border-radius:50%; background:var(--color-primary); color:var(--color-accent); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem; flex-shrink:0; }
    .share-row { margin-top:1.5rem; display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .share-row a { font-size:.82rem; border:1px solid #ddd6c8; border-radius:8px; padding:.3rem .8rem; text-decoration:none; color:var(--color-primary); }
    .side-box { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem; }
    .side-box h3 { font-size:.95rem; color:var(--color-primary); margin-bottom:.85rem; padding-bottom:.5rem; border-bottom:1px solid #f0ede8; }
    .related-item { display:flex; gap:.7rem; padding:.6rem 0; border-bottom:1px solid #f0ede8; text-decoration:none; }
    .related-item:last-child { border-bottom:none; }
    .related-item .rt { font-size:.85rem; color:var(--color-primary); line-height:1.6; }
    .related-item .rm { font-size:.72rem; color:var(--color-muted); }
    @media (max-width:900px){ .article-wrap{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="article-wrap">
    <article>
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a> ← <a href="{{ route('blog') }}">مجله</a> ← <span>{{ $post->title }}</span>
        </div>

        @if($post->cover_url)
            <img src="{{ $post->cover_url }}" class="article-cover" alt="{{ $post->title }}">
        @endif

        <h1 class="article-title">{{ $post->title }}</h1>

        <div class="article-meta">
            @if($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}" class="article-cat" style="background:{{ $post->category->color ?? '#f0ece0' }}20;color:{{ $post->category->color ?? '#7a5c00' }};text-decoration:none">{{ $post->category->name }}</a>
            @endif
            <span>✍ {{ $post->author?->name ?? 'تیم آوان' }}</span>
            <span>🗓 {{ $post->published_at?->format('Y/m/d') }}</span>
            <span>⏱ {{ $post->reading_time }} دقیقه مطالعه</span>
            <span>👁 {{ number_format($post->view_count) }} بازدید</span>
        </div>

        <div class="cms-content">{!! $post->content !!}</div>

        @if($post->tags->count())
        <div class="article-tags">
            @foreach($post->tags as $tag)
                <a href="{{ route('blog.tag', $tag->slug) }}">#{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif

        {{-- اشتراک‌گذاری (بدون اسکریپت خارجی) --}}
        <div class="share-row">
            <span style="font-size:.82rem;color:var(--color-muted)">اشتراک‌گذاری:</span>
            <a href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener">تلگرام</a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener">X</a>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" target="_blank" rel="noopener">واتساپ</a>
        </div>

        <div class="author-box">
            <div class="author-avatar">{{ mb_substr($post->author?->name ?? 'آ', 0, 1) }}</div>
            <div>
                <div style="font-weight:700;color:var(--color-primary)">{{ $post->author?->name ?? 'تیم آوان' }}</div>
                <div style="font-size:.82rem;color:var(--color-muted)">نویسندهٔ مجلهٔ آوان</div>
            </div>
        </div>
    </article>

    <aside>
        <div class="side-box">
            <h3>مقالات مرتبط</h3>
            @forelse($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" class="related-item">
                    <div>
                        <div class="rt">{{ $r->title }}</div>
                        <div class="rm">{{ $r->published_at?->format('Y/m/d') }} · ⏱ {{ $r->reading_time }} دقیقه</div>
                    </div>
                </a>
            @empty
                <p style="font-size:.85rem;color:var(--color-muted)">مقالهٔ مرتبطی یافت نشد.</p>
            @endforelse
            <a href="{{ route('blog') }}" style="display:inline-block;margin-top:1rem;font-size:.85rem;color:var(--color-accent)">← همه مقالات</a>
        </div>
    </aside>
</div>
@endsection
