<?php

if (Input::get('userID', true) && $mainUser->isLoggedIn()) {
    $editUsers = new Users();
    if (is_numeric(Input::get('userID'))) {
        $edituser = $editUsers->fetchUser(Input::get('userID'));
        if (!$edituser)  header('Location: users');
        if ($edituser->id == $mainUser->data()->id)  header('Location: profile');
    } else {
        header('Location: /users');
    }
}
?>
<div class="container mt-5">


    <div class="d-flex align-items-center justify-content-between ">
        <h3 class="text-primary m-0"><i class="bi bi-person"></i> <?php echo $edituser->firstName . " " . $edituser->lastName ?> </h3>
        <label for="userRole" class="ms-auto me-1 text-orange" style="font-weight: bold;">Ρόλος: </label>
        <select class="form-select form-select-sm " name="userRole" id="userRole" style="width: auto;">
            <option value="1" <?php echo $edituser->access == 1 ? "selected" : ""; ?>>Διαχειριστής</option>
            <option value="2" <?php echo $edituser->access == 2 ? "selected" : ""; ?>>Υπάλληλος</option>
        </select>
    </div>

    <form id="userProfileUpd">
        <div class="form-group p-1">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Εισάγετε το email σας" value="<?php echo $edituser->email ?>" aria-readonly="true" readonly>
        </div>
        <div class="form-group p-1">
            <label for="firstName">Όνομα</label>
            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Εισάγετε το όνομά σας" value="<?php echo $edituser->firstName ?>">
        </div>
        <div class="form-group p-1">
            <label for="lastName">Επώνυμο</label>
            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Εισάγετε το επώνυμό σας" value="<?php echo $edituser->lastName ?>">
        </div>

        <div class="form-group p-1">
            <label for="2faEnabled"><i class="bi bi-shield-lock"></i> Ενεργοποίηση Διπλής Ταυτοποίησης (2FA)</label>
            <div class="form-check form-switch">
                <input class="form-check-input genToggle" type="checkbox" id="2faEnabled" name="2faEnabled" <?php echo $edituser->twoFactorAuth ? "checked" : ""; ?>>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Αποθήκευση</button>
    </form>
    <hr>

    <form id="userPasswordUpd">
        <h4><i class="bi bi-key"></i> Ενημέρωση Κωδικού Πρόσβασης</h4>
        <div class="form-group p-1">
            <label for="newPassword">Νέος Κωδικός</label>
            <input type="password" class="form-control is-pass" id="newPassword" name="newPassword" placeholder="Εισάγετε τον νέο κωδικό σας">
            <i class="toggle-password bi bi-eye" title="Εμφάνιση Κωδικού" onclick="showpwd()"></i>
        </div>
        <div class="form-group p-1">
            <label for="confirmNewPassword">Επιβεβαίωση Νέου Κωδικού</label>
            <input type="password" class="form-control is-pass" id="confirmNewPassword" name="confirmNewPassword" placeholder="Επιβεβαιώστε τον νέο κωδικό σας">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Ενημέρωση</button>
    </form>

    <p class="response-message"></p>

    <input type="hidden" id="userID" value="<?php echo $edituser->id ?>">

</div>




<?php
function hook_end_scripts()
{
?>
    <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/edit_user.js"></script>
<?php
}
?>