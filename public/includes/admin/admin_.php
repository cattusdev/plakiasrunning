<?php
require_once __DIR__ . '/../../../src/core/init.php';

function permissionDenied()
{
    $response['success'] = false;
    $response['message'] = "Δεν έχετε άδεια για αυτήν την ενέργεια";
    $response['errors'] = array('Δεν έχετε άδεια για αυτήν την ενέργεια');
    header('Content-Type: application/json');
    exit(json_encode($response));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Input::get('action', true) && Token::checkToken(Input::get('csrf_token'), 'csrf_token') && $mainUser->isLoggedIn()) {
    $response = [];
    $response['errors'] = array("");
    switch (Input::get('action')) {

        case 'updCompanySettings':

            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'companyName' => [
                    'dname' => 'Τίτλος Επιχείρησης',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $settings = new Settings();
                $settingsArray = array(
                    'companyName' => Input::validateString(Input::get('companyName')),
                    'companyUrl' => Input::validateString(Input::get('companyUrl')),
                    'contactEmail' => Input::validateString(Input::get('contactEmail')),
                    'adminEmail' => Input::validateString(Input::get('adminEmail')),
                    'contactPhoneNumber' => Input::validateString(Input::get('contactPhoneNumber')),
                    'physicalAddress' => Input::validateString(Input::get('physicalAddress')),
                    'mapUrl' => Input::validateString(Input::get('mapUrl')),
                    'businessHours' => Input::validateString(Input::get('businessHours')),
                    'socialFacebook' => Input::validateString(Input::get('socialFacebook')),
                    'socialTwitter' => Input::validateString(Input::get('socialTwitter')),
                    'socialInstagram' => Input::validateString(Input::get('socialInstagram')),
                );

                $jsonOptions = json_encode($settingsArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $actionResult = $settings->updateSetting(array(
                    'option_value' => $jsonOptions,
                ), 'option_name', 'companySettings');

                $folder_name = date('Y') . '/' . date('F');
                if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] === UPLOAD_ERR_OK) {
                    $mediaHandler = new MediaHandler();
                    $crud = new Crud();

                    $file = array(
                        'name' => $_FILES['companyLogo']['name'],
                        'type' => $_FILES['companyLogo']['type'],
                        'tmp_name' => $_FILES['companyLogo']['tmp_name'],
                        'error' => $_FILES['companyLogo']['error'],
                        'size' => $_FILES['companyLogo']['size']
                    );

                    $image_path = $mediaHandler->uploadImage($file, 50000000, array("image/jpeg", "image/png", "image/gif"), "assets/uploads/images/" . $folder_name . "/", "returnFilePath");

                    if (is_string($image_path) && strpos($image_path, 'Error') === false) {

                        $settingsArray = array(
                            'logoPath' => $image_path,
                            'logoName' => $file['name'],
                        );

                        $jsonOptions = json_encode($settingsArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        $actionResult = $settings->updateSetting(array(
                            'option_value' => $jsonOptions,
                        ), 'option_name', 'companyLogo');
                    } else {
                        $response['success'] = false;
                        $response['errors'] = array($image_path);

                        header('Content-Type: application/json');
                        exit(json_encode($response));
                    }
                }

                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = array("Προστέθηκε επιτυχώς");
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'smtpSettingsUpd':

            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'smtpHost' => [
                    'dname' => 'Διακομιστής SMTP',
                    'required' => true,
                ],
                'smtpPort' => [
                    'dname' => 'Θύρα SMTP',
                    'required' => true,
                ],
                'fromMail' => [
                    'dname' => 'Αποστολέας Email',
                    'required' => true,
                ],
                'smtpUser' => [
                    'dname' => 'Όνομα Χρήστη SMTP',
                    'required' => true,
                ],
                'smtpPassword' => [
                    'dname' => 'Κωδικός SMTP',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $settings = new Settings();
                $smtpHost =  Input::get('smtpHost');
                $smtpPort =  Input::get('smtpPort');
                $fromMail = Input::get('fromMail');
                $smtpUser =  Input::get('smtpUser');
                $smtpPassword =  Input::get('smtpPassword');

                if (!Input::validateEmail(Input::get('smtpUser'))) {
                    $response['success'] = false;
                    $response['errors'] = array("Λάθος email");
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $settingsArray = array(
                    'smtpHost' => $smtpHost,
                    'smtpPort' => $smtpPort,
                    'fromMail' => $fromMail,
                    'smtpUser' => $smtpUser,
                    'smtpPassword' => $smtpPassword,
                );

                $jsonOptions = json_encode($settingsArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $actionResult = $settings->updateSetting(array(
                    'option_value' => $jsonOptions,
                ), 'option_name', 'smtpSettings');


                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = array("Ενημερώθηκε επιτυχώς");
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'checkSmtpConnection':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            if (checkEmailConnection(Input::get('smtpHost'), Input::get('smtpPort'), Input::get('smtpUser'), Input::get('smtpPassword'))) {
                $response['success'] = true;
                $response['message'] = array("Η σύνδεση με τον SMTP διακομιστή ήταν επιτυχής!");
            } else {
                $response['success'] = false;
                $response['errors'] = array("Η σύνδεση με τον SMTP διακομιστή απέτυχε. Παρακαλώ ελέγξτε τις ρυθμίσεις και δοκιμάστε ξανά.");
            }
            header('Content-Type: application/json');
            exit(json_encode($response));

            break;

        case 'updMailResponses':
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                // Newsletter Signup Confirmation
                'r_newsletterTitle' => array(
                    'dname' => 'Εγγραφή στο Newsletter - Τίτλος',
                    'required' => true,
                    'min' => 2,
                ),
                'r_newsletterMessage' => array(
                    'dname' => 'Εγγραφή στο Newsletter - Μήνυμα',
                    'required' => true,
                    'min' => 2,
                ),

                // Contact Form Submission Confirmation
                'r_submitMessageTitle' => array(
                    'dname' => 'Υποβολή Μηνύματος - Τίτλος',
                    'required' => true,
                    'min' => 2,
                ),
                'r_submitMessageMessage' => array(
                    'dname' => 'Υποβολή Μηνύματος - Μήνυμα',
                    'required' => true,
                    'min' => 2,
                ),

                'r_completeEdietBookingTitle' => array(
                    'dname' => 'E-diet - Τίτλος',
                    'required' => true,
                    'min' => 2,
                ),
                'r_completeEdietBookingMessage' => array(
                    'dname' => 'E-diet - Μήνυμα',
                    'required' => true,
                    'min' => 2,
                ),

                'r_completeBookingTitle' => array(
                    'dname' => 'E-diet - Τίτλος',
                    'required' => true,
                    'min' => 2,
                ),
                'r_completeBookingMessage' => array(
                    'dname' => 'E-diet - Μήνυμα',
                    'required' => true,
                    'min' => 2,
                ),

            ));
            if ($validation->passed()) {


                $mailResponses = array(
                    'r_newsletterTitle' => Input::get('r_newsletterTitle'),
                    'r_newsletterMessage' => Input::get('r_newsletterMessage'),
                    'r_submitMessageTitle' => Input::get('r_submitMessageTitle'),
                    'r_submitMessageMessage' => Input::get('r_submitMessageMessage'),
                    'r_completeEdietBookingTitle' => Input::get('r_completeEdietBookingTitle'),
                    'r_completeEdietBookingMessage' => Input::get('r_completeEdietBookingMessage'),
                    'r_completeBookingTitle' => Input::get('r_completeBookingTitle'),
                    'r_completeBookingMessage' => Input::get('r_completeBookingMessage'),
                );
                $jsonOptions = json_encode($mailResponses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $settings = new Settings();

                $actionResult = $settings->updateSetting(array(
                    'option_value' => $jsonOptions,
                ), 'option_name', 'mailResponses');


                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = array("Ενημερώθηκε επιτυχώς");
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        case 'profileUpd':

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'firstName' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ],
                'lastName' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ]
            ]);

            if ($validation->passed()) {
                $user = new User();

                $firstName = Input::get('firstName');
                $lastName = Input::get('lastName');
                $enable2FA = ((bool) Input::get("enable2FA"));

                $updateResult = $user->update(array(
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'twoFactorAuth' => $enable2FA,
                ));

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }
            break;

        case 'passwordUpd':

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'currentPassword' => array(
                    'dname' => 'Password',
                    'required' => true,
                    'min' => 6,
                ),
                'newPassword' => array(
                    'dname' => 'New Password',
                    'required' => true,
                    'min' => 6,
                ),
                'confirmNewPassword' => array(
                    'dname' => 'Confirm Password',
                    'required' => true,
                    'min' => 6,
                    'max' => 255,
                    'match' => 'newPassword',
                ),
            ]);

            if ($validation->passed()) {
                $user = new User();

                if (password_verify(Input::get('currentPassword'), $user->data()->password)) {
                    $updateResult = $user->update(array(
                        'password' => Hash::make(Input::get('confirmNewPassword'))
                    ));

                    if ($updateResult === true) {
                        $response['success'] = true;
                        $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                    } else {
                        $response['success'] = false;
                        $response['errors'] = $updateResult;
                    }
                } else {
                    $response['success'] = false;
                    $response['errors'] = array('Your current password is wrong');
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;
        case 'addUser':

            $startTime = microtime(true);
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                // 'registerUsername' => array(
                //     'dname' => 'Username',
                //     'isString' => true,
                //     'required' => true,
                //     'min' => 6
                // ),
                'registerEmail' => array(
                    'dname' => 'Email',
                    'required' => true,
                    'min' => 5,
                    'max' => 255,
                    'isMail' => true
                ),
                'registerPassword' => array(
                    'dname' => 'Password',
                    'required' => true,
                    'min' => 6,
                ),
                'confirmPassword' => array(
                    'dname' => 'Confirm Password',
                    'required' => true,
                    'min' => 6,
                    'max' => 255,
                    'match' => 'registerPassword',
                ),
                'registerFirstName' => array(
                    'dname' => 'First name',
                    'isString' => true,
                    'required' => true,
                    'min' => 2
                ),
                'registerLastName' => array(
                    'dname' => 'Last name',
                    'isString' => true,
                    'required' => true,
                    'min' => 2
                ),
            ));

            if ($validation->passed()) {
                try {
                    // $username = Input::validateString(Input::get('username', true));
                    $email = Input::validateEmail(Input::get('registerEmail'));
                    $password = Hash::make(Input::get('confirmPassword'));
                    $firstName = Input::validateString(Input::get('registerFirstName'));
                    $lastName = Input::validateString(Input::get('registerLastName'));

                    $user = new User();

                    if ($user->userExist(Input::get('registerEmail', true, FILTER_VALIDATE_EMAIL))) {
                        $response['success'] = false;
                        $response['errors'] = array("A user with the same email address already exists");
                        header('Content-Type: application/json');
                        exit(json_encode($response));
                    }


                    $userRoleValid = Input::validateInt(Input::get('userRole'));
                    if (!$userRoleValid) {
                        $response['success'] = false;
                        $response['errors'] = array("Λάθος Ρόλος");
                        header('Content-Type: application/json');
                        exit(json_encode($response));
                    }
                    //userRole 1 = Admin
                    //userRole 2 = Editor
                    //userRole 3 = User
                    $userRole = 3;
                    switch (Input::get('userRole')) {
                        case 1:
                            $userRole = 1;
                            break;
                        case 2:
                            $userRole = 2;
                            break;
                        case 3:
                            $userRole = 3;
                            break;
                        default:
                            $userRole = 3;
                            break;
                    }

                    $registrationResult = $user->createUser(array(
                        // 'username' => $username,
                        'password' => $password,
                        'email' => $email,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'access' => $userRole
                    ));

                    if ($registrationResult === true) {
                        $response['success'] = true;
                        $response['message'] = "Registration completed";
                    } else {
                        $response['success'] = false;
                        $response['errors'] = $registrationResult;
                    }
                } catch (Exception $e) {
                    $response['success'] = false;
                    $response['errors'] = array("Hmm.. Something went wrong [Error: ]" . $e->getMessage());
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $desiredDelay = 200000; //ms
            while (true) {
                $currentTime = microtime(true);
                $elapsedTime = ($currentTime - $startTime) * 1000000; // Convert to microseconds
                if ($elapsedTime >= $desiredDelay) {
                    break;
                }
            }

            header('Content-Type: application/json');
            exit(json_encode($response));

            break;

        case 'fetchUsers':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $users = new Users();

            if ($users->fetchUsers()) {
                header('Content-Type: application/json');
                exit(json_encode($users->data()));
            } else {
                $response['errors'] = array('Something wend wrong while fetching the clients');
            }

            header('Content-Type: application/json');
            exit(json_encode($response));

            break;

        case 'userProfileUpd':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'firstName' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ],
                'lastName' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ],
                'userRole' => [
                    'dname' => 'Ρόλος',
                    'required' => true,
                ]
            ]);

            if ($validation->passed()) {
                $users = new Users();

                $firstName = Input::validateString(Input::get('firstName'));
                $lastName = Input::validateString(Input::get('lastName'));
                $enable2FA = ((bool) Input::get("enable2FA"));

                $userID = Input::validateInt(Input::get('userID'));
                $userRole = Input::validateInt(Input::get('userRole'));

                if (!$userID || !$userRole) {
                    $response['success'] = false;
                    $response['errors'] = array('Λάθος Χρήστης');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $updateResult = $users->updateUser(array(
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'twoFactorAuth' => $enable2FA,
                    'access' => Input::get('userRole'),
                ), Input::get('userID'));

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }
            break;

        case 'userPasswordUpd':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'newPassword' => array(
                    'dname' => 'New Password',
                    'required' => true,
                    'min' => 6,
                ),
                'confirmNewPassword' => array(
                    'dname' => 'Confirm Password',
                    'required' => true,
                    'min' => 6,
                    'max' => 255,
                    'match' => 'newPassword',
                ),
            ]);

            $userID = Input::validateInt(Input::get('userID'));

            if (!$userID) {
                $response['success'] = false;
                $response['errors'] = array('Λάθος Χρήστης');
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            if ($validation->passed()) {
                $users = new Users();

                $updateResult = $users->updateUser(array(
                    'password' => Hash::make(Input::get('confirmNewPassword'))
                ), Input::get('userID'));

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        case 'delUsers':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'mainID' => [
                    'dname' => 'User ID',
                    'required' => true
                ],
            ]);

            if ($validation->passed()) {
                $users = new Users();
                $inputValidate = new Input();
                $userID = $inputValidate->validateInt(Input::get('mainID'));

                if (!$userID) {
                    $response['errors'] = array('Invalid User ID');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $userToDelete = $users->fetchUser(Input::get('mainID'));

                $isLastAdmin = 0;
                if ($userToDelete && $userToDelete->access == 1) {
                    $isLastAdmin = $users->fetchAdminCount();
                }
                if (!$isLastAdmin->count > 1) {
                    $response['success'] = true;
                    $response['message'] = "Πρέπει πάντα να υπάρχει 1 Διαχειριστής";
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                if ($users->deleteUser(Input::get('mainID'))) {
                    $response['success'] = true;
                    $response['message'] = "Αφαιρέθηκε επιτυχώς";
                } else {
                    $response['errors'] = array('Something wend wrong while deleting the location');
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'addSubscriber':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'email' => [
                    'dname' => 'Email',
                    'required' => true,
                    'isMail' => true
                ],
            ]);


            if ($validation->passed()) {
                $subscribers = new Newsletter();

                if ($subscribers->fetchSubscriptionByMail(Input::get('email'))) {
                    $response['errors'][] = 'Αυτό το email είναι ήδη εγγεγραμμένο στο newsletter.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }


                $hash = new Hash();
                $authToken = $hash->unique();

                $actionResult = $subscribers->addSubscription(array(
                    'email' => Input::get('email'),
                    'token' => $authToken
                ));


                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = array("Προστέθηκε επιτυχώς");
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }


                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        case 'updSubscriber':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'email' => [
                    'dname' => 'Email',
                    'required' => true,
                    'isMail' => true
                ],
            ]);

            if ($validation->passed()) {
                $subscribers = new Newsletter();


                $itemID = Input::validateInt(Input::get('mainID'));
                if (!$itemID) {
                    $response['errors'] = array('Invalid Item');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $oldMail = $subscribers->fetchSubscription(Input::get('mainID'));
                if ($oldMail->email != Input::get('email'))
                    if ($subscribers->fetchSubscriptionByMail(Input::get('email'))) {
                        $response['errors'][] = 'Αυτό το email είναι ήδη εγγεγραμμένο στο newsletter.';
                        header('Content-Type: application/json');
                        exit(json_encode($response));
                    }

                $updateResult = $subscribers->updateSubscription([
                    'email' => Input::get('email'),
                ], Input::get('mainID'));


                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'fetchSubscriptions':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $subscribers = new Newsletter();

            if ($subscribers->fetchSubscriptions()) {
                header('Content-Type: application/json');
                exit(json_encode($subscribers->data()));
            } else {
                $response['errors'] = array('Something wend wrong while fetching subscribers');
            }

            header('Content-Type: application/json');
            exit(json_encode($response));

            break;


        case 'delSubscriber':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'mainID' => [
                    'dname' => 'Item ID',
                    'required' => true
                ],
            ]);

            if ($validation->passed()) {
                $subscribers = new Newsletter();
                $inputValidate = new Input();
                $itemID = $inputValidate->validateInt(Input::get('mainID'));

                if (!$itemID) {
                    $response['errors'] = array('Invalid location');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }


                if ($subscribers->deleteSubscription(Input::get('mainID'))) {
                    $response['success'] = true;
                    $response['message'] = "Αφαιρέθηκε επιτυχώς";
                } else {
                    $response['errors'] = array('Something wend wrong while deleting the location');
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        /* -------------------------------------------------------------------------
           CASE: SMART AVAILABILITY CHECK (Για το Booking)
           Ελέγχει ποιες ώρες είναι διαθέσιμες για ένα συγκεκριμένο πακέτο
           ------------------------------------------------------------------------- */
        case 'fetchAvailableHours':
            // Εδώ δεν βάζουμε permission check αν θέλουμε να το βλέπει και ο πελάτης (public).
            // Αν είναι μόνο για το Admin Calendar, άσε το:
            // if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $response = ['success' => false, 'data' => [], 'errors' => []];

            $dateStr = Input::get('date');       // "YYYY-MM-DD"
            $packageId = Input::get('package_id');

            // 1. Validation
            if (!$dateStr || !$packageId) {
                $response['errors'][] = 'Λείπει η ημερομηνία ή το πακέτο.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                // 2. Βρίσκουμε τη διάρκεια του Πακέτου
                $packagesModel = new Packages();
                $package = $packagesModel->fetchPackage($packageId);

                if (!$package) {
                    throw new Exception('Το πακέτο δεν βρέθηκε.');
                }

                $duration = (int) $package->duration_minutes;
                if ($duration <= 0) $duration = 60; // Fallback safety

                // 3. Καλούμε τον "Smart Matchmaker" από το Slots Model
                $slotsModel = new Slots();

                // Αυτή είναι η νέα μέθοδος που έβαλες στο Slots.php
                $validSlots = $slotsModel->findAvailableStartTimes($dateStr, $duration);

                $response['success'] = true;
                $response['data'] = $validSlots; // Επιστρέφει array: [{slot_id, start, end}, ...]

            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'getAllSlots':
            $response = ['success' => false, 'data' => [], 'errors' => []];
            try {
                $slotsModel = new Slots();
                $allSlots = $slotsModel->fetchAllSlots(); // Or fetchSlotsRange if you prefer
                if ($allSlots) {
                    $response['success'] = true;
                    $response['data'] = [];
                    foreach ($allSlots as $slot) {
                        $response['data'][] = [
                            'id'                => $slot->id,
                            'start_datetime'    => $slot->start_datetime,
                            'end_datetime'      => $slot->end_datetime,
                            'status'            => $slot->status,
                            'appointment_type'  => $slot->appointment_type
                        ];
                    }
                } else {
                    $response['errors'][] = 'Δεν βρέθηκαν διαθέσιμα ραντεβού.';
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'bulkCreateSlots':
            $response = ['success' => false, 'errors' => [], 'createdCount' => 0];

            $slotsDataJson = Input::get('slotsData');
            $packageIdsJson = Input::get('package_ids');
            $overwriteFlag = Input::get('overwrite'); // 'true' or 'false'

            $therapistId = isset($_POST['therapist_id']) ? (int)$_POST['therapist_id'] : 1;

            if (!$slotsDataJson) {
                $response['errors'][] = 'Δεν παρέχονται δεδομένα ραντεβού.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            $slotsArray = json_decode($slotsDataJson, true);
            if (!is_array($slotsArray) || empty($slotsArray)) {
                $response['errors'][] = 'Μη έγκυρος πίνακας ραντεβού.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            if (!$packageIdsJson) {
                $response['errors'][] = 'Δεν παρέχονται αναγνωριστικά πακέτων.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            $packageIds = json_decode($packageIdsJson, true);
            if (!is_array($packageIds) || empty($packageIds)) {
                $response['errors'][] = 'Μη έγκυρα αναγνωριστικά πακέτων.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // e.g. validate each $slotsArray item
            $preparedData = [];
            foreach ($slotsArray as $slot) {
                if (
                    empty($slot['start_datetime']) ||
                    empty($slot['end_datetime']) ||
                    empty($slot['status']) ||
                    empty($slot['appointment_type'])
                ) {
                    $response['errors'][] = 'Λείπουν απαιτούμενα πεδία για ένα ραντεβού.';
                    continue;
                }
                $preparedData[] = [
                    'start_datetime'   => $slot['start_datetime'],
                    'end_datetime'     => $slot['end_datetime'],
                    'status'           => ($slot['status'] === 'booked') ? 'booked' : 'available',
                    'appointment_type' => $slot['appointment_type'],
                    'notes'            => $slot['notes'] ?? null
                ];
            }

            if (empty($preparedData)) {
                $response['errors'][] = 'Δεν υπάρχουν έγκυρα ραντεβού προς εισαγωγή.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $slotsModel = new Slots();

                // Optional final conflict check:
                foreach ($preparedData as $slot) {
                    // If 'overwrite' => remove existing overlaps
                    if ($overwriteFlag === 'true') {
                        $slotsModel->deleteOverlap($slot['start_datetime'], $slot['end_datetime']);
                    } else {
                        // If no overwrite => check conflict
                        $count = $slotsModel->checkConflict($slot['start_datetime'], $slot['end_datetime']);
                        if ($count > 0) {
                            $response['errors'][] = "Εντοπίστηκε επικάλυψη για το ραντεβού: {$slot['start_datetime']} - {$slot['end_datetime']}";
                        }
                    }
                }

                if (!empty($response['errors'])) {
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // 4) Now do the actual insertion
                $added = $slotsModel->addSlotsBulk($preparedData, $packageIds, $therapistId);
                if ($added) {
                    $response['success'] = true;
                    $response['createdCount'] = count($preparedData);
                    $response['message'] = 'Τα ραντεβού δημιουργήθηκαν με επιτυχία.';
                } else {
                    $response['errors'][] = 'Αποτυχία εισαγωγής ραντεβού.';
                }
            } catch (Exception $ex) {
                $response['errors'][] = $ex->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'getCalendarSlots':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $rangeStart = isset($_POST['start']) ? date('Y-m-d H:i:s', strtotime($_POST['start'])) : null;
            $rangeEnd   = isset($_POST['end'])   ? date('Y-m-d H:i:s', strtotime($_POST['end']))   : null;

            try {
                $slotsModel = new Slots();

                // 1. Fetch Generic Slots
                $conditions = [];
                if ($rangeStart && $rangeEnd) {
                    $conditions = [
                        's.start_datetime >=' => $rangeStart,
                        's.end_datetime <='   => $rangeEnd
                    ];
                }
                $slots = $slotsModel->fetchSlotsWithPackages($conditions);

                if ($slots) {
                    foreach ($slots as $slot) {
                        $response['data'][] = [
                            'id'               => $slot['id'],
                            'start_datetime'   => $slot['start_datetime'],
                            'end_datetime'     => $slot['end_datetime'],
                            'status'           => $slot['status'],
                            'appointment_type' => $slot['appointment_type'],
                            'notes'            => $slot['notes'],
                            'packages'         => $slot['packages'],
                            'source'           => 'slot' // Flag για το JS
                        ];
                    }
                }

                // 2. Fetch Group Events (PATCH)
                // Φέρνουμε τα Group Packages που πέφτουν στο διάστημα
                $db = Database::getInstance();
                $groupSql = "SELECT * FROM packages 
                             WHERE is_group = 1 
                               AND start_datetime >= :start 
                               AND start_datetime <= :end";
                $groupEvents = $db->query($groupSql, [':start' => $rangeStart, ':end' => $rangeEnd]);

                if ($groupEvents) {
                    foreach ($groupEvents as $evt) {
                        // Υπολογισμός τέλους βάσει διάρκειας
                        $endDateTime = date('Y-m-d H:i:s', strtotime($evt->start_datetime . " +{$evt->duration_minutes} minutes"));

                        $response['data'][] = [
                            'id'               => 'grp_' . $evt->id, // Ειδικό ID για να ξεχωρίζει
                            'start_datetime'   => $evt->start_datetime,
                            'end_datetime'     => $endDateTime,
                            'status'           => 'booked', // Τα groups φαίνονται ως booked
                            'appointment_type' => $evt->type,
                            'title'            => $evt->title . ' (Group)', // Εμφάνιση τίτλου
                            'source'           => 'group_event', // Flag
                            'is_group'         => 1,
                            'packages'         => []
                        ];
                    }
                }

                $response['success'] = true;
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'getCalendarSlots_old':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            // Possibly get the date range from $_POST
            $rangeStart = isset($_POST['start']) ? $_POST['start'] : null; // "2024-01-01T00:00:00Z"
            $rangeEnd   = isset($_POST['end'])   ? $_POST['end']   : null;

            // 1) Convert or parse them if you want to do date range queries
            // We'll just do a simple approach ignoring timezones for brevity
            // e.g. $rangeStart = substr($rangeStart, 0, 10); => "YYYY-MM-DD"
            // same with $rangeEnd

            try {
                $slotsModel = new Slots();

                // If you have a custom method that fetches by date range:
                // $slots = $slotsModel->fetchSlotsRange($rangeStart, $rangeEnd);

                // or just do a fetchAll for example:
                $slots = $slotsModel->fetchSlots(); // returns an array of slot objects from DB

                if ($slots) {
                    $response['success'] = true;
                    $response['data'] = [];
                    foreach ($slots as $slot) {
                        // e.g. $slot->start_datetime => "2024-01-10 09:00:00"
                        $response['data'][] = [
                            'id'               => $slot->id,
                            'start_datetime'   => $slot->start_datetime,
                            'end_datetime'     => $slot->end_datetime,
                            'status'           => $slot->status,
                            'appointment_type' => $slot->appointment_type,
                            'notes'            => $slot->notes
                        ];
                    }
                } else {
                    $response['errors'][] = 'Δεν βρέθηκαν διαθέσιμα ραντεβού.';
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'getCalendarSlotsRange':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            // Get the date range from $_POST
            $rangeStart = isset($_POST['start']) ? $_POST['start'] : null; // "YYYY-MM-DD HH:MM:SS"
            $rangeEnd   = isset($_POST['end'])   ? $_POST['end']   : null;
            $packageId  = isset($_POST['package_id']) ? $_POST['package_id'] : null;

            // Validate date range
            if (!$rangeStart || !$rangeEnd) {
                $response['errors'][] = 'Μη έγκυρο χρονικό διάστημα.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Validate package_id if provided
            if ($packageId && !is_numeric($packageId)) {
                $response['errors'][] = 'Μη έγκυρο αναγνωριστικό πακέτου.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $slotsModel = new Slots();

                // Fetch slots based on whether package_id is provided
                if ($packageId) {
                    $slots = $slotsModel->fetchSlotsRangeWithPackage($rangeStart, $rangeEnd, $packageId);
                } else {
                    $slots = $slotsModel->fetchSlotsRange($rangeStart, $rangeEnd);
                }

                if ($slots) {
                    $response['success'] = true;
                    $response['data'] = [];
                    foreach ($slots as $slot) {
                        $response['data'][] = [
                            'id'               => $slot->id,
                            'start_datetime'   => $slot->start_datetime,
                            'end_datetime'     => $slot->end_datetime,
                            'status'           => $slot->status,
                            'appointment_type' => $slot->appointment_type,
                            'notes'            => $slot->notes
                        ];
                    }
                } else {
                    if ($packageId) {
                        $response['errors'][] = 'Δεν βρέθηκαν διαθέσιμα ραντεβού για το επιλεγμένο πακέτο και χρονικό διάστημα.';
                    } else {
                        $response['errors'][] = 'Δεν βρέθηκαν διαθέσιμα ραντεβού για το επιλεγμένο χρονικό διάστημα.';
                    }
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'getBookingsForDay':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $dayDate = Input::get('dayDate'); // e.g. "2024-01-10"
            if (!$dayDate) {
                $response['errors'][] = 'Δεν παρέχεται ημερομηνία ημέρας.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Build date range: dayDate 00:00:00 => dayDate 23:59:59
            $startDay = $dayDate . ' 00:00:00';
            $endDay   = $dayDate . ' 23:59:59';

            // Example custom query in Bookings or Slots table
            // If you store bookings in a separate table, do that approach, or if you store them in "slots"
            // We'll do a custom approach:
            $sql = "SELECT 
              slots.id as slotID,
              slots.start_datetime as start,
              slots.end_datetime   as end,
              bookings.status      as bookingStatus,
              CONCAT(clients.first_name, ' ', clients.last_name) as clientName
            FROM bookings
            JOIN slots   ON slots.id   = bookings.slot_id
            JOIN clients ON clients.id = bookings.client_id
            WHERE slots.start_datetime >= :startDay
              AND slots.end_datetime   <= :endDay
              AND bookings.status <> 'canceled'";

            $params = [
                ':startDay' => $startDay,
                ':endDay'   => $endDay
            ];

            $db = Database::getInstance();
            $results = $db->query($sql, $params);

            if ($results) {
                $response['success'] = true;
                $response['data'] = $results; // we just pass them back as an array of objects
            } else {
                $response['errors'][] = 'Δεν βρέθηκαν κρατήσεις.';
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'getBookingsForSlot':
            $response = ['success' => false, 'data' => []];
            $slotId = Input::get('slotId');

            if ($slotId) {
                $slots = new Slots();
                $bookings = $slots->getBookingsForSlot($slotId); // Ensure you create this function
                if ($bookings) {
                    $response['success'] = true;
                    $response['data'] = $bookings;
                } else {
                    $response['errors'] = ['Δεν βρέθηκαν κρατήσεις για αυτό το ραντεβού.'];
                }
            } else {
                $response['errors'] = ['Μη έγκυρο αναγνωριστικό ραντεβού.'];
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'addSingleSlot':
            $response = ['success' => false, 'errors' => []];
            $status = Input::get('status');
            $start  = Input::get('start');
            $end    = Input::get('end');
            $notes  = Input::get('notes');
            $appointmentType = Input::get('appointmentType');

            $packageIds = json_decode(Input::get('package_ids'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response['errors'][] = 'Μη έγκυρο JSON για τα package_ids.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            // Validate...
            if (!$status || !$start || !$end || !$appointmentType) {
                $response['errors'][] = 'Λείπουν απαιτούμενα πεδία.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Additional validation for packages
            if (!is_array($packageIds) || empty($packageIds)) {
                $response['errors'][] = 'Πρέπει να επιλεγεί τουλάχιστον ένα πακέτο.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $slotsModel = new Slots();
            $fields = [
                'start_datetime'   => $start,
                'end_datetime'     => $end,
                'status'           => $status === 'booked' ? 'booked' : ($status === 'available' ? 'available' : 'other'),
                'appointment_type' => $appointmentType,
                'notes'            => $notes,
            ];
            $res = $slotsModel->addSlot($fields, $packageIds);
            if ($res) {
                $response['success'] = true;
                $response['message'] = 'Το ραντεβού δημιουργήθηκε.';
                $response['newID'] = $slotsModel->lastInsertedID();
            } else {
                $response['errors'][] = 'Αποτυχία δημιουργίας ραντεβού.';
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'updateSingleSlot':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            // Gather slot data from $_POST
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;
            $appointmentType = $_POST['appointmentType'] ?? null;
            $start = $_POST['start'] ?? null;
            $end = $_POST['end'] ?? null;
            $notes = $_POST['notes'] ?? '';

            $packageIds = isset($_POST['package_ids']) ? json_decode($_POST['package_ids'], true) : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response['errors'][] = 'Μη έγκυρο JSON για τα package_ids.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Validate required fields
            if (!$id || !$status || !$appointmentType || !$start || !$end) {
                $response['errors'][] = 'Λείπουν απαιτούμενα πεδία.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $slotsModel = new Slots();

                // Check if the slot exists
                $existingSlot = $slotsModel->fetchSlot($id);
                if (!$existingSlot) {
                    $response['errors'][] = 'Το ραντεβού δεν βρέθηκε.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // Convert 'start' and 'end' to 'Y-m-d H:i:s' format
                $start = date('Y-m-d H:i:s', strtotime($start));
                $end = date('Y-m-d H:i:s', strtotime($end));

                // Update slots table and package associations
                $slotUpdated = $slotsModel->updateSlot(
                    [
                        'status'           => $status,
                        'appointment_type' => $appointmentType,
                        'start_datetime'   => $start,
                        'end_datetime'     => $end,
                        'notes'            => $notes
                    ],
                    $id,
                    $packageIds
                );

                if ($slotUpdated) {
                    // Fetch all slots with updated packages
                    $updatedSlots = $slotsModel->fetchSlotsWithPackages();

                    $response['success'] = true;
                    $response['message'] = 'Το ραντεβού ενημερώθηκε με επιτυχία.';
                    $response['data'] = $updatedSlots;
                } else {
                    $response['errors'][] = 'Failed to update slot.';
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;

        case 'deleteSingleSlot':
            $response = ['success' => false, 'errors' => []];
            $id = (int)Input::get('id');
            if (!$id) {
                $response['errors'][] = 'Δεν παρέχεται αναγνωριστικό ραντεβού.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $slotsModel = new Slots();
            $res = $slotsModel->deleteSlot($id);
            if ($res) {
                $response['success'] = true;
                $response['message'] = 'Το ραντεβού διαγράφηκε.';
            } else {
                $response['errors'][] = 'Δεν ήταν δυνατή η διαγραφή του ραντεβού ή το ραντεβού δεν βρέθηκε.';
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;



        case 'bookSlot':
            $response = [
                'success' => false,
                'errors' => [],
            ];

            $slotId = (int)Input::get('slot_id');
            $clientId = (int)Input::get('client_id');

            if (!$slotId || !$clientId) {
                $response['errors'][] = 'Λείπει το αναγνωριστικό του ραντεβού ή του πελάτη.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // We'll do a simple approach: update slot to booked, or use Bookings class
            // If using the Bookings class approach:
            $bookingsModel = new Bookings();

            $fields = [
                'slot_id'   => $slotId,
                'client_id' => $clientId,
                'status'    => 'booked',
            ];

            if ($bookingsModel->addBooking($fields)) {
                // Optionally update the slot's status
                $slotsModel = new Slots();
                $slotsModel->updateSlot(['status' => 'booked'], $slotId);

                $response['success'] = true;
                $response['message'] = 'Το ραντεβού κρατήθηκε με επιτυχία.';
            } else {
                $response['errors'][] = 'Αποτυχία κράτησης ραντεβού.';
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'fetchJoinedBookings':
            $response = [
                'success' => false,
                'data'    => [],
                'errors'  => []
            ];

            try {
                $bookingsModel = new Bookings();
                $joinedBookings = $bookingsModel->fetchJoinedBookingsJoined();

                if ($joinedBookings) {
                    // Transform each row into the structure the DataTable expects
                    $data = [];
                    foreach ($joinedBookings as $row) {
                        $data[] = [
                            'id'             => $row->booking_id,
                            'client_name'    => $row->client_fname . ' ' . $row->client_lname,
                            'client_id'      => $row->client_id,
                            'phone'          => $row->client_phone,
                            'start_datetime' => $row->start_datetime,
                            'end_datetime'   => $row->end_datetime,
                            'status'         => $row->booking_status,
                            'created_at'     => $row->booking_created,
                            'slot_id'     => $row->slot_id,

                            // (Optional) If you want package info:
                            'package_id'   => $row->package_id,
                            'package_title' => $row->package_title,
                        ];
                    }

                    $response['success'] = true;
                    $response['data'] = $data;
                } else {
                    $response['errors'][] = 'Δεν βρέθηκαν κρατήσεις.';
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;

        /* -------------------------------------------------------------------------
           CASE: FETCH THERAPISTS (USERS)
           ------------------------------------------------------------------------- */
        case 'fetchTherapists':
            // Αν θέλεις να το βλέπουν μόνο admin:
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $therapistsModel = new Therapists();

            // Χρησιμοποιούμε τη μέθοδο του Model που φέρνει όλους τους θεραπευτές
            // Ταξινομημένους ήδη κατά Επώνυμο, Όνομα
            $data = $therapistsModel->fetchTherapists();

            if ($data) {
                // Επιστρέφουμε όλο το αντικείμενο (id, first_name, last_name, avatar, κλπ)
                header('Content-Type: application/json');
                exit(json_encode(['success' => true, 'data' => $data]));
            } else {
                header('Content-Type: application/json');
                exit(json_encode(['success' => true, 'data' => []]));
            }
            break;

        /* -------------------------------------------------------------------------
           THERAPISTS MANAGEMENT
           ------------------------------------------------------------------------- */

        // 1. Fetch List
        case 'fetchTherapistsData': // Άλλαξα λίγο το όνομα για να μην μπερδευτεί με το dropdowm
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $therapists = new Therapists();

            if ($therapists->fetchTherapists()) {
                $data = $therapists->data();
                header('Content-Type: application/json');
                exit(json_encode($data));
            } else {
                header('Content-Type: application/json');
                exit(json_encode([]));
            }
            break;

        // 2. Add Therapist
        case 'addTherapist':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'first_name' => ['dname' => 'First Name', 'required' => true, 'min' => 2],
                'last_name'  => ['dname' => 'Last Name', 'required' => true, 'min' => 2],
                'title'      => ['dname' => 'Title', 'required' => true]
            ]);

            if ($validation->passed()) {
                $mediaHandler = new MediaHandler();
                $therapistModel = new Therapists();

                // Image Upload
                $avatarPath = '';
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $mediaHandler->uploadImage(
                        $_FILES['avatar'],
                        5000000, // 5MB
                        ["image/jpeg", "image/png", "image/webp"],
                        'uploads/therapists/',
                        'returnFilePath'
                    );
                    if (strpos($uploadResult, 'Error:') === 0) {
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }
                    $avatarPath = $uploadResult;
                }

                $fields = [
                    'first_name' => Input::validateString(Input::get('first_name')),
                    'last_name'  => Input::validateString(Input::get('last_name')),
                    'title'      => Input::validateString(Input::get('title')),
                    'email'      => Input::validateString(Input::get('email')),
                    'phone'      => Input::validateString(Input::get('phone')),
                    'bio'        => Input::get('bio'), // Allow HTML or plain text
                    'avatar'     => $avatarPath
                ];

                if ($therapistModel->addTherapist($fields)) {
                    exit(json_encode(['success' => true, 'message' => "Ο θεραπευτής προστέθηκε."]));
                } else {
                    exit(json_encode(['success' => false, 'errors' => ['Σφάλμα βάσης δεδομένων.']]));
                }
            } else {
                exit(json_encode(['success' => false, 'errors' => $validation->errors()]));
            }
            break;

        // 3. Update Therapist
        case 'updTherapist':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $id = Input::validateInt(Input::get('therapistID'));
            if (!$id) exit(json_encode(['success' => false, 'errors' => ['Invalid ID']]));

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'first_name' => ['dname' => 'First Name', 'required' => true],
                'last_name'  => ['dname' => 'Last Name', 'required' => true]
            ]);

            if ($validation->passed()) {
                $therapistModel = new Therapists();
                $mediaHandler = new MediaHandler();

                $current = $therapistModel->fetchTherapist($id);
                if (!$current) exit(json_encode(['success' => false, 'errors' => ['Therapist not found']]));

                // Image Logic
                $avatarPath = $current->avatar;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $mediaHandler->uploadImage($_FILES['avatar'], 5000000, ["image/jpeg", "image/png", "image/webp"], 'uploads/therapists/', 'returnFilePath');
                    if (strpos($uploadResult, 'Error:') === 0) {
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }
                    // Delete old
                    if (!empty($current->avatar)) $mediaHandler->deleteImage($current->avatar);
                    $avatarPath = $uploadResult;
                }

                $fields = [
                    'first_name' => Input::validateString(Input::get('first_name')),
                    'last_name'  => Input::validateString(Input::get('last_name')),
                    'title'      => Input::validateString(Input::get('title')),
                    'email'      => Input::validateString(Input::get('email')),
                    'phone'      => Input::validateString(Input::get('phone')),
                    'bio'        => Input::get('bio'),
                    'avatar'     => $avatarPath
                ];

                if ($therapistModel->updateTherapist($fields, $id)) {
                    exit(json_encode(['success' => true, 'message' => "Ενημερώθηκε."]));
                } else {
                    exit(json_encode(['success' => false, 'errors' => ['Update failed.']]));
                }
            } else {
                exit(json_encode(['success' => false, 'errors' => $validation->errors()]));
            }
            break;

        // 4. Delete Therapist
        case 'delTherapist':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $id = Input::validateInt(Input::get('therapistID'));

            $therapistModel = new Therapists();
            $mediaHandler = new MediaHandler();

            $current = $therapistModel->fetchTherapist($id);
            if ($current) {
                if (!empty($current->avatar)) {
                    $mediaHandler->deleteImage($current->avatar);
                }
                if ($therapistModel->deleteTherapist($id)) {
                    exit(json_encode(['success' => true, 'message' => "Διαγράφηκε."]));
                }
            }
            exit(json_encode(['success' => false, 'errors' => ['Delete failed.']]));
            break;


        case 'addBooking':
            $response = [
                'success' => false,
                'errors'  => [],
            ];

            // 1. Retrieve input data
            $slotId   = (int) Input::get('slot_id');
            $clientId = (int) Input::get('client_id');
            $packageId = (int) Input::get('package_id'); // If package is required

            // 2. Basic validation
            if (!$slotId || !$clientId || !$packageId) {
                $response['errors'][] = 'Λείπουν απαιτούμενα δεδομένα: slot_id, client_id ή package_id.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 3. Check if slot is available (not already booked)
            $slotsModel = new Slots();
            $slot = $slotsModel->fetchSlot($slotId);
            if (!$slot) {
                $response['errors'][] = 'Το ραντεβού δεν βρέθηκε.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            if ($slot->status === 'booked') {
                $response['errors'][] = 'Αυτό το ραντεβού είναι ήδη κρατημένο.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 4. Optional: Validate client exists
            //    (Assuming you have a Clients class or similar)
            $clientsModel = new Clients();
            $clientExists = $clientsModel->fetchClient($clientId);
            if (!$clientExists) {
                $response['errors'][] = 'Ο πελάτης δεν βρέθηκε.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 5. Optional: Validate package exists
            //    (Assuming you have a Packages class or similar)
            $packagesModel = new Packages();
            $packageExists = $packagesModel->fetchPackage($packageId);
            if (!$packageExists) {
                $response['errors'][] = 'Το πακέτο δεν βρέθηκε.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 6. Create a new booking record
            $bookingsModel = new Bookings();
            $fields = [
                'slot_id'    => $slotId,
                'client_id'  => $clientId,
                'package_id' => $packageId,
                'status'     => 'booked',
            ];
            $bookingCreated = $bookingsModel->addBooking($fields);
            if (!$bookingCreated) {
                $response['errors'][] = 'Αποτυχία δημιουργίας κράτησης.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 7. Update the slot to 'booked'
            $slotUpdated = $slotsModel->updateSlot(['status' => 'booked'], $slotId);
            if (!$slotUpdated) {
                // Booking is created, but slot update failed
                $response['errors'][] = 'Αποτυχία ενημέρωσης της κατάστασης του ραντεβού.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 8. Return success
            $response['success'] = true;
            $response['message'] = 'Το ραντεβού κρατήθηκε με επιτυχία.';
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'updBooking':
            $response = [
                'success' => false,
                'errors'  => []
            ];

            // 1) Gather input
            $bookingId = (int)Input::get('booking_id');
            $clientId  = (int)Input::get('client_id');
            $packageId = (int)Input::get('package_id');
            $slotId    = (int)Input::get('slot_id');  // If you allow changing the slot
            $status    = Input::get('status');        // e.g. "booked", "canceled", "completed"

            // 2) Basic validation
            if (!$bookingId || !$clientId || !$slotId) {
                $response['errors'][] = 'Λείπει το αναγνωριστικό κράτησης, πελάτη ή ραντεβού.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            // Optionally, validate $packageId if required

            // 3) Fetch the existing booking
            $bookingsModel = new Bookings();
            $existingBooking = $bookingsModel->fetchBooking($bookingId);
            if (!$existingBooking) {
                $response['errors'][] = 'Η κράτηση δεν βρέθηκε.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 4) Build fields to update
            $fields = [
                'client_id'  => $clientId,
                'package_id' => $packageId,
                'slot_id'    => $slotId,
            ];

            // Ensure status is valid => default to 'booked' if invalid
            $validStatuses = ['booked', 'canceled', 'completed'];
            if (in_array($status, $validStatuses)) {
                $fields['status'] = $status;
            } else {
                // Or skip if you want to keep old status
                $fields['status'] = 'booked';
            }

            // 5) Update booking record
            $updated = $bookingsModel->updateBooking($fields, $bookingId);
            if (!$updated) {
                $response['errors'][] = 'Αποτυχία ενημέρωσης της κράτησης.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // 6) Optionally update slot status if relevant
            if ($fields['status'] === 'booked') {
                // Mark slot as booked
                $slotsModel = new Slots();
                $prevSlotUpdated = $slotsModel->updateSlot(['status' => 'available'], $existingBooking->slot_id);
                $slotUpdated = $slotsModel->updateSlot(['status' => 'booked'], $slotId);
                if (!$slotUpdated || !$prevSlotUpdated) {
                    $response['errors'][] = 'Αποτυχία ενημέρωσης της κατάστασης του ραντεβού.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }
            } elseif ($fields['status'] === 'canceled') {
                // Optionally free up the slot => set slot to 'available' or 'other'
                $slotsModel = new Slots();
                $slotsModel->updateSlot(['status' => 'available'], $slotId);
            }

            // 7) Return success
            $response['success'] = true;
            $response['message'] = 'Η κράτηση ενημερώθηκε με επιτυχία.';
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'delBooking':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'bookingID' => [
                    'dname' => 'Booking ID',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $bookings = new Bookings();
                $bookingID = Input::validateInt(Input::get('bookingID'));

                if (!$bookingID) {
                    $response['errors'] = ['Μη έγκυρο αναγνωριστικό πακέτου'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $booking = $bookings->fetchBooking(Input::get('bookingID'));
                if ($booking) {
                    $slots = new Slots();
                    $updateSlot = $slots->updateSlot(['status' => 'available'], $booking->slot_id);
                }
                if ($bookings->deleteBooking(Input::get('bookingID'))) {
                    $response['success'] = true;
                    $response['message'] = "Το πακέτο διαγράφηκε επιτυχώς.";
                } else {
                    $response['errors'] = ['Κάτι πήγε στραβά κατά τη διαγραφή του πακέτου.'];
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'addClient':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'fname' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ],
                'lname' => [
                    'dname' => 'Last Name',
                    'required' => true,
                    'min' => 2,
                ],
                'phone' => [
                    'dname' => 'Phone Number',
                    'required' => true,
                    'min' => 2,
                ],
            ]);

            if ($validation->passed()) {
                $clients = new Clients();



                $firstName = Input::validateString(Input::get('fname'));
                $lastName = Input::validateString(Input::get('lname'));
                $phone = Input::validateString(Input::get('phone'));
                $email = Input::validateString(Input::get('email'));


                if (!Input::validateEmail(Input::get('email'))) {
                    $response['success'] = false;
                    $response['errors'] = array("Λάθος Email");
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $clientNote = Input::validateString(Input::get('clientNote'));

                $clientExists = $clients->clientExists($phone, Input::get('email'));

                if ($clientExists) {
                    $response['success'] = false;
                    $response['errors'] = array("Υπάρχει ήδη πελάτης με τηλέφωνο $phone ή με email $email");
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }


                $actionResult = $clients->addClient(array(
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => Input::get('email'),
                    'phone' => $phone,
                    'client_note' => $clientNote,
                ), Input::get('returnID'));

                if ((bool)Input::get('returnID') === true && is_numeric($actionResult)) {
                    $response['success'] = true;
                    $response['message'] = array("Προστέθηκε επιτυχώς");
                    $response['client_id'] = $actionResult;
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = array("Προστέθηκε επιτυχώς");
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;



        case 'updClient':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'fname' => [
                    'dname' => 'First Name',
                    'required' => true,
                    'min' => 2,
                ],
                'lname' => [
                    'dname' => 'Last Name',
                    'required' => true,
                    'min' => 2,
                ],
                'phone' => [
                    'dname' => 'Phone Number',
                    'required' => true,
                    'min' => 2,
                ],
                'clientID' => [
                    'dname' => 'Client ID',
                    'required' => true
                ],
            ]);

            if ($validation->passed()) {
                $clients = new Clients();

                $firstName = Input::validateString(Input::get('fname'));
                $lastName = Input::validateString(Input::get('lname'));
                $phone = Input::validateString(Input::get('phone'));
                $clientNote = Input::validateString(Input::get('clientNote'));

                $clientID = Input::validateInt(Input::get('clientID'));
                if (!$clientID) {
                    $response['errors'] = array('Μη έγκυρος πελάτης');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }


                $currentClient = $clients->fetchClient(Input::get('clientID'));
                if ($currentClient) {
                    if ($currentClient->phone != $phone) {
                        if ($clients->clientExists($phone)) {
                            $response['success'] = false;
                            $response['errors'] = array("Ένας πελάτης με το τηλέφωνο $phone υπάρχει ήδη.");
                            header('Content-Type: application/json');
                            exit(json_encode($response));
                        }
                    }
                }

                $updateResult = $clients->updateClient(array(
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => Input::get('email'),
                    'phone' => $phone,
                    'client_note' => $clientNote,
                ), Input::get('clientID'));

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση ολοκληρώθηκε";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'fetchClients':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $clients = new Clients();

            if ($clients->fetchClients()) {

                if ((bool)Input::get('returnID') === true) {
                    $response['success'] = true;
                    $response['message'] = array("Προστέθηκε επιτυχώς");
                    $response['data'] = json_encode($clients->data());
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                header('Content-Type: application/json');
                exit(json_encode($clients->data()));
            } else {
                $response['errors'] = array('Κάτι πήγε στραβά κατά την ανάκτηση των πελατών.');
            }

            header('Content-Type: application/json');
            exit(json_encode($response));

            break;


        case 'delClients':

            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'clientID' => [
                    'dname' => 'Client ID',
                    'required' => true
                ],
            ]);


            if ($validation->passed()) {
                $clients = new Clients();
                $inputValidate = new Input();
                $clientID = $inputValidate->validateInt(Input::get('clientID'));

                if (!$clientID) {
                    $response['errors'] = array('Μη έγκυρος πελάτης');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                //Get All Client Vehicles/Images & Delete
                $crud = new Crud();
                $mediaHandler = new MediaHandler();
                // $vehicles = new Vehicles();
                $bookings = new Bookings();

                if ($clients->deleteClient(Input::get('clientID'))) {
                    $response['success'] = true;
                    $response['message'] = "Ο πελάτης αφαιρέθηκε επιτυχώς";
                } else {
                    $response['errors'] = array('Something wend wrong while deleting the client(s)');
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }


            break;


        /* -------------------------------------------------------------------------
           CASE: ADD PACKAGE (FIXED THERAPISTS)
           ------------------------------------------------------------------------- */
        case 'addPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'title'            => ['dname' => 'Τίτλος', 'required' => true, 'min' => 2, 'max' => 150],
                'category'         => ['dname' => 'Κατηγορία', 'required' => true, 'in' => ['adults', 'parents', 'kids', 'psychosexual']],
                'type'             => ['dname' => 'Τύπος', 'required' => true, 'in' => ['online', 'inPerson', 'mixed']],
                'price'            => ['dname' => 'Τιμή', 'numeric' => true, 'nullable' => true],
                'duration_minutes' => ['dname' => 'Διάρκεια', 'required' => true, 'numeric' => true],
                'max_attendants'   => ['dname' => 'Συμμετέχοντες', 'numeric' => true, 'min' => 1],
                'manual_bookings'  => ['dname' => 'Manual Bookings', 'numeric' => true, 'min' => 0]
            ]);

            if ($validation->passed()) {
                $packages = new Packages();

                // 1. Sanitize Inputs
                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = Input::get('price') ? (float)Input::get('price') : null;
                $type = Input::validateString(Input::get('type'));
                $category = Input::validateString(Input::get('category'));
                $duration = (int)Input::get('duration_minutes');

                // 2. Includes (JSON)
                $includesRaw = Input::get('includes');
                $includesArr = json_decode($includesRaw, true);
                if (!is_array($includesArr)) $includesArr = [];
                $includesJson = json_encode($includesArr, JSON_UNESCAPED_UNICODE);

                // 3. Group & Schedule Logic
                $is_group = ((int)Input::get('is_group') === 1) ? 1 : 0;
                $max_attendants = (int)Input::get('max_attendants');
                $manual_bookings = (int)Input::get('manual_bookings');

                $start_datetime = Input::get('start_datetime');
                if (empty($start_datetime)) $start_datetime = null;

                if ($is_group === 0) {
                    $max_attendants = 1;
                    $manual_bookings = 0;
                    $start_datetime = null;
                } else {
                    if ($max_attendants < 2) $max_attendants = 2;
                }

                // 4. Prepare Fields for DB
                $fields = [
                    'title'            => $title,
                    'category'         => $category,
                    'description'      => $description,
                    'price'            => $price,
                    'duration_minutes' => $duration,
                    'includes'         => $includesJson,
                    'type'             => $type,
                    'is_group'         => $is_group,
                    'start_datetime'   => $start_datetime,
                    'max_attendants'   => $max_attendants,
                    'manual_bookings'  => $manual_bookings
                ];

                // 5. Prepare Therapists (ROBUST HANDLING)
                $therapists = [];
                if (isset($_POST['therapists'])) {
                    $rawTherapists = $_POST['therapists'];

                    if (is_array($rawTherapists)) {
                        $therapists = $rawTherapists;
                    } elseif (is_string($rawTherapists)) {
                        // Handle "1,2" or "1" strings from FormData
                        if (strpos($rawTherapists, ',') !== false) {
                            $therapists = explode(',', $rawTherapists);
                        } elseif (is_numeric($rawTherapists)) {
                            $therapists = [$rawTherapists];
                        }
                    }
                }

                // 6. Save to DB & Sync
                if ($packages->addPackage($fields)) {
                    $newId = $packages->lastInsertedID();

                    // Sync the prepared therapists array
                    $packages->syncTherapists($newId, $therapists);

                    $response['success'] = true;
                    $response['message'] = "Το πακέτο δημιουργήθηκε επιτυχώς.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = ["Αποτυχία εγγραφής στη βάση δεδομένων."];
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        /* -------------------------------------------------------------------------
           CASE: UPDATE PACKAGE (CORRECTED ID LOGIC)
           ------------------------------------------------------------------------- */
        case 'updPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();

            // 1. Πλήρες Validation
            $validation = $validate->check($_POST, [
                'packageID'        => ['required' => true, 'numeric' => true],
                'title'            => ['required' => true, 'min' => 2, 'max' => 150],
                'category'         => ['required' => true, 'in' => ['adults', 'parents', 'kids', 'psychosexual']],
                'type'             => ['required' => true, 'in' => ['online', 'inPerson', 'mixed']],
                'duration_minutes' => ['required' => true, 'numeric' => true],
                'price'            => ['numeric' => true, 'nullable' => true],
                'max_attendants'   => ['numeric' => true, 'min' => 1],
                'manual_bookings'  => ['numeric' => true, 'min' => 0]
            ]);

            if ($validation->passed()) {
                $packages = new Packages();

                // 2. Διόρθωση ID Logic
                // Πρώτα ελέγχουμε αν είναι έγκυρος ακέραιος (επιστρέφει true/false)
                if (!Input::validateInt(Input::get('packageID'))) {
                    $response['success'] = false;
                    $response['errors'] = ['Μη έγκυρο ID πακέτου.'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // ΜΕΤΑ παίρνουμε την πραγματική τιμή
                $id = (int)Input::get('packageID');

                // 3. Προετοιμασία Δεδομένων
                $includesRaw = Input::get('includes');
                $includesArr = json_decode($includesRaw, true);
                if (!is_array($includesArr)) $includesArr = [];

                $start_datetime = Input::get('start_datetime');
                if (empty($start_datetime)) $start_datetime = null;

                $fields = [
                    'title'            => Input::validateString(Input::get('title')),
                    'category'         => Input::validateString(Input::get('category')),
                    'description'      => Input::validateString(Input::get('description')),
                    'price'            => Input::get('price') ? (float)Input::get('price') : null,
                    'duration_minutes' => (int)Input::get('duration_minutes'),
                    'type'             => Input::validateString(Input::get('type')),
                    'includes'         => json_encode($includesArr, JSON_UNESCAPED_UNICODE),
                    'is_group'         => ((int)Input::get('is_group') === 1) ? 1 : 0,
                    'start_datetime'   => $start_datetime,
                    'max_attendants'   => (int)Input::get('max_attendants'),
                    'manual_bookings'  => (int)Input::get('manual_bookings')
                ];

                // 4. Update Package
                // Ακόμα και αν επιστρέψει false (επειδή δεν άλλαξαν πεδία), συνεχίζουμε για τους therapists
                $packages->updatePackage($fields, $id);

                // 5. Update Therapists
                $therapists = [];
                if (isset($_POST['therapists'])) {
                    $rawT = $_POST['therapists'];
                    if (is_array($rawT)) {
                        $therapists = $rawT;
                    } elseif (is_string($rawT)) {
                        // Χειρισμός string "1,2" ή "1"
                        if (strpos($rawT, ',') !== false) {
                            $therapists = explode(',', $rawT);
                        } else {
                            $therapists = [$rawT];
                        }
                    }
                }

                // Καλεί το Sync (που πλέον έχει το σωστό $id)
                $packages->syncTherapists($id, $therapists);

                $response['success'] = true;
                $response['message'] = "Το πακέτο ενημερώθηκε επιτυχώς.";
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        /* -------------------------------------------------------------------------
       CASE: FETCH PACKAGES
       ------------------------------------------------------------------------- */
        case 'fetchPackages':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $packages = new Packages();

            $data = $packages->fetchPackages();

            // DataTables expects raw JSON array [ {...}, {...} ]
            header('Content-Type: application/json');
            exit(json_encode($data ?: []));
            break;


        case 'fetchPackage':
            if (!Input::validateInt(Input::get('id'))) {
                $response['success'] = false;
                $response['errors'] = ['Μη έγκυρο ID πακέτου.'];
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $id = Input::get('id');
            $packages = new Packages();
            $pkg = $packages->fetchPackage($id);
            if ($pkg) {

                $pkg->therapists =  $packages->getTherapistIds($id);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $pkg]);
            exit;
            break;
        /* -------------------------------------------------------------------------
       CASE: DELETE PACKAGE
       ------------------------------------------------------------------------- */
        case 'delPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'packageID' => ['required' => true, 'numeric' => true]
            ]);

            if ($validation->passed()) {
                $packages = new Packages();
                if (!Input::validateInt(Input::get('id'))) {
                    $response['success'] = false;
                    $response['errors'] = ['Μη έγκυρο ID πακέτου.'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }


                $id = Input::get('packageID');

                if ($packages->deletePackage($id)) {
                    $response['success'] = true;
                    $response['message'] = "Το πακέτο διαγράφηκε επιτυχώς.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = ["Αποτυχία διαγραφής."];
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'updateManualBookings':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $id = Input::validateInt(Input::get('id'));
            $count = Input::validateInt(Input::get('manual_bookings'));

            if (!$id) {
                $response['success'] = false;
                $response['errors'] = ['Μη έγκυρο ID'];
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            $id = Input::get('id');
            $count = Input::get('manual_bookings');

            // Validation: Το count δεν μπορεί να είναι αρνητικό
            if ($count < 0) $count = 0;

            // Χρήση του Crud Class αντί για raw SQL
            $crud = new Crud();

            // update(table, fields_array, where_array)
            $result = $crud->update(
                'packages',
                ['manual_bookings' => $count],
                ['id' => $id]
            );


            if ($result !== false) {
                $response['success'] = true;
                $response['message'] = "Οι κρατήσεις ενημερώθηκαν επιτυχώς.";
            } else {
                $response['success'] = false;
                $response['errors'] = ["Αποτυχία ενημέρωσης βάσης δεδομένων."];
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'addEdietPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();

            $validation = $validate->check($_POST, [
                'title' => [
                    'dname' => 'Title',
                    'required' => true,
                    'min' => 2,
                ],
            ]);

            if ($validation->passed()) {
                $packages = new EdietPackages();

                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = Input::get('price') ? parseCurrencyInput(Input::get('price')) : null;

                // Decode includes if sent as JSON
                $includesRaw = Input::get('includes');
                $includes = json_decode($includesRaw, true); // Decode JSON to array
                if (!is_array($includes)) {
                    $includes = []; // Default to empty array if decoding fails
                }
                $includesJson = json_encode($includes, JSON_UNESCAPED_UNICODE);

                $actionResult = $packages->addPackage([
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'includes' => $includesJson,
                ]);

                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Το πακέτο προστέθηκε επιτυχώς.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $actionResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'updEdietPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();

            $validation = $validate->check($_POST, [
                'title' => [
                    'dname' => 'Title',
                    'required' => true,
                    'min' => 2,
                ],
                'packageID' => [
                    'dname' => 'Package ID',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $packages = new EdietPackages();

                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = Input::get('price') ? parseCurrencyInput(Input::get('price')) : null;

                // Decode includes if sent as JSON
                $includesRaw = Input::get('includes');
                $includes = json_decode($includesRaw, true); // Decode JSON to array
                if (!is_array($includes)) {
                    $includes = []; // Default to empty array if decoding fails
                }
                $includesJson = json_encode($includes, JSON_UNESCAPED_UNICODE);

                $packageID = Input::validateInt(Input::get('packageID'));

                if (!$packageID) {
                    $response['errors'] = ['Μη έγκυρο αναγνωριστικό πακέτου'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $updateResult = $packages->updatePackage(
                    [
                        'title' => $title,
                        'description' => $description,
                        'price' => $price,
                        'includes' => $includesJson,
                    ],
                    Input::get('packageID')
                );

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση του πακέτου ολοκληρώθηκε επιτυχώς.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'fetchEdietPackages':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $packages = new EdietPackages();

            if ($packages->fetchPackages()) {
                header('Content-Type: application/json');
                exit(json_encode($packages->data()));
            } else {
                $response['errors'] = ['Κάτι πήγε στραβά κατά την ανάκτηση των πακέτων.'];
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'delEdietPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'packageID' => [
                    'dname' => 'Package ID',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $packages = new EdietPackages();
                $packageID = Input::validateInt(Input::get('packageID'));

                if (!$packageID) {
                    $response['errors'] = ['Μη έγκυρο αναγνωριστικό πακέτου'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                if ($packages->deletePackage(Input::get('packageID'))) {
                    $response['success'] = true;
                    $response['message'] = "Το πακέτο διαγράφηκε επιτυχώς.";
                } else {
                    $response['errors'] = ['Κάτι πήγε στραβά κατά τη διαγραφή του πακέτου.'];
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'addDigitalProduct':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();

            // 1. Validation των πεδίων κειμένου
            $validation = $validate->check($_POST, [
                'title' => ['dname' => 'Title', 'required' => true, 'min' => 2],
                'price' => ['dname' => 'Price', 'required' => true]
            ]);

            if ($validation->passed()) {
                $mediaHandler = new MediaHandler();
                $digitalProd = new DigitalProducts();

                // 2. Διαχείριση Upload PDF (Το eBook)
                $pdfPath = '';
                if (isset($_FILES['ebook_file']) && $_FILES['ebook_file']['error'] === UPLOAD_ERR_OK) {
                    // Upload στο φάκελο ebooks/ (θα πρέπει να δημιουργηθεί)
                    $uploadResult = $mediaHandler->uploadFile(
                        $_FILES['ebook_file'],
                        50000000, // 50MB όριο
                        ['pdf'],
                        'uploads/ebooks/',
                        'returnFilePath'
                    );

                    if (strpos($uploadResult, 'Error:') === 0) {
                        // Αν αποτύχει το upload
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }
                    $pdfPath = $uploadResult;
                } else {
                    exit(json_encode(['success' => false, 'errors' => ['Το αρχείο eBook είναι απαραίτητο.']]));
                }

                // 3. Διαχείριση Upload Εξωφύλλου (Προαιρετικό αλλά καλό)
                $coverPath = '';
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $mediaHandler->uploadImage(
                        $_FILES['cover_image'],
                        2000000, // 2MB
                        ["image/jpeg", "image/png", "image/webp"],
                        'uploads/ebooks/covers/',
                        'returnFilePath'
                    );
                    if (strpos($uploadResult, 'Error:') === 0) {
                        // Αν αποτύχει η εικόνα, ίσως δεν θέλουμε να κόψουμε όλη τη διαδικασία, 
                        // αλλά για τώρα ας το κάνουμε αυστηρό.
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }
                    $coverPath = $uploadResult;
                }

                // 4. Αποθήκευση στη Βάση
                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = parseCurrencyInput(Input::get('price'));

                $actionResult = $digitalProd->addProduct([
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'file_path' => $pdfPath,
                    'cover_image' => $coverPath
                ]);

                if ($actionResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Το eBook προστέθηκε επιτυχώς.";
                } else {
                    // Αν αποτύχει η βάση, καλό θα ήταν να σβήσουμε τα αρχεία που ανεβάσαμε (cleanup),
                    // αλλά προς το παρόν το αφήνουμε απλό.
                    $response['success'] = false;
                    $response['errors'] = ['Σφάλμα βάσης δεδομένων.'];
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'updDigitalProduct':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            // Προσοχή: Επειδή στέλνουμε αρχεία, η φόρμα θα είναι FormData, 
            // οπότε το $_POST δουλεύει κανονικά.

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'title' => ['dname' => 'Title', 'required' => true, 'min' => 2],
                'productID' => ['dname' => 'Product ID', 'required' => true],
                'price' => ['dname' => 'Price', 'required' => true]
            ]);

            if ($validation->passed()) {
                $digitalProd = new DigitalProducts();
                $mediaHandler = new MediaHandler();

                $id = Input::validateInt(Input::get('productID'));
                $currentProduct = $digitalProd->fetchProduct($id);

                if (!$currentProduct) {
                    exit(json_encode(['success' => false, 'errors' => ['Το προϊόν δεν βρέθηκε.']]));
                }

                // --- Διαχείριση PDF ---
                $pdfPath = $currentProduct->file_path; // Κρατάμε το παλιό αρχικά
                if (isset($_FILES['ebook_file']) && $_FILES['ebook_file']['error'] === UPLOAD_ERR_OK) {
                    // 1. Upload νέου
                    $uploadResult = $mediaHandler->uploadFile($_FILES['ebook_file'], 50000000, ['pdf'], 'uploads/ebooks/', 'returnFilePath');

                    if (strpos($uploadResult, 'Error:') === 0) {
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }

                    // 2. Διαγραφή παλιού (αν υπήρχε και αν πέτυχε το νέο upload)
                    if (!empty($currentProduct->file_path)) {
                        $mediaHandler->deleteFile($currentProduct->file_path);
                    }

                    $pdfPath = $uploadResult; // Ενημερώνουμε τη μεταβλητή για τη βάση
                }

                // --- Διαχείριση Cover Image ---
                $coverPath = $currentProduct->cover_image; // Κρατάμε το παλιό αρχικά
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                    // 1. Upload νέου
                    $uploadResult = $mediaHandler->uploadImage($_FILES['cover_image'], 2000000, ["image/jpeg", "image/png", "image/webp"], 'uploads/ebooks/covers/', 'returnFilePath');

                    if (strpos($uploadResult, 'Error:') === 0) {
                        exit(json_encode(['success' => false, 'errors' => [$uploadResult]]));
                    }

                    // 2. Διαγραφή παλιού
                    if (!empty($currentProduct->cover_image)) {
                        $mediaHandler->deleteImage($currentProduct->cover_image);
                    }

                    $coverPath = $uploadResult;
                }

                // --- Update στη Βάση ---
                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = parseCurrencyInput(Input::get('price'));

                $updateResult = $digitalProd->updateProduct([
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'file_path' => $pdfPath,   // Νέο ή παλιό path
                    'cover_image' => $coverPath // Νέο ή παλιό path
                ], $id);

                // Σημείωση: Αν δεν αλλάξει τίποτα, η update επιστρέφει false/0 rows affected σε μερικά συστήματα.
                // Εδώ υποθέτουμε ότι αν δεν σκάσει SQL error, είναι success.

                $response['success'] = true;
                $response['message'] = "Το προϊόν ενημερώθηκε.";
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'fetchDigitalProducts':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $digitalProd = new DigitalProducts();

            if ($digitalProd->fetchProducts()) {
                $data = $digitalProd->data();
                // Προαιρετικά: Μπορείς να προσθέσεις το full URL στα paths αν χρειάζεται
                // αλλά συνήθως το path αρκεί.
                header('Content-Type: application/json');
                exit(json_encode($data));
            } else {
                // Αν είναι άδειος ο πίνακας, στέλνουμε κενό array για να μην σκάσει το JS
                header('Content-Type: application/json');
                exit(json_encode([]));
            }
            break;

        case 'delDigitalProduct':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'productID' => ['dname' => 'Product ID', 'required' => true]
            ]);

            if ($validation->passed()) {
                $digitalProd = new DigitalProducts();
                $mediaHandler = new MediaHandler();

                $id = Input::validateInt(Input::get('productID'));

                // 1. Πρώτα παίρνουμε τα στοιχεία του προϊόντος για να βρούμε τα paths των αρχείων
                $product = $digitalProd->fetchProduct($id);

                if ($product) {
                    // 2. Διαγραφή φυσικών αρχείων
                    if (!empty($product->file_path)) {
                        $mediaHandler->deleteFile($product->file_path);
                    }
                    if (!empty($product->cover_image)) {
                        $mediaHandler->deleteImage($product->cover_image);
                    }

                    // 3. Διαγραφή από τη βάση
                    if ($digitalProd->deleteProduct($id)) {
                        $response['success'] = true;
                        $response['message'] = "Το προϊόν και τα αρχεία του διαγράφηκαν επιτυχώς.";
                    } else {
                        $response['errors'] = ['Το προϊόν δεν μπόρεσε να διαγραφεί από τη βάση.'];
                    }
                } else {
                    $response['errors'] = ['Το προϊόν δεν βρέθηκε.'];
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'fetchDigitalOrders':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $dp = new DigitalProducts();
            $orders = $dp->fetchOrders();

            if ($orders) {
                header('Content-Type: application/json');
                exit(json_encode($orders));
            } else {
                // Επιστροφή κενού πίνακα αν δεν υπάρχουν παραγγελίες
                header('Content-Type: application/json');
                exit(json_encode([]));
            }
            break;

        case 'resetDigitalOrder':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $orderID = Input::validateInt(Input::get('orderID'));
            $type = Input::get('reset_type'); // 'downloads' or 'expiration'

            if (!$orderID || !in_array($type, ['downloads', 'expiration'])) {
                $response['errors'] = ['Μη έγκυρα δεδομένα.'];
                exit(json_encode($response));
            }

            $rawID = Input::get('orderID');

            $dp = new DigitalProducts();

            if ($dp->resetOrder($rawID, $type)) {
                $response['success'] = true;
                $msg = ($type === 'downloads') ? "Ο μετρητής λήψεων μηδενίστηκε." : "Ο σύνδεσμος ανανεώθηκε για 7 ημέρες.";
                $response['message'] = $msg;
            } else {
                $response['errors'] = ['Προέκυψε σφάλμα κατά την ενημέρωση.'];
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'fetchPayments':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $payments = new Payments();

            if ($payments->fetchPayments()) {
                header('Content-Type: application/json');
                exit(json_encode($payments->data()));
            } else {
                $response['errors'] = ['Κάτι πήγε στραβά κατά την ανάκτηση των πακέτων.'];
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'delPayment':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'paymentID' => [
                    'dname' => 'Payment ID',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $payments = new Payments();
                $paymentID = Input::validateInt(Input::get('paymentID'));

                if (!$paymentID) {
                    $response['errors'] = ['Μη έγκυρο αναγνωριστικό πληρωμής'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                if ($payments->deletePayment(Input::get('paymentID'))) {
                    $response['success'] = true;
                    $response['message'] = "Διαγράφηκε επιτυχώς.";
                } else {
                    $response['errors'] = ['Κάτι πήγε στραβά κατά τη διαγραφή.'];
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;

        case 'updPayment':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $validate = new Validate();

            $validation = $validate->check($_POST, [
                'paymentID' => [
                    'dname' => 'Payment ID',
                    'required' => true,
                ],
            ]);

            if ($validation->passed()) {
                $payments = new Payments();

                $paymentID = Input::validateInt(Input::get('paymentID'));

                if (!$paymentID) {
                    $response['errors'] = ['Μη έγκυρο αναγνωριστικό πληρωμής'];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $note = Input::validateString(Input::get('note'));

                $updateResult = $payments->updatePayment(
                    [
                        'note' => $note,
                    ],
                    Input::get('paymentID')
                );

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ενημέρωση του πακέτου ολοκληρώθηκε επιτυχώς.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        case 'fetchNotifications':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $notifications = new Notifications();

            $allNotifs = $notifications->fetchNotifications();

            if ($allNotifs) {
                header('Content-Type: application/json');
                exit(json_encode([
                    'success' => true,
                    'notifications' => $notifications->data()
                ]));
            } else {
                $response['success'] = false;
                // $response['errors'] = array('Something went wrong while fetching notifications');
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            break;


        case 'markNotificationRead':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'notifID' => [
                    'dname'    => 'Notification ID',
                    'required' => true,
                    'numeric'  => true
                ],
            ]);

            if ($validation->passed()) {
                $notifications = new Notifications();

                $notifID = Input::validateInt(Input::get('notifID'));
                if (!$notifID) {
                    $response['errors'][] = 'Μη έγκυρο αναγνωριστικό ειδοποίησης';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $notifObj = $notifications->fetchNotification(Input::get('notifID'));
                if (!$notifObj) {
                    $response['errors'][] = 'Η ειδοποίηση δεν βρέθηκε';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // 4) Ενημέρωση (π.χ. is_read = true)
                $updateResult = $notifications->updateNotification([
                    'is_read' => true
                ], Input::get('notifID'));

                if ($updateResult === true) {
                    $response['success'] = true;
                    $response['message'] = "Η ειδοποίηση επισημάνθηκε ως αναγνωσμένη.";
                } else {
                    $response['success'] = false;
                    $response['errors'] = $updateResult;
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            break;

        case 'delNotification':
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            // 1) Validate inputs
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'notifID' => [
                    'dname'    => 'Notification ID',
                    'required' => true
                ],
            ]);

            if ($validation->passed()) {
                $notifications = new Notifications();
                $inputValidate = new Input();

                $notifID = $inputValidate->validateInt(Input::get('notifID'));
                if (!$notifID) {
                    $response['errors'] = array('Μη έγκυρο αναγνωριστικό ειδοποίησης');
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                if ($notifications->deleteNotification(Input::get('notifID'))) {
                    $response['success'] = true;
                    $response['message'] = "Η ειδοποίηση διαγράφηκε με επιτυχία.";
                } else {
                    $response['errors'] = array('Κάτι πήγε στραβά κατά τη διαγραφή της ειδοποίησης.');
                }

                header('Content-Type: application/json');
                exit(json_encode($response));
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            break;


        ///DEFAULT NOTHING
        default:
            break;
    }

    $response['errors'] = array('It seems nothing happened');

    header('Content-Type: application/json');
    exit(json_encode($response));
} else {
    $response['errors'] = array('Invalid Token');
    header('Content-Type: application/json');
    exit(json_encode($response));
}
