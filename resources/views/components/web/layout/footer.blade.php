<footer>
    <div class="footer-wrapper footer-bg">
        <div class="container">
            <div class="footer-area">
                <div class="row g-4">
                    <div class="col-xl-6 col-lg-6 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">{{ __('Quick Links') }}</h4>
                                <ul class="listing">
                                    <li class="single-lsit"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                                    <li class="single-lsit"><a href="{{ route('blogs') }}">{{ __('Magazine') }}</a></li>
                                    <li class="single-lsit">
                                        <a href="{{ route('about') }}">{{ __('About Us') }}</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">{{ __('Contact') }}</h4>
                                <ul class="listing">
                                    <li class="single-lsit">
                                        <a href="#" class="mb-20 d-block">420 Pittsburg Landing,
                                            Summerville, South Carolina, 29483</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="#">
                                            <div class="d-flex gap-12">
                                                <i class="ri-phone-line"></i>
                                                {{ $component->content['phone']['value'] ?? '' }}
                                            </div>
                                        </a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="#">
                                            <div class="d-flex gap-12">
                                                <i class="ri-mail-line"></i>
                                                {{ $component->content['email']['value'] ?? '' }}
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-middle-area">
                <div class="footer-body">
                    <div class="footer-content">
                        <div class="d-flex flex-column gap-20">
                            <div class="logo">
                                <a href="{{ route('home') }}"><img
                                        src="{{ asset('storage/' . $component->media ?? '') }}" width="50"
                                        alt="{{ config('app.name') }}" class="changeLogo"></a>
                            </div>
                            <h4>{{ __('Stay Informed with the Latest Pet Health Tips!') }}</h4>
                            <p class="pera">
                                {{ __('Don’t miss out on expert advice, pet care tips, and exclusive updates tailored for pet
                                                                                                parents like you. Subscribe to our newsletter to receive the latest on pet health
                                                                
                                                                                                Join Our Pet Parent Community Today!') }}
                            </p>
                        </div>
                        <div class="footer-right">
                            <h4 class="title">{{ __('Subscribe Our Newsletter') }}</h4>
                            <div class="subscribe-wraper">
                                <form action="{{ route('news-letter.subscribe') }}" method="post">
                                    @csrf
                                    <input class="footer-search" type="search" name="email"
                                        placeholder="Enter Your Email">
                                    <button class="subscribe-btn" submit>{{ __('Subscribe') }}</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer-bottom area -->
        <div class="footer-bottom-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-4 col-lg-4">
                                <p class="pera">
                                    © <span class="current-year">{{ date('Y-m-d') }}</span>.
                                    {{ __('All rights reserved') }}
                                </p>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <p class="pera">{{ __('Powered by') }} {{ config('app.name') }}</p>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <a href="{{ config('app.pinterest_url') }}" target="_blank">
                                    <i class="ri-pinterest-fill" style="font-size: 2rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
