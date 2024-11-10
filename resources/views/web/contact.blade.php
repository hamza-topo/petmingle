@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title[app()->getLocale()] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <x-web.layout.bread-crumb />
    <section class="contact-area section-padding2">
        <div class="position-relative contact-bg-before">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7 col-lg-9">
                        <div class="contact-card">
                            <h4 class="contact-heading">{{ __('Feel Free to Write us Anytime') }}</h4>
                            <form method="post" action="{{ route('contact.store') }}" class="contact-form">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <input class="custom-form @error('name') is-invalid @enderror" name="name"
                                            type="text" placeholder="Enter your name" value="{{ old('name') }}">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="custom-form @error('email') is-invalid @enderror" name="email"
                                            type="email" placeholder="Enter your email" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    {{-- <div class="col-sm-6">
                                    <input class="custom-form" type="text" placeholder="Your Phone">
                                </div> --}}
                                    <div class="col-sm-6">
                                        <input class="custom-form @error('subject') is-invalid @enderror" name="subject"
                                            type="text" placeholder="Enter subject" value="{{ old('subject') }}">
                                        @error('subject')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <textarea class="custom-form-textarea @error('message') is-invalid @enderror" name="message" rows="3"
                                            placeholder="Enter your message">{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-40">
                                    <button type="submit" class="send-btn">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
