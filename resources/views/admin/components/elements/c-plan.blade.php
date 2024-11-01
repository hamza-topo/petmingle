@extends('adminlte::page')

@section('title', __('Update Component') . $componentName ?? '')

@section('content_header')
    <h1>{{ __('Update Component') . $componentName ?? '' }}</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        @foreach ($langs as $lang)
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-{{ $lang }}" data-toggle="pill"
                                    href="#custom-tabs-one-{{ $lang }}" role="tab"
                                    aria-controls="custom-tabs-one-{{ $lang }}"
                                    aria-selected="false">{{ $lang }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <form class="row" action="{{ route('admin.components.store') }}" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="name" value="{{ $componentName }}" />
                            @foreach ($langs as $lang)
                                <div class="tab-pane fade col-12 {{ $lang === 'en' ? 'active show' : '' }}"
                                    id="custom-tabs-one-{{ $lang }}" role="tabpanel"
                                    aria-labelledby="custom-tabs-{{ $lang }}">
                                    @csrf
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Title : {{ $lang }}</label>
                                            <input type="text" class="form-control" name="title[{{ $lang }}]"
                                                value="{{ $component->title[$lang] ?? '' }}">
                                            @error('name')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
