@extends('adminlte::page')

@section('title', __('Species'))

@section('content_header')
    <h1>{{ __('Species') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.pets.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Owner</th>
                            <th scope="col">Species</th>
                            <th scope="col">Race</th>
                            <th scope="col">Name</th>
                            <th scope="col">Age</th>
                            <th scope="col" align="right" style="display: flex;justify-content:end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pets as $pet)
                            <tr>
                                <th scope="row">{{ $pet->id }}</th>
                                <td>{{ $pet->owner?->name }}</td>
                                <td>{{ $pet->species?->name }}</td>
                                <td>{{ $pet->race?->name }}</td>
                                <td>{{ $pet->name }}</td>
                                <td>{{ $pet->age }} .ms</td>
                                <td align="right" class="d-flex justify-content-end">
                                    <a href="{{ route('admin.pets.show', $pet->id) }}" type="button" class="btn btn-success me-2">
                                        <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                    </a>
                                    <a href="{{ route('admin.pets.edit', $pet->id) }}" type="button" class="btn btn-primary me-2">
                                        <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                    </a>
                                    <form action="{{ route('admin.pets.destroy', $pet->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="ri ri-delete-bin-5-line"></i> <!-- Remix Icon for delete -->
                                        </button>
                                    </form>
                                </td>                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $pets->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop

