@extends('layouts.app')
@section('title', $post->title . ' — آوان')
@section('content')
<div class="container" style="padding:3rem 0;max-width:800px">
    @if($post->cover_image) <img src="{{ asset('uploads/' . $post->cover_image) }}" style="width:100%;border-radius:var(--radius);margin-bottom:2rem" alt=""> @endif
    <h1 style="margin-bottom:.75rem">{{ $post->title }}</h1>
    <p style="font-size:.85rem;color:var(--color-muted);margin-bottom:2rem">{{ $post->published_at?->format('Y/m/d') }}</p>
    <div style="line-height:2">{!! nl2br(e($post->body)) !!}</div>
    <div style="margin-top:2rem"><a href="{{ route('blog') }}" style="color:var(--color-accent)">← بازگشت به وبلاگ</a></div>
</div>
@endsection
