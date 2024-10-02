@extends('adminlte::page')

@section('title', __('Detail User'))

@section('content_header')
    <h1>{{ __('Detail User') }}</h1>
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
                                <input disabled type="text" class="form-control" name="name" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input disabled type="email" class="form-control" name="email" value="{{ $user->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Admin Status</label>
                                <input disabled type="text" class="form-control" name="is_admin" value="{{ $user->is_admin ? __('Admin') : __('Regular User') }}">
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
