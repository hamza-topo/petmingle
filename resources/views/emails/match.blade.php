@extends('emails.layouts.master')
@section('title')
    <title>hello</title>
@endsection

@section('body')
    <div style="justify-content: center;align-items: center;display: flex;margin-bottom: -6%;">
        <div class="bullet bullet1"
            style="box-shadow: 0 0 4px 0px;background-size: cover;border-radius: 50%;width: 75px;height: 75px;background-image: url({{ asset('storage/' . $pet->images[0] ?? '') }})">
            <img src="{{ asset('storage/' . $fromPet->images[0] ?? '') }}" width="75" height="75" style="border-radius: 50%">
        </div>
        <div class="bullet bullet2"
            style="box-shadow: 0 0 4px 0px;background-size: cover;border-radius: 50%;width: 75px;height: 75px;margin-left: -5%; background-image: url()">
            <img src="{{ asset('storage/' . $pet->images[0] ?? '') }}" width="75" height="75" style="border-radius: 50%">
        </div>
    </div>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
        style="max-width:670px; background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding:0 35px;">
                <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">🎉
                    You're Matched with {{ $pet->name ?? '' }}! 🎉</h1>
                <p style="font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;">
                    Congratulations! 🎉 You and {{ $pet->name ?? '' }} are now connected. Get ready for exciting experiences
                    together.
                </p>
            </td>
        </tr>
        <tr>
            <td style="height:40px;">&nbsp;</td>
        </tr>
    </table>
@endsection
