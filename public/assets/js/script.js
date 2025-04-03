(function ($) {
    "use strict";
    function handlePreloader() {
        if ($(".preloader").length) {
            $(".preloader").delay(200).fadeOut(500);
        }
    }
    function headerStyle() {
        if ($(".main-header").length) {
            var windowpos = $(window).scrollTop();
            var siteHeader = $(".main-header");
            var scrollLink = $(".scroll-to-top");
            var sticky_header = $(".main-header");
            if (windowpos > 1) {
                siteHeader.addClass("fixed-header animated slideInDown");
                scrollLink.fadeIn(300);
            } else {
                siteHeader.removeClass("fixed-header animated slideInDown");
                scrollLink.fadeOut(300);
            }
        }
    }
    headerStyle();
    if ($(".sticky-header").length) {
        var stickyMenuContent = $(".main-header .main-box .nav-outer").html();
        $(".sticky-header .main-box").append(stickyMenuContent);
        $(".main-header .cart-btn, .mobile-header .cart-btn").on(
            "click",
            function () {
                $("body").addClass("sidebar-cart-active");
            }
        );
        $(".main-header .cart-back-drop, .main-header .close-cart").on(
            "click",
            function () {
                $("body").removeClass("sidebar-cart-active");
            }
        );
    }
    if ($(".dial").length) {
        var elm = $(".dial");
        var color = elm.attr("data-fgColor");
        var perc = elm.attr("value");
        elm.knob({
            value: 0,
            min: 0,
            max: 100,
            skin: "tron",
            readOnly: !0,
            thickness: 0.45,
            dynamicDraw: !0,
            displayInput: !1,
        });
        $({ value: 0 }).animate(
            { value: perc },
            {
                duration: 2000,
                easing: "swing",
                progress: function () {
                    elm.val(Math.ceil(this.value)).trigger("change");
                },
            }
        );
        var $t = $(".pie-graph .count-box"),
            n = $t.find(".count-text").attr("data-stop"),
            r = parseInt($t.find(".count-text").attr("data-speed"), 10);
        if (!$t.hasClass("counted")) {
            $t.addClass("counted");
            $({ countNum: $t.find(".count-text").text() }).animate(
                { countNum: n },
                {
                    duration: r,
                    easing: "linear",
                    step: function () {
                        $t.find(".count-text").text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $t.find(".count-text").text(this.countNum);
                    },
                }
            );
        }
    }
    if ($("#nav-mobile").length) {
        jQuery(function ($) {
            var $navbar = $("#navbar");
            var $mobileNav = $("#nav-mobile");
            $navbar.clone().removeClass("navbar").appendTo($mobileNav);
            $mobileNav.mmenu({
                counters: !1,
                extensions: ["position-bottom", "fullscreen", "theme-black"],
                offCanvas: { position: "left", zposition: "front" },
            });
            var outerHeightMainHeader = $(".main-header").outerHeight();
            $mobileNav.css({
                height: "calc(100% - " + outerHeightMainHeader + "px)",
            });
        });
    }
    if ($(".banner-carousel").length) {
        $(".banner-carousel").owlCarousel({
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            loop: !0,
            margin: 0,
            items: 1,
            nav: !0,
            dots: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 5000,
            touchDrag: !1,
            mouseDrag: !1,
            navText: [
                '<span class="fa fa-arrow-left"></span>',
                '<span class="fa fa-arrow-right"></span>',
            ],
        });
    }
    if ($(".testimonial-carousel").length) {
        $(".testimonial-carousel").owlCarousel({
            loop: !0,
            margin: 15,
            items: 1,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
        });
    }
    if ($(".testimonial-carousel-two").length) {
        $(".testimonial-carousel-two").owlCarousel({
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            loop: !0,
            margin: 15,
            items: 1,
            nav: !0,
            dots: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
        });
    }
    if ($(".testimonial-carousel-three").length) {
        $(".testimonial-carousel-three").owlCarousel({
            loop: !0,
            margin: 10,
            items: 2,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 1 },
                768: { items: 2 },
            },
        });
    }
    if ($(".sponsors-carousel").length) {
        $(".sponsors-carousel").owlCarousel({
            loop: !0,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                480: { items: 2 },
                600: { items: 3 },
                768: { items: 4 },
                1024: { items: 5 },
                1400: { items: 6 },
                1800: { items: 7 },
            },
        });
    }
    if ($(".sponsors-carousel-two").length) {
        $(".sponsors-carousel-two").owlCarousel({
            loop: !0,
            nav: !1,
            smartSpeed: 500,
            margin: 60,
            autoplay: !0,
            autoWidth: !0,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                480: { items: 2 },
                600: { items: 3 },
                768: { items: 4 },
                1024: { items: 5 },
                1400: { items: 6 },
            },
        });
    }
    if ($(".candidates-carousel").length) {
        $(".candidates-carousel").owlCarousel({
            loop: !0,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                768: { items: 3 },
                1024: { items: 4 },
            },
        });
    }
    if ($(".companies-carousel").length) {
        $(".companies-carousel").owlCarousel({
            loop: !0,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                768: { items: 3 },
                1024: { items: 4 },
            },
        });
    }
    if ($(".companies-carousel-two").length) {
        $(".companies-carousel-two").owlCarousel({
            loop: !0,
            nav: !1,
            smartSpeed: 500,
            autoplay: !0,
            margin: 0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                768: { items: 3 },
                1024: { items: 4 },
                1280: { items: 5 },
            },
        });
    }
    if ($(".job-carousel").length) {
        $(".job-carousel").owlCarousel({
            loop: !0,
            nav: !1,
            margin: 30,
            smartSpeed: 500,
            autoplay: !0,
            autoplayTimeout: 10000,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 1 },
                768: { items: 2 },
                1024: { items: 3 },
            },
        });
    }
    if ($(".single-item-carousel").length) {
        $(".single-item-carousel").owlCarousel({
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            loop: !0,
            margin: 0,
            nav: !0,
            smartSpeed: 500,
            autoHeight: !0,
            autoplay: !0,
            autoplayTimeout: 10000,
            touchDrag: !1,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
        });
    }
    if ($(".three-items-carousel").length) {
        $(".three-items-carousel").owlCarousel({
            loop: !0,
            margin: 22,
            nav: !0,
            smartSpeed: 400,
            autoplay: !0,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 1 },
                768: { items: 2 },
                1366: { items: 3 },
            },
        });
    }
    if ($(".four-items-carousel").length) {
        $(".four-items-carousel").owlCarousel({
            loop: !0,
            margin: 22,
            nav: !0,
            smartSpeed: 400,
            autoplay: !0,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                600: { items: 1 },
                768: { items: 2 },
                1366: { items: 3 },
                1600: { items: 4 },
            },
        });
    }
    if ($(".clients-carousel").length) {
        $(".clients-carousel").owlCarousel({
            loop: !0,
            margin: 30,
            nav: !0,
            smartSpeed: 400,
            autoplay: !0,
            navText: [
                '<span class="flaticon-back"></span>',
                '<span class="flaticon-next"></span>',
            ],
            responsive: {
                0: { items: 1 },
                480: { items: 2 },
                600: { items: 3 },
                768: { items: 4 },
                1280: { items: 5 },
            },
        });
    }
    if (
        $(".gallery-widget .image-carousel").length &&
        $(".gallery-widget .thumbs-carousel").length
    ) {
        var $sync1 = $(".gallery-widget .image-carousel"),
            $sync2 = $(".gallery-widget .thumbs-carousel"),
            flag = !1,
            duration = 500;
        $sync1
            .owlCarousel({
                loop: !1,
                items: 1,
                margin: 0,
                nav: !0,
                navText: [
                    '<span class="icon flaticon-back"></span>',
                    '<span class="icon flaticon-next"></span>',
                ],
                dots: !1,
                autoplay: !0,
                autoplayTimeout: 5000,
            })
            .on("changed.owl.carousel", function (e) {
                if (!flag) {
                    flag = !1;
                    $sync2.trigger("to.owl.carousel", [
                        e.item.index,
                        duration,
                        !0,
                    ]);
                    flag = !1;
                }
            });
        $sync2
            .owlCarousel({
                loop: !1,
                margin: 30,
                items: 1,
                nav: !1,
                navText: [
                    '<span class="icon flaticon-back"></span>',
                    '<span class="icon flaticon-next"></span>',
                ],
                dots: !1,
                center: !1,
                autoplay: !0,
                autoplayTimeout: 5000,
                responsive: {
                    0: { items: 2, autoWidth: !1 },
                    400: { items: 2, autoWidth: !1 },
                    600: { items: 3, autoWidth: !1 },
                    800: { items: 5, autoWidth: !1 },
                    1024: { items: 4, autoWidth: !1 },
                },
            })
            .on("click", ".owl-item", function () {
                $sync1.trigger("to.owl.carousel", [
                    $(this).index(),
                    duration,
                    !0,
                ]);
            })
            .on("changed.owl.carousel", function (e) {
                if (!flag) {
                    flag = !0;
                    $sync1.trigger("to.owl.carousel", [
                        e.item.index,
                        duration,
                        !0,
                    ]);
                    flag = !1;
                }
            });
    }
    if ($(".scroll-nav").length) {
        $(".scroll-nav ul.navigation").onePageNav();
    }
    function ratingOverview(ratingElem) {
        $(ratingElem).each(function () {
            var dataRating = $(this).attr("data-rating");
            if (dataRating >= 4.0) {
                $(this).addClass("high");
                $(this)
                    .find(".rating-bars-rating-inner")
                    .css({ width: (dataRating / 5) * 100 + "%" });
            } else if (dataRating >= 3.0) {
                $(this).addClass("mid");
                $(this)
                    .find(".rating-bars-rating-inner")
                    .css({ width: (dataRating / 5) * 80 + "%" });
            } else if (dataRating < 3.0) {
                $(this).addClass("low");
                $(this)
                    .find(".rating-bars-rating-inner")
                    .css({ width: (dataRating / 5) * 60 + "%" });
            }
        });
    }
    $(".rating-bars").appear(function () {
        ratingOverview(".rating-bars-rating");
    });
    $(".leave-rating input").change(function () {
        var $radio = $(this);
        $(".leave-rating .selected").removeClass("selected");
        $radio.closest("label").addClass("selected");
    });
    var uploadButton = {
        $button: $(".uploadButton-input"),
        $nameField: $(".uploadButton-file-name"),
    };
    uploadButton.$button.on("change", function () {
        _populateFileField($(this));
    });
    function _populateFileField($button) {
        var selectedFile = [];
        for (var i = 0; i < $button.get(0).files.length; ++i) {
            selectedFile.push($button.get(0).files[i].name + "<br>");
        }
        uploadButton.$nameField.html(selectedFile);
    }
    if ($(".mobile-search-btn").length) {
        $(".mobile-search-btn").on("click", function () {
            $(".main-header").addClass("moblie-search-active");
        });
        $(".close-search, .search-back-drop").on("click", function () {
            $(".main-header").removeClass("moblie-search-active");
        });
    }
    $(".header-search-form input").focus(function () {
        $(this).parent().addClass("active");
        $("body").addClass("search-active");
    });
    $(".header-search-form input").focusout(function () {
        $(this).parent().removeClass("active");
        $(".search-list").slideUp();
        $("body").removeClass("search-active");
    });
    if ($("#toggle-user-sidebar").length) {
        $("#toggle-user-sidebar, .dashboard-option a").on("click", function () {
            $("body").toggleClass("user-sidebar-active");
        });
        $(".sidebar-backdrop").on("click", function () {
            $("body").removeClass("user-sidebar-active");
        });
    }
    if ($("#more-options").length) {
        $("#more-options").on("click", function () {
            $(this).parent().toggleClass("active");
        });
    }
    if ($(".toggle-filters").length) {
        $(".toggle-filters").on("click", function () {
            $("body").toggleClass("active-filters");
        });
        $(".close-filters, .filters-backdrop").on("click", function () {
            $("body").removeClass("active-filters");
        });
        $(".hide-filters .toggle-filters").on("click", function () {
            $(this).html(
                $(this).html() ==
                    '<span class="icon flaticon-plus-symbol"></span>Hide Filters'
                    ? '<span class="icon flaticon-controls"></span>Show Filters'
                    : '<span class="icon flaticon-plus-symbol"></span>Hide Filters'
            );
        });
        $(".close-filters").on("click", function () {
            $(".hide-filters .toggle-filters").html(
                $(this).html() ==
                    '<span class="icon flaticon-controls"></span>Hide Filters'
                    ? '<span class="icon flaticon-plus-symbol"></span>Hide Filters'
                    : '<span class="icon flaticon-controls"></span>Show Filters'
            );
        });
    }
    function removeFiltersOnMobile() {
        if ($(window).width() <= 1023) {
            $("body").removeClass("active-filters");
            $(".hide-filters .toggle-filters").html(
                $(this).html() ==
                    '<span class="icon flaticon-controls"></span>Hide Filters'
                    ? '<span class="icon flaticon-plus-symbol"></span>Hide Filters'
                    : '<span class="icon flaticon-controls"></span>Show Filters'
            );
        }
    }
    removeFiltersOnMobile();
    if ($(".custom-select-box").length) {
        $(".custom-select-box")
            .selectmenu()
            .selectmenu("menuWidget")
            .addClass("overflow");
    }
    if ($(".chosen-select").length) {
        $(".chosen-select").chosen({
            disable_search_threshold: 10,
            width: "100%",
        });
    }
    if ($(".chosen-search-select").length) {
        $(".chosen-search-select").chosen({ width: "100%" });
    }
    if ($(".sortby-select").length) {
        $(".sortby-select").select2();
    }
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(".call-modal").on("click", function (event) {
        event.preventDefault();
        this.blur();
        $.get(this.href, function (html) {
            $(html)
                .appendTo("body")
                .modal({
                    closeExisting: !0,
                    fadeDuration: 300,
                    fadeDelay: 0.15,
                });
        });
    });
    if ($(".message-box").length) {
        $(".message-box .close-btn").on("click", function (e) {
            $(this).parent(".message-box").fadeOut();
        });
    }
    if ($(".toggle-contact").length) {
        $(".toggle-contact").on("click", function (e) {
            $("body").toggleClass("active-chat-contacts");
        });
        $(".contacts li").on("click", function (e) {
            $(this).addClass("active");
            $(this).siblings("li").removeClass("active");
            $("body").removeClass("active-chat-contacts");
        });
    }
    if ($(".accordion-box").length) {
        $(".accordion-box").on("click", ".acc-btn", function () {
            var outerBox = $(this).parents(".accordion-box");
            var target = $(this).parents(".accordion");
            if ($(this).hasClass("active") !== !0) {
                $(outerBox).find(".accordion .acc-btn").removeClass("active ");
            }
            if ($(this).next(".acc-content").is(":visible")) {
                return !1;
            } else {
                $(this).addClass("active");
                $(outerBox).children(".accordion").removeClass("active-block");
                $(outerBox)
                    .find(".accordion")
                    .children(".acc-content")
                    .slideUp(300);
                target.addClass("active-block");
                $(this).next(".acc-content").slideDown(300);
            }
        });
    }
    if ($(".count-box").length) {
        $(".count-box").appear(
            function () {
                var $t = $(this),
                    n = $t.find(".count-text").attr("data-stop"),
                    r = parseInt($t.find(".count-text").attr("data-speed"), 10);
                if (!$t.hasClass("counted")) {
                    $t.addClass("counted");
                    $({ countNum: $t.find(".count-text").text() }).animate(
                        { countNum: n },
                        {
                            duration: r,
                            easing: "linear",
                            step: function () {
                                $t.find(".count-text").text(
                                    Math.floor(this.countNum)
                                );
                            },
                            complete: function () {
                                $t.find(".count-text").text(this.countNum);
                            },
                        }
                    );
                }
            },
            { accY: 0 }
        );
    }
    if ($(".progress-line").length) {
        $(".progress-line").appear(
            function () {
                var el = $(this);
                var percent = el.data("width");
                $(el).css("width", percent + "%");
            },
            { accY: 0 }
        );
    }
    if ($(".tabs-box").length) {
        $(".tabs-box .tab-buttons .tab-btn").on("click", function (e) {
            e.preventDefault();
            var target = $($(this).attr("data-tab"));
            if ($(target).is(":visible")) {
                return !1;
            } else {
                target
                    .parents(".tabs-box")
                    .find(".tab-buttons")
                    .find(".tab-btn")
                    .removeClass("active-btn");
                $(this).addClass("active-btn");
                target
                    .parents(".tabs-box")
                    .find(".tabs-content")
                    .find(".tab")
                    .fadeOut(0);
                target
                    .parents(".tabs-box")
                    .find(".tabs-content")
                    .find(".tab")
                    .removeClass("active-tab animated fadeIn");
                $(target).fadeIn(300);
                $(target).addClass("active-tab animated fadeIn");
            }
        });
    }
    if ($(".price-range-slider").length) {
        $(".price-range-slider").slider({
            range: !0,
            min: 0,
            max: 90,
            values: [0, 84],
            slide: function (event, ui) {
                $("input.property-amount").val(
                    ui.values[0] + " - " + ui.values[1]
                );
            },
        });
        $("input.property-amount").val(
            $(".price-range-slider").slider("values", 0) +
                " - $" +
                $(".price-range-slider").slider("values", 1)
        );
    }
    if ($(".range-slider-one").length) {
        $(".range-slider-one .range-slider").slider({
            range: !0,
            min: 1900,
            max: 2030,
            values: [1923, 2023],
            slide: function (event, ui) {
                $(".range-slider-one .count").text(
                    ui.values[0] + " - " + ui.values[1]
                );
            },
        });
        $(".range-slider-one .count").text(
            $(".range-slider").slider("values", 0) +
                " - " +
                $(".range-slider").slider("values", 1)
        );
    }
    if ($(".area-range-slider").length) {
        $(".area-range-slider").slider({
            range: !0,
            min: 0,
            max: 100,
            values: [0, 50],
            slide: function (event, ui) {
                $(".area-amount").text(ui.values[1]);
            },
        });
        $(".area-amount").text($(".area-range-slider").slider("values", 1));
    }
    if ($(".salary-range-slider").length) {
        $(".salary-range-slider").slider({
            range: !0,
            min: 0,
            max: 20000,
            values: [0, 15000],
            slide: function (event, ui) {
                $(".salary-amount .min").text(ui.values[0]);
                $(".salary-amount .max").text(ui.values[1]);
            },
        });
        $(".salary-amount .min").text(
            $(".salary-range-slider").slider("values", 0)
        );
        $(".salary-amount .max").text(
            $(".salary-range-slider").slider("values", 1)
        );
    }
    if ($(".lightbox-image").length) {
        $(".lightbox-image").fancybox({
            openEffect: "fade",
            closeEffect: "fade",
            helpers: { media: {} },
        });
    }
    if ($("#email-form").length && 0) {
        $("#submit").click(function () {
            var o = new Object();
            var form = "#email-form";
            var username = $("#email-form .username").val();
            var email = $("#email-form .email").val();
            var subject = $("#email-form .subject").val();
            var message = $("#email-form .message").val();
            var regex =
                /<\s*script.*?>.*?<\s*\/\s*script\s*>|<\s*\?php.*?\?>|<.*?>/i;
            if (
                username == "" ||
                email == "" ||
                subject == "" ||
                message == ""
            ) {
                $(form + " .response").html(
                    '<div class="failed">Please fill the required fields.</div>'
                );
                return !1;
            }
            if (
                regex.test(username) ||
                regex.test(email) ||
                regex.test(subject) ||
                regex.test(message)
            ) {
                $(form + " .response").html(
                    '<div class="failed">Invalid input detected. Please avoid using special characters or code.</div>'
                );
                return !1;
            }
            $.ajax({
                url: base_url + "contact-us-submit",
                method: "POST",
                data: $(form).serialize(),
                beforeSend: function () {
                    $("#email-form .response").html(
                        '<div class="text-info"><img src="images/icons/preloader.gif"> Loading...</div>'
                    );
                },
                success: function (data) {
                    $("form").trigger("reset");
                    $("#email-form .response").fadeIn().html(data);
                    setTimeout(function () {
                        $("#email-form .response").fadeOut("slow");
                    }, 5000);
                },
                error: function () {
                    $("#email-form .response").fadeIn().html(data);
                },
            });
        });
    }
    if ($(".scroll-to-target").length) {
        $(".scroll-to-target").on("click", function () {
            var target = $(this).attr("data-target");
            $("html, body").animate(
                { scrollTop: $(target).offset().top },
                1500
            );
        });
    }
    if ($(".listing-nav li").length) {
        $(".listing-nav li").on("click", function () {
            var target = $(this).attr("data-target");
            $(this).addClass("active").siblings("li").removeClass("active");
            $(target).appear(function () {
                $(this).addClass("active");
            });
            $("html, body").animate(
                { scrollTop: $(target).offset().top + -90 },
                1000
            );
        });
    }
    if ($(".sticky-sidebar").length) {
        $(".sidebar-side").theiaStickySidebar({ additionalMarginTop: 90 });
    }
    if ($(".wow").length) {
        var wow = new WOW({
            boxClass: "wow",
            animateClass: "animated",
            offset: 0,
            mobile: !1,
            live: !0,
        });
        wow.init();
    }
    if ($(".anm").length) {
        anm.on();
    }
    if ($(".chosen-container").length > 0) {
        $(".chosen-container").on("touchstart", function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).trigger("mousedown");
        });
    }
    $(window).on("scroll", function () {
        headerStyle();
    });
    $(window).on("load", function () {
        handlePreloader();
    });
})(window.jQuery);
if (document.getElementById("show-hide-pwd")) {
    document
        .getElementById("show-hide-pwd")
        .addEventListener("click", function (e) {
            const pwdField = document.getElementById("toggle-pwd");
            const btn = document.getElementById("show-hide-pwd");
            if (pwdField.type === "password") {
                pwdField.type = "text";
                btn.innerHTML = "Hide";
            } else {
                pwdField.type = "password";
                btn.innerHTML = "Show";
            }
        });
}
