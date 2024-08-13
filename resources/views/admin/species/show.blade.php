@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('Detail Species') }}</h5>
            <div class="card-body">
                <div class="row col-sm-12 col-md-12 col-lg-12">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input disabled type="text" class="form-control" name="name" value="{{ $species->name }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" disabled>
                                {{ $species->description }}
                            </textarea>
                        </div>
                    </div>
                    <a href="{{ route('admin.species.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
