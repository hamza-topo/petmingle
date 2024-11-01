@extends('adminlte::page')

@section('title', __('New SEO Settings'))

@section('content_header')
    <h1>{{ __('New SEO Settings') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.seo.store') }}" method="POST">
                        @csrf
                        <div class="col-md-6">
                            <div class="mb-3">
                                @foreach ($langs as $key => $lang)
                                    <label class="form-label">Title : {{ $key }}</label>
                                    <input type="text" class="form-control" name="title.{{ $lang }}"
                                        value="{{ old('title.' . $lang) }}">
                                @endforeach
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                @foreach ($langs as $key => $lang)
                                <label class="form-label">Meta : {{ $lang }}</label>
                                <input class="form-control" name="meta[description][{{ $lang }}]" type="text"
                                    value="{{ old('meta.description.'. $lang ) }}">    
                                @endforeach
                                @error('meta')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">La page</label>
                                <select class="form-control" name="key">
                                    @foreach ($pages as $page)
                                        <option value="{{$page}}">{{ $page }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
