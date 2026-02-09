$(document).delegate('form', 'submit', function (event) {
    event.preventDefault();
    var form = $(this);
    var formID = form.attr('id');
    $(':submit', event.target).prop('disabled', true);

    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    var formElement = $("#" + formID)[0];
    var formData = new FormData(formElement);
    console.log(formData);
    formData.append('action', formID);
    formData.append('csrf_token', csrf_token);

    if (formID == "updMailResponses") {
        formData.set('r_newsletterMessage', r_newsletterMessageEditor.getData());
        formData.set('r_submitMessageMessage', r_submitMessageMessageEditor.getData());
        formData.set('r_completeEdietBookingMessage', r_completeEdietBookingMessageEditor.getData());
        formData.set('r_completeBookingMessage', r_completeBookingMessageEditor.getData());
    }

    showLoader('body');

    $.ajax({
        type: "POST",
        url: "includes/admin/ajax.php",
        data: formData,
        processData: false, // Important
        contentType: false, // Important
        success: function (response) {
            $(':submit', event.target).prop('disabled', false);
            if (response.success) {
                showLoader('body', 'success');
                setNotification('Action', response.message, 'success');
            } else {
                var errorMessage = '';
                $.each(response.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                showLoader('body', 'error');
                $(':submit', event.target).prop('disabled', false);
                setNotification('Warning', errorMessage, 'warning');
            }
        },
        error: function (error) {
            $(':submit', event.target).prop('disabled', false);
            var errorMessage = '';
            $.each(error.errors, function (index, error) {
                errorMessage += error + "<br>";
            });
            showLoader('body', 'error');
            setNotification('Error', errorMessage, 'error');
        }
    });
});


$(document).on("click", "#testSmtpConnection", function () {
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    $(':submit', "#testSmtpConnection").prop('disabled', true);
    let smtpHost = $("#smtpHost").val();
    let smtpPort = $("#smtpPort").val();
    let smtpUser = $("#smtpUser").val();
    let smtpPassword = $("#smtpPassword").val();
    showLoader('body');
    $.ajax({
        type: 'post',
        url: 'includes/admin/ajax.php',
        data: {
            action: 'checkSmtpConnection',
            csrf_token: csrf_token,
            smtpHost: smtpHost,
            smtpPort: smtpPort,
            smtpUser: smtpUser,
            smtpPassword: smtpPassword,
        },
        success: function (response) {
            $(':submit', "#testSmtpConnection").prop('disabled', false);
            if (response.success) {
                showLoader('body', 'success', 500, 500);
                setNotification('Action', response.message, 'success');
            } else {
                var errorMessage = '';
                $.each(response.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                showLoader('body', 'error', 500, 500);
                $(':submit', "#testSmtpConnection").prop('disabled', false);
                setNotification('Warning', errorMessage, 'warning');
            }
        },
        error: function (error) {
            $(':submit', "#testSmtpConnection").prop('disabled', false);
            var errorMessage = '';
            $.each(error.errors, function (index, error) {
                errorMessage += error + "<br>";
            });
            showLoader('body', 'error', 500, 500);
            setNotification('Error', errorMessage, 'error');
        }
    });
});

function showpwd() {
    $('.is-pass').each(function () {
        var value = $(this).attr('type');
        if (value == 'password' && $(this).hasClass('is-pass')) {
            $(this).attr('type', 'text');
        } else {
            if (value == 'text' && $(this).hasClass('is-pass')) {
                $(this).attr('type', 'password');
            }
        }
    });
    $('.show-password').toggleClass('bi-eye-slash bi-eye')
}

const originalLogoSrc = document.getElementById('logoPreview').src;

document.getElementById('companyLogo').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('logoPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('logoPreview').src = originalLogoSrc;
    }
});

document.querySelectorAll('.shortcutBlock').forEach(element => {
    element.addEventListener('click', function () {
        const textToCopy = element.textContent;
        copyToClipboard(textToCopy);

    });
});

function copyToClipboard(text) {
    var sampleTextarea = document.createElement("textarea");
    document.body.appendChild(sampleTextarea);
    sampleTextarea.value = text;
    sampleTextarea.select();
    document.execCommand("copy");
    document.body.removeChild(sampleTextarea);
    setNotification('Action', `Το κείμενο αντιγράφηκε στο πρόχειρο: ${text}`, 'success');
}