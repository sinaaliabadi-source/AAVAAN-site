@extends('layouts.app')
@section('title', 'سوالات متداول — آوان')
@section('meta-description', 'پاسخ سوالات پرتکرار هنرمندان و تیم‌های تولید درباره آوان')

@php
    use App\Models\CmsFaq;
    $labels = CmsFaq::CATEGORIES;
    $icons = ['artist'=>'🎭','production'=>'🎬','payment'=>'💳','honarbaz'=>'🎭','general'=>'💬'];
@endphp

@push('styles')
<style>
.faq-hero { background: linear-gradient(135deg, var(--color-primary) 0%, #2d3e60 100%); padding: 4rem 0 3.5rem; text-align: center; }
.faq-hero-title { font-family:'YekanBakh',Tahoma,sans-serif; font-size:clamp(1.6rem,3.5vw,2.4rem); font-weight:800; color:var(--color-accent); margin-bottom:.6rem; }
.faq-hero-sub { color:#c8d0e0; font-size:.95rem; }
.faq-body { padding: 4rem 0 5rem; }
.faq-columns { display:grid; grid-template-columns:1fr 1fr; gap:3rem; }
.faq-section-title { font-family:'YekanBakh',Tahoma,sans-serif; font-size:1.15rem; font-weight:800; color:var(--color-primary); margin-bottom:1.2rem; padding-right:1rem; border-right:4px solid var(--color-accent); display:flex; align-items:center; gap:.5rem; }
details.faq-item { background:#fff; border-radius:var(--radius); border:1px solid #ede8dc; margin-bottom:.55rem; overflow:hidden; }
details.faq-item summary { cursor:pointer; padding:1rem 1.1rem; font-weight:600; font-size:.9rem; color:var(--color-primary); list-style:none; display:flex; justify-content:space-between; align-items:center; gap:.75rem; user-select:none; }
details.faq-item summary::-webkit-details-marker { display:none; }
details.faq-item summary::after { content:'＋'; font-size:1.1rem; color:var(--color-accent); flex-shrink:0; transition:transform .2s; }
details.faq-item[open] summary::after { content:'−'; }
details.faq-item[open] summary { border-bottom:1px solid #f0ede8; }
.faq-answer { padding:.85rem 1.1rem 1.1rem; font-size:.88rem; color:var(--color-muted); line-height:1.9; }
.faq-cta { background:var(--color-primary); border-radius:12px; padding:2rem; text-align:center; margin-top:3rem; }
.faq-cta p { color:#c8d0e0; font-size:.92rem; margin-bottom:1rem; }
@media (max-width:820px){ .faq-columns { grid-template-columns:1fr; gap:2rem; } }
</style>
@endpush

@section('content')
<section class="faq-hero">
    <div class="container">
        <h1 class="faq-hero-title">سوالات متداول</h1>
        <p class="faq-hero-sub">پاسخ سریع سوال‌های رایج — برای هنرمندان و تیم‌های تولید</p>
    </div>
</section>

<section class="faq-body">
    <div class="container">
        @if($faqs->isEmpty())
            <p style="text-align:center;color:var(--color-muted)">هنوز سوالی ثبت نشده است.</p>
        @else
        <div class="faq-columns">
            @foreach($faqs as $category => $items)
            <div>
                <div class="faq-section-title">{{ $icons[$category] ?? '❓' }} {{ $labels[$category] ?? $category }}</div>
                @foreach($items as $faq)
                <details class="faq-item">
                    <summary>{{ $faq->question }}</summary>
                    <div class="faq-answer">{!! nl2br(e($faq->answer)) !!}</div>
                </details>
                @endforeach
            </div>
            @endforeach
        </div>
        @endif

        <div class="faq-cta">
            <p>سوالتان اینجا نبود؟ مستقیم با ما در میان بگذارید.</p>
            <a href="{{ route('contact') }}" class="btn btn-accent btn-sm">تماس با پشتیبانی</a>
        </div>
    </div>
</section>
@endsection
