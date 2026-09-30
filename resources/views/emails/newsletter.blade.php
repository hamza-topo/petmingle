@extends('emails.layouts.master')

@section('title')
    <title>{{ $newsLetter->title }}</title>
@endsection

@section('body')
    <table
        width="95%"
        border="0"
        align="center"
        cellpadding="0"
        cellspacing="0"
        style="max-width:670px; background:#fff; border-radius:3px; text-align:center;
               box-shadow:0 6px 18px 0 rgba(0,0,0,.06);"
    >
        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>

        <tr>
            <td style="padding:0 35px;">
                <h1
                    style="color:#1e1e2d; font-weight:500; margin:0;
                           font-size:28px; font-family:'Rubik',sans-serif;"
                >
                    {{ $newsLetter->title }}
                </h1>

                <div
                    style="font-size:15px; color:#455056;
                           margin:18px 0 0; line-height:24px;"
                >
                    {!! nl2br(e($newsLetter->content)) !!}
                </div>
            </td>
        </tr>

        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>
    </table>
@endsection