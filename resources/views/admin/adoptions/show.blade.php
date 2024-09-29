@extends('adminlte::page')

@section('title', __('Detail Adoption'))

@section('content_header')
    <h1>{{ __('Detail Adoption') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">From Owner</label>
                                <select class="form-control" disabled readonly>
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
                                <select class="form-control" disabled readonly>
                                    @empty(!$users)
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}" {{ $adoption->to == $id ? 'selected' : '' }}>
                                                {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pet</label>
                                <select class="form-control" disabled readonly>
                                    <option value="2" {{ $adoption->pet_id == 1 ? 'selected' : '' }}>PET</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('admin.adoptions.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
