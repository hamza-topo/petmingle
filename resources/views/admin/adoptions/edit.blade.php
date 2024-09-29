@extends('adminlte::page')

@section('title', __('Update Pet Adoption'))

@section('content_header')
    <h1>{{ __('Update Adoption') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.adoptions.update', $adoption->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Owner</label>
                                <select class="form-control" name="from">
                                    @empty(!$users)
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}" {{ $adoption->from == $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>

                                @error('from')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">New Owner</label>
                                <select class="form-control" name="to">
                                    @empty(!$users)
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}" {{ $adoption->to == $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>
                                @error('user_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pet</label>
                                <select class="form-control" name="pet_id">
                                    <option value="2">PET</option>
                                </select>

                                @error('pet_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.adoptions.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
