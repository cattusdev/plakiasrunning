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
                    'dname' => 'Booking - Τίτλος',
                    'required' => true,
                    'min' => 2,
                ),
                'r_completeBookingMessage' => array(
                    'dname' => 'Booking - Μήνυμα',
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
                    'avatar'     => $avatarPath,
                    'booking_window_days' => (int)Input::get('booking_window_days') ?: 60,
                    'min_notice_hours'    => (int)Input::get('min_notice_hours') ?: 12,
                    'languages'  => Input::validateString(Input::get('languages')),
                    'pace_range' => Input::validateString(Input::get('pace_range')),
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
            $id = Input::get('therapistID');
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
                    'avatar'     => $avatarPath,
                    'booking_window_days' => (int)Input::get('booking_window_days'),
                    'min_notice_hours'    => (int)Input::get('min_notice_hours'),
                    'languages'  => Input::validateString(Input::get('languages')),
                    'pace_range' => Input::validateString(Input::get('pace_range')),
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


        // --- 1. FETCH TABLE DATA ---
        case 'fetchJoinedBookings':
            // Αν χρειάζεται admin permission, το βάζεις εδώ
            $bookingsModel = new Bookings();
            $data = $bookingsModel->fetchJoinedBookings();

            // DataTables expects: { "data": [...] }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
            break;

        // --- 2. SAVE BOOKING (Create/Update) ---
        // --- 2. SAVE BOOKING (Create/Update) ---
        case 'saveBooking':
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'client_id' => ['dname' => 'Client', 'required' => true],
                'appointment_type' => ['dname' => 'Type', 'required' => true],
                'therapist_id' => ['dname' => 'Guide', 'required' => true],
                'start' => ['dname' => 'Start Date', 'required' => true],
                'end' => ['dname' => 'End Date', 'required' => true],
                'attendees_count' => ['dname' => 'Pax', 'numeric' => true, 'min' => 1]
            ]);

            if ($validation->passed()) {
                $bookingsModel = new Bookings();
                $db = Database::getInstance();

                $idParam = Input::get('id');
                $bookingId = ($idParam && Input::validateInt($idParam)) ? (int)$idParam : 0;

                // Inputs
                $clientId = (int)Input::get('client_id');
                $therapistId = (int)Input::get('therapist_id');
                $packageId = (int)Input::get('package_id') ?: null;
                $attendeesCount = (int)Input::get('attendees_count') ?: 1; // Default 1

                $startSQL = date('Y-m-d H:i:s', strtotime(Input::get('start')));
                $endSQL = date('Y-m-d H:i:s', strtotime(Input::get('end')));
                $notes = Input::get('notes');
                $status = Input::get('status') ?: 'booked';
                $paymentStatus = Input::get('payment_status') ?: 'unpaid';
                $apptType = Input::get('appointment_type');

                // Checks
                if ($endSQL <= $startSQL) {
                    echo json_encode(['success' => false, 'errors' => ['Η λήξη πρέπει να είναι μετά την έναρξη.']]);
                    exit;
                }

                // 1. Fetch Package Info (Limits)
                $maxAttendants = 1; // Default personal
                $manualBookings = 0;

                if ($packageId) {
                    $pkgObj = $db->query("SELECT max_attendants, manual_bookings FROM packages WHERE id = :id", [':id' => $packageId]);
                    if ($pkgObj && count($pkgObj) > 0) {
                        $maxAttendants = (int)$pkgObj[0]->max_attendants;
                        $manualBookings = (int)$pkgObj[0]->manual_bookings; // Αυτό ισχύει κυρίως για Events (is_group=1)
                    }
                }

                // 2. CAPACITY CHECK (Ο Κινητήρας)
                // Πόσοι είναι ήδη γραμμένοι σε αυτό το διάστημα;
                $currentPax = $bookingsModel->getCapacityUsage($therapistId, $startSQL, $endSQL, $bookingId);

                // Πόσοι θέλουμε να μπούμε τώρα;
                $totalPaxAfterBooking = $currentPax + $attendeesCount + $manualBookings;

                if ($totalPaxAfterBooking > $maxAttendants) {
                    echo json_encode(['success' => false, 'errors' => [
                        "Δεν υπάρχει επαρκής διαθεσιμότητα. (Ήδη: $currentPax, Max: $maxAttendants)"
                    ]]);
                    exit;
                }

                // 3. Save
                $fields = [
                    'client_id' => $clientId,
                    'therapist_id' => $therapistId,
                    'package_id' => $packageId,
                    'start_datetime' => $startSQL,
                    'end_datetime' => $endSQL,
                    'attendees_count' => $attendeesCount, // Save pax
                    'status' => $status,
                    'appointment_type' => $apptType,
                    'notes' => $notes,
                    'payment_status' => $paymentStatus
                ];

                if ($bookingId > 0) {
                    if ($bookingsModel->updateBooking($bookingId, $fields)) {
                        echo json_encode(['success' => true, 'message' => 'Ενημερώθηκε.']);
                    } else {
                        echo json_encode(['success' => false, 'errors' => ['Σφάλμα ενημέρωσης.']]);
                    }
                } else {
                    if ($bookingsModel->createBooking($fields)) {
                        echo json_encode(['success' => true, 'message' => 'Δημιουργήθηκε.']);
                    } else {
                        echo json_encode(['success' => false, 'errors' => ['Σφάλμα δημιουργίας.']]);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $validation->errors()]);
            }
            exit;
            break;

        // --- 3. GET DETAILS (Για Edit) ---
        case 'getBookingDetails':
            $id = (int)Input::get('id');
            $model = new Bookings();
            $data = $model->getBookingDetails($id);

            if ($data) {
                // Format dates for HTML Inputs
                $data->start_iso = date('Y-m-d\TH:i', strtotime($data->start_datetime));
                $data->end_iso = date('Y-m-d\TH:i', strtotime($data->end_datetime));

                // Client Name for Select2
                $data->client_text = $data->c_fname . ' ' . $data->c_lname;

                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'errors' => ['Booking not found']]);
            }
            exit;
            break;

        // --- 4. DELETE ---
        case 'deleteBooking':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();
            $id = (int)Input::get('id');
            $model = new Bookings();

            if ($model->deleteBooking($id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'errors' => ['Error deleting']]);
            }
            exit;
            break;

        case 'getTherapistPackages':
            $tid = (int)Input::get('therapist_id');
            $db = Database::getInstance();

            $sql = "SELECT 
                    p.id, p.title, p.duration_minutes, p.price,
                    p.is_group, p.max_attendants, p.start_datetime,
                    -- ΠΡΟΣΟΧΗ ΕΔΩ: Προσθέτουμε τα manual_bookings στο Count των bookings
                    ((SELECT COUNT(*) FROM bookings b WHERE b.package_id = p.id AND b.status != 'canceled') + p.manual_bookings) as current_bookings
                FROM packages p
                JOIN package_therapists pt ON p.id = pt.package_id
                WHERE pt.therapist_id = :tid
                ORDER BY p.is_group DESC, p.title ASC";

            $res = $db->query($sql, [':tid' => $tid]);

            echo json_encode(['success' => true, 'data' => $res ?: []]);
            exit;
            break;

        // --- 2. GET AVAILABLE SLOTS (NEW LOGIC USING AVAILABILITY CLASS) ---
        case 'getAvailableSlots':

    
            $tid = (int)Input::get('therapist_id');
            $pid = (int)Input::get('package_id');
            $date = Input::get('date'); // YYYY-MM-DD
            $duration = (int)Input::get('duration'); // Minutes

            if (!$tid || !$date || !$duration) {
                echo json_encode(['success' => false, 'error' => 'Missing data']);
                exit;
            }

            $db = Database::getInstance();

            // 1. Fetch Package Info for Buffer & Type Rules
            $buffer = 0;
            $pkgType = 'inPerson'; // Default fallback

           


            if ($pid) {
                $p = $db->query("SELECT buffer_minutes, type FROM packages WHERE id = ?", [$pid]);
                if ($p && count($p) > 0) {
                    $buffer = (int)$p[0]->buffer_minutes;
                    $pkgType = $p[0]->type; // 'inPerson', 'online', 'mixed'
                }
            }

            // 2. Determine Allowed Rule Types
            // Χρησιμοποιούμε τον helper της κλάσης Availability
            // Αν το πακέτο είναι 'inPerson', ψάχνουμε rules 'inPerson' ή 'mixed'
            $allowedRules = Availability::allowedRuleTypesForPackageType($pkgType);

            // 3. Compute Slots using the Engine
            $avail = new Availability();

            $step = ($pid && $duration > 0) ? $duration : 30;

            

            // Εδώ γίνεται η "μαγεία": Ο αλγόριθμος ελέγχει Rules, Blocks, Capacity και Package Linking
            $results = $avail->computeAvailableStartTimesForTherapist(
                $tid,
                $date,
                $duration,
                $allowedRules,
                $step, // Step (π.χ. ανά 30 λεπτά) - Μπορείς να το κάνεις δυναμικό αν θες
                $buffer,
                $pid // Περνάμε το ID για να φιλτράρει τα Specific Rules
            );

            // 4. Format for Frontend
            $slots = [];
            foreach ($results as $r) {
                // To JS περιμένει array από objects ή strings
                // Στέλνουμε object για να δείξουμε και τα διαθέσιμα spots
                $slots[] = [
                    'start' => $r['start_datetime'], // YYYY-MM-DD HH:mm:ss
                    'available_spots' => isset($r['available_spots']) ? $r['available_spots'] : 1
                ];
            }

            echo json_encode(['success' => true, 'slots' => $slots]);
            exit;
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

        case 'searchClientsSelect2':
            // Εδώ δεν χρειάζεται απαραίτητα admin permission αν το κάνουν και οι απλοί χρήστες, 
            // αλλά ας το αφήσουμε admin based στο permission system σου.
            if (!$mainUser->hasPermission(array('admin'))) permissionDenied();

            $term = isset($_POST['q']) ? trim($_POST['q']) : '';
            $clientsModel = new Clients();

            $results = $clientsModel->searchClients($term);

            $json = [];
            foreach ($results as $r) {
                // Format που αρέσει στο Select2
                $text = $r->first_name . ' ' . $r->last_name;
                if ($r->phone) $text .= ' (' . $r->phone . ')';

                $json[] = ['id' => $r->id, 'text' => $text];
            }

            header('Content-Type: application/json');
            echo json_encode(['results' => $json]);
            exit;
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
           CASE: ADD PACKAGE (NEW RUNNING ROUTE LOGIC)
           ------------------------------------------------------------------------- */
        case 'addPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'title'            => ['dname' => 'Τίτλος', 'required' => true, 'min' => 2, 'max' => 150],
                'category_id'      => ['dname' => 'Κατηγορία', 'required' => true, 'numeric' => true], // Changed from category string to ID
                'distance_km'      => ['dname' => 'Απόσταση (km)', 'required' => true, 'numeric' => true],
                'difficulty'       => ['dname' => 'Δυσκολία', 'in' => ['Easy', 'Moderate', 'Hard', 'Elite']],
                'terrain_type'     => ['dname' => 'Τερέν', 'in' => ['Road', 'Trail', 'Mixed', 'Track']],
                'elevation_gain'   => ['dname' => 'Υψομετρικά', 'numeric' => true],
                'price'            => ['dname' => 'Τιμή', 'numeric' => true, 'nullable' => true],
                'duration_minutes' => ['dname' => 'Διάρκεια', 'required' => true, 'numeric' => true],
                'max_attendants'   => ['dname' => 'Συμμετέχοντες', 'numeric' => true, 'min' => 1]
            ]);

            if ($validation->passed()) {
                $packages = new Packages();

                // 1. Standard Fields
                $title = Input::validateString(Input::get('title'));
                $description = Input::validateString(Input::get('description'));
                $price = Input::get('price') ? (float)Input::get('price') : null;
                $category_id = (int)Input::get('category_id');
                $duration = (int)Input::get('duration_minutes');

                // 2. Running Specific Fields
                $distance_km = (float)Input::get('distance_km');
                $elevation_gain = (int)Input::get('elevation_gain');
                $difficulty = Input::validateString(Input::get('difficulty'));
                $terrain_type = Input::validateString(Input::get('terrain_type'));
                $meeting_point_url = Input::validateString(Input::get('meeting_point_url'));

                // Force Type to 'inPerson' for Running context
                $type = 'inPerson';

                // 3. JSON Includes
                $includesRaw = Input::get('includes');
                $includesArr = json_decode($includesRaw, true);
                if (!is_array($includesArr)) $includesArr = [];
                $includesJson = json_encode($includesArr, JSON_UNESCAPED_UNICODE);

                $gearMandatoryRaw = Input::get('gear_mandatory'); // JSON string από JS
                $gearOptionalRaw  = Input::get('gear_optional');  // JSON string από JS

                $gearMandatoryArr = json_decode($gearMandatoryRaw, true);
                $gearOptionalArr  = json_decode($gearOptionalRaw, true);

                if (!is_array($gearMandatoryArr)) $gearMandatoryArr = [];
                if (!is_array($gearOptionalArr)) $gearOptionalArr = [];

                // Αποθήκευση σε ενιαίο JSON αντικείμενο
                $gearJson = json_encode([
                    'mandatory' => $gearMandatoryArr,
                    'optional'  => $gearOptionalArr
                ], JSON_UNESCAPED_UNICODE);

                // 4. Group Logic
                $is_group = ((int)Input::get('is_group') === 1) ? 1 : 0;
                $max_attendants = (int)Input::get('max_attendants');
                $manual_bookings = (int)Input::get('manual_bookings');

                $start_datetime = Input::get('start_datetime');
                if (empty($start_datetime)) $start_datetime = null;

                if ($is_group === 0) {
                    if ($max_attendants < 1) $max_attendants = 1;

                    $manual_bookings = 0;
                    $start_datetime = null;
                } else {
                    if ($max_attendants < 1) $max_attendants = 1;
                }

                // 5. Prepare Fields
                $fields = [
                    'title'            => $title,
                    'category_id'      => $category_id, // New FK
                    'description'      => $description,
                    'distance_km'      => $distance_km,
                    'elevation_gain'   => $elevation_gain,
                    'difficulty'       => $difficulty,
                    'terrain_type'     => $terrain_type,
                    'meeting_point_url' => $meeting_point_url,
                    'price'            => $price,
                    'duration_minutes' => $duration,
                    'buffer_minutes'   => (int)Input::get('buffer_minutes'),
                    'includes'         => $includesJson,
                    'gear_requirements'         => $gearJson,
                    'type'             => $type,
                    'is_group'         => $is_group,
                    'start_datetime'   => $start_datetime,
                    'max_attendants'   => $max_attendants,
                    'manual_bookings'  => $manual_bookings
                ];

                // 6. Therapists (Guides) Handling
                $therapists = [];
                if (isset($_POST['therapists'])) {
                    $rawTherapists = $_POST['therapists'];
                    if (is_array($rawTherapists)) {
                        $therapists = $rawTherapists;
                    } elseif (is_string($rawTherapists)) {
                        if (strpos($rawTherapists, ',') !== false) {
                            $therapists = explode(',', $rawTherapists);
                        } elseif (is_numeric($rawTherapists)) {
                            $therapists = [$rawTherapists];
                        }
                    }
                }

                if ($packages->addPackage($fields)) {
                    $newId = $packages->lastInsertedID();
                    $packages->syncTherapists($newId, $therapists);
                    $response['success'] = true;
                    $response['message'] = "Η διαδρομή δημιουργήθηκε επιτυχώς.";
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
           CASE: UPDATE PACKAGE (NEW RUNNING ROUTE LOGIC)
           ------------------------------------------------------------------------- */
        case 'updPackage':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'packageID'        => ['required' => true, 'numeric' => true],
                'title'            => ['required' => true, 'min' => 2, 'max' => 150],
                'category_id'      => ['required' => true, 'numeric' => true],
                'distance_km'      => ['required' => true, 'numeric' => true],
                'difficulty'       => ['in' => ['Easy', 'Moderate', 'Hard', 'Elite']],
                'terrain_type'     => ['in' => ['Road', 'Trail', 'Mixed', 'Track']],
                'duration_minutes' => ['required' => true, 'numeric' => true],
                'max_attendants'   => ['numeric' => true, 'min' => 1]
            ]);

            if ($validation->passed()) {
                $packages = new Packages();

                if (!Input::validateInt(Input::get('packageID'))) {
                    echo json_encode(['success' => false, 'errors' => ['Invalid ID']]);
                    exit;
                }
                $id = (int)Input::get('packageID');

                // 1. Inputs - Dates & JSONs
                $start_datetime = Input::get('start_datetime');
                if (empty($start_datetime)) $start_datetime = null;

                $includesRaw = Input::get('includes');
                $includesArr = json_decode($includesRaw, true);
                if (!is_array($includesArr)) $includesArr = [];

                // Gear Handling
                $gearMandatoryRaw = Input::get('gear_mandatory');
                $gearOptionalRaw  = Input::get('gear_optional');

                $gearMandatoryArr = json_decode($gearMandatoryRaw, true);
                $gearOptionalArr  = json_decode($gearOptionalRaw, true);

                if (!is_array($gearMandatoryArr)) $gearMandatoryArr = [];
                if (!is_array($gearOptionalArr)) $gearOptionalArr = [];

                $gearJson = json_encode([
                    'mandatory' => $gearMandatoryArr,
                    'optional'  => $gearOptionalArr
                ], JSON_UNESCAPED_UNICODE);

                // 2. LOGIC: Data Hygiene (Recurring vs Event)
                // Διαβάζουμε τις τιμές...
                $is_group = ((int)Input::get('is_group') === 1) ? 1 : 0;
                $max_attendants = (int)Input::get('max_attendants');
                $manual_bookings = (int)Input::get('manual_bookings');

                // ...και τις διορθώνουμε πριν την αποθήκευση
                if ($is_group === 0) {
                    // Recurring Run: Καθαρίζουμε τα "σκουπίδια" του Event
                    $start_datetime = null;
                    $manual_bookings = 0;

                    // Κρατάμε το όριο ατόμων ανά slot (π.χ. 5 άτομα)
                    if ($max_attendants < 1) $max_attendants = 1;
                } else {
                    // Event: Κρατάμε τα δεδομένα ως έχουν
                    if ($max_attendants < 1) $max_attendants = 1;
                }

                // 3. Prepare Fields (Χρησιμοποιούμε τις μεταβλητές, ΟΧΙ το Input::get)
                $fields = [
                    'title'            => Input::validateString(Input::get('title')),
                    'category_id'      => (int)Input::get('category_id'),
                    'description'      => Input::validateString(Input::get('description')),
                    'distance_km'      => (float)Input::get('distance_km'),
                    'elevation_gain'   => (int)Input::get('elevation_gain'),
                    'difficulty'       => Input::validateString(Input::get('difficulty')),
                    'terrain_type'     => Input::validateString(Input::get('terrain_type')),
                    'meeting_point_url' => Input::validateString(Input::get('meeting_point_url')),
                    'price'            => Input::get('price') ? (float)Input::get('price') : null,
                    'duration_minutes' => (int)Input::get('duration_minutes'),
                    'buffer_minutes'   => (int)Input::get('buffer_minutes'),
                    'includes'         => json_encode($includesArr, JSON_UNESCAPED_UNICODE),
                    'gear_requirements' => $gearJson, // New

                    // Εδώ μπαίνουν οι "καθαρές" μεταβλητές από το βήμα 2
                    'is_group'         => $is_group,
                    'start_datetime'   => $start_datetime,
                    'max_attendants'   => $max_attendants,
                    'manual_bookings'  => $manual_bookings
                ];

                $packages->updatePackage($fields, $id);

                // Update Guides
                $therapists = [];
                if (isset($_POST['therapists'])) {
                    $rawT = $_POST['therapists'];
                    if (is_array($rawT)) {
                        $therapists = $rawT;
                    } elseif (is_string($rawT)) {
                        if (strpos($rawT, ',') !== false) {
                            $therapists = explode(',', $rawT);
                        } else {
                            $therapists = [$rawT];
                        }
                    }
                }
                $packages->syncTherapists($id, $therapists);

                $response['success'] = true;
                $response['message'] = "Η διαδρομή ενημερώθηκε επιτυχώς.";
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
                echo json_encode(['success' => false, 'errors' => ['Μη έγκυρο ID πακέτου.']]);
                exit;
            }

            $id = (int)Input::get('id');
            $packages = new Packages();
            $pkg = $packages->fetchPackage($id);

            if ($pkg) {
                // 1. Therapists (Υπάρχον)
                $pkg->therapists = $packages->getTherapistIds($id);

                // 2. --- ΝΕΟ: Count Real Bookings ---
                // Μετράμε πόσες εγγραφές υπάρχουν στον πίνακα bookings για αυτό το package_id
                $db = Database::getInstance();
                $countSql = "SELECT COUNT(*) as c FROM bookings WHERE package_id = ? AND status != 'canceled'";
                $res = $db->query($countSql, [$id]);

                // Το προσθέτουμε στο αντικείμενο $pkg που θα επιστραφεί
                $pkg->db_bookings_count = ($res && count($res) > 0) ? (int)$res[0]->c : 0;
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


        // --- FETCH GROUP ATTENDEES ---
        // --- FETCH ATTENDEES (UPDATED: Supports specific slot date) ---
        case 'fetchGroupAttendees':
            $pid = (int)Input::get('package_id');
            $date = Input::get('date'); // YYYY-MM-DD HH:mm:ss (Optional)

            if (!$pid) {
                echo json_encode(['success' => false, 'error' => 'Missing Package ID']);
                exit;
            }

            $db = Database::getInstance();

            // SQL: Αν έχουμε ημερομηνία, φιλτράρουμε ΚΑΙ με αυτή
            $sql = "SELECT 
                    b.id AS booking_id,
                    b.status,
                    b.payment_status,
                    b.start_datetime as run_date, 
                    c.first_name, c.last_name, c.phone, c.email, b.attendees_count
                FROM bookings b
                JOIN clients c ON b.client_id = c.id
                WHERE b.package_id = :pid 
                AND b.status != 'canceled'";

            $params = [':pid' => $pid];

            if ($date) {
                $sql .= " AND b.start_datetime = :dt";
                $params[':dt'] = $date;
            }

            $sql .= " ORDER BY b.start_datetime DESC LIMIT 50";

            $rows = $db->query($sql, $params);

            // Manual Bookings (Counts)
            // Αν είναι recurring slot (έχουμε date), δεν κοιτάμε το γενικό 'manual_bookings' του πακέτου 
            // γιατί αυτό αφορά το Event. Εδώ μας νοιάζει μόνο αν υπάρχουν manual bookings για τη μέρα (που δεν έχουμε ακόμα πίνακα για αυτό).
            // Άρα το manual_count το αφήνουμε μόνο αν ΔΕΝ έχουμε date (δηλαδή είναι Group Event).
            $manualCount = 0;
            $isGroup = 0;

            // Φέρνουμε πληροφορίες πακέτου
            $pkgSql = "SELECT manual_bookings, is_group FROM packages WHERE id = :pid";
            $pkgRes = $db->query($pkgSql, [':pid' => $pid]);

            if ($pkgRes && count($pkgRes) > 0) {
                $isGroup = (int)$pkgRes[0]->is_group;
                // Δείχνουμε τα manual του πακέτου ΜΟΝΟ αν είναι Group Event (χωρίς συγκεκριμένη ημερομηνία slot)
                if (!$date && $isGroup == 1) {
                    $manualCount = (int)$pkgRes[0]->manual_bookings;
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $rows ?: [],
                'manual_count' => $manualCount,
                'is_group' => $isGroup
            ]);
            exit;
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

        // --- 1. Get Payments for Booking ---
        case 'getBookingPayments':
            $bid = (int)Input::get('booking_id');
            if (!$bid) {
                echo json_encode([]);
                exit;
            }

            $payModel = new Payments();
            $rows = $payModel->getPaymentsByBooking($bid);

            echo json_encode(['success' => true, 'data' => $rows]);
            exit;
            break;

        /* -------------------------------------------------------------------------
           CASE: ADD MANUAL PAYMENT (Manual Status Update from Frontend)
           ------------------------------------------------------------------------- */
        case 'addManualPayment':
            $bid = (int)Input::get('booking_id');
            $clientId = (int)Input::get('client_id');
            $amount = (float)Input::get('amount');
            $method = Input::get('method');
            $dateInput = Input::get('date');
            $payedAt = $dateInput ? ($dateInput . ' ' . date('H:i:s')) : date('Y-m-d H:i:s');
            $note = Input::get('note');

            // ΝΕΟ: Παίρνουμε το status που διάλεξε ο χρήστης
            $manualStatus = Input::get('manual_status_update'); // paid, partially_paid, unpaid

            if (!$bid || !$amount) {
                echo json_encode(['success' => false, 'error' => 'Λείπουν στοιχεία']);
                exit;
            }


            $db = Database::getInstance();
            $check = $db->query("SELECT id FROM payments WHERE reservation_id = :bid", [':bid' => $bid]);

            if ($check && count($check) > 0) {
                echo json_encode(['success' => false, 'error' => 'Υπάρχει ήδη καταχωρημένη πληρωμή. Παρακαλώ κάντε επεξεργασία της υπάρχουσας.']);
                exit;
            }

            $fields = [
                'reservation_id' => $bid,
                'client_id'      => $clientId,
                'amount_paid'    => $amount,
                'amount_total'   => $amount,
                'payment_method' => $method,
                'status'         => 'Completed',
                'payed_at'       => $payedAt,
                'note'           => $note,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $payModel = new Payments();

            if ($payModel->addPayment($fields)) {

                // --- MANUAL UPDATE BOOKING STATUS ---
                // Εδώ δεν κάνουμε υπολογισμούς. Απλά εμπιστευόμαστε το frontend.
                if ($manualStatus) {
                    $bkModel = new Bookings();
                    $bkModel->updateBooking($bid, ['payment_status' => $manualStatus]);

                    echo json_encode(['success' => true, 'new_status' => $manualStatus]);
                } else {
                    echo json_encode(['success' => true]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Database Error']);
            }
            exit;
            break;

        /* -------------------------------------------------------------------------
           CASE: UPDATE MANUAL PAYMENT
           ------------------------------------------------------------------------- */
        case 'updateManualPayment':
            $pid = (int)Input::get('payment_id');
            $bid = (int)Input::get('booking_id');
            $amount = (float)Input::get('amount');
            $method = Input::get('method');
            $dateInput = Input::get('date');

            $payedAt = $dateInput ? ($dateInput . ' ' . date('H:i:s')) : date('Y-m-d H:i:s');
            $note = Input::get('note');
            $manualStatus = Input::get('manual_status_update');

            if (!$pid || !$amount) {
                echo json_encode(['success' => false, 'error' => 'Λείπουν στοιχεία']);
                exit;
            }

            // Ενημερώνουμε την εγγραφή
            $fields = [
                'amount_paid'    => $amount,
                'amount_total'   => $amount,
                'payment_method' => $method,
                'payed_at'       => $payedAt,
                'note'           => $note,
                // 'updated_at'  => date('Y-m-d H:i:s') // Αν έχεις τέτοιο πεδίο
            ];

            $payModel = new Payments();

            // Προσοχή: Χρειάζεσαι updatePayment μέθοδο στο Class Payments
            if ($payModel->updatePayment($fields, $pid)) {

                // Manual Booking Status Update (ίδιο με το add)
                if ($manualStatus) {
                    $bkModel = new Bookings();
                    $bkModel->updateBooking($bid, ['payment_status' => $manualStatus]);
                    exit(json_encode(['success' => true, 'new_status' => $manualStatus]));
                } else {
                     exit(json_encode(['success' => true]));
                }
            } else {
                exit(json_encode(['success' => false, 'error' => 'Database Update Error']));
            }
            exit;
            break;

        /* -------------------------------------------------------------------------
           CASE: DELETE PAYMENT (Safe Auto-Recalculate without Price)
           ------------------------------------------------------------------------- */
        case 'delPayment':
            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $validation = new Validate();
            if ($validation->check($_POST, ['paymentID' => ['required' => true]])->passed()) {

                $payments = new Payments();
                $paymentID = (int)Input::get('paymentID');
                $targetPayment = $payments->fetchPayment($paymentID);

                if ($targetPayment && $payments->deletePayment($paymentID)) {

                    // --- SAFE STATUS UPDATE ---
                    // Εδώ κάνουμε μια απλή λογική για να μην μείνει "Paid" αν σβήσουμε τα λεφτά.
                    // Αν υπάρχουν υπόλοιπα λεφτά -> Partially Paid.
                    // Αν είναι 0 -> Unpaid.
                    // ΔΕΝ ΧΡΗΣΙΜΟΠΟΙΟΥΜΕ ΤΙΜΗ (Price).

                    $bid = $targetPayment->reservation_id;
                    $allPayments = $payments->getPaymentsByBooking($bid);

                    $totalPaid = 0;
                    foreach ($allPayments as $p) {
                        $val = isset($p->amount_paid) ? $p->amount_paid : $p->amount_total;
                        $totalPaid += (float)$val;
                    }

                    $newStatus = ($totalPaid > 0) ? 'partially_paid' : 'unpaid';

                    $bkModel = new Bookings();
                    $bkModel->updateBooking($bid, ['payment_status' => $newStatus]);

                    echo json_encode(['success' => true, 'new_status' => $newStatus]);
                } else {
                    echo json_encode(['success' => false, 'errors' => ['Error deleting']]);
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $validation->errors()]);
            }
            exit;
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


        case 'fetchAvailableHoursV2':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $dateStr = Input::get('date');        // "YYYY-MM-DD"
            $packageId = (int)Input::get('package_id');
            $therapistId = Input::get('therapist_id') ? (int)Input::get('therapist_id') : null;

            if (!$dateStr || !$packageId) {
                $response['errors'][] = 'Λείπει η ημερομηνία ή το πακέτο.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $packagesModel = new Packages();
                $package = $packagesModel->fetchPackage($packageId);
                if (!$package) {
                    throw new Exception('Το πακέτο δεν βρέθηκε.');
                }

                $duration = (int)$package->duration_minutes;
                if ($duration <= 0) $duration = 60;

                $allowedRuleTypes = Availability::allowedRuleTypesForPackageType((string)$package->type);

                $availability = new Availability();

                $stepMinutes = 30;
                $bufferMinutes = 0; // βάλε π.χ. 10 αν θες buffers

                if ($therapistId) {
                    $times = $availability->computeAvailableStartTimesForTherapist(
                        $therapistId,
                        $dateStr,
                        $duration,
                        $allowedRuleTypes,
                        $stepMinutes,
                        $bufferMinutes
                    );

                    $response['success'] = true;
                    $response['data'] = [
                        'therapist_id' => $therapistId,
                        'slots' => $times
                    ];
                } else {
                    $therapistIds = $availability->getTherapistsForPackage($packageId);

                    $out = [];
                    foreach ($therapistIds as $tid) {
                        $times = $availability->computeAvailableStartTimesForTherapist(
                            $tid,
                            $dateStr,
                            $duration,
                            $allowedRuleTypes,
                            $stepMinutes,
                            $bufferMinutes
                        );
                        if (!empty($times)) {
                            $out[] = [
                                'therapist_id' => $tid,
                                'slots' => $times
                            ];
                        }
                    }

                    $response['success'] = true;
                    $response['data'] = $out;
                }
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'availabilityRules_get':
            $response = ['success' => false, 'data' => [], 'policies' => [], 'errors' => []];

            $therapistId = (int)Input::get('therapist_id');
            if (!$therapistId) {
                echo json_encode(['success' => false, 'errors' => ['Missing therapist_id']]);
                exit;
            }

            try {
                $db = Database::getInstance();

                // 1. Fetch Rules (Added package_id)
                // Κάνουμε και JOIN με packages για να πάρουμε το όνομα του πακέτου στο Frontend
                $sql = "SELECT r.id, r.weekday, r.start_time, r.end_time, r.appointment_type, r.package_id, r.is_active,
                               p.title as package_title
                        FROM therapist_availability_rules r
                        LEFT JOIN packages p ON r.package_id = p.id
                        WHERE r.therapist_id = :tid
                        ORDER BY r.weekday ASC, r.start_time ASC";

                $rows = $db->query($sql, [':tid' => $therapistId]);
                $response['data'] = $rows ?: [];

                // 2. Fetch Policies
                $policySql = "SELECT booking_window_days, min_notice_hours FROM therapists WHERE id = :tid";
                $policyRow = $db->query($policySql, [':tid' => $therapistId]);
                $response['policies'] = ($policyRow && count($policyRow) > 0)
                    ? $policyRow[0]
                    : ['booking_window_days' => 60, 'min_notice_hours' => 12];

                // 3. Fetch Active Packages List (For the Dropdown in JS)
                $pkgSql = "SELECT id, title FROM packages ORDER BY title ASC";
                $packages = $db->query($pkgSql);
                $response['packages_list'] = $packages ?: [];

                $response['success'] = true;
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'saveTherapistPolicies':
            $response = ['success' => false, 'errors' => []];

            if (!$mainUser->hasPermission(['admin'])) permissionDenied();

            $therapistId = (int)Input::get('therapist_id');
            $window = (int)Input::get('booking_window_days');
            $notice = (int)Input::get('min_notice_hours');

            if (!$therapistId) {
                $response['errors'][] = 'Invalid ID.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Basic validation
            if ($window < 1) $window = 30; // Minimum 30 days window
            if ($notice < 0) $notice = 0;  // No negative notice

            try {
                $db = Database::getInstance();
                $db->query(
                    "UPDATE therapists SET booking_window_days = ?, min_notice_hours = ? WHERE id = ?",
                    [$window, $notice, $therapistId]
                );

                $response['success'] = true;
                $response['message'] = 'Οι πολιτικές ενημερώθηκαν.';
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'availabilityRules_saveBulk':
            $response = ['success' => false, 'errors' => []];

            $therapistId = (int)Input::get('therapist_id');
            $rulesJson = Input::get('rules_json');

            if (!$therapistId) {
                echo json_encode(['success' => false, 'errors' => ['Missing ID']]);
                exit;
            }

            $rules = json_decode($rulesJson, true);
            if (!is_array($rules)) {
                echo json_encode(['success' => false, 'errors' => ['Invalid JSON']]);
                exit;
            }

            try {
                $db = Database::getInstance();

                // 1) Delete old rules
                $db->query("DELETE FROM therapist_availability_rules WHERE therapist_id = :tid", [':tid' => $therapistId]);

                // 2) Insert new rules (with package_id)
                $insSql = "INSERT INTO therapist_availability_rules
                           (therapist_id, weekday, start_time, end_time, appointment_type, package_id, is_active)
                           VALUES (:tid, :wd, :st, :en, :at, :pid, :ia)";

                foreach ($rules as $r) {
                    // Αν το package_id είναι 0 ή κενό, το κάνουμε NULL (General Availability)
                    $pid = (isset($r['package_id']) && (int)$r['package_id'] > 0) ? (int)$r['package_id'] : null;

                    $db->query($insSql, [
                        ':tid' => $therapistId,
                        ':wd'  => (int)$r['weekday'],
                        ':st'  => $r['start_time'],
                        ':en'  => $r['end_time'],
                        ':at'  => $r['appointment_type'] ?? null,
                        ':pid' => $pid, // <--- NEW FIELD
                        ':ia'  => isset($r['is_active']) ? (int)$r['is_active'] : 1,
                    ]);
                }

                $response['success'] = true;
                $response['message'] = 'Το πρόγραμμα αποθηκεύτηκε.';
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'timeBlocks_add':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $therapistId = (int)Input::get('therapist_id');
            $start = Input::get('start_datetime');
            $end   = Input::get('end_datetime');
            $notes = Input::get('notes');
            $kind  = Input::get('kind') ?: 'block';

            if (!$therapistId || !$start || !$end) {
                $response['errors'][] = 'Λείπει therapist_id/start_datetime/end_datetime.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            if (!in_array($kind, ['block', 'extra_open'], true)) {
                $response['errors'][] = 'Invalid kind.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }
            if (strtotime($end) <= strtotime($start)) {
                $response['errors'][] = 'Το end πρέπει να είναι μετά το start.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $db = Database::getInstance();
                $db->query(
                    "INSERT INTO therapist_time_blocks (therapist_id, start_datetime, end_datetime, kind, notes)
             VALUES (:tid, :st, :en, :kind, :notes)",
                    [
                        ':tid' => $therapistId,
                        ':st' => date('Y-m-d H:i:s', strtotime($start)),
                        ':en' => date('Y-m-d H:i:s', strtotime($end)),
                        ':kind' => $kind,
                        ':notes' => $notes
                    ]
                );

                $response['success'] = true;
                $response['message'] = 'Block saved.';
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'timeBlocks_delete':
            $response = ['success' => false, 'errors' => []];

            $id = (int)Input::get('id');
            if (!$id) {
                $response['errors'][] = 'Λείπει id.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $db = Database::getInstance();
                $db->query("DELETE FROM therapist_time_blocks WHERE id = :id", [':id' => $id]);
                $response['success'] = true;
                $response['message'] = 'Block deleted.';
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        case 'calendar_getDataV2':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $therapistFilter = isset($_POST['therapist_id']) && $_POST['therapist_id'] !== '' ? (int)$_POST['therapist_id'] : null;
            $rangeStart = isset($_POST['start']) ? date('Y-m-d H:i:s', strtotime($_POST['start'])) : null;
            $rangeEnd   = isset($_POST['end'])   ? date('Y-m-d H:i:s', strtotime($_POST['end']))   : null;

            if (!$rangeStart || !$rangeEnd) {
                echo json_encode(['success' => false, 'errors' => ['Missing start/end']]);
                exit;
            }

            try {
                $db = Database::getInstance();

                // -------------------------------------------------
                // STEP 1: PRE-CALCULATE BOOKINGS (COUNTS)
                // -------------------------------------------------
                // We use COALESCE(SUM(attendees_count), COUNT(id)) so if attendees_count is NULL (old data), it counts rows.
                $countSql = "SELECT 
                            therapist_id, 
                            start_datetime, 
                            COALESCE(SUM(attendees_count), COUNT(id)) as total_pax 
                         FROM bookings 
                         WHERE status <> 'canceled' 
                           AND start_datetime < :re 
                           AND end_datetime > :rs";

                $cParams = [':rs' => $rangeStart, ':re' => $rangeEnd];
                if ($therapistFilter) {
                    $countSql .= " AND therapist_id = :tid";
                    $cParams[':tid'] = $therapistFilter;
                }
                $countSql .= " GROUP BY therapist_id, start_datetime";

                $bookingCounts = [];
                $rows = $db->query($countSql, $cParams) ?: [];

                foreach ($rows as $r) {
                    // Normalize date key to remove potential seconds issues or formatting differences
                    // We convert DB date to timestamp and back to string to ensure consistency
                    $keyTime = strtotime($r->start_datetime);
                    $key = $r->therapist_id . '_' . $keyTime;
                    $bookingCounts[$key] = (int)$r->total_pax;
                }

                // -------------------------------------------------
                // STEP 2: GENERATE SESSION CARDS (From Rules)
                // -------------------------------------------------

                // Get Therapists List
                $targetTids = [];
                if ($therapistFilter) {
                    $targetTids[] = $therapistFilter;
                } else {
                    $trows = $db->query("SELECT id FROM therapists WHERE is_active = 1") ?: [];
                    foreach ($trows as $t) $targetTids[] = (int)$t->id;
                }

                if (!empty($targetTids)) {
                    $in = implode(',', $targetTids);

                    // Fetch Rules
                    $rulesSql = "SELECT r.therapist_id, r.weekday, r.start_time, r.end_time, 
                                    p.id as pkg_id, p.title as pkg_title, p.max_attendants, p.price
                             FROM therapist_availability_rules r
                             LEFT JOIN packages p ON r.package_id = p.id
                             WHERE r.is_active = 1 AND r.therapist_id IN ($in)";

                    $rules = $db->query($rulesSql) ?: [];
                    $ruleMap = [];
                    foreach ($rules as $r) {
                        $ruleMap[(int)$r->therapist_id][(int)$r->weekday][] = $r;
                    }

                    // Iterate Days
                    $dtStart = new DateTime($rangeStart);
                    $dtEnd   = new DateTime($rangeEnd);
                    $dtEnd->modify('+1 second');
                    $period = new DatePeriod($dtStart, new DateInterval('P1D'), $dtEnd);

                    foreach ($period as $dt) {
                        $dateStr = $dt->format('Y-m-d');
                        $wd = (int)$dt->format('w');

                        foreach ($targetTids as $tid) {
                            if (empty($ruleMap[$tid][$wd])) continue;

                            foreach ($ruleMap[$tid][$wd] as $rule) {
                                $startFull = $dateStr . ' ' . $rule->start_time; // e.g., 2023-10-10 09:00:00
                                $endFull   = $dateStr . ' ' . $rule->end_time;

                                // Generate Key for Count Lookup matches the logic in Step 1
                                $keyTime = strtotime($startFull);
                                $lookupKey = $tid . '_' . $keyTime;

                                $currentBooked = isset($bookingCounts[$lookupKey]) ? $bookingCounts[$lookupKey] : 0;

                                // Define Max Capacity (Default 15)
                                $maxCap = (int)($rule->max_attendants ?? 15);
                                $title = $rule->pkg_title ?: 'Available Slot';

                                $response['data'][] = [
                                    'id'               => 'sess_' . $tid . '_' . $keyTime,
                                    'start_datetime'   => $startFull,
                                    'end_datetime'     => $endFull,
                                    'title'            => $title,
                                    'source'           => 'session',
                                    'is_group'         => 0,
                                    'therapist_id'     => $tid,
                                    'package_id'       => (int)$rule->pkg_id,
                                    'current_bookings' => $currentBooked,
                                    'max_capacity'     => $maxCap,
                                    'price'            => $rule->price,
                                    'display'          => 'auto'
                                ];
                            }
                        }
                    }
                }

                // -------------------------------------------------
                // STEP 3: GROUP EVENTS (Specific Dates)
                // -------------------------------------------------
                $grpSql = "SELECT 
                        p.id, p.title, p.start_datetime, p.duration_minutes, 
                        p.max_attendants, p.manual_bookings,
                        pt.therapist_id
                       FROM packages p
                       LEFT JOIN package_therapists pt ON pt.package_id = p.id
                       WHERE p.is_group = 1 
                         AND p.start_datetime < :re 
                         AND DATE_ADD(p.start_datetime, INTERVAL p.duration_minutes MINUTE) > :rs";

                $gParams = [':rs' => $rangeStart, ':re' => $rangeEnd];
                if ($therapistFilter) {
                    $grpSql .= " AND pt.therapist_id = :tid";
                    $gParams[':tid'] = $therapistFilter;
                }

                $groups = $db->query($grpSql, $gParams) ?: [];
                foreach ($groups as $g) {
                    $endDT = date('Y-m-d H:i:s', strtotime($g->start_datetime . " +{$g->duration_minutes} minutes"));

                    // Lookup Count
                    $tid = (int)$g->therapist_id;
                    $keyTime = strtotime($g->start_datetime);
                    $lookupKey = $tid . '_' . $keyTime;

                    $dbCount = isset($bookingCounts[$lookupKey]) ? $bookingCounts[$lookupKey] : 0;
                    $totalBooked = $dbCount + (int)$g->manual_bookings;

                    $response['data'][] = [
                        'id'               => 'grp_' . $g->id,
                        'start_datetime'   => $g->start_datetime,
                        'end_datetime'     => $endDT,
                        'title'            => $g->title,
                        'source'           => 'group_event',
                        'is_group'         => 1,
                        'therapist_id'     => $tid,
                        'current_bookings' => $totalBooked,
                        'max_capacity'     => (int)$g->max_attendants
                    ];
                }

                // -------------------------------------------------
                // STEP 4: BLOCKS
                // -------------------------------------------------
                $blockSql = "SELECT id, therapist_id, start_datetime, end_datetime, notes 
                         FROM therapist_time_blocks 
                         WHERE kind = 'block' AND start_datetime < :re AND end_datetime > :rs";
                if ($therapistFilter) {
                    $blockSql .= " AND therapist_id = " . (int)$therapistFilter;
                }
                $blocks = $db->query($blockSql, [':rs' => $rangeStart, ':re' => $rangeEnd]) ?: [];
                foreach ($blocks as $bl) {
                    $response['data'][] = [
                        'id' => 'bl_' . $bl->id,
                        'start_datetime' => $bl->start_datetime,
                        'end_datetime' => $bl->end_datetime,
                        'title' => 'Blocked',
                        'notes' => $bl->notes,
                        'source' => 'block',
                        'therapist_id' => (int)$bl->therapist_id
                    ];
                }

                $response['success'] = true;
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;


        case 'getSessionDetails':
            $response = ['success' => false, 'attendees' => [], 'manual_count' => 0];
            $sessionId = $_POST['session_id'] ?? '';

            try {
                $db = Database::getInstance();
                $attendees = [];
                $manualCount = 0;

                // 1. GROUP EVENT (ID starts with "grp_")
                if (strpos($sessionId, 'grp_') === 0) {
                    $parts = explode('_', $sessionId);
                    // Expected format: grp_{PackageID} or grp_{PackageID}_{TherapistID}
                    // We just need the PackageID which is usually index 1
                    $pkgId = (int)$parts[1];

                    // Fetch Attendees
                    $sql = "SELECT b.id as booking_id, b.status, b.payment_status, b.attendees_count,
                               c.first_name, c.last_name, c.phone,
                               b.notes
                        FROM bookings b
                        LEFT JOIN clients c ON c.id = b.client_id
                        WHERE b.package_id = :pid AND b.status != 'canceled'";

                    $rows = $db->query($sql, [':pid' => $pkgId]);
                    if ($rows) $attendees = $rows;

                    // Fetch Manual Count
                    $pkgRow = $db->query("SELECT manual_bookings FROM packages WHERE id = :pid", [':pid' => $pkgId]);
                    if ($pkgRow && count($pkgRow) > 0) {
                        $manualCount = (int)$pkgRow[0]->manual_bookings;
                    }
                }

                // 2. REGULAR SESSION (ID starts with "sess_")
                elseif (strpos($sessionId, 'sess_') === 0) {
                    $parts = explode('_', $sessionId);
                    // Expected format: sess_{TherapistID}_{Timestamp}
                    if (count($parts) >= 3) {
                        $tid = (int)$parts[1];
                        $timestamp = (int)$parts[2];
                        $dateTimeStr = date('Y-m-d H:i:s', $timestamp);

                        $sql = "SELECT b.id as booking_id, b.status, b.payment_status, b.attendees_count,
                                   c.first_name, c.last_name, c.phone,
                                   b.notes
                            FROM bookings b
                            LEFT JOIN clients c ON c.id = b.client_id
                            WHERE b.therapist_id = :tid 
                              AND b.start_datetime = :dt 
                              AND b.status != 'canceled'";

                        $rows = $db->query($sql, [':tid' => $tid, ':dt' => $dateTimeStr]);
                        if ($rows) $attendees = $rows;
                    }
                }

                // Client Name Fallback
                foreach ($attendees as &$att) {
                    $att->client_name = trim(($att->first_name ?? '') . ' ' . ($att->last_name ?? ''));
                    if (empty($att->client_name)) $att->client_name = '(Unknown Client)';
                }

                $response['success'] = true;
                $response['attendees'] = $attendees;
                $response['manual_count'] = $manualCount;
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;

        // --- FIND FIRST AVAILABLE DATE ---
        case 'findFirstAvailableDate':
            $tid = (int)Input::get('therapist_id');
            $pid = (int)Input::get('package_id');
            $dur = (int)Input::get('duration'); // σε λεπτά

            if (!$tid || !$pid) {
                echo json_encode(['success' => false]);
                exit;
            }

            $avail = new Availability();
            // Ψάχνουμε για τις επόμενες 90 μέρες
            $date = $avail->findFirstAvailableDate($tid, $pid, $dur ?: 60, 90);

            if ($date) {
                echo json_encode(['success' => true, 'date' => $date]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Δεν βρέθηκε διαθεσιμότητα']);
            }
            exit;
            break;


        case 'calendar_getMonthSummaryV2':
            $response = ['success' => false, 'data' => [], 'errors' => []];

            $startStr = isset($_POST['start']) ? $_POST['start'] : null;
            $endStr   = isset($_POST['end'])   ? $_POST['end']   : null;
            $tidFilter = isset($_POST['therapist_id']) && $_POST['therapist_id'] !== '' ? (int)$_POST['therapist_id'] : null;

            if (!$startStr || !$endStr) {
                $response['errors'][] = 'Missing dates';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            try {
                $db = Database::getInstance();

                // 1. Therapists
                $tids = [];
                if ($tidFilter) {
                    $tids[] = $tidFilter;
                } else {
                    $res = $db->query("SELECT id FROM therapists WHERE is_active = 1");
                    foreach ($res as $row) $tids[] = (int)$row->id;
                }

                if (empty($tids)) {
                    echo json_encode(['success' => true, 'data' => []]);
                    exit;
                }

                // 2. Rules (Ποιοι δουλεύουν γενικά)
                $inTids = implode(',', $tids);
                $rules = $db->query("SELECT therapist_id, weekday FROM therapist_availability_rules WHERE is_active=1 AND therapist_id IN ($inTids)") ?: [];

                $workingMap = [];
                foreach ($rules as $r) {
                    $workingMap[(int)$r->therapist_id][(int)$r->weekday] = true;
                }

                // 3. Build Days
                $dtStart = new DateTime($startStr);
                $dtEnd   = new DateTime($endStr);
                $dtEnd->modify('+1 second');

                $period = new DatePeriod($dtStart, new DateInterval('P1D'), $dtEnd);
                $summaryData = [];

                foreach ($period as $dt) {
                    $ymd = $dt->format('Y-m-d');
                    $wd  = (int)$dt->format('w');

                    $activeTherapists = [];
                    $status = 'none';

                    foreach ($tids as $tid) {
                        // Αν έχει βάρδια αυτή τη μέρα
                        if (isset($workingMap[$tid][$wd])) {
                            $activeTherapists[] = $tid;
                        }
                    }

                    if (count($activeTherapists) > 0) {
                        $status = 'available';
                    }

                    $summaryData[$ymd] = [
                        'status' => $status,
                        'therapist_ids' => $activeTherapists
                    ];
                }

                $response['success'] = true;
                $response['data'] = $summaryData;
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
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
