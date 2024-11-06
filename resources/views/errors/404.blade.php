@extends('layouts.app')

@section('meta')
<x-web.layout.meta :seo="null"/>
@endsection

@section('title')
404
@endsection
@section('main')
    <x-web.layout.bread-crumb />
   <h1>404. NOT FOUND</h1>
@endsection
