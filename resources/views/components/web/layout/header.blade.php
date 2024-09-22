<header>
    <div class="header-area">
        <div class="main-header">
            <!-- Header Top -->
            <div class="header-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="top-menu-wrapper d-flex align-items-center justify-content-between">
                                <!-- Top Left Side -->
                                <div class="top-header-left d-flex align-items-center">
                                    <!-- Logo-->
                                    <div class="logo">
                                        <a href="index.html"><img src="assets/images/logo/logo.png" width="50"
                                                alt="logo" class="changeLogo"></a>
                                    </div>
                                    <!-- search box -->
                                    
                                    <!-- / Mobile Device Seach & Theme Mode-->
                                </div>
                                <!--Top Right Side -->
                                <div class="top-header-right">
                                    <!-- contact us -->
                                    <div class="contact-section">
                                        <div class="circle-primary-sm">
                                            <i class="ri-mail-line"></i>
                                        </div>
                                        <div class="info">
                                            <p class="pera">Email Anytime</p>
                                            <h4 class="title">
                                                <a href="javascript:void(0)">example@gmail.com</a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="contact-section">
                                        <div class="circle-primary-sm">
                                            <i class="ri-phone-line"></i>
                                        </div>
                                        <div class="info">
                                            <p class="pera">Call Anytime</p>
                                            <h4 class="title">
                                                <a href="javascript:void(0)">00 (888) +123456</a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header Bottom -->
            <div class="header-bottom header-sticky">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="menu-wrapper">
                                <!-- Main-menu for desktop -->
                                <div class="main-menu d-none d-lg-block">
                                    <nav>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <ul class="listing" id="navigation">
                                                <li class="single-list">
                                                    <a href="{{ route('home') }}" class="single link-active">Home <i
                                                            class="ri-arrow-down-s-line"></i></a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('about') }}" class="single">About</a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('engine', ['type' => 0]) }}"
                                                        class="single">Dating</a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('engine', ['type' => 1]) }}" class="single">pet
                                                        sitters</a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('engine', ['type' => 2]) }}"
                                                        class="single">Adoption</a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('blogs') }}" class="single">Blogs</a>
                                                </li>
                                                <li class="single-list">
                                                    <a href="{{ route('contact') }}" class="single">Contact</a>
                                                </li>
                                                <li class="d-block d-lg-none">
                                                    <div class="header-right pl-15">
                                                        <div class="d-flex align-items-center gap-12">
                                                            <div class="lang">
                                                                <i class="ri-global-line"></i>
                                                            </div>
                                                            <div class="divider gradient-divider"></div>
                                                            <div class="money">
                                                                <p class="pera">USD</p>
                                                            </div>
                                                        </div>
                                                        <div class="sign-btn">
                                                            <a href="{{ route('user.login') }} "
                                                                class="btn-secondary-sm">Sign
                                                                In</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="header-right">
                                                <div class="d-flex align-items-center gap-12">
                                                    <div class="lang">
                                                        <i class="ri-global-line"></i>
                                                    </div>
                                                    <div class="divider gradient-divider"></div>
                                                    <div class="money">
                                                        <p class="pera">USD</p>
                                                    </div>
                                                </div>
                                                <div class="sign-btn">
                                                    <a href="{{ route('user.login') }} " class="btn-secondary-sm">Sign
                                                        In</a>
                                                </div>
                                                <!-- Theme Mode -->
                                                <li class="single-list">
                                                    <button
                                                        class="ToggleThemeButton change-theme-mode m-0 p-0 border-0">
                                                        <i class="ri-sun-line"></i>
                                                    </button>
                                                </li>
                                                {{ auth()->user()?->name }}
                                            </div>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                            <!-- Mobile Menu -->
                            <div class="div">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
