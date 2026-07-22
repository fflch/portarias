@extends('laravel-usp-theme::master')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
@endsection