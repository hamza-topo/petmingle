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
                                        <a href="{{ route('home') }}"><img src="{{ asset('storage/' . $component->media ?? '') }}"
                                                width="50" alt="logo" class="changeLogo"></a>
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
                                            <p class="pera">{{ $component->content['email']['text'] ?? '' }}</p>
                                            <h4 class="title">
                                                <a
                                                    href="javascript:void(0)">{{ $component->content['email']['value'] ?? '' }}</a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="contact-section">
                                        <div class="circle-primary-sm">
                                            <i class="ri-phone-line"></i>
                                        </div>
                                        <div class="info">
                                            <p class="pera">{{ $component->content['phone']['text'] ?? '' }}</p>
                                            <h4 class="title">
                                                <a
                                                    href="javascript:void(0)">{{ $component->content['phone']['value'] ?? '' }}</a>
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
                                                @foreach ($menus as $menu)
                                                    <li class="single-list">
                                                        <a href="{{ url($menu['url']) }}"
                                                            class="single">{{ $menu['Text'] }}<i
                                                                class="ri-arrow-down-s-line"></i></a>
                                                    </li>
                                                @endforeach
                                                <li class="d-block d-lg-none">
                                                    <div class="header-right pl-15">
                                                        <div class="d-flex align-items-center gap-12">
                                                            <div class="lang">
                                                                <i class="ri-global-line"></i>
                                                            </div>
                                                            <div class="divider gradient-divider"></div>
                                                            <div class="money">
                                                                <p class="pera">{{ App\Enums\App::CURRENCY }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="sign-btn">
                                                            <a href="{{ route('login') }} "
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
                                                        <p class="pera">{{ App\Enums\App::CURRENCY }}</p>
                                                    </div>
                                                </div>
                                                <div class="sign-btn">
                                                    <a href="{{ route('login') }} " class="btn-secondary-sm">Sign
                                                        In</a>
                                                </div>
                                                <!-- Theme Mode -->
                                                <li class="single-list">
                                                    <button onclick="setTheme()"
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
