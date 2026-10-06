@extends('emails.layouts.master')

@section('title')
    <title>{{ $mail['subject'] }}</title>
@endsection

@section('body')
    <h1>{{ $mail['subject'] }}</h1>
    <p>{{ __('Name') }}: {{ $mail['name'] }}</p>
    <p>{{ __('Email') }}: {{ $mail['email'] }}</p>
    <p>{!! nl2br(e($mail['message'])) !!}</p>
@endsection
