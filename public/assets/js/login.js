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
    // Updated toggle logic for bootstrap icons
    $('.toggle-password').toggleClass('bi-eye-slash bi-eye');
}

$(document).ready(function () {

    function isNotEmpty(caller) {
        if (caller.val() == '') {
            caller.css('border-color', '#ef663e'); // Hankatt Orange border
            return false;
        } else {
            caller.css('border-color', ''); // Reset
            return true;
        }
    }

    $("#loginForm").submit(function (e) {
        e.preventDefault();

        // Καθαρίζουμε παλιά μηνύματα
        $(".response-message").text('').removeClass('text-success text-danger');

        showLoader('.login-form'); // This expects .login-form class in HTML

        // Check if meta tag exists, otherwise handle gracefully
        let csrf_meta = document.getElementsByName('csrf_token')[0];
        let csrf_token = csrf_meta ? csrf_meta.getAttribute('content') : '';

        $("#loginBtn").prop('disabled', true).text('Verifying...');

        let email = $("#email");
        let password = $("#password");

        if (isNotEmpty(email) && isNotEmpty(password)) {
            $.ajax({
                type: "POST",
                url: 'includes/ajax.php',
                data: $(this).serialize() + `&action=login&csrf_token=${csrf_token}`,
                success: function (response) {
                    // Reset Button Text
                    $("#loginBtn").text('Sign In');

                    if (response.success) {
                        // Success Logic: Add Green Color
                        $(".response-message").addClass('text-success').text(response.message);

                        $("#loginBtn").prop('disabled', true).text('Success!');
                        showLoader('.login-form', 'success');

                        setTimeout(() => {
                            if (response.url) {
                                window.location.href = response.url;
                            }
                        }, 2000);
                    } else {
                        // Error Logic
                        if (response.errors && response.errors.length > 0) {
                            var errorMessage = "";
                            $.each(response.errors, function (index, error) {
                                errorMessage += error + "<br>";
                            });

                            showLoader('.login-form', 'error');
                            // Add Orange/Red Color
                            $(".response-message").addClass('text-danger').html(errorMessage);
                            $("#loginBtn").prop('disabled', false);
                        }
                    }
                },
                error: function (error) {
                    $("#loginBtn").text('Sign In');
                    var errorMessage = '';
                    if (error.responseJSON && error.responseJSON.errors) {
                        $.each(error.responseJSON.errors, function (index, err) {
                            errorMessage += err + "<br>";
                        });
                    } else {
                        errorMessage = "An unexpected error occurred.";
                    }

                    showLoader('.login-form', 'error');
                    $(".response-message").addClass('text-danger').html(errorMessage);
                    $("#loginBtn").prop('disabled', false);
                }
            });
        } else {
            $("#loginBtn").prop('disabled', false).text('Sign In');
            showLoader('.login-form', 'error'); // Stop loader if validation fails
        }
    });

    $("#logoutBtn").click(function () {
        let csrf_meta = document.getElementsByName('csrf_token')[0];
        let csrf_token = csrf_meta ? csrf_meta.getAttribute('content') : '';
        $("#logoutBtn").prop('disabled', true);

        $.ajax({
            type: "POST",
            url: 'includes/ajax.php',
            data: `&action=logout&csrf_token=${csrf_token}`,
            success: function (response) {
                if (response.success) {
                    window.location.href = response.url;
                } else {
                    var errorMessage = '';
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + "<br>";
                    });
                    $(".error-message").html(errorMessage);
                    $("#logoutBtn").prop('disabled', false);
                }
            },
            error: function (error) {
                var errorMessage = '';
                // Safety check for error format
                if (error.errors) {
                    $.each(error.errors, function (index, error) {
                        errorMessage += error + "<br>";
                    });
                } else {
                    errorMessage = "Logout failed.";
                }
                $(".error-message").html(errorMessage);
                $("#logoutBtn").prop('disabled', false);
            }
        });
    });
});