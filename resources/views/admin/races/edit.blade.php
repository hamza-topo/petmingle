@extends('adminlte::page')

@section('title', __('Edit Races'))

@section('content_header')
    <h1>{{ __('Edit Races') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.races.update', $race->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $race->name) }}">
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Species</label>
                                <select class="form-control" name="species_id">
                                    @empty(!$species)
                                        @foreach ($species as $id => $name)
                                            <option value="{{ $id }}" {{$race->species_id == $id ? 'selected' : ''}}> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>

                                @error('species_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.races.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
