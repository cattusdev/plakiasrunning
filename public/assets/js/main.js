$(window).on('load', function () {
    $('#status').fadeOut('fast');
    $('#preloader').fadeOut('fast');
    setTimeout(function () {
        document.getElementsByTagName('html')[0].style.overflow = "scroll";
        $('body').css('overflow', 'auto').fadeIn('fast');
    }, 100)

})
function showLoader(container = 'body', status = 'loading',) {

    $(".postLoader").remove();
    $(container).append(mainPreloader);
    // $('#postCheck').css('display', 'none');
    switch (status) {
        case 'loading':
            $('#postLoader').fadeIn();
            break;
        case 'success':
            $('#postLoader').css('display', 'block');
            $('#postCheck').fadeIn();
            $('#postCheck').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#postLoader').css('animation', 'none');
            $('#postLoader').css('stroke-dasharray', '1000');
            $('#postLoader').css('stroke-dashoffset', '0');
            break;
        case 'error':
            $('#postLoader').fadeOut();
            $('#postCheck').fadeOut();
            $('#failLoader').fadeIn();
            $('#failLoader').css('animation', 'none');
            $('#failLoader').css('stroke-dasharray', '1000');
            $('#failLoader').css('stroke-dashoffset', '0');
            $('#failLoaderLine1').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#failLoaderLine2').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#failLoaderLine1').fadeIn();
            $('#failLoaderLine2').fadeIn();
            setTimeout(() => {
                hideLoader();
            }, 3500);
            break;
        default:
            break;
    }

}


function hideLoader() {
    $('.postLoader').fadeOut();
}

let mainPreloader = `<div class="postLoader">
        <svg id="svgSuccess" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
            <circle id="postLoader" class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
            <polyline id="postCheck" class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
            <circle id="failLoader" class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
            <line id="failLoaderLine1" class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
            <line id="failLoaderLine2" class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
        </svg>
        <p class="response-message mt-3"></p>
    </div>`;
function setCookie(name, value, daysToExpire) {
    const date = new Date();
    date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000)); // Convert days to milliseconds
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    const cookieName = name + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i].trim();

        if (cookie.indexOf(cookieName) === 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
    }

    return "";
}
const themeToggle = document.getElementById('theme-toggle');
const body = document.body;
const iconToggle = document.getElementById('icon-toggle');
const iconToggleLight = document.getElementById('icon-toggle-light');

// Function to set the theme based on user preference
function setTheme(theme) {
    body.classList.toggle('dark-theme', theme === 'dark');
    if (iconToggle && iconToggleLight) {
        iconToggle.classList.toggle('d-none', theme !== 'dark');
        iconToggleLight.classList.toggle('d-none', theme === 'dark');
    }

}

// Check for saved theme preference
const savedTheme = localStorage.getItem('theme');

if (savedTheme === 'dark') {
    setTheme('dark');
    if (iconToggle && iconToggleLight) {
        themeToggle.checked = false; // Default to light theme
    }

} else {
    setTheme('light'); // Set light theme as the default
    if (iconToggle && iconToggleLight) {
        themeToggle.checked = true;
    }

}
if (iconToggle && iconToggleLight) {
    // Toggle theme and save preference when the switch is clicked
    themeToggle.addEventListener('change', function () {
        const currentTheme = this.checked ? 'light' : 'dark';
        setTheme(currentTheme);
        localStorage.setItem('theme', currentTheme);
    });
}


// $(".langswitch").click(function (event) {
//     if ($(this).data("lang") == "en") {
//         document.cookie = "clang=el;path=/";
//     } else {
//         document.cookie = "clang=en;path=/";
//     }
//     location.reload();
// });

$('#language .dropdown-item').click(function (event) {
    event.preventDefault();
    const selectedLang = $(this).data('lang');
    document.cookie = 'clang=' + selectedLang + ';path=/';
    location.reload();
});


//Currency
var currencyInput = document.querySelectorAll('input[type="currency"]');
for (var i = 0; i < currencyInput.length; i++) {

    var currency = 'EUR'
    // onBlur({
    // 	target: currencyInput[i]
    // })

    currencyInput[i].addEventListener('focus', onFocus)
    currencyInput[i].addEventListener('blur', onBlur)

    function localStringToNumber(s) {
        return Number(String(s).replace(/[^0-9.-]+/g, ""))
    }

    function onFocus(e) {
        var value = e.target.value;
        e.target.value = value ? localStringToNumber(value) : ''
    }

    function onBlur(e) {
        var value = e.target.value

        var options = {
            //maximumFractionDigits: 2,
            currency: currency,
            style: "currency",
            currencyDisplay: "symbol"
        }
        e.target.value = (value || value === 0) ?
            localStringToNumber(value).toLocaleString('en-US', options) :
            ''


    }
}

function localStringToNumber(s) {
    return Number(String(s).replace(/[^0-9.-]+/g, ""))
}

$(function () {
    AOS.init({
        duration: 850,
    });
});

$('#submitMessage').on('submit', function (e) {
    e.preventDefault();
    showLoader('#submitMessage');
    $("button[type='submit']").prop('disabled', true);


    // Check if reCAPTCHA is valid
    // let recaptchaResponse = grecaptcha.getResponse();
    // if (recaptchaResponse.length == 0) {
    //     $(".validateResp").text("Please complete the reCAPTCHA.");
    //     return false;
    // }

    // Prepare form data
    let csrf_token = document.querySelector('meta[name="csrf_token"]').getAttribute('content');
    const formData = $('#submitMessage').serialize() + `&action=submitMessage&csrf_token=${csrf_token}`;

    $.ajax({
        type: 'POST',
        url: '/includes/ajax.php',
        data: formData,
        success: function (response) {
            if (response.success) {
                $("#contactMessage").html(`<div class="alert alert-success">${response.message}</div>`);
                // Optionally, reset the form
                $('#submitMessage')[0].reset();
                hideLoader();
                // Disable the submit button
                $("button[type='submit']").prop('disabled', true);
            } else {
                if (response.errors && response.errors.length > 0) {
                    var errorMessage = response.errors.map(error => `<strong>${error}</strong>`).join('<br>');
                    $("#contactMessage").html(`<div class="alert alert-danger">${errorMessage}</div>`);
                    $("button[type='submit']").prop('disabled', false);
                    hideLoader();
                }
            }
        },
        error: function (error) {
            console.error('Error submitting the form.', error);
            $("#contactMessage").html(`<div class="alert alert-danger">Υπήρξε πρόβλημα κατά την υποβολή της φόρμας. Παρακαλούμε προσπαθήστε ξανά.</div>`);
            $("button[type='submit']").prop('disabled', false);
        }
    });
});

// $('#tagsSlider').slick({
//     lazyLoad: 'ondemand',
//     infinite: false,
//     slidesToShow: 2,
//     adaptiveHeight: false,
//     dots: false,
//     // slidesToScroll: 1,
//     arrows: true,
//     cssEase: 'linear',
//     autoplay: true,
//     variableWidth: true,
//     centerMode: false,
//     // nextArrow: $('.next'),
//     // prevArrow: $('.prev'),
//     responsive: [
//         {
//             breakpoint: 768,
//             settings: {
//                 variableWidth: false,
//                 slidesToShow: 3,
//                 centerMode: false,
//                 infinite: true,
//             }
//         }
//     ]
// });


var speed = 10000;
$("#tagsSlider").on('beforeChange', function (event, slick, currentSlide) {
    var currentSlide = $('#tagsSlider').find('.slick-active');
    var currentSlidewidth = currentSlide.width() * 100;
    $('.slick-track').css('transition', 'transform' + currentSlidewidth + 'ms linear');
});
$('#tagsSlider').slick({
    speed: speed,
    autoplay: true,
    autoplaySpeed: 0,
    cssEase: 'linear',
    slidesToShow: 1,
    slidesToScroll: 1,
    dots: false,
    arrows: false,
    variableWidth: true
});

document.addEventListener("DOMContentLoaded", function () {


    var backToTopBtn = document.getElementById("backToTop");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 450) {
            backToTopBtn.classList.add("show");
        } else {
            backToTopBtn.classList.remove("show");
        }
    });

    backToTopBtn.addEventListener("click", function () {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });

    if (window.location.hash) {
        const targetId = window.location.hash;
        const targetSection = document.querySelector(targetId);

        if (targetSection) {
            setTimeout(() => {
                const offset = 120;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetSection.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });

                const navLink = document.querySelector(`.nav-link-item[href="${targetId}"]`);
                if (navLink) {
                    navLink.click(); 
                }
            }, 300); 
        }
    }

    
});
