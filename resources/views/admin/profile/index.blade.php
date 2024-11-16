@extends('adminlte::page')

@section('title', __('Edit Profile'))

@section('content_header')
    <h1>{{ __('Edit Profile') }}</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ auth()->user()->name }}">
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Avatar:</label>
                                <input type="file" class="form-control" name="avatar">
                                @error('avatar')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                                <br>
                                @php
                                    $avatar = json_decode(auth()->user()->avatar, true);
                                    $avatar = $avatar[0] ?? '';
                                @endphp
                                <img style="width: 75px" src="{{ asset('storage/' . $avatar) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
