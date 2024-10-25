@extends('layouts.app')
@section('main')
    <x-web.layout.bread-crumb />
    <x-web.engine.main :pets="$pets" />
@endsection
