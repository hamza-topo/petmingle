@extends('adminlte::page')

@section('title', __('Detail Species'))

@section('content_header')
    <h1>{{ __('Detail Species') }}</h1>
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
                                <input disabled type="text" class="form-control" name="name" value="{{ $species->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="18" disabled>{{ $species->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.species.index') }}" class="btn btn-primary">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
