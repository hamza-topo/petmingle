@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('Pet Creation') }}</h5>
            <div class="card-body">
                <form class="row col-sm-12 col-md-12 col-lg-12">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Name</label>
                            <input type="text" class="form-control">
                            @error('name')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Races</label>
                            <select class="form-control" name="race_id">
                                <option value="">{{ __('choose a race') }}</option>
                            </select>
                            @error('race_id')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">{{ __('Color') }}</label>
                            <select class="form-control" name="sexe">
                                <option value="">{{ __('Choose the Color') }}</option>
                                <option value="">Male</option>
                                <option value="">Female</option>
                            </select>
                            @error('sexe')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Species</label>
                            <select class="form-control" name="species_id">
                                <option value="">{{ __('choose a species') }}</option>
                                @foreach ($species as $key => $specy)
                                    <option value="{{ $key }}">{{ $specy }}</option>
                                @endforeach
                            </select>
                            @error('species_id')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">{{ __('Gender') }}</label>
                            <select class="form-control" name="sexe">
                                <option value="">{{ __('Choose the Gender') }}</option>
                                <option value="">Male</option>
                                <option value="">Female</option>
                            </select>
                            @error('sexe')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Age</label>
                            <select class="form-control" name="age">
                                <option value="">{{ __('choose an Age') }}</option>
                            </select>
                            @error('age')
                                <div class="form-texttext-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">About</label>
                        <textarea class="form-control" name="about"></textarea>
                        @error('about')
                            <div class="form-texttext-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-group row">
                            <!--begin::Label-->
                            <label class="col-lg-2 col-form-label text-lg-right">Upload Files:</label>
                            <!--end::Label-->

                            <!--begin::Col-->
                            <div class="col-lg-10">
                                <!--begin::Dropzone-->
                                <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                    <!--begin::Controls-->
                                    <div class="dropzone-panel mb-lg-0 mb-2">
                                        <a class="dropzone-select btn btn-sm btn-primary me-2">Attach files</a>
                                        <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload All</a>
                                        <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remove All</a>
                                    </div>
                                    <!--end::Controls-->

                                    <!--begin::Items-->
                                    <div class="dropzone-items wm-200px">
                                        <div class="dropzone-item" style="display:none">
                                            <!--begin::File-->
                                            <div class="dropzone-file">
                                                <div class="dropzone-filename" title="some_image_file_name.jpg">
                                                    <span data-dz-name>some_image_file_name.jpg</span>
                                                    <strong>(<span data-dz-size>340kb</span>)</strong>
                                                </div>

                                                <div class="dropzone-error" data-dz-errormessage></div>
                                            </div>
                                            <!--end::File-->

                                            <!--begin::Progress-->
                                            <div class="dropzone-progress">
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                                                        data-dz-uploadprogress>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Progress-->

                                            <!--begin::Toolbar-->
                                            <div class="dropzone-toolbar">
                                                <span class="dropzone-start"><i class="bi bi-play-fill fs-3"></i></span>
                                                <span class="dropzone-cancel" data-dz-remove style="display: none;"><i
                                                        class="bi bi-x fs-3"></i></span>
                                                <span class="dropzone-delete" data-dz-remove><i
                                                        class="bi bi-x fs-1"></i></span>
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Dropzone-->

                                <!--begin::Hint-->
                                <span class="form-text text-muted">Max file size is 1MB and max number of files is
                                    5.</span>
                                <!--end::Hint-->
                            </div>
                            <!--end::Col-->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
