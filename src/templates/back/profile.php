<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>
<div class="container mt-5">

    <h3><i class="bi bi-person"></i> Προφίλ</h3>
    <form id="profileUpd">
        <div class="form-group p-1">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Εισάγετε το email σας" value="<?php echo $mainUser->data()->email ?>" aria-readonly="true" readonly>
        </div>
        <div class="form-group p-1">
            <label for="firstName">Όνομα</label>
            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Εισάγετε το όνομά σας" value="<?php echo $mainUser->data()->firstName ?>">
        </div>
        <div class="form-group p-1">
            <label for="lastName">Επώνυμο</label>
            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Εισάγετε το επώνυμό σας" value="<?php echo $mainUser->data()->lastName ?>">
        </div>

        <div class="form-group p-1">
            <label for="2faEnabled"><i class="bi bi-shield-lock"></i> Ενεργοποίηση Διπλής Ταυτοποίησης (2FA)</label>
            <div class="form-check form-switch">
                <input class="form-check-input genToggle" type="checkbox" id="2faEnabled" name="2faEnabled" <?php echo $mainUser->data()->twoFactorAuth ? "checked" : ""; ?>>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Αποθήκευση</button>
    </form>
    <hr>

    <form id="passwordUpd">
        <h4><i class="bi bi-key"></i> Ενημέρωση Κωδικού Πρόσβασης</h4>
        <div class="form-group p-1">
            <label for="currentPassword">Τρέχων Κωδικός</label>
            <input type="password" class="form-control is-pass" id="currentPassword" name="currentPassword" placeholder="Εισάγετε τον τρέχοντα κωδικό σας">
            <i class="toggle-password bi bi-eye" title="Εμφάνιση Κωδικού" onclick="showpwd()"></i>
        </div>
        <div class="form-group p-1">
            <label for="newPassword">Νέος Κωδικός</label>
            <input type="password" class="form-control is-pass" id="newPassword" name="newPassword" placeholder="Εισάγετε τον νέο κωδικό σας">
        </div>
        <div class="form-group p-1">
            <label for="confirmNewPassword">Επιβεβαίωση Νέου Κωδικού</label>
            <input type="password" class="form-control is-pass" id="confirmNewPassword" name="confirmNewPassword" placeholder="Επιβεβαιώστε τον νέο κωδικό σας">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Ενημέρωση</button>
    </form>

    <p class="response-message"></p>

</div>




<?php
function hook_end_scripts()
{
?>
    <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/profile.js"></script>
<?php
}
?>