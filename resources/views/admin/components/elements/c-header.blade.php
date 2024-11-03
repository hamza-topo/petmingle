@extends('adminlte::page')

@section('title', __('New SEO Settings'))

@section('content_header')
    <h1>{{ __('New SEO Settings') }}</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        {{-- @foreach ($langs as $lang)
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-" data-toggle="pill"
                                    href="#custom-tabs-one-" role="tab"
                                    aria-controls="custom-tabs-one-"
                                    aria-selected="false"></a>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <form class="row" action="{{ route('admin.components.store') }}" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="name" value="{{ $componentName }}" />
                            <div class="tab-pane fade col-12 active show" id="custom-tabs-one-" role="tabpanel"
                                aria-labelledby="custom-tabs-">
                                @csrf
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email Text: </label>
                                        <input class="form-control" name="content[email][text]" type="text"
                                            value="{{ $component->content['email']['text'] ?? '' }}">
                                        @error('meta')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email Value : </label>
                                        <input class="form-control" name="content[email][value]" type="email"
                                            value="{{ $component->content['email']['value'] ?? '' }}">
                                        @error('content')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Text : </label>
                                        <input class="form-control" name="content[phone][text]" type="text"
                                            value="{{ $component->content['phone']['text'] ?? '' }}">
                                        @error('content')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Value : </label>
                                        <input class="form-control" name="content[phone][value]" type="phone"
                                            value="{{ $component->content['phone']['value'] ?? '' }}">
                                        @error('content')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Media</label>
                                    <input class="form-control" name="media" type="file">
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
