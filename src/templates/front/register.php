<style>
    .register-form {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 420px;
        margin: 20px 0px;
    }

    .flex-center {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 1;
    }

    .toggle-password {
        float: right;
        cursor: pointer;
        margin-right: 10px;
        margin-top: -31px;
        z-index: auto;
        position: relative;
        font-size: 1rem;
    }

    footer {
        display: none;
    }
</style>

<div class="flex-center container">
    <div class="register-form bg-primary">
        <div class="logo mt-4 mb-5">
            <a class="brand mx-auto" href="/" style="background-image:url(/assets/images/app_logo.svg)"></a>
        </div>
        <h3 class="mb-4 mt-2 text-center">Register</h3>
        <form id="registerForm">
            <div class="mb-3">
                <label for="registerEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="registerEmail" name="registerEmail" required>
            </div>
            <div class="mb-3">
                <label for="registerFirstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="registerFirstName" name="registerFirstName" required>
            </div>
            <div class="mb-3">
                <label for="registerLastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="registerLastName" name="registerLastName" required>
            </div>
            <div class="mb-3">
                <label for="registerPassword" class="form-label">Password</label>
                <input type="password" class="form-control is-pass" id="registerPassword" name="registerPassword" required>
                <i class="toggle-password bi bi-eye" title="Show Password" onclick="showpwd()"></i>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control is-pass" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="mb-3 text-right">
                <a href="login" class="d-block text-end">Login</a>
            </div>
            <button type="sumbit" class="btn btn-primary" id="registerButton">Register</button>
            <p class="success-message text-success"></p>
            <p class="error-message text-danger"></p>
        </form>
    </div>
</div>



<?php
function hook_end_scripts()
{
?>
    <script>
        function showpwd() {
            $('.is-pass').each(function() {
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
        $(document).ready(function() {

            function isNotEmpty(caller) {
                if (caller.val() == '') {
                    caller.css('border', '1px solid red');
                    return false;
                } else {
                    caller.css('border', '');
                    return true;
                }
            }



            $("#registerForm").submit(function(e) {
                e.preventDefault();
                let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
                $("#registerButton").prop('disabled', true);
                // let username = $("#registerUsername");
                let email = $("#registerEmail");
                let firstName = $("#registerFirstName");
                let password = $("#registerPassword");
                let confirmPassword = $("#confirmPassword");

                if (isNotEmpty(email) && isNotEmpty(firstName) && isNotEmpty(password) && isNotEmpty(confirmPassword)) {
                    var form = $("#registerForm");
                    $.ajax({
                        type: "POST",
                        url: 'includes/ajax.php',
                        data: form.serialize() + `&action=register&csrf_token=${csrf_token}`,
                        success: function(response) {
                            if (response.success) {

                                $(".error-message").html('');
                                $(".success-message").text('Registration successful');
                                setTimeout(() => {
                                    location.reload();
                                }, 1200);

                            } else {

                                if (response.errors && response.errors.length > 0) {
                                    var errorMessage = "Registration failed with the following errors:<br>";
                                    errorMessage += "<ul>";
                                    $.each(response.errors, function(index, error) {
                                        errorMessage += "<li>" + error + "</li>";
                                    });
                                    errorMessage += "</ul>";
                                    $(".success-message").text('');
                                    $(".error-message").html(errorMessage);
                                    $("#registerButton").prop('disabled', false);
                                }

                            }
                        },
                        error: function(error) {
                            console.error(error);
                            $(".error-message").text("Registration failed. Please try again.");
                            $("#registerButton").prop('disabled', false);
                        }
                    });
                }
            });
        });
    </script>
<?php
}
?>