jQuery(document).ready(function() {
    console.log("acá estoy");
	Main.init();
	$.Main.init();
    
});
(function ($) {
    var _isRoleDemo;
    // no se sobreescribe el namespace, si ya existe
    $.Main = $.Main || {};
    $.Main.modal = {},
    $.Main.init = function() {
    }
    $.Main.moneyFormat = function(val){
        return val.toFixed(2).replace('.',',').replace(/(\d)(?=(\d{3})+\,)/g, '$1.');
    }

})(jQuery);
//Main Function
var Main = function() {
    "use strict";
    //function to init app
    var runInit = function() {

        // set blockUI options
        if ($.blockUI) {
            $.blockUI.defaults.css.border = 'none';
            $.blockUI.defaults.css.padding = '20px 5px';
            $.blockUI.defaults.css.width = '20%';
            $.blockUI.defaults.css.left = '40%';
            $.blockUI.defaults.overlayCSS.backgroundColor = '#DDDDDD';
        }

        // Add Fade Animation to Dropdown
        $('.dropdown').on('show.bs.dropdown', function(e) {
            $(this).find('.dropdown-menu').first().stop(true, true).fadeIn(300);
        });
        $('.dropdown').on('hide.bs.dropdown', function(e) {
            $(this).find('.dropdown-menu').first().stop(true, true).fadeOut(300);
        });
    };

    //function to close searchbox, pageslide-left and pageslide-right when the user clicks outside of them
    var documentEvents = function() {
        $("html").click(function(e) {
            if (!e.isDefaultPrevented()) {
                if ($body.hasClass("right-sidebar-open") && !hoverSideBar && !isMobile) {
                    $(".sb-toggle-right").trigger("click");
                } else if ($body.hasClass("sidebar-mobile-open") && !hoverSideBar && !isMobile) {
                    $("header .sb-toggle-left").trigger("click");
                }
            }
        });
    };

    // function to handle SlideBar Toggle
    var runSideBarToggle = function() {
        $(".sb_toggle").click(function() {
            var sb_toggle = $(this);
            $("#slidingbar").slideToggle("fast", function() {
                if (sb_toggle.hasClass('open')) {
                    sb_toggle.removeClass('open');
                } else {
                    sb_toggle.addClass('open');
                }
            });
        });
    };

    //function to activate the Tooltips, if present
    var runTooltips = function() {
        if ($(".tooltips").length) {
            $('.tooltips').tooltip();
        }
    };
    //function to activate the Popovers, if present
    var runPopovers = function() {
        if ($(".popovers").length) {
            $('.popovers').popover();
        }
    };
    // function to allow a button or a link to open a tab
    var runShowTab = function(e) {
        if ($(".show-tab").length) {
            $('.show-tab').on('click', function(e) {
                e.preventDefault();
                var tabToShow = $(this).attr("href");
                if ($(tabToShow).length) {
                    $('a[href="' + tabToShow + '"]').tab('show');
                }
            });
        };
        //if (getParameterByName('tabId').length) {
        //    $('a[href="#' + getParameterByName('tabId') + '"]').tab('show');
        //}
    };
    // function to enable panel scroll with perfectScrollbar
    var runPanelScroll = function() {
        if ($(".panel-scroll").length && $body.hasClass("isMobile") == false) {
            $('.panel-scroll').perfectScrollbar({
                wheelSpeed: 50,
                minScrollbarLength: 20,
                suppressScrollX: true
            });
        }
    };
    //function to activate the panel tools
    var runModuleTools = function() {
        // fullscreen
        $('body').on('click', '.panel-expand', function(e) {
            e.preventDefault();
            $('.panel-tools > a, .panel-tools .dropdown').hide();

            if ($('.full-white-backdrop').length == 0) {
                $body.append('<div class="full-white-backdrop"></div>');
            }
            var backdrop = $('.full-white-backdrop');
            var wbox = $(this).parent().parents('.panel');
            wbox.attr('style', '');
            if (wbox.hasClass('panel-full-screen')) {
                backdrop.fadeIn(200, function() {
                    $('.panel-tools > .tmp-tool').remove();
                    $('.panel-tools > a, .panel-tools .dropdown').show();
                    wbox.removeClass('panel-full-screen');
                    backdrop.fadeOut(200, function() {
                        backdrop.remove();
                        $(window).trigger('resize');
                    });
                });
            } else {

                backdrop.fadeIn(200, function() {

                    $('.panel-tools').append("<a class='panel-expand tmp-tool' href='#'><i class='fa fa-compress'></i></a>");
                    backdrop.fadeOut(200, function() {
                        backdrop.hide();
                    });
                    wbox.addClass('panel-full-screen').css({
                        'max-height': $windowHeight,
                        'overflow': 'auto'
                    });
                    $(window).trigger('resize');
                });
            }
        });
        // panel close
        $('body').on('click', '.panel-close', function(e) {
            $(this).parents(".panel").fadeOut();
            e.preventDefault();
        });
        // panel refresh
        $('body').on('click', '.panel-refresh', function(e) {
            var el = $(this).parents(".panel");
            el.block({
                overlayCSS: {
                    backgroundColor: '#fff'
                },
                message: '<i class="fa fa-spinner fa-spin"></i>',
                css: {
                    border: 'none',
                    color: '#333',
                    background: 'none'
                }
            });
            window.setTimeout(function() {
                el.unblock();
            }, 1000);
            e.preventDefault();
        });
        // panel collapse
        $('body').on('click', '.panel-collapse', function(e) {
            e.preventDefault();
            var el = $(this);
            var bodyPanel = jQuery(this).parent().closest(".panel").children(".panel-body");
            if ($(this).hasClass("collapses")) {
                bodyPanel.slideUp(200, function() {
                    el.addClass("expand").removeClass("collapses").children("span").text("Expand").end().children("i").addClass("fa-rotate-180");
                });
            } else {
                bodyPanel.slideDown(200, function() {
                    el.addClass("collapses").removeClass("expand").children("span").text("Collapse").end().children("i").removeClass("fa-rotate-180");
                });
            }
        });
    };
    //function to activate the main menu functionality
    var runNavigationMenu = function() {
        if ($("body").hasClass("single-page") == false) {
            $('.main-navigation-menu > li.active').addClass('open');
            $('.main-navigation-menu > li a').on('click', function() {

                if ($(this).parent().children('ul').hasClass('sub-menu') && ((!$body.hasClass('navigation-small') || $windowWidth < 767) || !$(this).parent().parent().hasClass('main-navigation-menu'))) {
                    if (!$(this).parent().hasClass('open')) {
                        $(this).parent().addClass('open');
                        $(this).parent().parent().children('li.open').not($(this).parent()).not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200);
                        $(this).parent().children('ul').slideDown(200, function() {
                            if (mainNavigation.height() > $(".main-navigation-menu").outerHeight()) {

                                mainNavigation.scrollTo($(this).parent("li"), 300, {
                                    onAfter: function() {
                                        if ($body.hasClass("isMobile") == false) {
                                            mainNavigation.perfectScrollbar('update');
                                        }
                                    }
                                });
                            } else {

                                mainNavigation.scrollTo($(this).parent("li"), 300, {
                                    onAfter: function() {
                                        if ($body.hasClass("isMobile") == false) {
                                            mainNavigation.perfectScrollbar('update');
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        if (!$(this).parent().hasClass('active')) {
                            $(this).parent().parent().children('li.open').not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200, function() {
                                if (mainNavigation.height() > $(".main-navigation-menu").outerHeight()) {
                                    mainNavigation.scrollTo(0, 300, {
                                        onAfter: function() {
                                            if ($body.hasClass("isMobile") == false) {
                                                mainNavigation.perfectScrollbar('update');
                                            }
                                        }
                                    });
                                } else {
                                    mainNavigation.scrollTo($(this).parent("li").closest("ul").children("li:eq(0)"), 300, {
                                        onAfter: function() {
                                            if ($body.hasClass("isMobile") == false) {
                                                mainNavigation.perfectScrollbar('update');
                                            }
                                        }
                                    });
                                }
                            });
                        } else {
                            $(this).parent().parent().children('li.open').removeClass('open').children('ul').slideUp(200, function() {
                                if (mainNavigation.height() > $(".main-navigation-menu").outerHeight()) {
                                    mainNavigation.scrollTo(0, 300, {
                                        onAfter: function() {
                                            if ($body.hasClass("isMobile") == false) {
                                                mainNavigation.perfectScrollbar('update');
                                            }
                                        }
                                    });
                                } else {
                                    mainNavigation.scrollTo($(this).parent("li"), 300, {
                                        onAfter: function() {
                                            if ($body.hasClass("isMobile") == false) {
                                                mainNavigation.perfectScrollbar('update');
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    }
                } else {
                    $(this).parent().addClass('active');
                }
            });
        } else {
            var url, ajaxContainer = $("#ajax-content");
            var start = $('.main-navigation-menu li.start');
            if (start.length) {
                start.addClass("active");
                if (start.closest('ul').hasClass('sub-menu')) {
                    start.closest('ul').parent('li').addClass('active open');
                }
                url = start.children("a").attr("href");
                ajaxLoader(url, ajaxContainer);
            }
            $('.main-navigation-menu > li.active').addClass('open');
            $('.main-navigation-menu > li a').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);

                if ($this.parent().children('ul').hasClass('sub-menu') && (!$('body').hasClass('navigation-small') || !$this.parent().parent().hasClass('main-navigation-menu'))) {
                    if (!$this.parent().hasClass('open')) {
                        $this.parent().addClass('open');
                        $this.parent().parent().children('li.open').not($this.parent()).not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200);
                        $this.parent().children('ul').slideDown(200, function() {
                            runContainerHeight();
                        });
                    } else {
                        if (!$this.parent().hasClass('active')) {
                            $this.parent().parent().children('li.open').not($('.main-navigation-menu > li.active')).removeClass('open').children('ul').slideUp(200, function() {
                                runContainerHeight();
                            });
                        } else {
                            $this.parent().parent().children('li.open').removeClass('open').children('ul').slideUp(200, function() {
                                runContainerHeight();
                            });
                        }
                    }
                } else {

                    $('.main-navigation-menu ul.sub-menu li').removeClass('active');
                    $this.parent().addClass('active');
                    var closestUl = $this.parent('li').closest('ul');
                    if (closestUl.hasClass('main-navigation-menu')) {
                        $('.main-navigation-menu > li.active').removeClass('active').removeClass('open').children('ul').slideUp(200);
                        $this.parents('li').addClass('active');
                    } else if (!closestUl.parent('li').hasClass('active') && !closestUl.parent('li').closest('ul').hasClass('sub-menu')) {
                        $('.main-navigation-menu > li.active').removeClass('active').removeClass('open').children('ul').slideUp(200);
                        $this.parent('li').closest('ul').parent('li').addClass('active');
                    } else {

                        if (closestUl.parent('li').closest('ul').hasClass('sub-menu')) {
                            if (!closestUl.parents('li.open').hasClass('active')) {
                                $('.main-navigation-menu > li.active').removeClass('active').removeClass('open').children('ul').slideUp(200);
                                closestUl.parents('li.open').addClass('active');
                            }
                        }

                    }
                    url = $(this).attr("href");
                    ajaxLoader(url, ajaxContainer);
                };
            });
        }
    };

    //function to Right and Left PageSlide
    var runToggleSideBars = function() {
        var configAnimation, extendOptions, globalOptions = {
            duration: 150,
            easing: "ease",
            mobileHA: true,
            progress: function() {
                activeAnimation = true;
            }
        };
        $("#pageslide-left, #pageslide-right").on("mouseover", function(e) {
            hoverSideBar = true;
        }).on("mouseleave", function(e) {
            hoverSideBar = false;
        });
        $(".sb-toggle-left, .closedbar").on("click", function(e) {
            if (activeAnimation == false) {
                if ($windowWidth > 991) {
                    $body.removeClass("sidebar-mobile-open");
                    if ($body.hasClass("sidebar-close")) {
                        if ($body.hasClass("layout-boxed") || $body.hasClass("isMobile")) {
                            $body.removeClass("sidebar-close");
                            closedbar.removeClass("open");
                            $(window).trigger('resize');
                        } else {
                            closedbar.removeClass("open").hide();
                            closedbar.css({
                                //translateZ: 0,
                                left: -closedbar.width()
                            });

                            extendOptions = {
                                complete: function() {
                                    $body.removeClass("sidebar-close");
                                    $(".main-container, #pageslide-left, #footer .footer-inner, #horizontal-menu .container, .closedbar").attr('style', '');
                                    $(window).trigger('resize');
                                    activeAnimation = false;
                                }
                            };
                            configAnimation = $.extend({}, extendOptions, globalOptions);
                            $(".main-container, footer .footer-inner, #horizontal-menu .container").velocity({
                                //translateZ: 0,
                                marginLeft: sidebarWidth
                            }, configAnimation);

                        }

                    } else {
                        if ($body.hasClass("layout-boxed") || $body.hasClass("isMobile")) {
                            $body.addClass("sidebar-close");
                            closedbar.addClass("open");
                            $(window).trigger('resize');
                        } else {
                            sideLeft.css({
                                zIndex: 0

                            });
                            extendOptions = {
                                complete: function() {
                                    closedbar.show().velocity({
                                        //translateZ: 0,
                                        left: 0
                                    }, 100, 'ease', function() {
                                        activeAnimation = false;
                                        closedbar.addClass("open");
                                        $body.addClass("sidebar-close");
                                        $(".main-container, footer .footer-inner, #horizontal-menu .container, .closedbar").attr('style', '');
                                        $(window).trigger('resize');
                                    });
                                }
                            };
                            configAnimation = $.extend({}, extendOptions, globalOptions);
                            $(".main-container, footer .footer-inner, #horizontal-menu .container").velocity({
                                //translateZ: 0,
                                marginLeft: 0
                            }, configAnimation);
                        }
                    }

                } else {
                    if ($body.hasClass("sidebar-mobile-open")) {
                        if (supportTransition) {
                            extendOptions = {
                                complete: function() {
                                    inner.attr('style', '').removeClass("inner-transform");
                                    // remove inner-transform (hardware acceleration) for restore z-index
                                    $body.removeClass("sidebar-mobile-open");
                                    activeAnimation = false;
                                }
                            };
                            configAnimation = $.extend({}, extendOptions, globalOptions);

                            inner.velocity({
                                translateZ: 0,
                                translateX: [-sidebarWidth, 0]
                            }, configAnimation);
                        } else {
                            $body.removeClass("sidebar-mobile-open");
                        }
                    } else {
                        if (supportTransition) {
                            inner.addClass("inner-transform");
                            // add inner-transform for hardware acceleration
                            extendOptions = {
                                complete: function() {
                                    inner.attr('style', '');
                                    $body.addClass("sidebar-mobile-open");
                                    activeAnimation = false;
                                }
                            };
                            configAnimation = $.extend({}, extendOptions, globalOptions);
                            inner.velocity({
                                translateZ: 0,
                                translateX: [sidebarWidth, 0]
                            }, configAnimation);
                        } else {
                            $body.addClass("sidebar-mobile-open");
                        }

                    }
                }
            }
            e.preventDefault();
        });
        $(".sb-toggle-right").on("click", function(e) {
            if (activeAnimation == false) {
                if ($windowWidth > 991) {
                    $body.removeClass("sidebar-mobile-open");
                }
                if ($body.hasClass("right-sidebar-open")) {
                    if (supportTransition) {
                        extendOptions = {
                            complete: function() {
                                inner.attr('style', '').removeClass("inner-transform");
                                // remove inner-transform (hardware acceleration) for restore z-index
                                $body.removeClass("right-sidebar-open");
                                activeAnimation = false;
                            }
                        };
                        configAnimation = $.extend({}, extendOptions, globalOptions);
                        inner.velocity({
                            translateZ: 0,
                            translateX: [sidebarWidth, 0]
                        }, configAnimation);
                    } else {
                        $body.removeClass("right-sidebar-open");
                    }
                } else {
                    if (supportTransition) {
                        inner.addClass("inner-transform");
                        // add inner-transform for hardware acceleration
                        extendOptions = {
                            complete: function() {
                                inner.attr('style', '');
                                $body.addClass("right-sidebar-open");
                                activeAnimation = false;
                            }
                        };
                        configAnimation = $.extend({}, extendOptions, globalOptions);
                        inner.velocity({
                            translateZ: 0,
                            translateX: [-sidebarWidth, 0]
                        }, configAnimation);
                    } else {
                        $body.addClass("right-sidebar-open");
                    }
                }
            }
            e.preventDefault();
        });
    };
    return {
        //main function to initiate template pages
        init: function() {
            runInit();
            documentEvents();
            runSideBarToggle();
            runTooltips();
            runPopovers();
            runShowTab();
            runPanelScroll();
            runModuleTools();
            runNavigationMenu();
            runToggleSideBars();
        }
    };
}();