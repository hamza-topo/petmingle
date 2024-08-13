@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('New Race') }}</h5>
            <div class="card-body">
                <form class="row col-sm-12 col-md-12 col-lg-12" action="{{ route('admin.races.store') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name">
                            @error('name')
                                <div class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Species') }}</label>
                            <select class="form-control" name="species_id">
                                <option value="">{{ __('Choose species') }}</option>
                                @foreach ($species as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('description')
                                <div class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
