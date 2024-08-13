@extends('layouts.admin.master')

@section('content')
    <div class="row" style="margin-top: 10%">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('Species') }}</h5>
            <a href="{{ route('admin.species.create') }}" type="button" class="btn btn-success"><i class="ti ti-plus"></i></a>
            <div class="card-body">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">name</th>
                            <th scope="col">description</th>
                            <th scope="col" align="right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($species as $specy)
                            <tr>
                                <th scope="row">{{ $specy->id }}</th>
                                <td>{{ $specy->name }}</td>
                                <td>{{ $specy->description }}</td>
                                <td align="right">
                                    <a href="{{ route('admin.species.show', $specy->id) }}" type="button"
                                        class="btn btn-success"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.species.edit', $specy->id) }}" type="button"
                                        class="btn btn-primary"><i class="ti ti-pencil"></i></a>
                                    <button type="button" class="btn btn-danger"><i class="ti ti-trash"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $species->links() }}
            </div>
        </div>
    </div>
@endsection
