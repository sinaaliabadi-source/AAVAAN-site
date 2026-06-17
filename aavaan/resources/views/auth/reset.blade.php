@extends('layouts.app')
@section('title', 'تغییر رمز عبور — آوان')
@section('content')
<div style="max-width:420px;margin:4rem auto;padding:0 1rem">
    <div class="card">
        <h2 style="margin-bottom:1.5rem;text-align:center">تغییر رمز عبور</h2>
        @if($errors->any()) <div class="alert alert-error">{{ $errors->first() }}</div> @endif
        <form action="{{ route('auth.reset.do') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>رمز عبور جدید</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>تکرار رمز عبور جدید</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">تغییر رمز</button>
        </form>
    </div>
</div>
@endsection
