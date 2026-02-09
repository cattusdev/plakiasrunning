function showpwd() {
    $('.is-pass').each(function () {
        var value = $(this).attr('type');
        if (value == 'password' && $(this).hasClass('is-pass')) {
            $(this).attr('type', 'text');
            $(this).prop('title', 'Hide Password');

        } else {
            if (value == 'text' && $(this).hasClass('is-pass')) {
                $(this).attr('type', 'password');
                $(this).prop('title', 'Show Password');
            }
        }
    });
    $('.toggle-password').toggleClass('bi-eye-slash bi-eye')
}
$(document).ready(function () {

    function isNotEmpty(caller) {
        if (caller.val() == '') {
            caller.css('border', '1px solid red');
            return false;
        } else {
            caller.css('border', '');
            return true;
        }
    }

    $("#otpForm").submit(function (e) {
        e.preventDefault();
        showLoader('#otpForm', 'loading');
        let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
        $("#otpBtn").prop('disabled', true);

        let otpKey = $("#otpKey");

        if (isNotEmpty(otpKey)) {
            $("#otpBtn").prop('disabled', true);
            $.ajax({
                type: "POST",
                url: 'includes/ajax.php',
                data: $(this).serialize() + `&action=2FAlogin&csrf_token=${csrf_token}`,
                success: function (response) {
                    if (response.success) {
                        showLoader('#otpForm', 'success');
                        $(".response-message").html(response.message);
                        $("#otpBtn").prop('disabled', true);
                        setTimeout(() => {
                            if (response.url) {
                                window.location.href = response.url;
                            }
                        }, 2500);
                    } else {
                        if (response.errors && response.errors.length > 0) {

                            var errorMessage = "";
                            $.each(response.errors, function (index, error) {
                                errorMessage += "<strong>" + error + "</strong><br>";
                            });
                            showLoader('#otpForm', 'error');
                            $(".response-message").html(errorMessage);
                            $("#otpBtn").prop('disabled', false);
                        }

                    }
                },
                error: function (error) {
                    var errorMessage = '';
                    $.each(error.errors, function (index, error) {
                        errorMessage += error + "<br>";
                    });
                    showLoader('#otpForm', 'error');
                    $(".response-message").html(errorMessage);
                    $("#otpBtn").prop('disabled', false);
                }
            });
        }
    });

    $("#resendOTP").click(function (e) {
        e.preventDefault();
        showLoader('#otpForm')
        let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
        $.ajax({
            type: "POST",
            url: 'includes/ajax.php',
            data: $(this).serialize() + `&action=resendOTP&csrf_token=${csrf_token}`,
            success: function (response) {
                if (response.success) {
                    showLoader('#otpForm', 'success');
                    $(".response-message").html(response.message);
                    setTimeout(() => {
                        if (response.url) {
                            window.location.href = response.url;
                        }
                    }, 2800);
                } else {
                    if (response.errors && response.errors.length > 0) {
                        var errorMessage = "";
                        $.each(response.errors, function (index, error) {
                            errorMessage += "<strong>" + error + "</strong><br>";
                        });
                        showLoader('#otpForm', 'error');
                        $(".response-message").html(errorMessage);
                    }

                }
            },
            error: function (error) {
                var errorMessage = '';
                $.each(error.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                showLoader('#otpForm', 'error');
                $(".response-message").html(errorMessage);
            }
        });
    });
});