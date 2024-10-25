<!DOCTYPE html>
<html lang="zxx" dir="lrt">

<head>

   @yield('meta')
    <!-- Title -->
    <title>
        @yield('title')
    </title>
    <link rel="icon" type="image/x-icon" sizes="20x20" href="{{ asset('assets/images/icon/favicon.png') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-5.3.0.min.css') }}">
    <!-- Fonts & icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/remixicon.css') }}">
    <!-- Plugin -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugin.css') }}">
    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main-style.css') }}">
    <!-- RTL CSS::When Need RTL Uncomments File -->
    <!-- <link rel="stylesheet" type="text/css" href="assets/css/rtl.css')}}"> -->
</head>

<body>
    <x-web.layout.header />
    <main>
        @yield('main')
    </main>

    <!-- Footer S t a r t -->
    <x-web.layout.footer />
    <!--/ End-of Footer -->

    <!-- Scroll Up  -->
    <div class="progressParent" id="back-top">
        <svg class="backCircle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- Add an search-overlay element -->
    <div class="search-overlay"></div>
    <!-- jquery-->
    <script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-5.3.0.min.js') }}"></script>
    <!-- Plugin -->
    <script src="{{ asset('assets/js/plugin.js') }}"></script>
    <!-- Main js-->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        function setTheme() {
            if (localStorage.theme == 'light') {
                theme = 'dark';
            } else {
                theme = 'light';
            }
            theme ??= localStorage.theme || "light";
            document.documentElement.dataset.theme = theme;
            localStorage.theme = theme;
        };
    </script>
</body>

</html>
