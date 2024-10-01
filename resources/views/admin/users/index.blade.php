@extends('adminlte::page')

@section('title', __('Users'))

@section('content_header')
    <h1>{{ __('Users') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <a href="{{ route('admin.users.create') }}" type="button" class="btn btn-success btn-sm top-0 end-0 m-2">
                    <i class="ri ri-add-line"></i> <!-- Remix Icon for add -->
                </a>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col" align="right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <th scope="row">{{ $user->id }}</th>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{ \App\Enums\User::options()[$user->is_admin] }}
                                </td>
                                <td align="right" class="d-flex justify-content-end">
                                    <a href="{{ route('admin.users.show', $user->id) }}" type="button" class="btn btn-success me-2">
                                        <i class="ri ri-eye-line"></i> <!-- Remix Icon for view -->
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" type="button" class="btn btn-primary me-2">
                                        <i class="ri ri-pencil-line"></i> <!-- Remix Icon for edit -->
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
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
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop
