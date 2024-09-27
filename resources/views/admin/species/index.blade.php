@extends('adminlte::page')

@section('title', __('Species'))

@section('content_header')
    <h1>{{ __('Species') }}</h1>
@stop

@section('content')
    <x-admin.data-table
        routeCreate="{{ route('admin.species.create') }}"
        :items="$species"
        :columns="['id', 'name', 'description']"
        :actionRoutes="[    
            ['route' => 'admin.species.show', 'class' => 'btn btn-success', 'icon' => 'ri ri-eye-line'],
            ['route' => 'admin.species.edit', 'class' => 'btn btn-primary', 'icon' => 'ri ri-pencil-line'],  
        ]"
        :actionDeleteRoutes="[
            'restore' => 'admin.species.restore',
            'destroy' => 'admin.species.destroy'
        ]"
    />
@stop
