<!-- Special area S t a r t -->
<section class="special-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-7">
                <div class="section-title mx-430 mx-auto text-center">
                    <span class="highlights fancy-font font-400"></span>
                    <h4 class="title">
                        {{ __('They need a ') }}<span class="highlights">PetParent</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">

                <!-- Trip Buttons -->
                {{-- <ul class="nav nav-pills trip-pills" id="pills-tab" role="tablist">
                    <li class="nav-item trip-item" role="presentation">
                        <button class="nav-link trip-nav active" id="pills-domestic-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-domestic" type="button" role="tab"
                            aria-controls="pills-domestic" aria-selected="true">
                            Domestic
                        </button>
                    </li>
                    <li class="nav-item trip-item" role="presentation">
                        <button class="nav-link trip-nav" id="pills-international-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-international" type="button" role="tab"
                            aria-controls="pills-international" aria-selected="false" tabindex="-1">
                            International
                        </button>
                    </li>
                </ul> --}}
                <!-- / End-of Trip Buttons -->

                <!-- Tab Contents -->
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-domestic" role="tabpanel"
                        aria-labelledby="pills-domestic-tab">
                        <div class="row g-4">
                            @foreach ($pets as $pet)
                                <div class="col-xl-4 col-md-6">
                                    <a href="{{ route('engine.detail', [slugify($pet->name), $pet->id]) }}"
                                        class="trip-card">
                                        <div class="from-flex">
                                            <h4 class="from-title">{{ $pet->name ?? '' }}</h4>
                                            <p class="from-pera line-clamp-1">{{ $pet->owner->name }}</p>
                                        </div>
                                        <div class="trip-icon-flex">
                                            <div class="trip-icon">
                                                @if (empty($pet->images[0]))
                                                @else
                                                    <img src="assets/images/package/package-2.png"
                                                        style="height:75px;width:75px;border-radius:50%" />
                                                @endif
                                            </div>
                                        </div>
                                        <div class="from-flex">
                                            <h4 class="from-title">{{ $pet->race->name }}</h4>
                                            <p class="from-pera line-clamp-1">Age: {{ $pet->age }} months</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <!-- / End-of Tab contents -->
            </div>
        </div>
    </div>
</section>
<!--/ End-of special-->
