@extends('layouts.app')
@section('title', 'وبلاگ آوان')
@section('content')
<div class="container" style="padding:3rem 0">
    <h1 style="margin-bottom:2rem">وبلاگ</h1>
    <div class="grid-3">
        @forelse($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="card" style="display:block">
            @if($post->cover_image) <img src="{{ asset('uploads/' . $post->cover_image) }}" style="width:100%;height:160px;object-fit:cover;border-radius:var(--radius);margin-bottom:1rem" alt=""> @endif
            <h3 style="font-size:1rem;margin-bottom:.5rem">{{ $post->title }}</h3>
            <p style="font-size:.85rem;color:var(--color-muted)">{{ Str::limit($post->excerpt, 100) }}</p>
            <p style="font-size:.78rem;color:var(--color-muted);margin-top:.75rem">{{ $post->published_at?->format('Y/m/d') }}</p>
        </a>
        @empty <p style="color:var(--color-muted)">مقاله‌ای منتشر نشده است.</p>
        @endforelse
    </div>
    <div style="margin-top:2rem">{{ $posts->links() }}</div>
</div>
@endsection
