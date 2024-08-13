@extends('layouts.admin.master')

@section('content')
    <div class="row" style="margin-top: 10%">
        <div class="card">
            <h5 class="card-title fw-semibold mb-4">{{ __('Races') }}</h5>
            <a href="{{ route('admin.races.create') }}" type="button" class="btn btn-success"><i class="ti ti-plus"></i></a>
            <div class="card-body">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('name') }}</th>
                            <th scope="col">{{ __('species') }}</th>
                            <th scope="col" align="right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($races as $race)
                            <tr>
                                <th scope="row">{{ $race->id }}</th>
                                <td>{{ $race->name }}</td>
                                <td>{{ $race->species->name ?? 'walou' }}</td>
                                <td align="right">
                                    <a href="{{ route('admin.races.show', $race->id) }}" type="button"
                                        class="btn btn-success"><i class="ti ti-eye"></i></a>
                                    <a href="{{ route('admin.races.edit', $race->id) }}" type="button"
                                        class="btn btn-primary"><i class="ti ti-pencil"></i></a>
                                    <button type="button" class="btn btn-danger"><i class="ti ti-trash"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $races->links() }}
            </div>
        </div>
    </div>
@endsection
