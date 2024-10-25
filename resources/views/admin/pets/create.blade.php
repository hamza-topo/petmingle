@extends('adminlte::page')

@section('title', __('New Pet'))

@section('content_header')
    <h1>{{ __('New Pet') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"> 
                    <form class="row" action="{{ route('admin.pets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">
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
                                            <option value="{{ $id }}"> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>

                                @error('species_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <select class="form-control" name="user_id">
                                    @empty(!$users)
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}"> {{ $name }}</option>
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
                                <label class="form-label">Races</label>
                                <select class="form-control" name="race_id">
                                    @empty(!$races)
                                        @foreach ($races as $id => $name)
                                            <option value="{{ $id }}"> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>

                                @error('race_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <select class="form-control" name="age">
                                    @for ($i = App\Enums\App::MIN_AGE; $i < App\Enums\App::MAX_AGE; $i++)
                                        <option value="{{ $i }}"> {{ $i }}. month(s)</option>
                                    @endfor
                                </select>

                                @error('age')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sexe</label>
                                <select class="form-control" name="sexe">
                                    <option value="{{ App\Enums\Pet::FEMALE }}">F</option>
                                    <option value="{{ App\Enums\Pet::MALE }}">M</option>
                                </select>
                                @error('sexe')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" name="color" class="form-control">
                                @error('color')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">About</label>
                                <textarea name="about" class="form-control"></textarea>
                                @error('about')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gallery</label>
                                <input type="file"  name="images" class="form-control">
                                @error('images')
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
