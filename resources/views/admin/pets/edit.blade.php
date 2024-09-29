@extends('adminlte::page')

@section('title', __('Update Pet'))

@section('content_header')
    <h1>{{ __('Update Pet') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"> 
                    <form class="row" action="{{ route('admin.pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $pet->name }}">
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
                                            <option value="{{ $id }}" {{$pet->species_id == $id ? 'selected' : ''}}> {{ $name }}</option>
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
                                            <option value="{{ $id }}" {{$pet->user_id == $id ? 'selected' : ''}}> {{ $name }}</option>
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
                                            <option value="{{ $id }}" {{$pet->race_id == $id ? 'selected' : ''}}> {{ $name }}</option>
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
                                        <option value="{{ $i }}" {{$pet->age == $i ? 'selected' : ''}}> {{ $i }}. month(s)</option>
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
                                    <option value="{{ App\Enums\Pet::FEMALE }}" {{$pet->sex == App\Enums\Pet::FEMALE ? 'selected' : ''}}>F</option>
                                    <option value="{{ App\Enums\Pet::MALE }}" {{$pet->sex == App\Enums\Pet::MALE ? 'selected' : ''}}>M</option>
                                </select>
                                @error('sexe')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" name="color" class="form-control" value="{{$pet->color}}">
                                @error('color')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">About</label>
                                <textarea name="about" class="form-control">{{ $pet->about }}</textarea>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Apercu</label>
                                @if(!empty($pet->images) && is_array($pet->images))
                                    @foreach ($pet->images as $img)
                                        <img src="{{ asset('storage/'.$img)}}" width="75" height="75">
                                    @endforeach
                                @endif
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
