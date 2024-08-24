/*---------------------------------------------------------
    Index Of Script
-----------------------------------------------------------

    @version         : 1.0.0
    @Template Name   : Travello
    @Template author : initTheme
    
    :: Swiper Slider Active         :: Nice Scroll js             
    :: Fancy box                    :: Hover Tilt Effect        
    :: Search box                   :: Select 2                                  
    :: Mobile Menu                  :: Slick Nav       
    :: Animation                    :: Wow Animation      
    :: Sticky And Scroll Up         :: JS Year
    :: JS for Price Range slider    :: Play video Slider
    :: Custom click ad remove       :: Custom Dropdown
    :: Filter side nav              :: Password Show Hide
    :: Payment Gateway selection    
    
------------------------------------------------------------
    End-of Script
------------------------------------------------------------*/

(function ($) {
    "use strict";

    /*----------------------------------------------
        :: Sticky And Scroll Up
    -----------------------------------------------*/
    $(window).on("scroll", function () {
        var scroll = $(window).scrollTop();
        if (scroll < 400) {
            $(".header-sticky").removeClass("sticky-bar");
            $("#back-top").fadeOut(300);
        } else {
            $(".header-sticky").addClass("sticky-bar");
            $("#back-top").fadeIn(300);
        }
    });
    
    
    /*----------------------------------------------
        :: Swiper Slider Active
    ----------------------------------------------*/
    var swiper = new Swiper(".h1-Hero-active", {
        effect: "fade",
        loop: true,
        slidesPerView: 1,
        speed: 4000,
        clickable: false,
        pagination: {
            el: ".swiper-pagination",
            clickable: false,
        },
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        scrollbar: {
            el: ".swiper-pagination",
        },
    });
    var swiper = new Swiper(".testimonialTwo-active", {
        effect: "fade",
        pagination: {
            el: ".swiper-pagination",
            clickable: false,
        },
        autoplay: {
            delay: 6000,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });
    var swiper = new Swiper(".testimonialThree-active", {
        loop: true,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: ".swiper-pagination",
            type: "progressbar",
        },
    });
    var swiper = new Swiper(".tourSwiper-active", {
        loop: true,
        slidesPerView: 1,
        centeredSlides: true,
        spaceBetween: 24,
        autoplay: {
            delay: 6000,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            992: {
                slidesPerView: 2,
            },
        },
    });
    var swiper = new Swiper(".favSwiper-active", {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 24,
        autoplay: {
            delay: 6000,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            1200: {
                slidesPerView: 4,
            },
            992: {
                slidesPerView: 3,
            },
            576: {
                slidesPerView: 2,
            },
        },
    });
    var swiper = new Swiper(".brandSwiper-active", {
        centeredSlides: true,
        loop: true,
        speed: 500,
        slidesPerView: 2,
        spaceBetween: 40,
        autoplay: {
            delay: 3000,
        },
        breakpoints: {
            992: {
                slidesPerView: 5,
            },
            768: {
                slidesPerView: 3,
            },
        },
    });
    var swiper = new Swiper(".whySwiper-active", {
        loop: true,
        speed: 500,
        slidesPerView: 1,
        spaceBetween: 24,
        autoplay: {
            delay: 3000,
        },
        breakpoints: {
            992: {
                slidesPerView: 3,
            },
            768: {
                slidesPerView: 2,
            },
        },
    });
    var swiper = new Swiper(".bulletLeftSwiper-active", {
        allowTouchMove: true,
        slidesPerView: 1,
        speed: 6000,
        loop: true,
        autoplay: {
            delay: 0,
            disableOnInteraction: true,
            reverseDirection: true,
            pauseOnMouseEnter: true,
        },
        spaceBetween: 24,
        breakpoints: {
            1400: {
                slidesPerView: 5,
            },
            992: {
                slidesPerView: 3,
            },
            576: {
                slidesPerView: 2,
            },
        },
    });
    var swiper = new Swiper(".bulletRightSwiper-active", {
        allowTouchMove: true,
        slidesPerView: 1,
        speed: 6000,
        loop: true,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        spaceBetween: 24,
        breakpoints: {
            1400: {
                slidesPerView: 5,
            },
            992: {
                slidesPerView: 3,
            },
            576: {
                slidesPerView: 2,
            },
        },
    });
    var swiper = new Swiper(".destinationSwiper-active", {
        slidesPerView: 1,
        speed: 1000,
        loop: true,
        fade: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
    });

    /*----------------------------------------------
        :: Select 2
    ----------------------------------------------*/
    $(".select2").select2({
        placeholder: "Choose one",
        width: "100%",
    });

    /*----------------------------------------------
        :: Fancy box
    ----------------------------------------------*/
    $(document).ready(function() {
        $(document).on("click", "[data-fancybox]", function(event) {
            event.preventDefault();
            var videoUrl = $(this).attr("href");
            Fancybox.show([
                {
                    type: "iframe",
                    src: videoUrl
                }
            ]);
        });
    });
    
    /*----------------------------------------------
        :: Custom click expand icon
    ----------------------------------------------*/
    document.addEventListener("DOMContentLoaded", function () {
        const hoverIcons = document.querySelectorAll(".hover-icon");
        hoverIcons.forEach((hoverIcon) => {
            hoverIcon.addEventListener("click", function () {
                const socialIcons = this.nextElementSibling;
                socialIcons.classList.toggle("active");
            });
        });
    });

    /*----------------------------------------------
        :: JS for Price Range slider
    ----------------------------------------------*/
    $(function () {
        $("#slider-range").slider({
            range: true,
            min: 130,
            max: 500,
            values: [130, 250],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
            },
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));
    });

    /*----------------------------------------------
        :: Custom Dropdown
    ----------------------------------------------*/
    $(document).ready(function () {
        function updateTotalQuantity() {
            var totalQuantity = 0;
            $(".input-qty").each(function () {
                totalQuantity += parseInt($(this).val(), 10);
            });
            if (totalQuantity > 0) {
                $(".user-result").text(totalQuantity);
            } else {
                $(".user-result").text("");
            }
        }
        function updateTotalQuantityTwo() {
            var totalQuantity = 0;
            $(".input-qty-two").each(function () {
                totalQuantity += parseInt($(this).val(), 10) || 0;
            });
            if (totalQuantity > 0) {
                $(".calc-user").text(totalQuantity);
            } else {
                $(".calc-user").text("");
            }
        }
        $(".qty-btn-plus, .qty-btn-minus").click(function () {
            var $input = $(this).siblings(".input-qty");
            var value = parseInt($input.val(), 10);
    
            if ($(this).hasClass("qty-btn-plus")) {
                value++;
            } else {
                if (value > 0) {
                    value--;
                }
            }
            $input.val(value);
        });
        $(".qty-btn-plus-two, .qty-btn-minus-two").click(function () {
            var $input = $(this).siblings(".input-qty-two");
            var value = parseInt($input.val(), 10);
    
            if ($(this).hasClass("qty-btn-plus-two")) {
                value++;
            } else {
                if (value > 0) {
                    value--;
                }
            }
            $input.val(value);
            updateTotalQuantityTwo();
        });
        $(".done-btn").click(function () {
            updateTotalQuantity();
        });
    });


    /*----------------------------------------------
        Custom select2 Data
    ----------------------------------------------*/
    var destination = [
        { id: "", text: "Destination", description: "" },
        {
            id: 1,
            text: "USA, Turkish ",
            description: "Istanbul Airport",
        },
        {
            id: 2,
            text: "Chittagong, Turkish ",
            description: "Shah Amanat International Airport (CGP)",
        },
        { id: 3, text: "Sydney, Australia", description: "Sydney Airport (SYD)" },
        {
            id: 4,
            text: "Melbourne, Australia",
            description: "Melbourne Airport (MEL)",
        },
        {
            id: 5,
            text: "New York, United States",
            description: "John F. Kennedy International Airport (JFK)",
        },
        {
            id: 6,
            text: "Los Angeles, United States",
            description: "Los Angeles International Airport (LAX)",
        },
        {
            id: 7,
            text: "London, United Kingdom",
            description: "Heathrow Airport (LHR)",
        },
        {
            id: 8,
            text: "Manchester, United Kingdom",
            description: "Manchester Airport (MAN)",
        },
    ];

    // Initialize Select2
    $(".select2-title").on('click', function() {
    var $select = $(this).closest('.select-dropdown-section').find('select');
    $select.select2('open');
    });
    $(".destination-dropdown").select2({
        containerCssClass: "custom-select2-dropdown",
        dropdownCssClass: "custom-select2-dropdown-container",
    });
    
    $(".destination-dropdown-two").select2({
        data: destination,
        placeholder: "Destination",
        containerCssClass: "custom-select2-dropdown",
        dropdownCssClass: "custom-select2-dropdown-container",
        templateResult: destinationResult,
        templateSelection: destinationSelectionTwo,
    });
    $(".destination-dropdown-three").select2({
        data: destination,
        placeholder: "Destination",
        containerCssClass: "custom-select2-dropdown-three",
        dropdownCssClass: "custom-select2-dropdown-container",
        templateResult: destinationResult,
        templateSelection: destinationSelectionThree,
    });

    function destinationResult(item) {
        if (!item.id) {
            return item.text;
        }
        var $result = $('<div class="select2-result">' + '<h4 class="airport-desc">' + item.text + "</h4>" + '<p class="airport-name">' + item.description + "</p>" + "</div>");
        return $result;
    }

    function destinationSelectionTwo(item) {
        if (!item.id) {
            return "USA";
        }

        $(".destination-result").html(item.description);
        return item.text;
    }
    function destinationSelectionThree(item) {
        if (!item.id) {
            return "USA";
        }

        $(".destination-result-three").html(item.description);
        return item.text;
    }
    
    /*----------------------------------------------
        Date picker
    ----------------------------------------------*/
    $(".custom-date").daterangepicker();
    $(".custom-date-three").daterangepicker();
    $(".custom-date").on("apply.daterangepicker", function (ev, picker) {
        var startDate = picker.startDate.format("DD-MM-YYYY");
        var endDate = picker.endDate.format("DD-MM-YYYY");
        $(".date-result").text(startDate);
    });
    $(".date-time-dropdown").daterangepicker();
    $(".date-time-dropdown").on("apply.daterangepicker", function (ev, picker) {
        var startDate = picker.startDate.format("dddd, MMM D, YYYY");
        var endDate = picker.endDate.format("dddd, MMM D, YYYY");
        $(".date-time-result").text(startDate);
    });
    $(".custom-date-three").on("apply.daterangepicker", function (ev, picker) {
        var StartMonth = picker.startDate.format("MMMM");
        var StartYear = picker.startDate.format("dddd, D, YYYY");
        var EndMonth = picker.endDate.format("MMMM");
        var EndYear = picker.endDate.format("dddd, D, YYYY");
        $(".month-result").text(StartMonth);
        $(".year-result").text(StartYear);
        $(".month-result-two").text(EndMonth);
        $(".year-result-two").text(EndYear);
    });

    
    /*----------------------------------------------
        Always open Date picker
    ----------------------------------------------*/
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function (start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });


    /*----------------------------------------------
        User picker Dropdown
    ----------------------------------------------*/
    $(".user-picker-dropdown").on("click", function (event) {
        $(".user-picker").toggleClass("show");
        event.stopPropagation();
    });
    $(".user-picker").click(function (event) {
        event.stopPropagation();
    });
    $(document).click(function (event) {
        if (!$(event.target).closest(".user-picker").length && !$(event.target).is(".user-picker-dropdown")) {
            $(".user-picker").removeClass("show");
        }
    });
    $(".done-btn").click(function (event) {
        $(".user-picker").removeClass("show");
        event.stopPropagation();
    });
    $(".swap-icon").click(function () {
        $(this).find('i').toggleClass('rotate-icon');
    });


    /*----------------------------------------------
        :: Custom click ad remove
    ----------------------------------------------*/
    $(document).ready(function () {
        $(document).on("click", ".cancel-btn", function () {
            $(".ad-area").css("display", "none");
        });
        $(document).on("click", ".cancel-btn-two", function () {
            $(".ad-area-two").css("display", "none");
        });
    });


    /*-----------------------------------------------
        :: Slick Nav [ Mobile Menu ]
    -----------------------------------------------*/
    var slickNavInitialized = false;
    $(document).ready(function () {
        if (!slickNavInitialized) {
            var menu = $("#navigation");
            var filterMenu = $("#filterMenu");
            if (menu.length) {
                menu.slicknav({
                    prependTo: ".mobile_menu",
                    closedSymbol: "+",
                    openedSymbol: "-",
                });
                slickNavInitialized = true;
            }
            if (filterMenu.length) {
                filterMenu.slicknav({
                    label: "Filter",
                    duplicate: true,
                    duration: 200,
                    easingOpen: "swing",
                    easingClose: "swing",
                    closedSymbol: "&#9658;",
                    openedSymbol: "&#9660;",
                    prependTo: ".filter_menu",
                });
                slickNavInitialized = true;
            }
        }
    });
    
    /*-----------------------------------------------
        :: Search box
    -----------------------------------------------*/
    var $searchIcon = $(".search-bar");
    var $searchContainer = $(".search-container");
    var $searchOverlay = $(".search-overlay");

    function closeSearch() {
        $searchContainer.hide();
        $searchOverlay.hide();
    }
    $(document).on("click", ".hamburger-icon", function () {
        closeSearch();
    });
    $(document).on("click", ".search-bar", function () {
        $searchContainer.toggle();
        $searchOverlay.toggle();
        $('body').addClass('no-scroll');
    });
    $(document).on("click", "#closeSearch", function () {
        closeSearch();
        $('body').removeClass('no-scroll');
    });
    $(document).on("click", function (event) {
        if (!$(event.target).closest($searchIcon).length && !$(event.target).closest($searchContainer).length) {
            closeSearch();
            $('body').removeClass('no-scroll');
        }
    });
    $(document).on("keydown", function (event) {
        if (event.key === "Escape") {
            closeSearch();
            $('body').removeClass('no-scroll');
        }
        if ((event.ctrlKey || event.metaKey) && event.key === "k") {
            $searchContainer.show();
            $searchOverlay.show();
            $('body').addClass('no-scroll');
        }
    });


    /*------------------------------------------------
        :: Filter side nav
    ------------------------------------------------*/
    $(document).ready(function () {
        $(document).on("click", "#hamburger", function (e) {
            e.stopPropagation();
            $(".search-filter-section").toggleClass("sidenav-active");
            $(".cover").addClass("cover-active");
        });
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".search-filter-section").length && !$(e.target).is("#hamburger")) {
                $(".search-filter-section").removeClass("sidenav-active");
                $(".cover").removeClass("cover-active");
            }
        });
        $(document).on("click", ".close-btn", function (e) {
            $(".search-filter-section").removeClass("sidenav-active");
            $(".cover").removeClass("cover-active");
        });
    });


    /*-------------------------------------------------
        :: Nav tab
    -------------------------------------------------*/

    $(".tab-section-one").show();
    $(".tab-btn a").on("click", function () {
        $(".tab-btn a").removeClass("active");
        $(this).addClass("active");
        $(".tab-section").hide();
        var tabId = $(this).data("tab-id");
        $("#" + tabId).show();
    });

    /*-------------------------------------------------
        :: WOW active
    -------------------------------------------------*/
    new WOW().init();

    /*-------------------------------------------------
        :: Hover Tilt Effect
    -------------------------------------------------*/
    $(".tilt-effect").tilt({
        maxTilt: 6,
        easing: "cubic-bezier(.03,.98,.52,.99)",
        speed: 500,
        transition: true,
    });

    /*-------------------------------------------------
        :: Password Show Hide
    -------------------------------------------------*/
    $(document).ready(function () {
        $(document).on("click", ".toggle-password", function () {
            var passwordInput = $($(this).siblings(".password-input"));
            var icon = $(this);
            if (passwordInput.attr("type") == "password") {
                passwordInput.attr("type", "text");
                icon.removeClass("ri-eye-line").addClass("ri-eye-off-line");
            } else {
                passwordInput.attr("type", "password");
                icon.removeClass("ri-eye-off-line").addClass("ri-eye-line");
            }
        });
    });


    /*-------------------------------------------------
        :: Play video Slider
    -------------------------------------------------*/
    document.addEventListener("DOMContentLoaded", function () {
        var videos = document.querySelectorAll(".hero-slider-video");
        videos.forEach(function (video) {
            video.controls = false;
            ["mousedown", "mouseup", "click"].forEach(function (eventType) {
                video.addEventListener(eventType, function (event) {
                    event.preventDefault();
                });
            });
        });
    });
    function togglePlay(event) {
        event.preventDefault();
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }

    /*----------------------------------------------
        Light & Dark Mode
    ----------------------------------------------*/
    function updateLogoOnTheme() {
        // White logo Dark logo path Change
        var changeLogos = document.querySelectorAll(".changeLogo");
        var whiteLogoPath = document.head
            .querySelector("meta[white-logo]")
            .getAttribute("white-logo");
        var lightLogoPath = document.head
            .querySelector("meta[logo]")
            .getAttribute("logo");

        var imagePath =
            localStorage.theme === "dark" ? whiteLogoPath : lightLogoPath;
        changeLogos.forEach(function (changeLogo) {
            changeLogo.src = imagePath;
        });
    }
    updateLogoOnTheme();
    // Button icon change
    const themeModeAction = () => {
        if (localStorage.theme === "dark") {
            $(".change-theme-mode i").attr(
                "class",
                "ri-moon-line",
                "ri-sun-line"
            );
        } else {
            $(".change-theme-mode i").attr(
                "class",
                "ri-sun-line",
                "ri-moon-line"
            );
        }
    };
    themeModeAction();
    //Toggle Buttons
    const ToggleThemes = document.getElementsByClassName("ToggleThemeButton");
    for (const ToggleTheme of ToggleThemes) {
        ToggleTheme.addEventListener("click", () => {
            const theme = localStorage.theme === "dark" ? "light" : "dark";
            setTheme(theme);
            themeModeAction();
            updateLogoOnTheme();
        });
    }


    /*-----------------------------------
        OFF inspections
    -----------------------------------*/
    // document.addEventListener("contextmenu", (event) => event.preventDefault());
    // document.onkeydown = function (e) {
    //     if (event.keyCode == 123) {
    //         return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == "I".charCodeAt(0)) {
    //         return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == "J".charCodeAt(0)) {
    //         return false;
    //     }
    //     if (e.ctrlKey && e.keyCode == "U".charCodeAt(0)) {
    //         return false;
    //     }
    // };

    /*-------------------------------------------------
        :: Back To Top
    -------------------------------------------------*/
    (function () {
        var progressPath = document.querySelector(".progressParent path");
        var pathLength = progressPath?.getTotalLength();
        if (pathLength) {
            progressPath.style.transition = progressPath.style.WebkitTransition = "none";
            progressPath.style.strokeDasharray = pathLength + " " + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition = progressPath.style.WebkitTransition = "stroke-dashoffset 10ms linear";
            var updateProgress = function () {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength) / height;
                progressPath.style.strokeDashoffset = progress;
            };
            updateProgress();
            $(window).scroll(updateProgress);
            var offset = 50;
            var duration = 550;
            jQuery(window).on("scroll", function () {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery(".progressParent").addClass("rn-backto-top-active");
                } else {
                    jQuery(".progressParent").removeClass("rn-backto-top-active");
                }
            });
            jQuery(".progressParent").on("click", function (event) {
                event.preventDefault();
                jQuery("html, body").animate({ scrollTop: 0 }, duration);
                return false;
            });
        }
    })();

    /*----------------------------------------------
        :: JS Year
    ---------------------------------------------*/
    const currentYear = document.querySelectorAll(".current-year");
    if (currentYear.length > 0) {
        const date = new Date();
        const currYear = date.getFullYear();
        currentYear.forEach((ele) => {
            ele.innerText = currYear;
        });
    }

    /*--------------------------------------------
        :: Payment Gateway selection
    ---------------------------------------------*/
    $(".payment-gateway-list li").on("click", function () {
        $(".payment-gateway-list li").removeClass("selected");
        $(this).addClass("selected");
    });

})(jQuery);
