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
                            </div>
                        </div>
                    </div>
                    <!-- / Details Heading -->

                    <div class="mt-30">
                        <div class="row g-4">
                            <!-- Left content -->
                            <div class="col-xl-8 col-lg-7">
                                <!-- Privacy Policy -->
                                <div class="tour-details-content">
                                    <h4 class="title">{{ $blog->title[app()->getLocale()] }}</h4>
                                    {!! $blog->content[app()->getLocale() ?? ''] !!}
                                </div>
                                <!-- / Privacy Policy -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
