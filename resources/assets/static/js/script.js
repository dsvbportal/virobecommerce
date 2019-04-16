/*
-----------------------------------------------------------------------
BeOne - One Page Parallax
-----------------------------------------------------------------------
    - Author         : EvenThemes
    - Author URI     : http://bit.ly/2sjMgHz
    - Email          : eventhemes.contact@gmail.com
-----------------------------------------------------------------------
*/

(function($) {

    "use strict";

    var bodySelector = $("body"),
        htmlAndBody = $("html, body"),
        windowSelector = $(window);
    $.fn.hasAttr = function(attr) {
        if (typeof attr !== typeof undefined && attr !== false && attr !== undefined) {
            return true;
        }
        return false;
    };

    /* -------------------------------------
        BACKGROUND IMAGE
    ------------------------------------- */
    var background_image = function() {
        $("[data-bg-img]").each(function() {
            var attr = $(this).attr('data-bg-img');
            if (typeof attr !== typeof undefined && attr !== false && attr !== "") {
                $(this).css('background-image', 'url(' + attr + ')');
            }
        });
    };

    /* -------------------------------------
        PRELOADER
    ------------------------------------- */
    var preloader = function() {
        var pageLoader = $('#preloader');
        if (pageLoader.length) {
            pageLoader.children().fadeOut(); /* will first fade out the loading animation */
            pageLoader.delay(150).fadeOut('slow'); /* will fade out the white DIV that covers the website.*/
            bodySelector.delay(150).removeClass('preloader-active');
        }
    };

    /* -------------------------------------
        BACK TO TOP
    ------------------------------------- */
    var back_to_top = function() {
        var backTop = $('#backTop');
        if (backTop.length) {
            var scrollTrigger = 200,
                scrollTop = $(window).scrollTop();
            if (scrollTop > scrollTrigger) {
                backTop.addClass('show');
            } else {
                backTop.removeClass('show');
            }
        }
    };
    var click_back = function() {
        var backTop = $('#backTop');
        backTop.on('click', function(e) {
            htmlAndBody.animate({
                scrollTop: 0
            }, 700);
            e.preventDefault();
        });
    };

    /*-------------------------------------
        MAGNIFIC POPUP
    -------------------------------------*/
    var magnific_popup = function() {
        $('.img-lightbox').magnificPopup({
            type: 'image',
            mainClass: 'mfp-fade',
            gallery: {
                enabled: true
            }

        });
        $('.iframe-lightbox').magnificPopup({
            disableOn: 700,
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false,
            iframe: {
                patterns: {
                    youtube: {
                        src: 'https://www.youtube.com/embed/%id%?autoplay=1' /* URL that will be set as a source for iframe. */
                    },
                    vimeo: {
                        src: 'https://player.vimeo.com/video/%id%?autoplay=1'
                    },
                    gmaps: {
                        index: 'https://maps.google.'
                    }
                }
            }
        });
    };

    /* -------------------------------------
        NAVBAR
    ------------------------------------- */
    var navbar = function() {
        var navbarCollapse = $(".navbar-collapse"),
            navbarHeader = $(".navbar-header");
        windowSelector.on('resize', function() {
            navbarCollapse.css({ maxHeight: $(window).height() - navbarHeader.height() + "px" });
        });
        navbarCollapse.find('a.page-scroll').on('click', function() {
            $('.navbar-toggle:visible').click();
        });
        $('a.page-scroll').on('click', function(event) {
            var $anchor = $(this);
            htmlAndBody.stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top - 70
            }, 1500, 'easeInOutExpo');
            event.preventDefault();
        });
    };
    var scroll_spy = function() {
        bodySelector.scrollspy({
            target: '.navbar-default',
            offset: 100
        });
    };
    var stickyHeader = function() {
        var mainHeader = $(".main-header");
        if (mainHeader.hasClass("transparent")) {
            mainHeader.addClass("has-transparent");
        }
        if ($(window).scrollTop() > 4) {
            if (mainHeader.hasClass("has-transparent")) {
                mainHeader.removeClass("transparent");
            }
        } else {
            if (mainHeader.hasClass("has-transparent")) {
                mainHeader.addClass('transparent');
            }
        }
    };

    /* -------------------------------------
        OWL CAROUSEL
    ------------------------------------- */
    var owl_carousel = function() {
        $('.owl-carousel').each(function() {
            var carousel = $(this),
                autoplay_hover_pause = carousel.data('autoplay-hover-pause'),
                loop = carousel.data('loop'),
                animation = carousel.data('animation'),
                items_general = carousel.data('items'),
                margin = carousel.data('margin'),
                autoplay = carousel.data('autoplay'),
                autoplayTimeout = carousel.data('autoplay-timeout'),
                smartSpeed = carousel.data('smart-speed'),
                nav_general = carousel.data('nav'),
                navSpeed = carousel.data('nav-speed'),
                xxs_items = carousel.data('xxs-items'),
                xxs_nav = carousel.data('xxs-nav'),
                xs_items = carousel.data('xs-items'),
                xs_nav = carousel.data('xs-nav'),
                sm_items = carousel.data('sm-items'),
                sm_nav = carousel.data('sm-nav'),
                md_items = carousel.data('md-items'),
                md_nav = carousel.data('md-nav'),
                lg_items = carousel.data('lg-items'),
                lg_nav = carousel.data('lg-nav'),
                center = carousel.data('center'),
                dots_global = carousel.data('dots'),
                xxs_dots = carousel.data('xxs-dots'),
                xs_dots = carousel.data('xs-dots'),
                sm_dots = carousel.data('sm-dots'),
                md_dots = carousel.data('md-dots'),
                lg_dots = carousel.data('lg-dots');
            if ($('.owl-carousel').children().length > 1) {
                carousel.owlCarousel({
                    animateOut: (animation ? animation : 'fadeOut'),
                    autoplayHoverPause: autoplay_hover_pause,
                    loop: (loop ? loop : false),
                    items: (items_general ? items_general : 1),
                    lazyLoad: true,
                    margin: (margin ? margin : 0),
                    autoplay: (autoplay ? autoplay : false),
                    autoplayTimeout: (autoplayTimeout ? autoplayTimeout : 1000),
                    smartSpeed: (smartSpeed ? smartSpeed : 250),
                    dots: (dots_global ? dots_global : false),
                    nav: (nav_general ? nav_general : false),
                    navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>", "<i class='fa fa-angle-right' aria-hidden='true'></i>"],
                    navSpeed: (navSpeed ? navSpeed : false),
                    center: (center ? center : false),
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: (xxs_items ? xxs_items : (items_general ? items_general : 1)),
                            nav: (xxs_nav ? xxs_nav : (nav_general ? nav_general : false)),
                            dots: (xxs_dots ? xxs_dots : (dots_global ? dots_global : false))
                        },
                        480: {
                            items: (xs_items ? xs_items : (items_general ? items_general : 1)),
                            nav: (xs_nav ? xs_nav : (nav_general ? nav_general : false)),
                            dots: (xs_dots ? xs_dots : (dots_global ? dots_global : false))
                        },
                        768: {
                            items: (sm_items ? sm_items : (items_general ? items_general : 1)),
                            nav: (sm_nav ? sm_nav : (nav_general ? nav_general : false)),
                            dots: (sm_dots ? sm_dots : (dots_global ? dots_global : false))
                        },
                        992: {
                            items: (md_items ? md_items : (items_general ? items_general : 1)),
                            nav: (md_nav ? md_nav : (nav_general ? nav_general : false)),
                            dots: (md_dots ? md_dots : (dots_global ? dots_global : false))
                        },
                        1199: {
                            items: (lg_items ? lg_items : (items_general ? items_general : 1)),
                            nav: (lg_nav ? lg_nav : (nav_general ? nav_general : false)),
                            dots: (lg_dots ? lg_dots : (dots_global ? dots_global : false))
                        }
                    }
                });
            }

        });
    };

    /* -------------------------------------
        VEGAS SLIDER
    ------------------------------------- */
    var vegasSlider = function() {
        $(".hero-vegas-slider").vegas({
            slides: [
                { src: "assets/images/bg/06.jpg" },
                { src: "assets/images/bg/07.jpg" },
                { src: "assets/images/bg/08.jpg" },
                { src: "assets/images/bg/09.jpg" }
            ],
            timer: false
        });
    };


    /* -------------------------------------
        YOUTUBE VIDEO BACKGROUND
    ------------------------------------- */
    var YTPlayer = function() {
        $("#header-video").YTPlayer({
            showControls: false
        });
    };

    /* -------------------------------------
        GOOGLE MAP
    ------------------------------------- */
    var gmap = function() {
        if ($('.gmap').length) {
            var i = 0;
            $('.gmap').each(function() {
                i++;
                var self = $(this).attr("id", "gmap" + i),
                    mapDiv = "#gmap" + i,
                    mapLat = self.data('lat'),
                    mapLng = self.data('lng'),
                    mapZoom = self.data('zoom'),
                    map = new GMaps({
                        div: mapDiv,
                        lat: mapLat,
                        lng: mapLng,
                        zoom: mapZoom,
                        enableNewStyle: true,
                        scrollwheel: false
                    });
                map.addMarker({
                    lat: map.getCenter().lat(),
                    lng: map.getCenter().lng(),
                    title: 'Our Location',
                    infoWindow: {
                        content: '<h4>Our Location</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>'
                    }
                });
            });
        }
    };

    /* -------------------------------------
        SKILLS PROGRESS BAR
    ------------------------------------- */
    var skillsProgress = function() {
        $('.skill-percentage').each(function() {
            var $this = $(this);
            var width = $(this).data('percent');
            $this.html('<span class="progress-tooltip">' + width + ' %</span>');
            $this.css({
                'transition': 'width 3s'
            });
            setTimeout(function() {
                $this.appear(function() {
                    $this.css('width', width + '%');
                    setTimeout(function() {
                        $this.find('.progress-tooltip').css({
                            'opacity': '1'
                        });
                    }, 200);
                });
            }, 1000);
        });
    };

	
	
    /* -------------------------------------
        MIXITUP
    ------------------------------------- */
    var gallery_mixitup = function() {
        var mixItUpSelector = $('.portfolio-area .portfolio-wrapper'),
            mixItUpFilter = $('.portfolio-area .portfolio-filter .filter');
        if (mixItUpSelector.length) {
            mixItUpSelector.mixItUp();
        }
        if (mixItUpFilter.length) {
            mixItUpFilter.click(function(e) {
                e.preventDefault();
            });
        }
    };

    /*-------------------------------------
        CONTACT FORM JS
    -------------------------------------*/
    var validateEmail = function(email) {
        var patt = /^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;
        if (patt.test(email) === true) {
            return true;
        }
        return false;
    };
    var bootstrapAlert = function(type, text) {
        var alert = '<div class="alert alert-' + type + ' alert-dismissable">';
        alert += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        alert += text;
        alert += '</div>';
        return alert;
    };
    var contactResponse = function(responseNode, type, response) {
        if (response !== '') {
            responseNode.html(bootstrapAlert(type, response));
        } else {
            responseNode.html(bootstrapAlert('danger', 'Oops! An error occured.'));
        }
        bootstrapAlert();
    };
    var contactForm = function() {
        var contactForm = $("#contactForm");
        var responseNode = $('#contactResponse');
        contactForm.on("submit", function(e) {
            e.preventDefault();

            var self = $(this);
            var valid_form = true;
            var name = contactForm.find($("input[name='contactName']"));
            var email = contactForm.find($("input[name='contactEmail']"));
            var subject = contactForm.find($("input[name='contactSubject']"));
            var message = contactForm.find($("textarea[name='contactMessage']"));
            var formFields = [name, message, subject];

            formFields.forEach(function(input) {
                if (input.val() === '') {
                    input.addClass('input-error');
                    valid_form = false;
                }
            });

            if (email.val() === '' || validateEmail(email.val()) !== true) {
                email.addClass('input-error');
                valid_form = false;
            }

            self.find('input, textarea, select').on('change', function() {
                $(this).removeClass('input-error');
            });

            if (valid_form === true) {
                var response = "Thank You! Your message has been sent.";
                contactResponse(responseNode, "success", response);
            }

        });
    };

    $(document).on('ready', function() {
        preloader();
        background_image();
        click_back();
        magnific_popup();
        navbar();
        scroll_spy();
        owl_carousel();
        vegasSlider();
        YTPlayer();
        skillsProgress();
        stickyHeader();
        gmap();
        gallery_mixitup();
        contactForm();
    });

    windowSelector.on('load', function() {
        preloader();
    });

    windowSelector.on('scroll', function() {
        back_to_top();
        stickyHeader();
    });

})(jQuery);
