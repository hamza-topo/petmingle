@extends('adminlte::page')

@section('title', __('Manage Blogs'))

@section('content_header')
    <h1>{{ __('Manage Blogs') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.blogs.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <a href="{{ route('admin.blogs.index') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    Blogs
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Slug</th>
                            <th scope="col">scheduled</th>
                            <th scope="col">Title</th>
                            <th scope="col">Short description</th>
                            <th scope="col">Media</th>
                            <th scope="col" align="right" style="display: flex;justify-content:end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @empty(!$blogs)
                            @foreach ($blogs as $blog)
                                @php
                                    $img = !empty($blog->media[0]) ? $blog->media[0] : '';
                                @endphp
                                @php
                                    $blogUrl =
                                        $blog->slug[app()->getLocale()] ?? slugify($blog->title[app()->getLocale()]);
                                @endphp
                                <tr>
                                    <th scope="row">{{ $blog->slug[app()->getLocale()] ?? '' }}</th>
                                    <td>{{ $blog->title[app()->getLocale()] ?? '' }}</td>
                                    <td>{{ $blog->publish_it_at }}</td>
                                    <td>{{ generateTextPreview(strip_tags($blog->content[app()->getLocale()] ?? '')) }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $img) }}"
                                            alt="{{ $blog->title[app()->getLocale()] ?? '' }}" width='75'>
                                    </td>
                                    <td align="right" class="d-flex justify-content-end">
                                        @if (!empty($blog->id))
                                            <a href="{{ route('blogs.read', $blogUrl) }}" target="_blank" type="button"
                                                class="btn btn-success me-2">
                                                <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.blogs.edit', \strtolower($blog->id)) }}" type="button"
                                            class="btn btn-primary me-2">
                                            <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                        </a>
                                        @if (!empty($blog->id))
                                            <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="ri ri-delete-bin-5-line"></i> <!-- Remix Icon for delete -->
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endempty
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                </div>
            </div>
        </div>
    </div>
@stop
