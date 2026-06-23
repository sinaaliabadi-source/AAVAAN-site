@extends('emails.layouts.base')
@section('content')
<h2>{{ $emailSubject }}</h2>
{!! nl2br(e($emailBody)) !!}
@endsection
