@extends('adminlte::page')

@section('title', __('Detail Pet'))

@section('content_header')
    <h1>{{ __('Update Pet') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"> 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" disabled readonly value="{{ $pet->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Species</label>
                                <select class="form-control" disabled readonly >
                                    @empty(!$species)
                                        @foreach ($species as $id => $name)
                                            <option value="{{ $id }}" {{$pet->species_id == $id ? 'selected' : ''}}> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <select class="form-control" disabled readonly>
                                    @empty(!$users)
                                        @foreach ($users as $id => $name)
                                            <option value="{{ $id }}" {{$pet->user_id == $id ? 'selected' : ''}}> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Races</label>
                                <select class="form-control" disabled readonly>
                                    @empty(!$races)
                                        @foreach ($races as $id => $name)
                                            <option value="{{ $id }}" {{$pet->race_id == $id ? 'selected' : ''}}> {{ $name }}</option>
                                        @endforeach
                                    @endempty
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <select class="form-control" disabled readonly>
                                    @for ($i = App\Enums\App::MIN_AGE; $i < App\Enums\App::MAX_AGE; $i++)
                                        <option value="{{ $i }}" {{$pet->age == $i ? 'selected' : ''}}> {{ $i }}. month(s)</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sexe</label>
                                <select class="form-control" disabled readonly>
                                    <option value="{{ App\Enums\Pet::FEMALE }}" {{$pet->sex == App\Enums\Pet::FEMALE ? 'selected' : ''}}>F</option>
                                    <option value="{{ App\Enums\Pet::MALE }}" {{$pet->sex == App\Enums\Pet::MALE ? 'selected' : ''}}>M</option>
                                </select>
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <input type="color" class="form-control" disabled readonly value="{{$pet->color}}">
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">About</label>
                                <textarea class="form-control" disabled readonly>{{ $pet->about }}</textarea>
                               
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
                            <a href="{{ route('admin.pets.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
