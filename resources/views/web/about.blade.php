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
            <div class="tour-details-container">
                <div class="container">
                    <!-- Details Heading -->
                    <div class="details-heading">
                        <div class="d-flex flex-column">
                            <h4 class="title">{{ $blog->title[app()->getLocale()] ?? '' }}</h4>
                            <div class="d-flex flex-wrap align-items-center gap-30 mt-16">
                                {{-- <div class="location">
                                    <i class="ri-map-pin-line"></i>
                                    <div class="name">Bangkok, Thailand</div>
                                </div>
                                <div class="divider"></div>
                                <div class="d-flex align-items-center flex-wrap gap-20">
                                    <div class="count">
                                        <i class="ri-time-line"></i>
                                        <p class="pera">3 Days 2 Night</p>
                                    </div>
                                    <div class="count">
                                        <i class="ri-user-line"></i>
                                        <p class="pera">2 Person</p>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        {{-- <div class="price-review">
                            <div class="d-flex gap-10 align-items-end">
                                <p class="light-pera">From</p>
                                <p class="pera">$95</p>
                            </div>
                            <div class="rating">
                                <i class="ri-star-s-fill"></i>
                                <p class="pera">4.7 (20 Reviews)</p>
                            </div>
                        </div> --}}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
