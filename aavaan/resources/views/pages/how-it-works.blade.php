@extends('layouts.app')
@section('title', 'نحوه کار')
@section('content')
<div class="container" style="padding: 3rem 1rem;">
    <h1>نحوه کار آوان</h1>
    <div style="margin-top:2rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:2rem">
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;margin-bottom:1rem">🎭</div>
            <h3>ثبت‌نام هنرمند</h3>
            <p style="color:var(--color-muted);font-size:.9rem;margin-top:.5rem">پروفایل خود را بسازید و نمونه‌کارتان را آپلود کنید.</p>
        </div>
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;margin-bottom:1rem">🎬</div>
            <h3>جستجوی تیم تولید</h3>
            <p style="color:var(--color-muted);font-size:.9rem;margin-top:.5rem">گروه‌های تولید هنرمندان را جستجو و فیلتر می‌کنند.</p>
        </div>
        <div class="card" style="text-align:center">
            <div style="font-size:3rem;margin-bottom:1rem">🔓</div>
            <h3>دسترسی به اطلاعات</h3>
            <p style="color:var(--color-muted);font-size:.9rem;margin-top:.5rem">با خرید دسترسی، اطلاعات تماس هنرمند نمایش داده می‌شود.</p>
        </div>
    </div>
</div>
@endsection
