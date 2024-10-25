@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title['fr'] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <x-web.layout.bread-crumb />
    <x-web.home.blog.main />
@endsection
