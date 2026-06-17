@extends('layouts.app')
@section('title', 'بازیابی رمز عبور — آوان')
@section('content')
<div style="max-width:420px;margin:4rem auto;padding:0 1rem">
    <div class="card">
        <h2 style="margin-bottom:1.5rem;text-align:center">بازیابی رمز عبور</h2>
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="alert alert-error">{{ $errors->first() }}</div> @endif
        <form action="{{ route('auth.forgot.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>آدرس ایمیل</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">ارسال لینک بازیابی</button>
        </form>
        <p style="text-align:center;margin-top:1rem;font-size:.85rem"><a href="{{ route('auth') }}">بازگشت به ورود</a></p>
    </div>
</div>
@endsection
