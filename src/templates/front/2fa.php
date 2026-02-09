<?php
$currentDateTime = new DateTime();
$storedDateTime = Session::getSessVal('_2FA_VALID_UNTIL_');
$is2FaValid = false;

$currentDateTime >= $storedDateTime ?  $is2FaValid = false : $is2FaValid = true;

if (Session::exists('_2FA_KEY_') && $is2FaValid) {
} else {
    Session::delete('tmpUsername');
    Session::delete('tmpPassword');
    Session::delete('_2FA_KEY_');
    Session::delete('_2FA_VALID_UNTIL_');
    header("Location: login", true, 302); // Redirect to the login page
    exit;
}
?>

<style>
    .otp-form {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        width: 420px;
        margin: 20px 0px;
        position: relative;
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
    <div class="otp-form text-center bg-primary">
        <i class="bi bi-shield-lock display-1"></i>
        <h2 class="mb-4">Two-factor authentication</h2>
        <form id="otpForm">
            <div class="mb-3">
                <label for="otpKey" class="form-label">Authentication code</label>
                <input type="text" class="form-control text-center" id="otpKey" name="otpKey" required>
            </div>
            <div class="mb-3 text-right">
                <a href="#" id="resendOTP" class="d-block text-end">Resend</a>
            </div>
            <button type="submit" class="btn btn-primary d-block w-100" id="otpBtn">Submit</button>

        </form>
    </div>


</div>


<?php
function hook_end_scripts()
{
?>
    <script src=" <?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/2fa.js"></script>
<?php
}
?>