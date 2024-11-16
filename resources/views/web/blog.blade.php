@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title[app()->getLocale()] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <section class="news-area section-padding2">
        <div class="container">
            {{-- <x-web.home.blog.side-item :blogs="$blogs->take(4)"/> --}}
            <div class="row g-4">
                <div class="col-xl-5 col-lg-6">
                    <div class="section-title mx-430 mb-30 w-md-100">
                        <h4 class="title">
                            {{ $blogs->first()->title[app()->getLocale()] ?? '' }}
                        </h4>
                        {!! generateTextPreview($blogs->first()->content[app()->getLocale()] ?? '') !!}

                        <div class="section-button mt-27 d-inline-block">
                            @if(!empty($blogs->first()->slug[app()->getLocale()]))
                            <a href="{{ route('blogs.read', $blogs->first()->slug[app()->getLocale()]) }}"
                                class="btn-primary-icon-sm radius-20">
                                <p class="pera mt-0">Learn More</p>
                                <i class="ri-arrow-right-up-line"></i>
                            </a>
                            @endif
                        </div>
                        <div class="about-imp-link mt-40">
                            <div class="icon">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="content">
                                <p class="pera font-16">
                                    <span class="font-700">published </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                    <div class="about-count-section about-count-before-bg">
                        <div class="banner">
                            @php
                                $img = !empty($blogs->first()->media[0]) ? $blogs->first()->media[0] : '';
                            @endphp
                            <img src="{{ asset('storage/' . $img) }}"
                                alt="{{ $blogs->first()->title[app()->getLocale()] ?? '' }}">
                        </div>
                        <div class="all-count-list">
                            <div class="details">
                                <p class="pera">{{ readTimesStamps($blogs->first()->created_at ?? Illuminate\Support\Carbon::now()) }}</p>
                            </div>
                            <div class="divider"></div>
                            <div class="details">
                                <p class="pera">{{ $blogs->first()->author->name ?? '' }}</p>
                            </div>
                            <div class="divider"></div>
                            <div class="details">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-web.home.blog.side-section :blogs="$blogs->skip(1)->take(6)" />
                {{$blogs->links('vendor.pagination.default')}}
        </div>
    </section>
@endsection
