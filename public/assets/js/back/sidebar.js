"use strict";
const Dashboard = (() => {
    const global = {
        menuClass: ".side-menu"
    };

    $('.copyright-text').addClass('expanded-padding');

    $(document).ready(function () {
        if (!getCookie("sidebarCookie")) {
            $("body").addClass('sidebar-is-reduced');
            setCookie("sidebarCookie", true, 7);
            setCookie("sidebarExpand", false, 7);
        }

        if (getCookie("sidebarExpand") === 'true') {
            $("body").addClass('sidebar-is-reduced');
            sidebarChangeWidth();
        } else {
            $("body").addClass('sidebar-is-reduced');
        }
    });

    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip({
            placement: 'right',
            boundary: 'window',
            trigger: 'hover'
        });
        $('[data-bs-toggle="popover"]').popover({
            boundary: 'window',
            html: true
        });
    });

    $(window).scroll(function () {
        if (window.matchMedia('(max-width: 768px)').matches) {
            const $this = $(this);
            const st = $this.scrollTop();
            const navbar = $('.c-header');

            if (st > 150) {
                if (!navbar.hasClass('scrolled')) {
                    navbar.addClass('scrolled');
                }
            }
            if (st < 150) {
                if (navbar.hasClass('scrolled')) {
                    navbar.removeClass('scrolled sleep');
                }
            }
            if (st > 350) {
                if (!navbar.hasClass('awake')) {
                    navbar.addClass('awake');
                }
            }
            if (st < 350) {
                if (navbar.hasClass('awake')) {
                    navbar.removeClass('awake');
                    navbar.addClass('sleep');
                }
            }
        }
    });

    $('.u-list > li').click(function (evt) {
        if ($(evt.target).closest('li').has('ul').length) {
            if (!$('body').hasClass('sidebar-is-reduced')) {
                $(this).find("ul").removeClass('side-menu-collapsed');
                $(this).find("ul").slideToggle(100);
                $(this).find('.js-expand-submenu').find('i').toggleClass('bi-chevron-down bi-chevron-up');
            }
        }
    });

    let sidebarChangeWidth = () => {
        let $menuItemsTitle = $("li .menu-item__title");
        $("body").toggleClass("sidebar-is-reduced sidebar-is-expanded");

        $('.copyright-text').toggleClass('expanded-padding collapsed-padding');
        $(".hamburger-toggle").toggleClass("is-opened");
        $(".hamburger-toggle").toggleClass("locked");

        if ($(".hamburger-toggle").hasClass("is-opened")) {
            setCookie("sidebarExpand", true, 7);
        } else {
            setCookie("sidebarExpand", false, 7);
        }

        if ($('.u-list > li').find("ul").is(":visible")) {
            $('.u-list > li').find("ul").slideToggle(200);
            $('.u-list > li').find("ul").addClass('side-menu-collapsed');
            $('.u-list > li').find("ul").css('display', 'none');
            $('.u-list > li').find('.js-expand-submenu').find('i').removeClass('bi-chevron-up');
            $('.u-list > li').find('.js-expand-submenu').find('i').addClass('bi-chevron-down');
        }

        if ($("body").hasClass('sidebar-is-expanded')) {
            $('.u-list > li').find("ul").removeClass('side-menu-collapsed');
            $('.u-list').find('.isOn').next('.u-list').fadeIn();
            $('.u-list').find('.isOn').next('.u-list').find('.tmp-ref').parent().css('display', 'none');
        } else {
            $('.u-list').find('.isOn').next('.u-list').find('.tmp-ref').parent().css('display', 'block');
        }

        if ($('.side-menu-item__title').hasClass('word-wrap')) {
            $('.side-menu-item__title').toggleClass('word-wrap');
        }
    };

    return {
        init: () => {
            $(".js-hamburger").on("click", sidebarChangeWidth);
        }
    };
})();

if ($(".c-sidebar").length) {
    Dashboard.init();
}

$(document).ready(function () {
    if (window.matchMedia('(max-width: 768px)').matches) {
        $(".hamburger-toggle").removeClass("is-opened");
        $('body').addClass('sidebar-is-reduced');
        $('body').removeClass('sidebar-is-expanded');
        $('.copyright-text').toggleClass('expanded-padding collapsed-padding');
    }
});

function sidebarChange() {
    $("body").toggleClass("sidebar-is-reduced sidebar-is-expanded");

    $('.copyright-text').toggleClass('expanded-padding collapsed-padding');
    $(".hamburger-toggle").toggleClass("is-opened");

    if ($('.u-list > li').find("ul").is(":visible")) {
        $('.u-list > li').find("ul").slideToggle(200);
        $('.u-list > li').find("ul").addClass('side-menu-collapsed');
        $('.u-list > li').find("ul").css('display', 'none');
        $('.u-list > li').find('.js-expand-submenu').find('i').removeClass('bi-chevron-up');
        $('.u-list > li').find('.js-expand-submenu').find('i').addClass('bi-chevron-down');
    }
}


$(document).mouseup(function (e) {
    if (window.matchMedia('(max-width: 768px)').matches) {
        const sidebar = $('.c-sidebar');

        if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0 && document.body.classList.contains('sidebar-is-expanded')) {
            $(".side-menu-collapsed").css('display', 'none');
            sidebarChange();
        }
    }
});

$("a").click(function (evt) {
    if ($(this).hasClass('main-ref') && $('body').hasClass('sidebar-is-reduced') && $(evt.target).closest('li').has('ul').length) {
        return false;
    }
});

$(document).ready(function () {
    const currentURL = window.location.href;
    const currentPath = window.location.pathname; // Get current path including sub-paths

    $('.u-list a').each(function () {
        const mainvalue = $(this).attr('href'); // Get href of the menu item
        const dataName = $(this).attr('data-name'); // Get the data-name attribute

        // Check if the mainvalue or the data-name matches the current path
        if (mainvalue === currentURL || currentPath.endsWith(dataName)) {
            $(this).closest('li').find('.side-menu__item__inner').addClass('isOn');
            $(this).parent().addClass('sub-is-active');

            // Open submenu if it exists
            if ($(this).closest('ul').hasClass('side-menu__submenu')) {
                $(this).closest('ul').css('display', 'block'); // Show parent submenu
                $(this).closest('ul').parent().find('.js-expand-submenu i')
                    .removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }

            // Highlight parent items and expand them
            $(this).parents('.side-menu__submenu').css('display', 'none');
            $(this).parents('.side-menu__item').find('.js-expand-submenu i')
                .removeClass('bi-chevron-down').addClass('bi-chevron-up');
            $(this).parents('.side-menu__item').find('.side-menu__item__inner').addClass('isOn');
        }

        
    });
});


// $(document).ready(function () {
//     const currentURL = window.location.href;
//     const domainMatch = currentURL.match(/:\/\/([^/]+)\//); // Extract domain

//     if (domainMatch && domainMatch.length > 1) {
//         const domain = domainMatch[1];

//         $('.u-list a').each(function () {
//             const mainvalue = $(this).attr('href');
//             const pageName = $(this).attr('data-name');

//             if (mainvalue === currentURL) {
//                 $(this).closest('li').find('.side-menu__item__inner').addClass('isOn');
//                 $(this).siblings('.js-expand-submenu').find('i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
//             }

//             const currPath = window.location.href.substring(this.href.lastIndexOf('/') + 1);

//             if (currPath.replace(/[^a-zA-Z0-9 ]/g, '') == pageName) {
//                 $(this).parent().addClass('sub-is-active');

//                 if ($('body').hasClass('sidebar-is-reduced')) {
//                     $(this).siblings(".collapsed-ref").css('display', 'block');
//                     $(this).closest('ul').css('display', 'none');
//                 } else {
//                     $("ul.side-menu__submenu a.tmp-ref").parent().css('display', 'none');
//                     $(this).siblings(".collapsed-ref .tmp-ref").css('display', 'none');
//                     $(this).closest('ul').css('display', 'block');
//                 }

//                 $(this).closest('ul').parent().find('.js-expand-submenu').find('i').toggleClass('bi-chevron-down bi-chevron-up');
//                 $(this).closest('ul').parent().find('.side-menu__item__inner').addClass('isOn');
//             }
//         });
//     }
// });



$('.u-list > li').hover(function (evt) {
    if ($('body').hasClass('sidebar-is-reduced')) {
        if ($(evt.target).closest('li').has('ul').length) {
            const $tooltip = $('[data-bs-toggle="tooltip"]');
            $tooltip.each(function () {
                $(this).tooltip('hide');
            });
            $(this).find("ul").addClass('side-menu-collapsed');
            $(this).find('.collapsed-ref').css('display', 'block');
            $(this).find("ul").fadeIn(100);
            $(this).find('.js-expand-submenu').find('i').toggleClass('bi-chevron-down bi-chevron-up');
        }
    } else {
        const $tooltip = $('[data-bs-toggle="tooltip"]');
        $tooltip.each(function () {
            $(this).tooltip('hide');
        });
    }
});

$('.u-list > li').mouseleave(function (evt) {
    if ($(evt.target).closest('li').has('ul').length) {
        if ($('body').hasClass('sidebar-is-reduced')) {
            if ($(this).find("ul").is(":visible")) {
                $(this).find("ul").fadeOut(100);
                $(this).find('.js-expand-submenu').find('i').removeClass('bi-chevron-up');
                $(this).find('.js-expand-submenu').find('i').addClass('bi-chevron-down');
            }
        }
    }
});
