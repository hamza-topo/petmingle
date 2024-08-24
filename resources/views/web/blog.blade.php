@extends('layouts.app')
@section('main')
    <x-web.layout.bread-crumb />
    <section class="news-area section-padding2">
        <div class="container">
            <div class="row g-4 mb-60">
                <div class="col-xl-7 col-lg-7">
                    <div class="tab-content" id="v-pills-tabContent-two">
                        <div class="tab-pane  fade show active" id="pills-news-one" role="tabpanel"
                            aria-labelledby="pills-news-one">
                            <div class="about-banner imgEffect4">
                                <img src="assets/images/news/news-banner.png" alt="travello">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-news-two" role="tabpanel" aria-labelledby="pills-news-two">
                            <div class="about-banner imgEffect4">
                                <img src="assets/images/news/banner-1.png" alt="travello">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-news-three" role="tabpanel" aria-labelledby="pills-news-three">
                            <div class="about-banner imgEffect4">
                                <img src="assets/images/news/banner-2.png" alt="travello">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="all-contents" id="v-pills-tab-two" role="tablist" aria-orientation="vertical">
                        <div class="news-content active" id="pills-news-one-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-news-one" role="tab" aria-controls="pills-news-one"
                            aria-selected="true">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title">
                                <a href="javascript:void(0)">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="author-img">
                                        <img src="assets/images/news/news-1.jpeg" alt="travello">
                                    </div>
                                    <p class="name">Crish Jorden</p>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                        <div class="news-content" id="pills-news-two-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-news-two" role="tab" aria-controls="pills-news-two"
                            aria-selected="true">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title">
                                <a href="javascript:void(0)">A Good Traveler Has no Fixed Plans and is Not Intent on
                                    Arriving.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="author-img">
                                        <img src="assets/images/news/news-2.jpeg" alt="travello">
                                    </div>
                                    <p class="name">David Warner</p>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                        <div class="news-content" id="pills-news-three-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-news-three" role="tab" aria-controls="pills-news-three"
                            aria-selected="true">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title">
                                <a href="javascript:void(0)">We Travel, Some of us Forever, to Seek Other States,
                                    Other Lives, Other Souls.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="author-img">
                                        <img src="assets/images/news/news-3.jpeg" alt="travello">
                                    </div>
                                    <p class="name">David Malan</p>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-4.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-5.png" alt="travello">

                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-6.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-7.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-8.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-9.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-10.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-11.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <article class="news-card-two">
                        <figure class="news-banner-two imgEffect">
                            <a href="news-details.html">
                                <img src="assets/images/news/news-12.png" alt="travello">
                            </a>
                        </figure>
                        <div class="news-content">
                            <div class="heading">
                                <span class="heading-pera">Tour Guide</span>
                            </div>
                            <h4 class="title line-clamp-2">
                                <a href="news-details.html">The World is a Book and Those Who do not Travel Read
                                    Only
                                    One Page.</a>
                            </h4>
                            <div class="news-info">
                                <div class="d-flex gap-10 align-items-center">
                                    <div class="all-user">
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-1.jpeg" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-2.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-3.png" alt="image">
                                        </div>
                                        <div class="happy-user">
                                            <img src="assets/images/hero/user-4.jpeg" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <p class="time">10 min read</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-12 text-center">
                    <div class="section-button d-inline-block">
                        <a href="javascript:void(0)">
                            <div class="btn-primary-icon-sm">
                                <i class="ri-loader-2-line"></i>
                                <p class="pera">Loading</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
