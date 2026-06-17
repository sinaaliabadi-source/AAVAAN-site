@extends('layouts.app')
@section('title', 'سوالات متداول')
@section('content')
<div class="container" style="padding: 3rem 1rem; max-width: 800px;">
    <h1 style="margin-bottom: 2rem;">سوالات متداول</h1>
    @forelse($faqs as $category => $items)
    <h2 style="margin-bottom: 1rem;">{{ $category }}</h2>
    @foreach($items as $faq)
    <details style="background:#fff; border-radius:6px; padding:1rem; margin-bottom:0.5rem;">
        <summary style="cursor:pointer; font-weight:bold;">{{ $faq->question }}</summary>
        <p style="margin-top:0.5rem;">{{ $faq->answer }}</p>
    </details>
    @endforeach
    @empty
    <p>سوالی یافت نشد.</p>
    @endforelse
</div>
@endsection
