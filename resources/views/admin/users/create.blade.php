@extends('adminlte::page')

@section('title', __('New User'))

@section('content_header')
    <h1>{{ __('New User') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>@extends('adminlte::page')

                            @section('title', __('New User'))
                            
                            @section('content_header')
                                <h1>{{ __('New User') }}</h1>
                            @stop
                            
                            @section('content')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form class="row" action="{{ route('admin.users.store') }}" method="POST">
                                                    @csrf
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                                            @error('name')
                                                                <div class="form-text text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                                            @error('email')
                                                                <div class="form-text text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Password</label>
                                                            <input type="password" class="form-control" name="password" required>
                                                            @error('password')
                                                                <div class="form-text text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Admin Status</label>
                                                            <select class="form-control" name="is_admin" required>
                                                                @foreach (\App\Enums\User::options() as $value => $label)
                                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('is_admin')
                                                                <div class="form-text text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                            
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endsection
                            
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
