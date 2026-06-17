@extends('layouts.app')
@section('title', 'تماس با ما')
@section('content')
<div class="container" style="padding: 3rem 1rem; max-width: 600px;">
    <h1 style="margin-bottom: 2rem;">تماس با ما</h1>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-error">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('contact.send') }}" style="background:#fff; padding:2rem; border-radius:8px;">
        @csrf
        <div class="form-group"><label>نام</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div class="form-group"><label>ایمیل</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        <div class="form-group"><label>تلفن</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
        <div class="form-group"><label>موضوع</label><input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required></div>
        <div class="form-group"><label>پیام</label><textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea></div>
        <button type="submit" class="btn btn-primary">ارسال پیام</button>
    </form>
</div>
@endsection
