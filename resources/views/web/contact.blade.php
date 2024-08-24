@extends('layouts.app')
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
                                        <input class="custom-form" name="name" type="text"
                                            placeholder="Enter your name">
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="custom-form" name="email" type="email"
                                            placeholder="Enter your email">
                                    </div>
                                    {{-- <div class="col-sm-6">
                                        <input class="custom-form" type="text" placeholder="Your Phone">
                                    </div> --}}
                                    <div class="col-sm-6">
                                        <input class="custom-form" name="subject" type="text"
                                            placeholder="Select subject">
                                    </div>
                                    <div class="col-sm-12">
                                        <textarea class="custom-form-textarea" name="message" id="exampleFormControlTextarea1" rows="3"
                                            placeholder="Enter your message..."></textarea>
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
