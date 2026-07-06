@extends('layouts.app')
@section('title', 'پشتیبانی — آوان')
@section('meta-description', 'مرکز پشتیبانی آوان — سوالات متداول و ثبت تیکت')

@push('styles')
<style>
    .support-hero { background: var(--color-primary); color:#fff; text-align:center; padding:3.5rem 1.5rem; }
    .support-hero h1 { color:#fff; font-size:2rem; margin-bottom:.6rem; }
    .support-hero p { color:rgba(255,255,255,.75); max-width:520px; margin:0 auto; }
    .support-wrap { max-width:860px; margin:0 auto; padding:2.5rem 1.5rem; }
    .dept-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; margin:1.5rem 0 2.5rem; }
    .dept-card { background:#fff; border:1px solid #ece6da; border-radius:var(--radius); padding:1.35rem; text-align:center; }
    .dept-card .icon { font-size:1.8rem; display:block; margin-bottom:.5rem; }
    .dept-card h3 { font-size:1rem; color:var(--color-primary); margin-bottom:.35rem; }
    .dept-card p { font-size:.82rem; color:var(--color-muted); line-height:1.7; }
    .faq-item { background:#fff; border:1px solid #ece6da; border-radius:10px; margin-bottom:.7rem; overflow:hidden; }
    .faq-q { width:100%; text-align:right; background:none; border:none; cursor:pointer; padding:1rem 1.2rem; font-family:inherit; font-size:.95rem; font-weight:600; color:var(--color-primary); display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .faq-a { padding:0 1.2rem 1.1rem; color:var(--color-muted); line-height:2; font-size:.9rem; }
    .cta-box { background:linear-gradient(135deg,#1F2A44,#2d3e60); color:#fff; border-radius:var(--radius); padding:2rem; text-align:center; margin:2.5rem 0; }
    .cta-box h2 { color:#fff; font-size:1.3rem; margin-bottom:.5rem; }
    .cta-box p { color:#c8d0e0; margin-bottom:1.2rem; }
    .track-box { background:#faf7f2; border:1px solid #ece6da; border-radius:var(--radius); padding:1.5rem; }
    .track-box h3 { font-size:1.05rem; color:var(--color-primary); margin-bottom:.4rem; }
    .section-h { font-size:1.3rem; color:var(--color-primary); margin-bottom:.3rem; }
</style>
@endpush

@section('content')
<section class="support-hero">
    <h1>چطور می‌توانیم کمک کنیم؟</h1>
    <p>پاسخ سوال‌های پرتکرار را در پایین ببینید یا برای دریافت کمک اختصاصی، تیکت ثبت کنید.
        @if($avgHours) میانگین زمان پاسخ‌دهی حدود {{ $avgHours }} ساعت است. @endif
    </p>
</section>

<div class="support-wrap">

    {{-- دپارتمان‌ها --}}
    <h2 class="section-h">دپارتمان‌ها</h2>
    <div class="dept-grid">
        @foreach($departments as $key => $dep)
        <div class="dept-card">
            <span class="icon">{{ $dep['icon'] }}</span>
            <h3>{{ $dep['label'] }}</h3>
            <p>{{ $dep['desc'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- CTA ثبت تیکت --}}
    <div class="cta-box">
        <h2>پاسخ سوالتان را پیدا نکردید؟</h2>
        <p>تیم پشتیبانی آوان آماده‌ی کمک به شماست.</p>
        <a href="{{ route('support.create') }}" class="btn btn-accent btn-lg">➕ ثبت تیکت جدید</a>
    </div>

    {{-- FAQ --}}
    <h2 class="section-h">سوالات متداول</h2>
    <p style="color:var(--color-muted);margin-bottom:1.2rem">پیش از ثبت تیکت، این موارد را بررسی کنید.</p>
    <div x-data="{ open: null }">
        @foreach($faqs as $i => $faq)
        <div class="faq-item">
            <button type="button" class="faq-q" @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                <span>{{ $faq['q'] }}</span>
                <span x-text="open === {{ $i }} ? '−' : '+'" style="color:var(--color-accent);font-size:1.2rem"></span>
            </button>
            <div class="faq-a" x-show="open === {{ $i }}" x-cloak>{{ $faq['a'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- پیگیری تیکت --}}
    <div class="track-box" style="margin-top:2.5rem">
        <h3>🔎 پیگیری تیکت</h3>
        <p style="color:var(--color-muted);font-size:.88rem;margin-bottom:1rem">شماره تیکت دارید؟ وضعیت آن را بدون نیاز به ورود ببینید.</p>
        <a href="{{ route('support.track') }}" class="btn btn-outline btn-sm">پیگیری با شماره تیکت</a>
        @auth
            <a href="{{ route('support.tickets') }}" class="btn btn-ghost btn-sm">تیکت‌های من</a>
        @endauth
    </div>

</div>
@endsection
