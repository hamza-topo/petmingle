@extends('emails.layouts.master')
@section('title')
<title>__('Matched with') {{ $pet->name ?? '' }}</title>
@endsection

@section('body')
<div class="container">
    <span class="emoji">&#x1F389;</span>
    <h1>You're Matched with {{ $pet->name ?? '' }}!</h1>
    <p>Congratulations! 🎉 You and {{ $pet->name ?? '' }} are now connected. Get ready for exciting experiences
        together.</p>
    <a href="#" class="button">Start Chatting &#x1F609;</a>
</div>
@endsection
