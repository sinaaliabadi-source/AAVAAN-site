@extends('admin.layouts.app')

@section('title', 'در دست توسعه')
@section('page-title', 'در دست توسعه')

@section('content')

<div class="card" style="text-align:center; padding:4rem 2rem;">
    <div style="font-size:3.5rem; margin-bottom:1.25rem; opacity:.35;">🔧</div>
    <h2 style="color:var(--color-primary); margin-bottom:.6rem;">این بخش در فاز بعدی تکمیل می‌شود</h2>
    <p class="text-muted" style="max-width:400px; margin:0 auto 1.75rem;">
        این قابلیت در نقشه‌راه توسعه آوان قرار دارد و در فاز مربوطه پیاده‌سازی خواهد شد.
    </p>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">بازگشت به نمای کلی</a>
</div>

@endsection
