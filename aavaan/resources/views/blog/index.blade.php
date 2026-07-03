@extends('layouts.app')
@section('title', ($pageHeading ?? 'مجله آوان') . ' | آوان')
@section('meta-description', 'مجله آوان — آموزش هنری، اخبار صنعت سینما و داستان موفقیت هنرمندان')

@push('styles')
<style>
    .blog-hero { background:linear-gradient(135deg,var(--color-primary),#2d3e60); color:#fff; text-align:center; padding:3.5rem 1.5rem; }
    .blog-hero h1 { color:var(--color-accent); font-size:2rem; margin-bottom:.5rem; }
    .blog-hero p { color:#c8d0e0; }
    .blog-wrap { max-width:1140px; margin:0 auto; padding:2.5rem 1.5rem; display:grid; grid-template-columns:1fr 280px; gap:2rem; align-items:start; }
    .post-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
    .post-card { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s, transform .2s; }
    .post-card:hover { box-shadow:0 6px 20px rgba(31,42,68,.1); transform:translateY(-2px); }
    .post-thumb { height:170px; background:#f0ece0 center/cover no-repeat; display:block; }
    .post-thumb.ph { display:flex; align-items:center; justify-content:center; font-size:2.2rem; color:var(--color-accent); }
    .post-body { padding:1.1rem; display:flex; flex-direction:column; flex:1; }
    .post-cat { font-size:.72rem; font-weight:700; padding:.12rem .55rem; border-radius:99px; align-self:flex-start; margin-bottom:.5rem; }
    .post-title { font-weight:700; color:var(--color-primary); font-size:1rem; margin-bottom:.4rem; line-height:1.6; }
    .post-excerpt { font-size:.85rem; color:var(--color-muted); line-height:1.9; flex:1; }
    .post-meta { font-size:.75rem; color:var(--color-muted); margin-top:.75rem; display:flex; gap:.75rem; flex-wrap:wrap; }
    .featured-card { grid-column:1/-1; display:grid; grid-template-columns:1.2fr 1fr; background:#fff; border:1px solid #ece6da; border-radius:var(--radius); overflow:hidden; margin-bottom:.5rem; }
    .featured-card .fc-thumb { min-height:240px; background:#f0ece0 center/cover no-repeat; }
    .featured-card .fc-thumb.ph { display:flex; align-items:center; justify-content:center; font-size:3rem; color:var(--color-accent); }
    .featured-card .fc-body { padding:1.75rem; display:flex; flex-direction:column; justify-content:center; }
    .featured-badge { font-size:.72rem; font-weight:700; color:var(--color-accent); margin-bottom:.5rem; }
    .side-box { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.25rem; margin-bottom:1.25rem; }
    .side-box h3 { font-size:.95rem; color:var(--color-primary); margin-bottom:.85rem; padding-bottom:.5rem; border-bottom:1px solid #f0ede8; }
    .side-cat { display:flex; justify-content:space-between; padding:.4rem 0; font-size:.87rem; color:var(--color-muted); text-decoration:none; }
    .side-cat:hover, .side-cat.active { color:var(--color-accent); }
    .tag-cloud { display:flex; flex-wrap:wrap; gap:.4rem; }
    .tag-chip { font-size:.78rem; background:#f5f0e8; color:var(--color-primary); border-radius:99px; padding:.15rem .65rem; text-decoration:none; }
    .tag-chip.active { background:var(--color-accent); color:#fff; }
    @media (max-width:900px){ .blog-wrap{ grid-template-columns:1fr; } .post-grid{ grid-template-columns:1fr; } .featured-card{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<section class="blog-hero">
    <h1>{{ $pageHeading ?? 'مجله آوان' }}</h1>
    <p>آموزش، اخبار صنعت سینما و داستان موفقیت هنرمندان</p>
</section>

<div class="blog-wrap">
    <div>
        @if(!$posts->count() && empty($featured))
            <div style="background:#fff;border:1px solid #ece6da;border-radius:var(--radius);padding:3rem;text-align:center;color:var(--color-muted)">
                <div style="font-size:2rem;margin-bottom:.5rem">📰</div>
                <p>هنوز مقاله‌ای منتشر نشده است.</p>
            </div>
        @endif

        <div class="post-grid">
            @if(!empty($featured))
            <a href="{{ route('blog.show', $featured->slug) }}" class="featured-card" style="text-decoration:none">
                @if($featured->cover_url)
                    <div class="fc-thumb" style="background-image:url('{{ $featured->cover_url }}')"></div>
                @else
                    <div class="fc-thumb ph">✦</div>
                @endif
                <div class="fc-body">
                    <span class="featured-badge">★ مقالهٔ ویژه</span>
                    @if($featured->category)
                        <span class="post-cat" style="background:{{ $featured->category->color ?? '#f0ece0' }}20;color:{{ $featured->category->color ?? '#7a5c00' }}">{{ $featured->category->name }}</span>
                    @endif
                    <div class="post-title" style="font-size:1.3rem">{{ $featured->title }}</div>
                    <p class="post-excerpt">{{ $featured->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($featured->content), 160) }}</p>
                    <div class="post-meta">
                        <span>{{ $featured->published_at?->format('Y/m/d') }}</span>
                        <span>⏱ {{ $featured->reading_time }} دقیقه</span>
                    </div>
                </div>
            </a>
            @endif

            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="post-card" style="text-decoration:none">
                @if($post->cover_url)
                    <span class="post-thumb" style="background-image:url('{{ $post->cover_url }}')"></span>
                @else
                    <span class="post-thumb ph">✦</span>
                @endif
                <div class="post-body">
                    @if($post->category)
                        <span class="post-cat" style="background:{{ $post->category->color ?? '#f0ece0' }}20;color:{{ $post->category->color ?? '#7a5c00' }}">{{ $post->category->name }}</span>
                    @endif
                    <div class="post-title">{{ $post->title }}</div>
                    <p class="post-excerpt">{{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
                    <div class="post-meta">
                        <span>{{ $post->published_at?->format('Y/m/d') }}</span>
                        <span>⏱ {{ $post->reading_time }} دقیقه</span>
                        <span>👁 {{ number_format($post->view_count) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div style="margin-top:1.5rem">{{ $posts->links() }}</div>
    </div>

    {{-- Sidebar --}}
    <aside>
        <div class="side-box">
            <h3>دسته‌بندی‌ها</h3>
            <a href="{{ route('blog') }}" class="side-cat {{ empty($activeCategory) && empty($activeTag) ? 'active' : '' }}">
                <span>همه مقالات</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('blog.category', $cat->slug) }}" class="side-cat {{ ($activeCategory ?? null) === $cat->slug ? 'active' : '' }}">
                <span>{{ $cat->name }}</span>
                <span>{{ $cat->posts_count }}</span>
            </a>
            @endforeach
        </div>

        @if($popularTags->count())
        <div class="side-box">
            <h3>برچسب‌های محبوب</h3>
            <div class="tag-cloud">
                @foreach($popularTags as $tag)
                <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-chip {{ ($activeTag ?? null) === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
        @endif
    </aside>
</div>
@endsection
