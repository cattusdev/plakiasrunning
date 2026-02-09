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

$("#userProfileUpd, #userPasswordUpd").on("submit", function (event) {
    event.preventDefault();

    let enable2FA = $("#2faEnabled").is(':checked') ? 1 : 0;
    let formData = $(this).serialize();

    let action = $(this).attr('id');

    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    let userID = document.getElementById('userID').value;
    let userRole = document.getElementById('userRole').value;
    // $('type="submit').prop('disabled', true);

    $.ajax({
        type: "POST",
        url: 'includes/admin/ajax.php',
        data: formData + `&action=${action}&enable2FA=${enable2FA}&csrf_token=${csrf_token}&userID=${userID}&userRole=${userRole}`,
        success: function (response) {
            if (response.success) {
                setNotification('Action', response.message, 'success');
            } else {
                var errorMessage = '';
                $.each(response.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                setNotification('Warning', errorMessage, 'warning');
            }
        },
        error: function (error) {
            var errorMessage = '';
            $.each(error.errors, function (index, error) {
                errorMessage += error + "<br>";
            });
            setNotification('Error', errorMessage, 'error');
        }
    });
});