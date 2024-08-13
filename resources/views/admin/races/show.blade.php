@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('New Race') }}</h5>
            <div class="card-body">
                <div class="row col-sm-12 col-md-12 col-lg-12">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $race->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Species') }}</label>
                            <select class="form-control" name="species_id" disabled>
                                <option value="" selected>{{ $race->species->name ?? '_' }}</option>
                            </select>
                        </div>
                    </div>
                    <a href="{{ route('admin.races.index') }}" type="submit"
                        class="btn btn-primary">{{ __('Annuler') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
