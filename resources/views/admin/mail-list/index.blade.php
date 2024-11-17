@extends('adminlte::page')

@section('title', __('Mail List'))

@section('content_header')
    <h1>{{ __('Mail List') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="card col-12">
            <div class="card-body">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">email</th>
                            <th scope="col">created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($mailList))
                            @foreach ($mailList as $mail)
                                <tr>
                                    <th scope="row">{{ $mail->email }}</th>
                                    <td>{{ $mail->created_at }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="d-flex justify-content-center">
                    {{ $mailList->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop
