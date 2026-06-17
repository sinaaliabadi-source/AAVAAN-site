@extends('layouts.app')
@section('title', 'برای گروه‌های تولید')
@section('content')
<div class="container" style="padding: 3rem 1rem;">
    <h1>برای گروه‌های تولید</h1>
    <p style="margin-top:1rem;color:var(--color-muted)">به هزاران هنرمند حرفه‌ای ایران دسترسی داشته باشید.</p>
    <a href="{{ route('auth') }}?role=production" class="btn btn-accent" style="margin-top:2rem">ثبت‌نام تیم تولید</a>
</div>
@endsection
