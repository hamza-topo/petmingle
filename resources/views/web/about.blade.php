@extends('layouts.app')

@section('meta')
<x-web.layout.meta :seo="$seo"/>
@endsection

@section('title')
{{ $seo->title['fr'] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <x-web.layout.bread-crumb />
    <section class="about-area">
        <div class="container">
            <div class="row g-4">
                <div class="col-xl-5 col-lg-6">
                    <div class="section-title mx-430 mb-30 w-md-100">
                        <span class="highlights fancy-font font-400">About Us</span>
                        <h4 class="title">
                            Get The Best Travel Experience With Travello
                        </h4>
                        <p class="pera">
                            Travel is a transformative and enriching experience that
                            allows individuals to explore new destinations, cultures, and
                            landscapes. It is a fundamental human activity that has been
                            practiced for centuries and continues to be a source of joy,
                            learning, and personal growth.
                        </p>
                        <p class="pera">
                            Travel is a transformative and enriching experience that
                            allows individuals to explore new destinations, cultures.
                        </p>
                        <div class="section-button mt-27 d-inline-block">
                            <a href="about.html" class="btn-primary-icon-sm radius-20">
                                <p class="pera mt-0">Learn More</p>
                                <i class="ri-arrow-right-up-line"></i>
                            </a>
                        </div>
                        <div class="about-imp-link mt-40">
                            <div class="icon">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="content">
                                <p class="pera font-16">
                                    <span class="font-700">2,500</span> People Booked Tomorrow
                                    Land Event in the Last 24 hours
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                    <div class="about-count-section about-count-before-bg">
                        <div class="banner">
                            <img src="assets/images/gallery/about-banner-three.png" alt="{{config('app.name')}}">
                        </div>
                        <div class="all-count-list">
                            <div class="details">
                                <h4 class="count">150k</h4>
                                <p class="pera">Happy Traveler</p>
                            </div>
                            <div class="divider"></div>
                            <div class="details">
                                <h4 class="count">95.7%</h4>
                                <p class="pera">Satisfaction Rate</p>
                            </div>
                            <div class="divider"></div>
                            <div class="details">
                                <h4 class="count">5000+</h4>
                                <p class="pera">Tour Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pricing-area pb-0 section-bg-before-two">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-7">
                    <div class="section-title text-center mx-605 mx-auto position-relative">
                        <span class="highlights-primary">Package Pricing Plan</span>
                        <h4 class=" title">
                            Simply Choose The Pricing Plan That Fits You Best
                        </h4>
                    </div>
                </div>
            </div>
            <div class="position-relative">
                <div class="row g-4">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="price-card h-calc wow fadeInUp" data-wow-delay="0.0s"
                            style="visibility: visible; animation-delay: 0s; animation-name: fadeInUp;">
                            <div class="price-header">
                                <div class="d-flex gap-7 mb-2">
                                    <h4 class="title">Basic</h4>
                                    <div class="price-badge d-none">popular</div>
                                </div>
                                <p class="pera">Best for personal and basic needs</p>
                            </div>
                            <div class="price-tag-section">
                                <div class="price-tag">
                                    <h4 class="title">$10</h4>
                                    <p class="pera">One-time payment</p>
                                </div>
                            </div>
                            <ul class="feature-points">
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">20+ Partners</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Mass Messaging</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor sit amet</p>
                                </li>
                                <li class="feature-point disable">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor</p>
                                </li>
                                <li class="feature-point disable">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Online booking engine</p>
                                </li>
                                <li class="feature-point disable">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Business Card Scanner</p>
                                </li>
                            </ul>
                            <div class="button-section">
                                <a href="payment.html">
                                    <div class="btn-primary-icon-outline">
                                        <span class="pera">Try Now</span>
                                        <i class="ri-arrow-right-up-line"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="imp-note">
                                <p class="pera">Per month +2% per online Booking</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="price-card h-calc wow fadeInUp" data-wow-delay="0.0s"
                            style="visibility: visible; animation-delay: 0s; animation-name: fadeInUp;">
                            <div class="price-header">
                                <div class="d-flex gap-7 mb-2">
                                    <h4 class="title">Pro</h4>
                                    <div class="price-badge">popular</div>
                                </div>
                                <p class="pera">Best for personal and basic needs</p>
                            </div>
                            <div class="price-tag-section">
                                <div class="price-tag">
                                    <h4 class="title">$77</h4>
                                    <p class="pera">One-time payment</p>
                                </div>
                            </div>
                            <ul class="feature-points">
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">20+ Partners</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Mass Messaging</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor sit amet</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Online booking engine</p>
                                </li>
                                <li class="feature-point disable">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Business Card Scanner</p>
                                </li>
                            </ul>
                            <div class="button-section">
                                <a href="payment.html">
                                    <div class="btn-primary-icon-outline">
                                        <span class="pera">Try Now</span>
                                        <i class="ri-arrow-right-up-line"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="imp-note">
                                <p class="pera">Per month +1.9% per online Booking</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="price-card h-calc wow fadeInUp" data-wow-delay="0.0s"
                            style="visibility: visible; animation-delay: 0s; animation-name: fadeInUp;">
                            <div class="price-header">
                                <div class="d-flex gap-7 mb-2">
                                    <h4 class="title">Custom</h4>
                                    <div class="price-badge d-none">popular</div>
                                </div>
                                <p class="pera">Best for personal and basic needs</p>
                            </div>
                            <ul class="feature-points">
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Mass Messaging</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor sit amet</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Unlimited Everything</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Lorem ipsum dolor</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Online booking engine</p>
                                </li>
                                <li class="feature-point">
                                    <div class="tick-icon">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <p class="pera">Business Card Scanner</p>
                                </li>
                            </ul>
                            <div class="button-section">
                                <a href="payment.html">
                                    <div class="btn-primary-icon-outline">
                                        <span class="pera">Contact</span>
                                        <i class="ri-arrow-right-up-line"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="imp-note">
                                <p class="pera">Please contact anytime</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="brand-area">
        <div class="container">
            <div class="border-section-title">
                <h4 class="title">We’ve been mentioned in Below Brands</h4>
            </div>
            <div class="swiper brandSwiper-active swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" id="swiper-wrapper-dd0d8f71ce5e3b83" aria-live="off"
                    style="transition-duration: 0ms; transform: translate3d(-981.333px, 0px, 0px); transition-delay: 0ms;">







                    <div class="swiper-slide" role="group" aria-label="1 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="0">
                        <img src="assets/images/brand/brand-1.jpeg" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide" role="group" aria-label="2 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="1">
                        <img src="assets/images/brand/brand-2.jpg" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide" role="group" aria-label="3 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="2">
                        <img src="assets/images/brand/brand-3.jpg" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide" role="group" aria-label="4 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="3">
                        <img src="assets/images/brand/brand-4.png" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide swiper-slide-prev" role="group" aria-label="5 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="4">
                        <img src="assets/images/brand/brand-5.png" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide swiper-slide-active" role="group" aria-label="6 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="5">
                        <img src="assets/images/brand/brand-1.jpeg" alt="{{config('app.name')}}">
                    </div>
                    <div class="swiper-slide swiper-slide-next" role="group" aria-label="7 / 7"
                        style="width: 205.333px; margin-right: 40px;" data-swiper-slide-index="6">
                        <img src="assets/images/brand/brand-2.jpg" alt="{{config('app.name')}}">
                    </div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
    </section>
    <section class="testimonial-area section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-7">
                    <div class="section-title mx-430 mx-auto text-center">
                        <span class="highlights">Testimonial</span>
                        <h4 class="title">
                            What People Have Said About Our Service
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-24">
            <div class="swiper bulletLeftSwiper-active swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" id="swiper-wrapper-7f2a74dc1062b8f410" aria-live="off"
                    style="transition-duration: 0ms; transform: translate3d(-1648px, 0px, 0px); transition-delay: 0ms;">






                    <div class="swiper-slide testimonial-card" role="group" aria-label="1 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="0">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card" role="group" aria-label="2 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="1">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card" role="group" aria-label="3 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="2">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-prev" role="group" aria-label="4 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="3">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-active" role="group" aria-label="5 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="4">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-next" role="group" aria-label="6 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="5">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
            <div class="swiper bulletRightSwiper-active swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" id="swiper-wrapper-77e10b48f3cc47c51" aria-live="off"
                    style="transition-duration: 6000ms; transform: translate3d(-1648px, 0px, 0px);">






                    <div class="swiper-slide testimonial-card" role="group" aria-label="6 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="5">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card" role="group" aria-label="1 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="0">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card" role="group" aria-label="2 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="1">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-prev" role="group" aria-label="3 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="2">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-active" role="group" aria-label="4 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="3">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                    <div class="swiper-slide testimonial-card swiper-slide-next" role="group" aria-label="5 / 6"
                        style="width: 388px; margin-right: 24px;" data-swiper-slide-index="4">
                        <div class="testimonial-header">
                            <div class="user-img">
                                <img src="assets/images/testimonial/testimonial-1.jpeg" alt="{{config('app.name')}}">
                            </div>
                            <div class="user-info">
                                <p class="name">David Malan</p>
                                <p class="designation">Traveler</p>
                            </div>
                        </div>
                        <div class="testimonial-body">
                            <p class="pera">
                                Lorem ipsum dolor sit amet consectetur. Et amet nulla in
                                adipiscing. Donec tincidunt dui vel adipiscing sit turpis
                                neque at cursus. Dignissim scelerisque mattis ultricies
                                vitae.
                            </p>
                        </div>
                        <div class="testimonial-footer">
                            <div class="logo">
                                <img src="assets/images/logo/logo-white.png" alt="{{config('app.name')}}" class="changeLogo">
                            </div>
                            <p class="date">Jan 20, 2025</p>
                        </div>
                    </div>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="section-button d-inline-block">
                        <a href="javascript:void(0)">
                            <div class="btn-primary-icon-sm">
                                <p class="pera">All Customers Say</p>
                                <i class="ri-arrow-right-up-line"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
