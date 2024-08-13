<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.html" class="text-nowrap logo-img">
                <img src="../assets/images/logos/dark-logo.svg" width="180" alt="">
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="init">
            <div class="simplebar-wrapper selected" style="margin: 0px -24px;">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask selected">
                    <div class="simplebar-offset selected" style="right: 0px; bottom: 0px;">
                        <div class="simplebar-content-wrapper selected" tabindex="0" role="region"
                            aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                            <div class="simplebar-content selected" style="padding: 0px 24px;">
                                <ul id="sidebarnav" class="in">
                                    <li class="nav-small-cap">
                                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                        <span class="hide-menu">Home</span>
                                    </li>
                                    <li class="sidebar-item selected">
                                        <a class="sidebar-link active" href="./index.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-layout-dashboard"></i>
                                            </span>
                                            <span class="hide-menu">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="nav-small-cap">
                                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                        <span class="hide-menu">Set Up</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('admin.species.index') }}"
                                            aria-expanded="false">
                                            <span>
                                                <i class="ti ti-article"></i>
                                            </span>
                                            <span class="hide-menu">{{ __('Species') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('admin.races.index') }}"
                                            aria-expanded="false">
                                            <span>
                                                <i class="ti ti-alert-circle"></i>
                                            </span>
                                            <span class="hide-menu">{{ __('Races') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./ui-card.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-cards"></i>
                                            </span>
                                            <span class="hide-menu">{{ __('Languages') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./ui-forms.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-file-description"></i>
                                            </span>
                                            <span class="hide-menu">{{ __('Modes') }}</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./ui-typography.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-typography"></i>
                                            </span>
                                            <span class="hide-menu">{{ __('Blogs') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-small-cap">
                                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                        <span class="hide-menu">AUTH</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./authentication-login.html"
                                            aria-expanded="false">
                                            <span>
                                                <i class="ti ti-login"></i>
                                            </span>
                                            <span class="hide-menu">Login</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./authentication-register.html"
                                            aria-expanded="false">
                                            <span>
                                                <i class="ti ti-user-plus"></i>
                                            </span>
                                            <span class="hide-menu">Register</span>
                                        </a>
                                    </li>
                                    <li class="nav-small-cap">
                                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                                        <span class="hide-menu">EXTRA</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./icon-tabler.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-mood-happy"></i>
                                            </span>
                                            <span class="hide-menu">Icons</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="./sample-page.html" aria-expanded="false">
                                            <span>
                                                <i class="ti ti-aperture"></i>
                                            </span>
                                            <span class="hide-menu">Sample Page</span>
                                        </a>
                                    </li>
                                </ul>
                                <div
                                    class="unlimited-access hide-menu bg-light-primary position-relative mb-7 mt-5 rounded">
                                    <div class="d-flex">
                                        <div class="unlimited-access-title me-3">
                                            <h6 class="fw-semibold fs-4 mb-6 text-dark w-85">Upgrade to pro</h6>
                                            <a href="https://adminmart.com/product/modernize-bootstrap-5-admin-template/"
                                                target="_blank" class="btn btn-primary fs-2 fw-semibold lh-sm">Buy
                                                Pro</a>
                                        </div>
                                        <div class="unlimited-access-img">
                                            <img src="../assets/images/backgrounds/rocket.png" alt=""
                                                class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="simplebar-placeholder" style="width: auto; height: 888px;"></div>
            </div>
            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar simplebar-visible" style="width: 0px; display: none;"></div>
            </div>
            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                <div class="simplebar-scrollbar simplebar-visible"
                    style="height: 103px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
            </div>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
