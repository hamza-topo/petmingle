<footer>
    <div class="footer-wrapper footer-bg">
        <div class="container">
            <div class="footer-area">
                <div class="row g-4">
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">Company</h4>
                                <ul class="listing">
                                    <li class="single-lsit">
                                        <a href="{{ route('about') }}">About Us</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="{{ route('blogs') }}">Blogs</a>
                                    </li>
                                    <li class="single-lsit"><a href={{ route('faq') }}>Faq</a></li>
                                    <li class="single-lsit">
                                        <a href="{{ route('contact') }}">Contact</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">Explore</h4>
                                <ul class="listing">
                                    <li class="single-lsit"><a href={{ route('faq') }}>Faq</a></li>
                                    <li class="single-lsit">
                                        <a href="tour-list.html">Tour Listings</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="{{ route('engine', ['type' => 0]) }}">Dating</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">Quick Links</h4>
                                <ul class="listing">
                                    <li class="single-lsit"><a href="index.html">Home</a></li>
                                    <li class="single-lsit">
                                        <a href="{{ route('about') }}">About Us</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="{{ route('contact') }}">Contact Us</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="single-footer-caption">
                            <div class="footer-tittle">
                                <h4 class="title">Contact</h4>
                                <ul class="listing">
                                    <li class="single-lsit">
                                        <a href="#" class="mb-20 d-block">70/A Floor Divo Tower Melbourne,
                                            Australia</a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="#">
                                            <div class="d-flex gap-12">
                                                <i class="ri-phone-line"></i> (00) +888 123456 789
                                            </div>
                                        </a>
                                    </li>
                                    <li class="single-lsit">
                                        <a href="#">
                                            <div class="d-flex gap-12">
                                                <i class="ri-mail-line"></i> example@gmail.com
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
                                <img src="assets/images/logo/logo.png" width="50" alt="travello" class="changeLogo">
                            </div>
                            <p class="pera">
                                Travel is a transformative and enriching experience that
                                allows individuals to explore new destinations, cultures,
                                and landscapes.
                            </p>
                        </div>
                        <div class="footer-right">
                            <h4 class="title">Subscribe Our Newsletter</h4>
                            <div class="subscribe-wraper">
                                <input class="footer-search" type="search" name="footer"
                                    placeholder="Enter Your Email">
                                <button class="subscribe-btn">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <ul class="listing">
                        <li class="single-list">
                            <a href="terms-condition.html" class="single">Terms of usa</a>
                        </li>
                        <li class="single-list">
                            <a href="privacy-policy.html" class="single">Privacy and Cookies Statement</a>
                        </li>
                        <li class="single-list">
                            <a href="{{ route('contact') }}" class="single">How the site works</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- footer-bottom area -->
        <div class="footer-bottom-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between gap-14 flex-wrap">
                            <p class="pera">
                                © <span class="current-year">2023</span> initTheme. All rights
                                reserved
                            </p>
                            <p class="pera">Powered by @topo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
