@extends('layouts.app')
@section('title')
    {{ config('app.name') }}
@endsection
@section('main')
    <x-web.home.hero />

    {{-- <x-web.home.plan /> --}}

    {{-- <x-web.home.special /> --}}

    {{-- <x-web.home.about /> --}}

    {{-- <x-web.home.destination /> --}}

    {{-- <x-web.home.brand /> --}}

    {{-- <x-web.home.package /> --}}

    {{-- <x-web.home.testimonial /> --}}

    {{-- <x-web.home.pricing /> --}}

    {{-- <x-web.home.blog /> --}}

    <x-web.home.promotion />
@endsection
