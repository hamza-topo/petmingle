@extends('adminlte::page')

@section('title', __('Adoptions'))

@section('content_header')
    <h1>{{ __('Adoptions') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.adoptions.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">From</th>
                            <th scope="col">Race</th>
                            <th scope="col">Adopted By</th>
                            <th scope="col" align="right" style="display: flex;justify-content:end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adoptions as $ad)
                            <tr>
                                <th scope="row">{{ $ad->id }}</th>
                                <td>{{ $ad->owner?->name }}</td>
                                <td>{{ $ad->pet?->name }}</td>
                                <td>{{ $ad->newOwner?->name }}</td>
                                <td align="right" class="d-flex justify-content-end">
                                    <a href="{{ route('admin.adoptions.show', $ad->id) }}" type="button" class="btn btn-success me-2">
                                        <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                    </a>
                                    <a href="{{ route('admin.adoptions.edit', $ad->id) }}" type="button" class="btn btn-primary me-2">
                                        <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                    </a>
                                    <form action="{{ route('admin.adoptions.destroy', $ad->id) }}" method="POST" style="display:inline;">
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
                    {{ $adoptions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop

