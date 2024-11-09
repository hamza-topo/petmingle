@extends('layouts.app')
@section('meta')
    <x-web.layout.meta :seo="$seo" />
@endsection

@section('title')
    {{ $seo->title['fr'] ?? config('app.name', 'Petmingle') }}
@endsection
@section('main')
    <section class="tour-details-section section-padding2">
        <div class="tour-details-area">

            <!-- Details Banner Slider -->
            <div class="tour-details-banner">
                <div class="swiper tourSwiper-active">
                    <div class="swiper-wrapper">
                        @foreach ($randoms as $random)
                            <div class="swiper-slide">
                                @php
                                    $img = !empty($random->media[0]) ? $random->media[0] : '';
                                @endphp
                                <img src="{{ asset('storage/' . $img) }}" style="width: 665.5px; margin-right: 24px;"
                                    alt="{{ $random->title[app()->getLocale()] ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                    @if ($randoms->count() > 4)
                        <div class="swiper-button-next"><i class="ri-arrow-right-s-line"></i></div>
                        <div class="swiper-button-prev"><i class="ri-arrow-left-s-line"></i></div>
                    @endif
                </div>
            </div>
            <!-- / Slider-->

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
                        <div class="price-review">
                            <div class="d-flex gap-10 align-items-end">
                                <p class="light-pera">From</p>
                                <p class="pera">$95</p>
                            </div>
                            <div class="rating">
                                <i class="ri-star-s-fill"></i>
                                <p class="pera">4.7 (20 Reviews)</p>
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

                                {{-- <!-- Tour Include Exclude -->
                                <div class="tour-include-exclude radius-6">
                                    <div class="includ-exclude-point">
                                        <h4 class="title">Included</h4>
                                        <ul class="expect-list">
                                            <li class="list">Welcome Breakfast</li>
                                            <li class="list">All Entry Tickets of Hopping Destinations</li>
                                            <li class="list">Lunch Platter</li>
                                            <li class="list">Evening Snacks</li>
                                            <li class="list">First Aid Kit (In case of emergency)</li>
                                        </ul>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="includ-exclude-point">
                                        <h4 class="title">Exclude</h4>
                                        <ul class="expect-list">
                                            <li class="list">Personal expenses</li>
                                            <li class="list">Anything else that isn't mentioned on Inclusions</li>
                                            <li class="list">Additional Service</li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- / Tour Include Exclude -->

                                <!-- Tour Plan accordion-->
                                <div class="tour-details-content mb-30">
                                    <h4 class="title">Tour Plan</h4>
                                    <div class="destination-accordion">
                                        <div class="accordion" id="accordionPanelsStayOpenExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                                        aria-controls="panelsStayOpen-collapseOne">
                                                        Day 1 - Samyan Bangkok
                                                    </button>
                                                </h2>
                                                <div id="panelsStayOpen-collapseOne"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="panelsStayOpen-headingOne">
                                                    <div class="accordion-body">
                                                        <p class="pera mb-16">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do
                                                            eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Ut enim ad minim veniam,
                                                            quis nostrud exercitation ullamco laboris nisi ut
                                                            aliquip ex ea commodo consequat. Duis
                                                            aute irure dolor in reprehenderit in voluptate velit
                                                            esse cillum dolore eu fugiat nulla
                                                            pariatur. Excepteur sint occaecat cupidatat non
                                                            proident, sunt in culpa qui officia
                                                            deserunt mollit anim id est laborum."</p>
                                                        <ul class="listing">
                                                            <li class="list">
                                                                “Life is either a daring adventure or nothing at
                                                                all.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Travel far enough, you meet yourself.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Wherever you go becomes a part of you somehow.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Once a year, go someplace you've never been
                                                                before.”
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                                                        aria-controls="panelsStayOpen-collapseTwo">
                                                        Day 2 - Samyan Bangkok
                                                    </button>
                                                </h2>
                                                <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse"
                                                    aria-labelledby="panelsStayOpen-headingTwo">
                                                    <div class="accordion-body">
                                                        <p class="pera mb-16">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do
                                                            eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Ut enim ad minim veniam,
                                                            quis nostrud exercitation ullamco laboris nisi ut
                                                            aliquip ex ea commodo consequat. Duis
                                                            aute irure dolor in reprehenderit in voluptate velit
                                                            esse cillum dolore eu fugiat nulla
                                                            pariatur. Excepteur sint occaecat cupidatat non
                                                            proident, sunt in culpa qui officia
                                                            deserunt mollit anim id est laborum."</p>
                                                        <ul class="listing">
                                                            <li class="list">
                                                                “Life is either a daring adventure or nothing at
                                                                all.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Travel far enough, you meet yourself.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Wherever you go becomes a part of you somehow.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Once a year, go someplace you've never been
                                                                before.”
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapseThree"
                                                        aria-expanded="false"
                                                        aria-controls="panelsStayOpen-collapseThree">
                                                        Day 3 - Samyan Bangkok
                                                    </button>
                                                </h2>
                                                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse"
                                                    aria-labelledby="panelsStayOpen-headingThree">
                                                    <div class="accordion-body">
                                                        <p class="pera mb-16">Lorem ipsum dolor sit amet,
                                                            consectetur adipiscing elit, sed do
                                                            eiusmod tempor incididunt ut labore et dolore magna
                                                            aliqua. Ut enim ad minim veniam,
                                                            quis nostrud exercitation ullamco laboris nisi ut
                                                            aliquip ex ea commodo consequat. Duis
                                                            aute irure dolor in reprehenderit in voluptate velit
                                                            esse cillum dolore eu fugiat nulla
                                                            pariatur. Excepteur sint occaecat cupidatat non
                                                            proident, sunt in culpa qui officia
                                                            deserunt mollit anim id est laborum."</p>
                                                        <ul class="listing">
                                                            <li class="list">
                                                                “Life is either a daring adventure or nothing at
                                                                all.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Travel far enough, you meet yourself.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Wherever you go becomes a part of you somehow.” ...
                                                            </li>
                                                            <li class="list">
                                                                “Once a year, go someplace you've never been
                                                                before.”
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- / Tour Plan accordion-->

                                <!-- Tour Privacy Policy -->
                                <div class="tour-details-content">
                                    <h4 class="title">Policy</h4>
                                    <p class="pera">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                        eiusmod tempor
                                        incididunt
                                        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                        exercitation ullamco
                                        laboris
                                        nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
                                        reprehenderit in voluptate velit
                                        esse
                                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                                        non proident, sunt in
                                        culpa
                                        qui officia deserunt mollit anim id est laborum."</p>
                                    <p class="pera">Sed ut perspiciatis unde omnis iste natus error sit voluptatem
                                        accusantium
                                        doloremque
                                        laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis
                                        et quasi architecto
                                        beatae
                                        vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit
                                        aspernatur aut odit aut
                                        fugit,
                                        sed quia consequuntur magni dolores eos qui ratione voluptatem sequi
                                        nesciunt. Neque porro
                                        quisquam est,
                                        qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia
                                        non numquam eius modi
                                        tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut
                                        enim ad minima veniam,
                                        quis
                                        nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid
                                        ex ea commodi
                                        consequatur?
                                        Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam
                                        nihil molestiae
                                        consequatur,
                                        vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"</p>
                                    <ol class="policy-point">
                                        <li class="list">Neque porro quisquam est, qui dolorem ipsum quia dolor sit
                                            amet, consectetur,
                                            adipisci velit.</li>
                                        <li class="list">Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut
                                            odit aut fugit.</li>
                                        <li class="list">Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                            sed do eiusmod.</li>
                                    </ol>
                                </div>
                                <!-- / Tour Privacy Policy --> --}}
                            </div>

                            <!-- Right content -->
                            {{-- <div class="col-xl-4 col-lg-5">
                                <div class="date-travel-card position-sticky top-0">
                                    <div class="price-review">
                                        <div class="d-flex gap-10 align-items-end">
                                            <p class="light-pera">From</p>
                                            <p class="pera">$95</p>
                                        </div>
                                        <div class="rating">
                                            <p class="pera">Price varies by group size</p>
                                        </div>
                                    </div>
                                    <h4 class="heading-card">Select Date and Travelers</h4>
                                    <div class="date-time-dropdown">
                                        <i class="ri-time-line"></i>
                                        <p class="date-time-result">Wednesday, Jan 17, 2023</p>
                                    </div>
                                    <div class="dropdown-section position-relative user-picker-dropdown">
                                        <div class="d-flex gap-12 align-items-center">
                                            <i class="dropdown-icon ri-user-line"></i>
                                            <div class="custom-dropdown">
                                                <h4 class="title">Guests</h4>
                                                <div class="arrow">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="user-result"></div>
                                        <div class="user-picker dropdown-shadow">
                                            <div class="user-category">
                                                <div class="category-name">
                                                    <h4 class="title">Adults</h4>
                                                    <p class="pera">12years and above</p>
                                                </div>
                                                <div class="qty-container">
                                                    <button class="qty-btn-minus mr-1" type="button">
                                                        <i class="ri-subtract-fill"></i>
                                                    </button>
                                                    <input type="text" name="qty" value="0"
                                                        class="input-qty input-rounded">
                                                    <button class="qty-btn-plus ml-1" type="button">
                                                        <i class="ri-add-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="user-category">
                                                <div class="category-name">
                                                    <h4 class="title">Children</h4>
                                                    <p class="pera">2-11 years</p>
                                                </div>
                                                <div class="qty-container">
                                                    <button class="qty-btn-minus mr-1" type="button">
                                                        <i class="ri-subtract-fill"></i>
                                                    </button>
                                                    <input type="text" name="qty" value="0"
                                                        class="input-qty input-rounded">
                                                    <button class="qty-btn-plus ml-1" type="button">
                                                        <i class="ri-add-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="user-category">
                                                <div class="category-name">
                                                    <h4 class="title">infant</h4>
                                                    <p class="pera">belwo 2 years</p>
                                                </div>
                                                <div class="qty-container">
                                                    <button class="qty-btn-minus mr-1" type="button">
                                                        <i class="ri-subtract-fill"></i>
                                                    </button>
                                                    <input type="text" name="qty" value="0"
                                                        class="input-qty input-rounded">
                                                    <button class="qty-btn-plus ml-1" type="button">
                                                        <i class="ri-add-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="btn-section">
                                                <a href="javascript:void(0)" class="done-btn">Done</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-30">
                                        <button type="submit" class="send-btn w-100">Check Availability</button>
                                    </div>
                                    <div class="footer bg-transparent">
                                        <h4 class="title">Free Cancellation</h4>
                                        <p class="pera">Up to 24 hours in advance</p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
