<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>
<div class="container mt-5">

    <h3><i class="bi bi-building"></i> Πληροφορίες Εταιρείας</h3>
    <form id="updCompanySettings">
        <div class="form-group p-1">
            <label for="companyName">Όνομα Εταιρείας</label>
            <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Εισάγετε το όνομα της εταιρείας" value="<?php echo $mainSettings->companySettings->companyName; ?>">
        </div>
        <div class="form-group p-1">
            <div class="d-flex align-items-start flex-column">
                <img src="<?php echo $mainSettings->companyLogo->logoPath; ?>" alt="Logo" class="img-fluid" id="logoPreview" style="width:160px; height:auto;">
                <label for="companyLogo">Λογότυπο Εταιρείας</label>

            </div>

            <input type="file" class="form-control" id="companyLogo" name="companyLogo">
        </div>



        <div class="form-group p-1">
            <label for="companyUrl">Ιστότοπος Εταιρείας</label>
            <input type="text" class="form-control" id="companyUrl" name="companyUrl" placeholder="Εισάγετε τον ιστότοπο της εταιρείας" value="<?php echo $mainSettings->companySettings->companyUrl; ?>">
        </div>
        <div class="form-group p-1">
            <label for="contactEmail">Email Επικοινωνίας</label>
            <input type="email" class="form-control" id="contactEmail" name="contactEmail" placeholder="Εισάγετε το email επικοινωνίας" value="<?php echo $mainSettings->companySettings->contactEmail; ?>">
        </div>
        <div class="form-group p-1">
            <label for="adminEmail">Email Διαχειριστή</label>
            <input type="email" class="form-control" id="adminEmail" name="adminEmail" placeholder="Εισάγετε το email επικοινωνίας διαχειριστή. Σε αυτό το email πηγαίνουν ειδοποιήσεις Π.χ: Νέα παραγγελία,Newsletter Subscription " value="<?php echo $mainSettings->companySettings->adminEmail; ?>">
        </div>
        <div class="form-group p-1">
            <label for="contactPhoneNumber">Τηλέφωνο Επικοινωνίας</label>
            <input type="tel" class="form-control" id="contactPhoneNumber" name="contactPhoneNumber" placeholder="Εισάγετε το τηλέφωνο επικοινωνίας" value="<?php echo $mainSettings->companySettings->contactPhoneNumber; ?>">
        </div>
        <div class="form-group p-1">
            <label for="physicalAddress">Φυσική Διεύθυνση</label>
            <input type="text" class="form-control" id="physicalAddress" name="physicalAddress" placeholder="Εισάγετε τη φυσική διεύθυνση" value="<?php echo $mainSettings->companySettings->physicalAddress; ?>">
        </div>
        <div class="form-group p-1">
            <label for="mapUrl">Url Χάρτη</label>
            <input type="text" class="form-control" id="mapUrl" name="mapUrl" placeholder="Εισάγετε τη URL χάρτη πχ: https://g.page/cattus" value="<?php echo $mainSettings->companySettings->mapUrl; ?>">
        </div>
        <div class="form-group p-1">
            <label for="businessHours">Ώρες Λειτουργίας</label>
            <input type="text" class="form-control" id="businessHours" name="businessHours" placeholder="Εισάγετε τις ώρες λειτουργίας" value="<?php echo $mainSettings->companySettings->businessHours; ?>">
        </div>
        <div class="form-group p-1">
            <label for="socialFacebook">Facebook</label>
            <input type="text" class="form-control" id="socialFacebook" name="socialFacebook" placeholder="Εισάγετε το Facebook της εταιρείας" value="<?php echo $mainSettings->companySettings->socialFacebook; ?>">
        </div>

        <div class="form-group p-1">
            <label for="socialTwitter">Tik-Tok</label>
            <input type="text" class="form-control" id="socialTwitter" name="socialTwitter" placeholder="Εισάγετε το Tik-tok της εταιρείας" value="<?php echo $mainSettings->companySettings->socialTwitter; ?>">
        </div>

        <div class="form-group p-1">
            <label for="socialInstagram">Instagram</label>
            <input type="text" class="form-control" id="socialInstagram" name="socialInstagram" placeholder="Εισάγετε το Instagram της εταιρείας" value="<?php echo $mainSettings->companySettings->socialInstagram; ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-2 ms-auto">Αποθήκευση</button>
    </form>
    <hr>

    <h3><i class="bi bi-chat-dots"></i> Ρυθμίσεις Επικοινωνίας</h3>
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="smtp-tab" data-bs-toggle="tab" href="#smtpSettings" role="tab" aria-controls="smtpSettings" aria-selected="true">Ρυθμίσεις SMTP</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="responses-tab" data-bs-toggle="tab" href="#emailResponses" role="tab" aria-controls="emailResponses" aria-selected="false">Απαντήσεις Email</a>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="settingsTabContent">
        <!-- SMTP Settings Tab -->
        <div class="tab-pane fade show active p-3" id="smtpSettings" role="tabpanel" aria-labelledby="smtp-tab">
            <form id="smtpSettingsUpd">
                <div class="form-group p-1">
                    <label for="smtpHost">Διακομιστής SMTP</label>
                    <div class="input-group">
                        <input type="text" id="smtpHost" name="smtpHost" class="form-control" placeholder="Εισάγετε τον διακομιστή SMTP" value="<?php echo $mainSettings->smtpSettings->smtpHost; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="bi bi-server"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group p-1">
                    <label for="smtpPort">Θύρα SMTP</label>
                    <div class="input-group">
                        <input type="number" id="smtpPort" name="smtpPort" class="form-control" placeholder="Εισάγετε τη θύρα SMTP" value="<?php echo $mainSettings->smtpSettings->smtpPort; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="bi bi-gear"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group p-1">
                    <label for="fromMail">Αποστολέας Email</label>
                    <div class="input-group">
                        <input type="text" id="fromMail" name="fromMail" class="form-control" placeholder="Εισάγετε το email αποστολέα" value="<?php echo $mainSettings->smtpSettings->fromMail; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group p-1">
                    <label for="smtpUser">Όνομα Χρήστη SMTP</label>
                    <div class="input-group">
                        <input type="text" id="smtpUser" name="smtpUser" class="form-control" placeholder="Εισάγετε το όνομα χρήστη SMTP" value="<?php echo $mainSettings->smtpSettings->smtpUser; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group p-1">
                    <label for="smtpPassword">Κωδικός SMTP</label>
                    <div class="input-group">
                        <input type="password" id="smtpPassword" name="smtpPassword" class="form-control is-pass" placeholder="Εισάγετε τον κωδικό SMTP" value="<?php echo $mainSettings->smtpSettings->smtpPassword; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text hand"><i class="show-password bi bi-eye-slash" title="Εμφάνιση Κωδικού" onclick="showpwd()"></i></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-optional my-2 d-flex" type="button" id="testSmtpConnection">
                        <i class="bi bi-send-check"></i> Έλεγχος Σύνδεσης
                    </button>
                    <button type="submit" class="btn btn-primary my-2">Αποθήκευση</button>
                </div>

            </form>
        </div>

        <!-- Email Responses Tab -->
        <div class="tab-pane fade p-3" id="emailResponses" role="tabpanel" aria-labelledby="responses-tab">
            <h4>Απαντήσεις Email</h4>
            <p>Διαμορφώστε τις απαντήσεις email που θα λαμβάνουν οι χρήστες.</p>

            <div class="row m-0">
                <div class="col-12 p-3">
                    <form action="includes/admin/ajax.php" method="post" id="updMailResponses" class="needs-validation">
                        <!-- Newsletter Signup Confirmation -->
                        <div class="form-group my-3 p-3 bg-secondary border">
                            <h5 for="r_newsletterTitle" class="font-weight-bold">Εγγραφή στο Newsletter</h5>
                            <input type="text" class="form-control mb-1 w-75" placeholder="Τίτλος" name="r_newsletterTitle" id="r_newsletterTitle" value="<?php echo htmlspecialchars($mainSettings->mailResponses->r_newsletterTitle); ?>" required>
                            <textarea class="form-control" rows="3" placeholder="Το Email αυτό αποστέλλεται μετά την εγγραφή στο newsletter." name="r_newsletterMessage" id="r_newsletterMessage" required><?php echo htmlspecialchars($mainSettings->mailResponses->r_newsletterMessage); ?></textarea>
                            <div class="d-flex ">
                                <div class="d-flex flex-wrap align-items-center p-1">
                                    <p class="text-orange m-0">Συντομεύσεις:</p>
                                    <small class="m-1 shortcutBlock hand" title="Email Πελάτη">{email}</small>
                                    <small class="m-1 shortcutBlock hand" title="Κωδικός Απεγγραφής">{newsletterToken}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form Submission Confirmation -->
                        <div class="form-group my-3 p-3 bg-secondary border">
                            <h5 for="r_submitMessageTitle" class="font-weight-bold">Υποβολή Μηνύματος</h5>
                            <input type="text" class="form-control mb-1 w-75" placeholder="Τίτλος" name="r_submitMessageTitle" id="r_submitMessageTitle" value="<?php echo htmlspecialchars($mainSettings->mailResponses->r_submitMessageTitle); ?>" required>
                            <textarea class="form-control" rows="3" placeholder="Το Email αυτό αποστέλλεται μετά την υποβολή μηνύματος." name="r_submitMessageMessage" id="r_submitMessageMessage" required><?php echo htmlspecialchars($mainSettings->mailResponses->r_submitMessageMessage); ?></textarea>
                            <div class="d-flex">
                                <div class="d-flex flex-wrap align-items-center p-1">
                                    <p class="text-orange m-0">Συντομεύσεις:</p>
                                    <small class="m-1 shortcutBlock hand" title="Email Πελάτη">{email}</small>
                                    <small class="m-1 shortcutBlock hand" title="Όνομα Πελάτη">{fullName}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Form Submission Confirmation -->
                        <div class="form-group my-3 p-3 bg-secondary border">
                            <h5 for="r_submitMessageTitle" class="font-weight-bold">Booking</h5>
                            <input type="text" class="form-control mb-1 w-75" placeholder="Τίτλος" name="r_completeBookingTitle" id="r_completeBookingTitle" value="<?php echo htmlspecialchars($mainSettings->mailResponses->r_completeBookingTitle); ?>" required>
                            <textarea class="form-control" rows="3" placeholder="Το Email αυτό αποστέλλεται μετά την υποβολή κράτησης." name="r_completeBookingMessage" id="r_completeBookingMessage" required><?php echo htmlspecialchars($mainSettings->mailResponses->r_completeBookingMessage); ?></textarea>
                            <div class="d-flex">
                                <div class="d-flex flex-wrap align-items-center p-1">
                                    <p class="text-orange m-0">Συντομεύσεις:</p>
                                    <small class="m-1 shortcutBlock hand" title="Email Πελάτη">{email}</small>
                                    <small class="m-1 shortcutBlock hand" title="Όνομα Πελάτη">{fullName}</small>
                                    <small class="m-1 shortcutBlock hand" title="Όνομα Πακέτου">{packageTitle}</small>
                                    <small class="m-1 shortcutBlock hand" title="Κωδ. Κράτησης">{bookingID}</small>
                                    <small class="m-1 shortcutBlock hand" title="Τιμή">{price}</small>
                                    <small class="m-1 shortcutBlock hand" title="Κώδ. Πληρωμής">{paymentToken}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Ediet Form Submission Confirmation -->
                        <div class="form-group my-3 p-3 bg-secondary border">
                            <h5 for="r_submitMessageTitle" class="font-weight-bold">E-Diet</h5>
                            <input type="text" class="form-control mb-1 w-75" placeholder="Τίτλος" name="r_completeEdietBookingTitle" id="r_completeEdietBookingTitle" value="<?php echo htmlspecialchars($mainSettings->mailResponses->r_completeEdietBookingTitle); ?>" required>
                            <textarea class="form-control" rows="3" placeholder="Το Email αυτό αποστέλλεται μετά την υποβολή μηνύματος." name="r_completeEdietBookingMessage" id="r_completeEdietBookingMessage" required><?php echo htmlspecialchars($mainSettings->mailResponses->r_completeEdietBookingMessage); ?></textarea>
                            <div class="d-flex">
                                <div class="d-flex flex-wrap align-items-center p-1">
                                    <p class="text-orange m-0">Συντομεύσεις:</p>
                                    <small class="m-1 shortcutBlock hand" title="Email Πελάτη">{email}</small>
                                    <small class="m-1 shortcutBlock hand" title="Όνομα Πελάτη">{fullName}</small>
                                    <small class="m-1 shortcutBlock hand" title="Όνομα Πακέτου">{packageTitle}</small>
                                    <small class="m-1 shortcutBlock hand" title="Τιμή">{price}</small>
                                    <small class="m-1 shortcutBlock hand" title="Κώδ. Πληρωμής">{paymentToken}</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary my-2">Αποθήκευση</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #f5f5f5;
        border-color: #dee2e6 #dee2e6 #fff;
    }
</style>

<?php
function hook_end_scripts()
{
?>
    <script>
        let r_newsletterMessageEditor;
        let r_eventFormMessageEditor;
        let r_submitMessageMessageEditor;
        let r_completeBookingMessageEditor;
        let r_completeEdietBookingMessageEditor;
        let r_requestOfferMessageEditor;
    </script>
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/ckeditor5/ckeditor5.css">
    <script type="importmap">
        {
			"imports": {
				"ckeditor5": "/assets/vendor/plugins/ckeditor5/ckeditor5.js",
				"ckeditor5/": "/assets/vendor/plugins/ckeditor5/"
			}
		}
		</script>


    <script type="module" src="/assets/vendor/plugins/ckeditor5/editor.js"></script>

    <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/settings.js?100"></script>
<?php
}
?>