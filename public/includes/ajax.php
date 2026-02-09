<?php


require_once __DIR__ . '/../../src/core/init.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && Input::get('action', true) && Token::checkToken(Input::get('csrf_token'), 'csrf_token')) {
    $response = [];
    $response['errors'] = array("");
    $smtpCredentials = array(
        'smtp_host' => $mainSettings->smtpSettings->smtpHost,
        'smtp_port' => $mainSettings->smtpSettings->smtpPort,
        'sender' => $mainSettings->smtpSettings->fromMail,
        'smtp_user' => $mainSettings->smtpSettings->smtpUser,
        'smtp_password' => $mainSettings->smtpSettings->smtpPassword,
    );
    switch (Input::get('action')) {
        case 'register':

            //Disable to enable
            return false;

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
                    }

                    //userRole 1 = Admin
                    //userRole 2 = Editor
                    //userRole 3 = User
                    $userRole = 3;
                    // switch (Input::validateInt(Input::get('userRole'))) {
                    //     case 1:
                    //         $userRole = 1;
                    //         break;
                    //     case 2:
                    //         $userRole = 3;
                    //         break;
                    //     case 3:
                    //         $userRole = 3;
                    //         break;
                    //     default:
                    //         $userRole = 3;
                    //         break;
                    // }

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

        case 'login':
            $startTime = microtime(true);

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'email' => [
                    'dname' => 'Email',
                    'required' => true,
                    'isMail' => true,
                    'min' => 3,
                    'max' => 50,
                ],
                'password' => [
                    'dname' => 'Password',
                    'required' => true,
                    'min' => 6,
                ],
            ]);

            if ($validation->passed()) {
                $username = Input::validateEmail(Input::get('email', true));
                $password = Input::get('password');
                $user = new User();

                $userExists = $user->find($username);

                if ($userExists) {

                    if ($user->data()->twoFactorAuth) {
                        Session::delete('tmpUsername');
                        Session::delete('tmpPassword');
                        Session::delete('_2FA_KEY_');
                        Session::delete('_2FA_VALID_UNTIL_');
                        $loginResult = $user->login($username, $password, true);
                        if ($loginResult === true) {
                            $otp = Session::getSessVal('_2FA_KEY_');
                            $currentDateTime = new DateTime();
                            $currentDateTime->add(new DateInterval('PT2M'));
                            $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');
                            Session::put('_2FA_VALID_UNTIL_', $currentDateTime);
                            Session::put('_2FA_EMAIL_', $username);

                            if (sendMail($smtpCredentials, "Authentication Code", "Please use the OTP provided below to complete your secure login<br>Authentication Code: $otp<br> Valid Until: $formattedDateTime", $username)) {

                                Session::put('tmpUsername', $username);
                                Session::put('tmpPassword', $password);

                                $response['success'] = true;
                                $response['message'] = "Redirecting..";
                                $response['url'] = "2Auth";
                            } else {
                                $response['success'] = false;
                                $response['errors'] = array("Something while sending the otp code. Please try again");
                                $response['url'] = "login";
                            }
                        } else {
                            $response['success'] = false;
                            $response['errors'] = array("Wrong Email Or Password");
                        }
                    } else {
                        $loginResult = $user->login($username, $password);
                        if ($loginResult === true) {
                            $response['success'] = true;
                            $response['url'] = "profile";
                        } else {
                            $response['success'] = false;
                            $response['errors'] = array("Wrong Email Or Password");
                        }
                    }
                } else {
                    $response['success'] = false;
                    $response['errors'] = array("Wrong Email Or Password");
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
            }


            $desiredDelay = 200000; //ms
            while (true) {
                $currentTime = microtime(true);
                $elapsedTime = ($currentTime - $startTime) * 1000000; // Convert to microseconds
                if ($elapsedTime >= $desiredDelay) {
                    break;
                }
            }

            // $endTime = microtime(true);
            // $timeDifference = ($endTime - $startTime) * 1000; // Convert to milliseconds
            // $formattedTimeBetween = number_format($timeDifference, 2);
            // array_push($response['errors'], "Start Time: " . ($startTime * 1000) . " milliseconds");
            // array_push($response['errors'], "End Time: " . ($endTime * 1000) . " milliseconds");
            // array_push($response['errors'], "Time Between: " . $timeDifference . " milliseconds ($formattedTimeBetween ms)");
            header('Content-Type: application/json');
            exit(json_encode($response));

            break;

        case '2FAlogin':
            $startTime = microtime(true);

            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'otpKey' => [
                    'dname' => 'OTP Password',
                    'required' => true,
                    'min' => 4,
                ],
            ]);

            if ($validation->passed()) {
                $otp = Input::validateString(Input::get('otpKey', true));
                if (Session::exists('tmpUsername') && Session::exists('tmpPassword') && Session::exists('_2FA_KEY_')) {
                    $user = new User();
                    $username = Input::validateEmail(Session::getSessVal('tmpUsername'));
                    $password = Session::getSessVal('tmpPassword');

                    $userExists = $user->find($username);

                    if ($userExists) {
                        $loginResult = $user->login($username, $password, true, $otp);
                        if ($loginResult === true) {
                            Session::delete('tmpUsername');
                            Session::delete('tmpPassword');
                            Session::delete('_2FA_KEY_');
                            Session::delete('_2FA_VALID_UNTIL_');
                            $response['success'] = true;
                            $response['message'] = "Logged in successful";
                            $response['url'] = "profile";
                        } else {
                            $response['success'] = false;
                            $response['errors'] = array("Invalid/Expired OTP");
                        }
                    } else {
                        $response['success'] = false;
                        $response['errors'] = array("Invalid/Expired OTP");
                    }
                }
            } else {
                $response['success'] = false;
                $response['errors'] = $validation->errors();
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


        case 'resendOTP':
            if (Session::exists('tmpUsername') && Session::exists('tmpPassword') && Session::exists('_2FA_KEY_')) {

                $startTime = microtime(true);
                Session::delete('_2FA_KEY_');
                Session::delete('_2FA_VALID_UNTIL_');

                Session::put('_2FA_KEY_', Hash::genRandomPass(4));
                $otp = Session::getSessVal('_2FA_KEY_');
                $email = Session::getSessVal('_2FA_EMAIL_');

                $currentDateTime = new DateTime();

                $currentDateTime->add(new DateInterval('PT2M'));

                $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');

                Session::put('_2FA_VALID_UNTIL_', $currentDateTime);

                if (sendMail($smtpCredentials, "Authentication Code", "Please use the OTP provided below to complete your secure login<br>Authentication Code: $otp <br> Valid Until: $formattedDateTime", $email)) {

                    $response['success'] = true;
                    $response['message'] = "OTP has been send successfully";
                    $response['url'] = "2Auth";
                } else {
                    $response['success'] = false;
                    $response['errors'] = array("Something while sending the otp code. Please try again");
                    $response['url'] = "login";
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
            }
            break;

        case 'logout':
            $user = new User();

            if ($user->logout()) {
                $response['success'] = true;
                $response['url'] = "/";
            } else {
                $response['success'] = false;
                $response['errors'] = array("User could not logged out");
            }


            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        case 'subscribeNewsletter':

            $response = [
                'success' => false,
                'errors' => [],
            ];

            // Get the email from POST data
            $email = trim(Input::get('email'));

            // Validate the email
            $validate = new Validate();
            $validation = $validate->check(['email' => $email], [
                'email' => [
                    'dname' => 'Email',
                    'required' => true,
                    'isMail' => true,
                ],
            ]);

            if ($validation->passed()) {
                // Check if email is already subscribed
                $newsletter = new Newsletter();
                if ($newsletter->fetchSubscriptionByMail($email)) {
                    $response['errors'][] = 'Αυτό το email είναι ήδη εγγεγραμμένο στο newsletter.';
                } else {
                    // Subscribe email
                    $hash = new Hash();
                    $authToken = $hash->unique();

                    if ($newsletter->addSubscription(array(
                        'email' => Input::get('email'),
                        'token' => $authToken,
                        // 'redeem_code' => $coupon,
                        // 'redeemed' => false
                    ))) {



                        // $notifications = new Notifications();
                        // $notifications->addNotification(array(
                        //     'title' => 'Newsletter subscription',
                        //     'url' => 'newsletter',
                        //     'text' => 'You have a new subscriber <b>' . Input::get('email') . '</b>',
                        //     'ntype' => 4,
                        // ));

                        $mailData = [
                            'email' => $email,
                            'newsletterToken' => $authToken,
                            'message' => $mainSettings->mailResponses->r_newsletterMessage,
                        ];

                        $emailContent = Template::mailTemplate($mailData);
                        $subject = $mainSettings->mailResponses->r_newsletterTitle;


                        $mailSent = sendMail($smtpCredentials, $subject, $emailContent, $email);

                        if ($mailSent) {
                            $response['success'] = true;
                            $response['message'] = 'Εγγραφήκατε επιτυχώς στο newsletter μας.';
                        } else {
                            $response['errors'][] = 'Υπήρξε πρόβλημα κατά την αποστολή του email επιβεβαίωσης.';
                        }
                    } else {
                        $response['errors'][] = 'Υπήρξε πρόβλημα κατά την εγγραφή σας. Παρακαλούμε προσπαθήστε ξανά.';
                    }
                }
            } else {
                $response['errors'] = $validation->errors();
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;

        case 'unsubscribe':
            $response = [
                'success' => false,
                'errors' => [],
            ];

            // Get and sanitize inputs
            $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
            $sec_token = isset($_POST['sec_token']) ? preg_replace('/[^a-f0-9]/', '', $_POST['sec_token']) : '';

            // Validate inputs
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['errors'][] = 'Μη έγκυρη διεύθυνση email.';
            }

            if (empty($sec_token)) {
                $response['errors'][] = 'Μη έγκυρο token.';
            }

            if (empty($response['errors'])) {
                // Verify token and email match
                $newsletter = new Newsletter();
                if ($newsletter->unsubscribe($sec_token, $email)) {
                    $response['success'] = true;
                    $response['message'] = 'Διαγραφτήκατε επιτυχώς από το newsletter μας.';
                } else {
                    $response['errors'][] = 'Υπήρξε πρόβλημα κατά τη διαγραφή σας. Παρακαλούμε προσπαθήστε ξανά.';
                }
            }

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
            break;

        case 'submitMessage':
            // Get POST data
            $fullName = trim(Input::get('fullName'));
            $email = trim(Input::get('email'));
            $mailSubject = trim(Input::get('mailSubject')); // Added Subject for Admin
            $messageContent = trim(Input::get('message'));

            // Validate inputs
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'fullName' => ['dname' => 'Όνομα', 'required' => true, 'min' => 2, 'max' => 255, 'isString' => true],
                'email' => ['dname' => 'Email', 'required' => true, 'isMail' => true],
                'mailSubject' => ['dname' => 'Θέμα', 'required' => true, 'min' => 2, 'max' => 255, 'isString' => true], // Validate subject for admin
                'message' => ['dname' => 'Μήνυμα', 'required' => true, 'min' => 5, 'max' => 5000, 'isString' => true],
            ]);

            if (!$validation->passed()) {
                $response['errors'] = $validation->errors();
            } else {
                // Prepare user email content (remains unchanged)
                $mailDataUser = [
                    'message' => $mainSettings->mailResponses->r_submitMessageMessage,
                    'fullName' => htmlspecialchars($fullName),
                    'email' => htmlspecialchars($email),
                    'messageContent' => nl2br(htmlspecialchars($messageContent)),
                ];
                $userEmailContent = Template::mailTemplate($mailDataUser);

                // Prepare admin email content
                $mailDataAdmin = [
                    'fullName' => htmlspecialchars($fullName),
                    'email' => htmlspecialchars($email),
                    'mailSubject' => htmlspecialchars($mailSubject),
                    'messageContent' => nl2br(htmlspecialchars($messageContent)),
                ];

                $adminMessage = nl2br(htmlspecialchars($messageContent));
                $adminEmailAddress = htmlspecialchars($email);
                $adminFullName = htmlspecialchars($fullName);
                $adminSubject = htmlspecialchars($mailSubject); // Use subject only for admin

                // Email template for admin
                $emailTemplate = "
            <p><strong>Όνομα:</strong> {$adminFullName}</p>
            <p><strong>Email:</strong> {$adminEmailAddress}</p>
            <p><strong>Θέμα:</strong> {$adminSubject}</p>
            <p><strong>Μήνυμα:</strong><br>{$adminMessage}</p>
        ";

                // Generate admin email content
                $mailDataAdmin['message'] = $emailTemplate;
                $emailContent = Template::mailTemplate($mailDataAdmin);

                // Send emails
                $adminEmail = $mainSettings->companySettings->adminEmail;

                if (
                    sendMail($smtpCredentials, $mainSettings->mailResponses->r_submitMessageTitle, $userEmailContent, $email) // User email (unchanged)
                    && sendMail($smtpCredentials, $mailSubject, $emailContent, $adminEmail)
                ) { // Admin email with custom subject
                    $response['success'] = true;
                    $response['message'] = 'Το μήνυμά σας εστάλη επιτυχώς. Θα επικοινωνήσουμε μαζί σας σύντομα.';
                } else {
                    $response['errors'][] = 'Υπήρξε πρόβλημα κατά την αποστολή του μηνύματος. Παρακαλούμε προσπαθήστε ξανά.';
                }
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;


        // ============================================================
        // PUBLIC: Fetch Package Details for Booking Modal
        // ============================================================
        case 'fetchPublicPackage':
            $id = (int)Input::get('id');
            if (!$id) {
                echo json_encode(['success' => false, 'errors' => ['Invalid ID']]);
                exit;
            }

            $db = Database::getInstance();

            // 1. Fetch Package Data
            // We select specific fields to avoid leaking internal data if any
            $pkg = $db->query("SELECT id, title, description, price, duration_minutes, type, is_group, start_datetime, max_attendants 
                           FROM packages WHERE id = ?", [$id]);

            if (!$pkg || count($pkg) === 0) {
                echo json_encode(['success' => false, 'errors' => ['Package not found']]);
                exit;
            }
            $package = $pkg[0];

            if ($package->is_group == 1) {
                $startTs = strtotime($package->start_datetime);
                if ($startTs < time()) {
                    // It's in the past
                    echo json_encode(['success' => false, 'error' => 'Αυτό το workshop έχει ολοκληρωθεί.']);
                    exit;
                }
            }

            // 2. Fetch Associated Therapists (ID + Name)
            // We join with the therapists table to get names for the dropdown
            $therapists = $db->query(
                "SELECT t.id, t.first_name, t.last_name, t.avatar 
             FROM therapists t
             JOIN package_therapists pt ON pt.therapist_id = t.id
             WHERE pt.package_id = ?",
                [$id]
            );

            $package->therapists = $therapists ? $therapists : [];

            // 3. (Optional) If Group, verify spots availability again? 
            // For now, just return data. The frontend has badges, but double check on submit is better.

            header('Content-Type: application/json');
            exit(json_encode(['success' => true, 'data' => $package]));
           
            break;


        // ============================================================
        // PUBLIC: Get Next Available Date
        // ============================================================
        case 'getNextAvailableDate':
            $tid = (int)Input::get('therapist_id');
            $duration = (int)Input::get('duration');

            if (!$tid || !$duration) {
                echo json_encode(['success' => false]);
                exit;
            }

            // Loop through next 30 days to find first availability
            // This is a naive approach; optimization would be to check db rules first
            // but reusing getPublicSlots logic ensures consistency.

            // To avoid huge overhead, we check day-by-day or week-by-week logic ideally.
            // For now, let's just find the next working day from rules and return it.

            $db = Database::getInstance();
            $today = date('Y-m-d');

            // Check next 14 days
            for ($i = 0; $i < 14; $i++) {
                $checkDate = date('Y-m-d', strtotime("+$i days"));
                $weekday = date('w', strtotime($checkDate));

                // 1. Check Rule Existence
                $rules = $db->query("SELECT id FROM therapist_availability_rules WHERE therapist_id = ? AND weekday = ? AND is_active = 1", [$tid, $weekday]);

                if ($rules) {
                    // 2. We found a working day! 
                    // Ideally we should check if it's full, but for UX speed, just jumping 
                    // to the first "Working Day" is usually enough.
                    echo json_encode(['success' => true, 'date' => $checkDate]);
                    exit;
                }
            }

            echo json_encode(['success' => false, 'message' => 'No slots found soon']);
            exit;
            break;


        // ============================================================
        // PUBLIC: Get Available Slots (Frontend Calendar)
        // ============================================================
        case 'getPublicSlots':
            $tid = (int)Input::get('therapist_id');
            $date = Input::get('date');
            $duration = (int)Input::get('duration');
            $type = Input::get('type');

            if (!$tid || !$date || !$duration) {
                echo json_encode(['success' => false, 'error' => 'Missing data']);
                exit;
            }

            $db = Database::getInstance();
            $weekday = date('w', strtotime($date));

            // --- 1. GET THERAPIST POLICIES (Booking Window & Notice) ---
            $policyQ = $db->query("SELECT booking_window_days, min_notice_hours FROM therapists WHERE id = ?", [$tid]);
            $policy = ($policyQ && count($policyQ) > 0) ? $policyQ[0] : null;

            // Defaults: 60 days window, 12 hours notice (if not set)
            $limitDays = ($policy && $policy->booking_window_days) ? (int)$policy->booking_window_days : 60;
            $limitHours = ($policy && isset($policy->min_notice_hours)) ? (int)$policy->min_notice_hours : 12;

            // Calculate Constraints Timestamps
            $now = time();
            $earliestAllowed = $now + ($limitHours * 3600); // Now + Notice
            $latestAllowed   = $now + ($limitDays * 86400);  // Now + Window

            // --- 2. CHECK IF DATE IS OUTSIDE WINDOW (Optimization) ---
            // If the requested date's end (23:59:59) is before earliest or start (00:00) is after latest, return empty immediately.
            $dateTsStart = strtotime("$date 00:00:00");
            $dateTsEnd   = strtotime("$date 23:59:59");

            if ($dateTsEnd < $earliestAllowed || $dateTsStart > $latestAllowed) {
                echo json_encode(['success' => true, 'slots' => []]); // No slots possible
                exit;
            }

            // --- 3. FETCH RULES (Filtered by Type) ---
            $sql = "SELECT start_time, end_time 
                    FROM therapist_availability_rules 
                    WHERE therapist_id = ? 
                    AND weekday = ? 
                    AND is_active = 1
                    AND (appointment_type IS NULL OR appointment_type = 'mixed' OR appointment_type = ?)";

            $rules = $db->query($sql, [$tid, $weekday, $type]);

            if (!$rules) {
                echo json_encode(['success' => true, 'slots' => []]);
                exit;
            }

            // --- 4. FETCH BUSY SLOTS ---
            $busy = [];
            $startDay = "$date 00:00:00";
            $endDay   = "$date 23:59:59";

            // Bookings (with buffer)
            $bks = $db->query(
                "SELECT b.start_datetime, b.end_datetime, IFNULL(p.buffer_minutes, 0) as buffer
                 FROM bookings b
                 LEFT JOIN packages p ON b.package_id = p.id
                 WHERE b.therapist_id = ? 
                 AND b.status != 'canceled' 
                 AND b.start_datetime < ? AND b.end_datetime > ?",
                [$tid, $endDay, $startDay]
            );
            foreach ($bks ?: [] as $b) {
                $endTs = strtotime($b->end_datetime);
                $bufferedEnd = $endTs + ($b->buffer * 60);
                $busy[] = ['s' => strtotime($b->start_datetime), 'e' => $bufferedEnd];
            }

            // Blocks
            $bls = $db->query(
                "SELECT start_datetime, end_datetime FROM therapist_time_blocks 
                 WHERE therapist_id = ? AND kind='block' 
                 AND start_datetime < ? AND end_datetime > ?",
                [$tid, $endDay, $startDay]
            );
            foreach ($bls ?: [] as $b) {
                $busy[] = ['s' => strtotime($b->start_datetime), 'e' => strtotime($b->end_datetime)];
            }

            // --- 5. GENERATE SLOTS ---
            $slots = [];
            $step = 30 * 60;

            foreach ($rules as $rule) {
                $workStart = strtotime("$date " . $rule->start_time);
                $workEnd   = strtotime("$date " . $rule->end_time);

                for ($time = $workStart; $time <= ($workEnd - ($duration * 60)); $time += $step) {
                    $slotStart = $time;
                    $slotEnd   = $time + ($duration * 60);

                    // A. Check Policy Constraints (Precise)
                    if ($slotStart < $earliestAllowed) continue; // Too soon
                    if ($slotStart > $latestAllowed) continue;   // Too far

                    // B. Check Busy Collisions
                    $isFree = true;
                    foreach ($busy as $b) {
                        if ($slotStart < $b['e'] && $slotEnd > $b['s']) {
                            $isFree = false;
                            break;
                        }
                    }

                    if ($isFree) {
                        $slots[] = date('H:i', $time);
                    }
                }
            }

            $slots = array_unique($slots);
            sort($slots);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'slots' => array_values($slots)]);
            exit;
            break;


        case 'fetchPackagesByType':
            // Optional: Restrict access to certain user roles
            // Uncomment the following lines if you want to restrict access
            // to admin users only.
            /*
            if (!$mainUser->hasPermission(['admin'])) {
                permissionDenied();
            }
            */

            // Initialize the Validate class
            $validate = new Validate();

            // Perform validation on the incoming 'type' parameter
            $validation = $validate->check($_POST, [
                'type' => [
                    'dname' => 'Type',
                    'required' => true,
                    'isString' => true,
                    'in' => ['online', 'inPerson', 'mixed'],
                ]
            ]);

            // Initialize the response array
            $response = [];

            if ($validation->passed()) {
                // Retrieve the validated and sanitized 'type'
                $type = Input::validateString($_POST['type']);

                // Initialize the Packages class
                $packages = new Packages();

                // Fetch packages based on the validated 'type'
                $data = $packages->fetchPackagesByType($type);

                if ($data && count($data) > 0) {
                    // Success: Packages found
                    $response['success'] = true;
                    $response['packages'] = $data;
                } else {
                    // No packages found for the given type
                    $response['success'] = false;
                    $response['errors'] = ["No packages found for type '$type'."];
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }
            } else {
                // Validation failed: Retrieve and return errors
                $response['success'] = false;
                $response['errors'] = $validation->errors();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Set the appropriate header and output the JSON response
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

      

        case 'createPublicBooking':
            $response = [
                'success' => false,
                'errors'  => []
            ];

            // ---------------------------------------------------------
            // 1. VALIDATION
            // ---------------------------------------------------------
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'package_id'   => ['required' => true, 'numeric' => true],
                'first_name'   => ['required' => true, 'min' => 2],
                'last_name'    => ['required' => true, 'min' => 2],
                'email'        => ['required' => true],
                'phone'        => ['required' => true],
                'date'         => ['required' => true],
                'time'         => ['required' => true]
            ]);

            if (!$validation->passed()) {
                $response['errors'] = $validation->errors();
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Extract Data
            $pkgId = (int)Input::get('package_id');
            $tid   = (int)Input::get('therapist_id');
            $type  = Input::get('type');
            $date  = Input::get('date');
            $time  = Input::get('time');
            $token = Input::get('payment_token');

            $fname = Input::validateString($_POST['first_name']);
            $lname = Input::validateString($_POST['last_name']);
            $email = Input::validateEmail($_POST['email']) ? $_POST['email'] : '';
            $phone = Input::validateString($_POST['phone']);
            $notes = Input::get('notes');

            if (!$email) {
                $response['errors'][] = 'Μη έγκυρο Email.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // ---------------------------------------------------------
            // 2. FETCH PACKAGE & PREPARE DATA
            // ---------------------------------------------------------
            $packages = new Packages();
            $pkg = $packages->fetchPackage($pkgId);

            if (!$pkg) {
                $response['errors'][] = 'Το πακέτο δεν βρέθηκε.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $allowedTypes = ($pkg->type === 'mixed') ? ['online', 'inPerson'] : [$pkg->type];

            if (!in_array($type, $allowedTypes)) {
                $response['errors'][] = 'Ο επιλεγμένος τρόπος διεξαγωγής δεν υποστηρίζεται από αυτό το πακέτο.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $price = (float)$pkg->price;

            // --- NEW: Block packages with 0 price ---
            if ($price <= 0) {
                $response['errors'][] = 'Το πακέτο δεν είναι διαθέσιμο για online κράτηση.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            $isPaid = true;

            // Calculate Start/End
            $startSQL = date('Y-m-d H:i:s', strtotime("$date $time"));
            $endSQL   = date('Y-m-d H:i:s', strtotime("$startSQL +{$pkg->duration_minutes} minutes"));

            // ---------------------------------------------------------
            // 3. AVAILABILITY & RULES CHECK (Using your Bookings logic)
            // ---------------------------------------------------------
            $bookingsModel = new Bookings();
            $db = Database::getInstance();

            if ($pkg->is_group == 1) {
                // Group Logic: Check Capacity (DB Count + Manual Bookings)
                $countSql = "SELECT COUNT(*) as c FROM bookings WHERE package_id = ? AND status != 'canceled'";
                $res = $db->query($countSql, [$pkgId]);
                $dbCount = ($res && count($res) > 0) ? (int)$res[0]->c : 0;

                // Your logic: manual_bookings is stored in package
                $manualBookings = (int)$pkg->manual_bookings;
                $maxAttendants  = (int)$pkg->max_attendants;
                $totalParticipants = $dbCount + $manualBookings;

                if (($totalParticipants + 1) > $maxAttendants && $maxAttendants > 0) {
                    $response['errors'][] = "Το Group είναι πλήρες ({$totalParticipants}/{$maxAttendants}).";
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }
            } else {
                // Personal Logic: Check Overlap using your Class Method
                if (!$tid) {
                    $response['errors'][] = 'Παρακαλώ επιλέξτε θεραπευτή.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // Using your existing checkOverlap method!
                if ($bookingsModel->checkOverlap($tid, $startSQL, $endSQL)) {
                    $response['errors'][] = 'Η επιλεγμένη ώρα δεν είναι πλέον διαθέσιμη.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }
            }

            // ---------------------------------------------------------
            // 4. CLIENT HANDLING
            // ---------------------------------------------------------
            $clients = new Clients();
            $clientExists = $clients->clientExists($phone, $email, true);

            $clientId = null;
            if (!$clientExists) {
                $newClientID = $clients->addClient([
                    'first_name'  => $fname,
                    'last_name'   => $lname,
                    'email'       => $email,
                    'phone'       => $phone,
                    'client_note' => "Created via Website Booking"
                ], true); // returns ID

                if (!is_numeric($newClientID)) {
                    $response['errors'][] = 'Σφάλμα δημιουργίας πελάτη.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }
                $clientId = $newClientID;
            } else {
                $clientId = $clientExists->id;
            }

            // ---------------------------------------------------------
            // 5. PROCESS PAYMENT
            // ---------------------------------------------------------
            $paymentId = null;
            $paymentStatus = 'unpaid';
            $paymentObj = null;

            if ($isPaid) {
                if (!$token) {
                    $response['errors'][] = 'Λείπει το token πληρωμής.';
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                $payments = new Payments();
                $amountCents = round($price * 100);

                // Charge
                $paymentObj = $payments->doPaymentEveryPay(
                    $token,
                    $amountCents,
                    "Booking #{$clientId} - {$pkg->title}",
                    $email
                );

                if (property_exists($paymentObj, 'error')) {
                    $response['errors'][] = 'Η πληρωμή απέτυχε: ' . $paymentObj->error->message;
                    header('Content-Type: application/json');
                    exit(json_encode($response));
                }

                // --- FIX: Date Parsing ---
                // Parse the ISO 8601 date from EveryPay (e.g. "2026-02-02T19:35:47+0200")
                $dt = new DateTime($paymentObj->date_created);
                $paymentDate = $dt->format('Y-m-d H:i:s');

                // --- FIX: Add Payment with ALL fields ---
                $payments->addPayment([
                    'client_id'       => $clientId,
                    'status'          => 'completed',
                    'date_created'    => $paymentDate,
                    'payed_at'        => $paymentDate, // [FIX] Added missing field
                    'amount_paid'     => $pkg->price,
                    'amount_total'    => $pkg->price,
                    'currency'        => 'EUR',
                    'token'           => $token,
                    'payment_method'  => 'EveryPay',
                    'payer_email'     => $email,

                    // Detailed Card Info (Mapped from your object dump)
                    'card_type'       => $paymentObj->payment_method_details->card->type ?? '',
                    'card_name'       => $paymentObj->payment_method_details->card->friendly_name ?? '',
                    'paymentRef'      => substr($token, 0, 10),
                    'billing_country' => $paymentObj->payment_method_details->card->billing->country ?? '',
                    'billing_city'    => $paymentObj->payment_method_details->card->billing->city ?? '',
                    'billing_zip'     => $paymentObj->payment_method_details->card->billing->postal_code ?? '',
                    'billing_address' => $paymentObj->payment_method_details->card->billing->address_line1 ?? ''
                ]);

                $paymentId = $payments->lastInsertedID();
                $paymentStatus = 'paid';
            }

            // ---------------------------------------------------------
            // 6. CREATE BOOKING
            // ---------------------------------------------------------
            $bookingFields = [
                'client_id'      => $clientId,
                'therapist_id'   => $tid,
                'package_id'     => $pkgId,
                'start_datetime' => $startSQL,
                'end_datetime'   => $endSQL,
                'status'         => 'confirmed', // Auto-confirm for public
                'notes'          => $notes,
                'payment_status' => $paymentStatus,
                'status'         => ($paymentStatus === 'paid') ? 'booked' : 'pending',
                'appointment_type' => $type
                // 'type' is not in your saveBooking fields, but useful if DB has it
                // 'type'           => $type 
            ];

            if ($bookingsModel->createBooking($bookingFields)) {
                $bookingId = $bookingsModel->lastInsertedID();
            } else {
                $response['errors'][] = 'Σφάλμα δημιουργίας κράτησης.';
                header('Content-Type: application/json');
                exit(json_encode($response));
            }

            // Link Payment
            if ($paymentId) {
                $db->query("UPDATE payments SET reservation_id = ? WHERE id = ?", [$bookingId, $paymentId]);
            }

            // ---------------------------------------------------------
            // 7. NOTIFICATIONS & EMAILS
            // ---------------------------------------------------------
            $notificationMail = htmlspecialchars($email);
            $notificationToken = $token ? substr($token, 0, 10) : '-';

            $notifications = new Notifications();
            $notifications->addNotification([
                'title'   => 'Νέα Κράτηση (Website)',
                'type'    => 'success',
                'message' => "Νέα Κράτηση από <strong>{$notificationMail}</strong> (#{$bookingId}).",
                'url'     => 'bookings'
            ]);

            // Email Config
            $userSubject = $mainSettings->mailResponses->r_completeBookingTitle ?? 'Επιβεβαίωση Κράτησης';
            $userMessageTemplate = $mainSettings->mailResponses->r_completeBookingMessage ?? 'Η κράτησή σας ολοκληρώθηκε.';
            $fullName = "$lname $fname";
            $typeDisplay = ($type === 'online') ? 'Online (Video)' : 'Δια Ζώσης';
           
            // Client Email Data
            $userMailData = [
                'fullName'     => htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'),
                'packageTitle' => htmlspecialchars($pkg->title, ENT_QUOTES, 'UTF-8'),
                'price'        => htmlspecialchars($pkg->price, ENT_QUOTES, 'UTF-8'),
                'bookingID'    => $bookingId,
                'paymentToken' => $notificationToken,
                'message'      => $userMessageTemplate,
                'date'         => date('d/m/Y', strtotime($startSQL)),
                'time'         => date('H:i', strtotime($startSQL)),
                'type'         => $typeDisplay
            ];
            $userEmailContent = Template::mailTemplate($userMailData);

            // Admin Email Data (Table)
            $field_labels = [
                'fullName'      => 'Ονοματεπώνυμο',
                'email'         => 'Email',
                'phone'         => 'Τηλέφωνο',
                'package_title' => 'Υπηρεσία',
                'date'          => 'Ημερομηνία',
                'time'          => 'Ώρα',
                'price'         => 'Ποσό',
                'payment_token' => 'Ref Πληρωμής',
                'notes'         => 'Σημειώσεις'
            ];
            $adminMailData = [
                'fullName'      => $fullName,
                'email'         => $email,
                'phone'         => $phone,
                'package_title' => $pkg->title,
                'date'          => date('d/m/Y', strtotime($startSQL)),
                'time'          => date('H:i', strtotime($startSQL)),
                'type'          => $typeDisplay,
                'price'         => $pkg->price . '€',
                'payment_token' => $notificationToken,
                'notes'         => $notes
            ];

            $field_labels['type'] = 'Τρόπος Διεξαγωγής';

            // Generate Admin HTML
            if (!function_exists('generateAdminEmailTable')) {
                function generateAdminEmailTable($data, $labels)
                {
                    $html = '<h2>Νέα Online Κράτηση</h2>';
                    $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width:100%; max-width:600px;">';
                    foreach ($data as $key => $value) {
                        if (!isset($labels[$key])) continue;
                        $label = $labels[$key];
                        $val = nl2br(htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
                        $html .= "<tr><td style='background:#f9f9f9; width:30%;'><strong>{$label}</strong></td><td>{$val}</td></tr>";
                    }
                    $html .= '</table>';
                    return $html;
                }
            }
            $adminEmailContent = generateAdminEmailTable($adminMailData, $field_labels);

            $adminEmail = $mainSettings->companySettings->adminEmail ?? 'info@yourdomain.com';
            $adminSubject = 'Νέα Κράτηση #' . $bookingId;

            // Send
            if (isset($smtpCredentials)) {
                sendMail($smtpCredentials, $userSubject, $userEmailContent, $email);
                sendMail($smtpCredentials, $adminSubject, $adminEmailContent, $adminEmail);
            }

            // ---------------------------------------------------------
            // 8. RESPONSE
            // ---------------------------------------------------------
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'    => true,
                'message'    => 'Η κράτησή σας ολοκληρώθηκε επιτυχώς.',
                'booking_id' => $bookingId
            ], JSON_UNESCAPED_UNICODE);
            exit;
            break;

        


        // 1. Fetch Products για το Frontend
        case 'fetchPublicDigitalProducts':
            $products = new DigitalProducts();
            $data = $products->fetchProductsFront(); // Υποθέτουμε ότι επιστρέφει array

            if ($data) {
                $response['success'] = true;
                $response['products'] = $data;
            } else {
                $response['success'] = false;
                $response['errors'] = ["Δεν βρέθηκαν προϊόντα."];
            }
            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        // 2. Ολοκλήρωση Αγοράς eBook
        // 2. Ολοκλήρωση Αγοράς eBook
        case 'purchaseDigitalProduct':
            $response = ['success' => false, 'errors' => []];

            // Validate Inputs
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'firstName' => ['required' => true, 'min' => 2],
                'lastName' => ['required' => true, 'min' => 2],
                'email' => ['required' => true, 'isMail' => true],
                'product_id' => ['required' => true, 'isNumeric' => true],
                'payment_token' => ['required' => true],
                'csrf_token' => ['required' => true]
            ]);

            if (!$validation->passed()) {
                $response['errors'] = $validation->errors();
                exit(json_encode($response));
            }

            try {
                // A. Διαχείριση Πελάτη
                $clients = new Clients();
                $email = Input::get('email');
                $phoneNumber = Input::get('phoneNumber');

                $clientData = $clients->fetchClientByEmail($email);

                if ($clientData) {
                    $clientId = $clientData->id;
                } else {
                    $clientId = $clients->addClient([
                        'first_name' => Input::get('firstName'),
                        'last_name' => Input::get('lastName'),
                        'email' => $email,
                        'phone' => $phoneNumber
                    ], true);
                }

                // B. Ανάκτηση Προϊόντος
                $prodModel = new DigitalProducts();
                $product = $prodModel->fetchProduct(Input::get('product_id'));

                if (!$product) {
                    throw new Exception("Το προϊόν δεν βρέθηκε.");
                }

                // C. Πληρωμή (EveryPay)
                $amountInCents = $product->price * 100;
                $token = Input::get('payment_token');
                $payments = new Payments();

                $payment = $payments->doPaymentEveryPay(
                    $token,
                    $amountInCents,
                    'E-Book Purchase: ' . $product->title,
                    $email
                );

                if (property_exists($payment, 'error')) {
                    throw new Exception($payment->error->message);
                }

                // Format Ημερομηνίας από EveryPay
                $paymentCreatedAt = $payment->date_created;
                $paymentCreatedAt = (new DateTime($paymentCreatedAt))->format('Y-m-d H:i:s');

                // D. Καταγραφή Πληρωμής
                $payments->addPayment([
                    'client_id' => $clientId,
                    'reservation_id' => '', // Κενό για eBooks
                    'status' => $payment->status,
                    'date_created' => $paymentCreatedAt,
                    'amount_paid' => $product->price,
                    'amount_total' => $product->price,
                    'currency' => $payment->currency,
                    'token' => $payment->token,
                    'billing_country' => $payment->card->billing->country ?? '',
                    'billing_city' => $payment->card->billing->city ?? '',
                    'payer_email' => $payment->payee_email ?? $email,
                    'billing_zip' => $payment->card->billing->postal_code ?? '',
                    'billing_address' => $payment->card->billing->address_line1 ?? '',
                    'payment_method' => 'EveryPay',
                    'card_type' => $payment->payment_method_details->card->type ?? '',
                    'card_name' => $payment->payment_method_details->card->friendly_name ?? '',
                    'paymentRef' => 'EBOOK-' . substr($payment->token, 0, 8)
                ]);

                // E. Δημιουργία Download Token & Order
                $downloadToken = bin2hex(random_bytes(32));

                $db = Database::getInstance();
                $db->query("INSERT INTO product_orders (client_id, product_id, payment_token, download_token) VALUES (?, ?, ?, ?)", [
                    $clientId,
                    $product->id,
                    $payment->token,
                    $downloadToken
                ]);

                // F. Αποστολή Email στον ΠΕΛΑΤΗ
                // ... (Ο κώδικας πριν από το email παραμένει ίδιος) ...

                // F. Αποστολή Email στον ΠΕΛΑΤΗ
                $downloadLink = Config::get('base_url') . "download-view?t=" . $downloadToken;
                $firstName = Input::get('firstName'); // Παίρνουμε το όνομα για προσωποποίηση

                $userSubject = "Η παραγγελία σας είναι έτοιμη: " . $product->title;

                // EMAIL TEMPLATE
                $userMessage = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body { margin: 0; padding: 0; background-color: #f6f9fc; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
                        .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
                        .header { background-color: #51BBA0; padding: 30px 20px; text-align: center; }
                        .header h1 { margin: 0; color: #ffffff; font-size: 24px; letter-spacing: 1px; }
                        .content { padding: 40px 30px; color: #525f7f; line-height: 1.6; }
                        .product-box { background-color: #f9f9f9; border: 1px solid #e6ebf1; border-radius: 6px; padding: 20px; margin: 25px 0; text-align: center; }
                        .product-title { color: #32325d; font-size: 18px; font-weight: bold; margin-bottom: 5px; display: block; }
                        .product-price { color: #51BBA0; font-weight: bold; font-size: 16px; }
                        .btn-container { text-align: center; margin: 35px 0; }
                        .btn { background-color: #51BBA0; color: #ffffff !important; padding: 14px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 6px rgba(81, 187, 160, 0.2); }
                        .footer { background-color: #f6f9fc; padding: 20px; text-align: center; font-size: 12px; color: #8898aa; }
                        .link-fallback { word-break: break-all; color: #51BBA0; }
                    </style>
                </head>
                <body>
                    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                        <tr>
                            <td align='center' style='padding: 20px 0;'>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>Alma Psychology</h1>
                                    </div>

                                    <div class='content'>
                                        <h2 style='color: #32325d; margin-top: 0;'>Ευχαριστούμε για την αγορά!</h2>
                                        <p>Γεια σου <strong>{$firstName}</strong>,</p>
                                        <p>Η παραγγελία σου ολοκληρώθηκε με επιτυχία. Το ψηφιακό αρχείο είναι έτοιμο για λήψη και σε περιμένει.</p>

                                        <div class='product-box'>
                                            <span style='font-size: 12px; text-transform: uppercase; color: #8898aa; letter-spacing: 1px;'>Το eBook σου</span><br>
                                            <span class='product-title'>{$product->title}</span>
                                            <span class='product-price'>{$product->price} €</span>
                                        </div>

                                        <div class='btn-container'>
                                            <a href='{$downloadLink}' class='btn'>Λήψη Αρχείου</a>
                                        </div>

                                        <p style='font-size: 14px;'>
                                            <small>Ο σύνδεσμος θα είναι ενεργός για <strong>7 ημέρες</strong> ή για <strong>2 λήψεις</strong>.</small>
                                        </p>

                                        <hr style='border: none; border-top: 1px solid #e6ebf1; margin: 30px 0;'>

                                        <p style='font-size: 13px; margin-bottom: 5px;'>Αν το κουμπί δεν λειτουργεί, αντέγραψε τον παρακάτω σύνδεσμο:</p>
                                        <a href='{$downloadLink}' class='link-fallback' style='font-size: 12px;'>{$downloadLink}</a>
                                    </div>

                                    <div class='footer'>
                                        <p>&copy; " . date("Y") . " Alma Psychology. All rights reserved.</p>
                                        <p>Αυτό είναι ένα αυτοματοποιημένο μήνυμα. Παρακαλώ μην απαντάτε.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
                ";

                sendMail($smtpCredentials, $userSubject, $userMessage, $email);

                // --- G. Αποστολή Email στον ADMIN (ΝΕΟ) ---
                $adminEmail = $mainSettings->companySettings->adminEmail; // Βάλε εδώ το email που θες να λαμβάνει τις ειδοποιήσεις
                $adminSubject = "Νέα Πώληση eBook: " . $product->title;

                $customerName = Input::get('firstName') . ' ' . Input::get('lastName');
                $adminMessage = "
                    <h3>Νέα Αγορά eBook από Website</h3>
                    <p>Έχει πραγματοποιηθεί μια νέα αγορά ψηφιακού προϊόντος.</p>
                    <hr>
                    <p><strong>Πελάτης:</strong> {$customerName}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Τηλέφωνο:</strong> {$phoneNumber}</p>
                    <p><strong>Προϊόν:</strong> {$product->title}</p>
                    <p><strong>Ποσό:</strong> {$product->price} €</p>
                    <p><strong>Ημερομηνία:</strong> " . date('d/m/Y H:i') . "</p>
                    <hr>
                    <p><small>Η πληρωμή έχει ολοκληρωθεί μέσω EveryPay.</small></p>
                ";

                sendMail($smtpCredentials, $adminSubject, $adminMessage, $adminEmail);
                // ------------------------------------------

                // Notification στο Admin Panel
                $notifications = new Notifications();
                $notificationToken = substr($payment->token, 0, 10);

                $notifications->addNotification([
                    'title' => 'Νέα Αγορά <strong>eBook</strong>',
                    'type' => 'success',
                    'message' => "Ο/Η <strong>{$email}</strong> αγόρασε το eBook: <strong>{$product->title}</strong>. Ref: {$notificationToken}",
                    'url' => 'digital-orders'
                ]);

                $response['success'] = true;
                $response['message'] = "Η αγορά ολοκληρώθηκε! Ελέγξτε το email σας για το αρχείο.";
            } catch (Exception $e) {
                $response['errors'][] = $e->getMessage();
            }

            header('Content-Type: application/json');
            exit(json_encode($response));
            break;

        default:

            break;
    }
    header('Content-Type: application/json');
    exit(json_encode($response));
}
