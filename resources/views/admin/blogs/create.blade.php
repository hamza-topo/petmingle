@extends('adminlte::page')

@section('title', __('New Blog'))

@section('content_header')
    <h1>{{ __('New Blog') }}</h1>
@stop
<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row" action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-4 row">
                            <div class="mb-3">
                                <label class="form-label">Mark as draft:</label>
                                <input type="checkbox" class="form-control col-sm-6" name="active"
                                    value="">
                                @error('active')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4 row">
                            <div class="mb-3">
                                <label class="form-label">Media:</label>
                                <input type="file" name="media" multiple class="fomr-control col-sm-6" />
                                @error('file')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                                
                            </div>
                        </div>
                        <div class="col-md-4 row">
                            <div class="mb-3">
                                <label class="form-label">Publish It At:</label>
                                <input type="datetime" name="publish_it_at" class="fomr-control col-sm-6" />
                                @error('publish_it_at')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                @enderror
                                
                            </div>
                        </div>
                        @foreach ($langs as $lang)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title {{ $lang }}:</label>
                                    <input type="text" class="form-control" name="title[{{ $lang }}]"
                                        value="{{ old('title.' . $lang) }}">
                                    @error('title')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Slug {{ $lang }}:</label>
                                    <input type="text" class="form-control" name="slug[{{ $lang }}]"
                                        value="{{ old('slug.' . $lang) }}">
                                    @error('slug')
                                        <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                        <input type="file" name="media" multiple class="fomr-control" />
                        <textarea id="editor-en" name="content[en]"></textarea>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Custom Upload Adapter
        class MyUploadAdapter {
            constructor(loader) {
                // CKEditor 5's file loader instance
                this.loader = loader;
            }

            // Start the upload process
            upload() {
                return this.loader.file
                    .then(file => new Promise((resolve, reject) => {
                        const formData = new FormData();
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');
                        formData.append('upload', file);
                        formData.append('_token', csrfToken);

                        // Send the request to the server
                        fetch('{{ route('admin.blogs.upload') }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.url) {
                                    // Resolve with the URL of the uploaded image
                                    resolve({
                                        default: result.url
                                    });
                                } else {
                                    reject(result.message || 'Upload failed');
                                }
                            })
                            .catch(error => {
                                reject(error);
                            });
                    }));
            }

            // Aborts the upload process
            abort() {
                // Can implement abort logic if needed
            }
        }

        // Custom Upload Adapter plugin
        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
        }

        // Initialize CKEditor with the custom upload adapter
        ClassicEditor
            .create(document.querySelector('#editor-en'), {
                extraPlugins: [MyCustomUploadAdapterPlugin],
            })
            .then(editor => {
                console.log('CKEditor initialized with custom upload adapter:', editor);
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    </script>
@endsection
