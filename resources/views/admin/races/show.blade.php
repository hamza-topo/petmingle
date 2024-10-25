@extends('adminlte::page')

@section('title', __('Detail Race'))

@section('content_header')
    <h1>{{ __('Detail Race') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input disabled type="text" class="form-control" name="name" value="{{ $race->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Species</label>
                                <input class="form-control" disabled value="{{ $race->species?->name }}"/>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.races.index') }}" class="btn btn-primary">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
