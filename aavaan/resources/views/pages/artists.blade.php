@extends('layouts.app')
@section('title', 'هنرمندان')
@section('content')
<div class="container" style="padding: 3rem 1rem;">
    <h1>هنرمندان</h1>
    <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
        @forelse($artists as $artist)
        <div style="background: #fff; border-radius: 8px; padding: 1rem; text-align: center;">
            <img src="{{ $artist->avatar_url }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:.5rem">
            <h3>{{ $artist->user->name }}</h3>
            <p>{{ $artist->field }} | {{ $artist->city }}</p>
            <a href="{{ route('profile.show', $artist->username ?? $artist->id) }}" class="btn btn-primary">پروفایل</a>
        </div>
        @empty
        <p>هنرمندی یافت نشد.</p>
        @endforelse
    </div>
    {{ $artists->links() }}
</div>
@endsection
