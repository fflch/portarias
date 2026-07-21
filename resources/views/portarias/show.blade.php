@extends('laravel-usp-theme::master')

@section('content')
    {{!! $stepper !!}}

    <div class="container">
        <p>{{$portaria->title}}</p>
        <p>{{$portaria->introduction}}</p>
        <p>{{$portaria->content}}</p>
    </div>
@endsection('content')
