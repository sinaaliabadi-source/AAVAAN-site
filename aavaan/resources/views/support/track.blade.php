@extends('layouts.app')
@section('title', 'پیگیری تیکت')

@section('content')
<div style="max-width:460px;margin:0 auto;padding:3rem 1.5rem">
    <h1 style="font-size:1.5rem;color:var(--color-primary);margin-bottom:.4rem">پیگیری تیکت</h1>
    <p style="color:var(--color-muted);margin-bottom:1.5rem">شماره تیکت و ایمیلی که هنگام ثبت وارد کرده‌اید را بنویسید.</p>

    <form method="POST" action="{{ route('support.track.result') }}"
          style="background:#fff;border:1px solid #ece6da;border-radius:var(--radius);padding:1.5rem">
        @csrf
        <div class="form-group">
            <label>شماره تیکت</label>
            <input type="text" name="ticket_number" class="form-control" dir="ltr"
                   value="{{ old('ticket_number') }}" placeholder="TKT-2607-0001" required>
        </div>
        <div class="form-group">
            <label>ایمیل</label>
            <input type="email" name="email" class="form-control" dir="ltr"
                   value="{{ old('email') }}" placeholder="you@example.com" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">پیگیری</button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:.85rem;color:var(--color-muted)">
        حساب کاربری دارید؟ <a href="{{ route('support.tickets') }}">تیکت‌های من</a>
    </p>
</div>
@endsection
