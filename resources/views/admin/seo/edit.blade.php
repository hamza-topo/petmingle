@extends('adminlte::page')

@section('title', __('Edit SEO Settings'))

@section('content_header')
    <h1>{{ __('Edit SEO Settings') }}: {{ $page->key ?? '' }}</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.seo.update', $page->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="mb-3">
                                @foreach ($langs as $key => $lang)
                                    <label class="form-label">Title : {{ $key }}</label>
                                    <input type="text" class="form-control" name="title[{{ $lang }}]"
                                        value="{{ $page->title[$lang] ?? '' }}">
                                @endforeach
                                @error('name')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                @foreach ($langs as $key => $lang)
                                    <label class="form-label">Meta : {{ $lang }}</label>
                                    <input class="form-control" name="meta[description][{{ $lang }}]" type="text"
                                        value="{{ $page->meta['description'][$lang] ?? '' }}">
                                @endforeach
                                @error('meta')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if(empty($seo->key))
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">La page</label>
                                <select class="form-control" name="key">
                                    @foreach ($pages as $curpage)
                                        <option value="{{ $curpage }}" {{ $page->key === $curpage ? 'selected' : '' }}>
                                            {{ $curpage }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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
