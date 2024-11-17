@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection
@section('title')
    {{ $seo->title[app()->getLocale()] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <section class="tour-details-section section-padding2">
        <div class="tour-details-area">
            <!-- Details Banner Slider -->
            <div class="tour-details-banner">
            </div>
            <!-- / Slider-->
            <div class="tour-details-container">
                <div class="container">
                    <!-- Details Heading -->
                    <div class="details-heading">
                        <div class="d-flex flex-column">
                            <h4 class="title">{{ $blog->title[app()->getLocale()] ?? '' }}</h4>
                            <div class="d-flex flex-wrap align-items-center gap-30 mt-16">
                            </div>
                        </div>
                    </div>
                    <!-- / Details Heading -->
                    <div class="mt-30">
                        <div class="row g-4">
                            <!-- Left content -->
                            <div class="col-xl-8 col-lg-7">
                                <!-- About tour -->
                                <div class="tour-details-content">
                                    <h4 class="title">{{ $blog->title[app()->getLocale()] }}</h4>
                                    {!! $blog->content[app()->getLocale() ?? ''] !!}
                                </div>
                                <!-- / About tour -->
                            </div>
                            <!-- Right content -->
                            <div class="col-xl-4 col-lg-5">
                                <div class="date-travel-card position-sticky top-0">
                                    <div class="price-review">
                                        <div class="d-flex gap-10 align-items-end">
                                            <p class="light-pera">Stay Connected</p>
                                            <p class="pera">Help Us Grow!</p>
                                        </div>
                                    </div>
                                    <h4 class="heading-card">Subscribe to our newsLetter</h4>
                                    <div class="subscribe-wraper">
                                        <form action="{{ route('news-letter.subscribe') }}" method="post">
                                            @csrf
                                            <input class="footer-search" type="email" name="email"
                                                placeholder="Enter Your Email">
                                            <div class="mt-30">
                                                <button type="submit" class="send-btn w-100">Subscribe</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="footer bg-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <x-web.home.blog.side-section :blogs="$randoms" />
            </div>
        </div>
    </section>
@endsection
