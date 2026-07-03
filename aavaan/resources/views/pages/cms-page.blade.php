@extends('layouts.app')
@section('title', ($page->meta_title ?: $page->title) . ' — آوان')
@section('meta-description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 155))

@push('styles')
<style>
.cms-page-hero { background:linear-gradient(135deg,var(--color-primary),#2d3e60); padding:3rem 0; text-align:center; }
.cms-page-hero h1 { font-family:'YekanBakh',Tahoma,sans-serif; font-size:clamp(1.5rem,3.5vw,2.2rem); font-weight:800; color:var(--color-accent); }
.cms-page-body { padding:3rem 0 5rem; }
.cms-page-body .container { max-width:820px; }
</style>
@endpush

@section('content')
<section class="cms-page-hero">
    <div class="container"><h1>{{ $page->title }}</h1></div>
</section>
<section class="cms-page-body">
    <div class="container">
        <div class="cms-content">{!! $page->content !!}</div>
        <p style="font-size:.8rem;color:var(--color-muted);margin-top:2.5rem;padding-top:1rem;border-top:1px solid #ece6da">
            آخرین به‌روزرسانی: {{ $page->updated_at?->format('Y/m/d') }}
        </p>
    </div>
</section>
@endsection
