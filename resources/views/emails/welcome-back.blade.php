@extends('emails.layouts.master')
@section('title')
    <title>{{ __('Welcome Back!') }}. {{ $user->name ?? '' }}</title>
@endsection

@section('body')
    <div style="justify-content: center;align-items: center;display: flex;margin-bottom: -6%;">
        <div class="bullet bullet1"
            style="box-shadow: 0 0 4px 0px;background-size: cover;border-radius: 50%;width: 75px;height: 75px;background-image: url({{ asset('storage/' . $user->avatar ?? '') }})">
            <img src="{{ asset('storage/' . $user->avatar ?? '') }}" width="75" height="75" style="border-radius: 50%">
        </div>
    </div>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
        style="max-width:670px; background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding:0 35px;">
                <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">
                    {{ __('We are happy to See You Again!') }} {{ $user->name ?? '' }}
                </h1>
                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                    {{ __('We hope this message finds you in great spirits. It brings us immense joy to inform you that your ') . config('app.name') }}
                    {{ __('account has been reactivated! 🎉 Welcome back to our paws-itively wonderful community!') }}</br>

                    {{ __('Your return means a lot to us, and we\'re thrilled to have you back in the Pet-Mingle family. Our furry friends and fellow pet enthusiasts have missed your presence, and we\'re excited to see you reconnect with the vibrant community.') }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>
    </table>
@endsection
