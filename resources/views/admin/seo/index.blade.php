@extends('adminlte::page')

@section('title', __('Manage SEO'))

@section('content_header')
    <h1>{{ __('Manage SEO') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.seo.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Page</th>
                            <th scope="col">Title</th>
                            <th scope="col">Meta</th>
                            <th scope="col" align="right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @empty(!$seos)
                            @foreach ($seos as $page)
                                <tr>
                                    <th scope="row">{{ $page->id }}</th>
                                    <td>{{ $page->key }}</td>
                                    <td>{{ $page->title[App\Enums\App::LOCALES['FR']] ?? '' }}</td>
                                    <td>{{ $page->meta['description'][App\Enums\App::LOCALES['FR']] ?? '' }}</td>
                                    <td align="right" class="d-flex justify-content-end">
                                        <a href="{{ route('admin.seo.show', $page->id) }}" type="button"
                                            class="btn btn-success me-2">
                                            <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                        </a>
                                        <a href="{{ route('admin.seo.edit', $page->id) }}" type="button"
                                            class="btn btn-primary me-2">
                                            <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                        </a>
                                        <form action="{{ route('admin.species.destroy', $page->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="ri ri-delete-bin-5-line"></i> <!-- Remix Icon for delete -->
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endempty
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{-- {{ $seos->links('pagination::bootstrap-4') }} --}}
                </div>
            </div>
        </div>
    </div>
@stop
