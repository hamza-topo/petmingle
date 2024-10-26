@extends('adminlte::page')

@section('title', __('Manage Components Content'))

@section('content_header')
    <h1>{{ __('Manage Components Content') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.components.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Title</th>
                            <th scope="col">Contenu</th>
                            <th scope="col">Media</th>
                            <th scope="col" align="right" style="display: flex;justify-content:end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($components as $component)
                            <tr>
                                <th scope="row">{{ $component->id ?? '' }}</th>
                                <td>{{ $component->name }}</td>
                                <td>{{ $component->title['fr'] ?? '' }}</td>
                                <td>{{ $component->contenu['fr'] ?? '' }}</td>
                                <td>
                                    <video width="75" class="hero-slider-video video-cover"
                                        poster="{{ asset('assets/images/videos/pets-hero.mp4') }}" loop=""
                                        autoplay="" muted="">
                                        <source src="{{ asset('storage/' . $component->media ?? '') }}" type="video/mp4">
                                    </video>
                                </td>
                                <td align="right" class="d-flex justify-content-end">
                                    @if (!empty($component->id))
                                        <a href="{{ route('admin.components.show', $component->id) }}" type="button"
                                            class="btn btn-success me-2">
                                            <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                        </a>
                                        <a href="{{ route('admin.components.edit', $component->id) }}" type="button"
                                            class="btn btn-primary me-2">
                                            <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                        </a>
                                        <form action="{{ route('admin.components.destroy', $component->id) }}"
                                            method="POST" style="display:inline;">
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
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{-- {{ $components->links('pagination::bootstrap-4') }} --}}
                </div>
            </div>
        </div>
    </div>
@stop
