@extends('layouts.app')
@section('title', 'صفحه پیدا نشد — آوان')
@section('content')
<div style="text-align:center;padding:6rem 1rem">
    <h1 style="font-size:5rem;color:var(--color-accent);margin-bottom:0">۴۰۴</h1>
    <h2 style="margin-bottom:1rem">صفحه پیدا نشد</h2>
    <p style="color:var(--color-muted);margin-bottom:2rem">صفحه‌ای که دنبال آن بودید وجود ندارد یا جابجا شده است.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">بازگشت به خانه</a>
</div>
@endsection
