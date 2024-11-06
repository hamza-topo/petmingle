@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title['fr'] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <section class="breadcrumbs-area breadcrumb-bg">
        <div class="container">
            <h1 class="title wow fadeInUp" data-wow-delay="0.0s"
                style="visibility: visible; animation-delay: 0s; animation-name: fadeInUp;">News</h1>
            <div class="breadcrumb-text">
                <nav aria-label="breadcrumb" class="breadcrumb-nav wow fadeInUp" data-wow-delay="0.1s"
                    style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                    <ul class="breadcrumb listing">
                        <li class="breadcrumb-item single-list"><a href="index.html" class="single">Home</a></li>
                        <li class="breadcrumb-item single-list" aria-current="page"><a href="javascript:void(0)"
                                class="single active">News</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
    <section class="news-area section-padding2">
        <div class="container">
            <x-web.home.blog.side-item :blogs="$blogs->take(4)"/>
            <x-web.home.blog.side-section :blogs="$blogs->skip(4)->take(6)"/>
        </div>
    </section>
@endsection
