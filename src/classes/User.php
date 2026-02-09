<?php
class User
{
    private $_crud,
        $_data,
        $_sessionName,
        $_isLoggedIn;

    public function __construct($user = null)
    {
        $this->_crud = new Crud();

        $this->_sessionName = Config::get('session/session_name');

        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::getSessVal($this->_sessionName);

                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    $this->_isLoggedIn = false;
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function createUser($fields = array())
    {
        if ($this->_crud->add('users', $fields)) {
            return true;
        }
        return false;
    }

    public function find($username = null)
    {
        if ($username) {
            $field = (is_numeric($username)) ? 'id' : 'email';
            $data = $this->_crud->getSpecific('users', $field, '=', $username);

            if ($data) {
                $this->_data = $data;
                return true;
            }
        }
        return false;
    }

    public function userExist($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data = $this->_crud->getSpecific('users', 'email', '=', $email);
            return ($data) ? true : false;
        }
        return false;
    }

    public function fetchUser($id)
    {
        if (is_numeric($id)) {
            $data = $this->_crud->getSpecific('users', 'id', '=', $id);
            return ($data) ? $this->_data = $data : false;
        }
    }

    public function login($username = null, $password = null, $is2FAenabled = false, $otp = null)
    {
        $startTime = microtime(true);
        try {
            if (!$username && !$password && $this->exists()) {
                // Regenerate the session ID
                session_regenerate_id(true);
                Session::put($this->_sessionName, $this->data()->id);

                return true;
            } else {

                // Implement rate limiting
                $startTime = microtime(true);
                $lockAt = 10;

                if ($this->_data->total_attempts >= $lockAt && !$this->_data->locked) {
                    $this->lockAccount($username);
                }

                if ($this->isRateLimitExceeded($username)) {
                    $response = array();
                    $response['errors'] = array("");



                    // Log excessive login attempts and return an error
                    //$this->logExcessiveLoginAttempts($username);
                    $response['success'] = false;
                    $response['errors'] = ["Excessive login attempts. Please try again later."];


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

                if ($this->isAccountLocked($username)) {
                    $response = array();
                    $response['errors'] = array("");

                    // Log excessive login attempts and return an error
                    //$this->logExcessiveLoginAttempts($username);
                    $response['success'] = false;
                    $response['errors'] = ["This Acount is locked"];

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

                if (password_verify($password, $this->_data->password)) {
                    // Regenerate the session ID

                    if ($is2FAenabled && $otp === null) {
                        $_SESSION['_2FA_KEY_'] = Hash::genRandomPass(4);
                        return true;
                    }

                    if ($is2FAenabled && $otp) {

                        if ($this->validateOTP($otp)) {
                            session_regenerate_id(true);
                            Session::put($this->_sessionName, $this->data()->id);
                            $this->clearLoginAttempts($username);
                            $this->unlockAccount($username);
                            return true;
                        } else {
                            $this->incrementLoginAttempts($username);
                            return false;
                        }
                    } else {
                        session_regenerate_id(true);
                        Session::put($this->_sessionName, $this->data()->id);
                        $this->clearLoginAttempts($username);
                        $this->unlockAccount($username);
                        return true;
                    }
                } else {

                    $this->incrementLoginAttempts($username);
                }
            }
        } catch (\Exception $e) {
            $response = array();
            $response['errors'] = array("");
            $response['success'] = false;
            $response['errors'] = ["Something went terrible wrong."];
        }
        
        return false;
    }

    private function isRateLimitExceeded($username)
    {
        // Define the maximum allowed login attempts and the time frame (in seconds)
        $maxAttempts = 5; // Adjust this value as needed
        $timeFrame = 60; // Adjust this value as needed (e.g., 60 seconds = 1 minute)

        // Retrieve the user's login attempt count and timestamp from your database
        $userData = $this->_crud->getSpecific('users', 'email', '=', Input::validateEmail($username));

        // Calculate the time elapsed since the last login attempt
        $currentTime = time();
        $attempts = $userData->attempts;
        $lastLoginTime = strtotime($userData->last_login_attempt);
        $timeElapsed = $currentTime - $lastLoginTime;

        // Check if the time elapsed is within the time frame
        if ($timeElapsed >= $timeFrame) {
            // Reset login attempts and last login attempt timestamp
            if ($this->clearLoginAttempts($username)) {
                // $this->isRateLimitExceeded($username);
                return false; // Return early to prevent further checks
            }
        }

        // Check if the number of attempts exceeds the limit
        if ($attempts >= $maxAttempts && $timeElapsed <= $timeFrame) {
            return true; // Rate limit exceeded
        }

        return false; // Rate limit not exceeded
    }

    private function incrementLoginAttempts($username)
    {
        $userData = $this->_crud->getSpecific('users', 'email', '=', Input::validateEmail($username));
        $attempts = $userData->attempts;
        $totalAttempts = $userData->total_attempts;
        $date = date("Y-m-d H:i:s");
        $updateData = [
            'attempts' => $attempts + 1,
            'total_attempts' => $totalAttempts + 1,
            'last_login_attempt' => $date
        ];

        $whereCondition = [
            'email' => $username,
        ];
        $this->_crud->update('users', $updateData, $whereCondition);
    }

    private function isAccountLocked($username)
    {
        $isLocked = $this->_data->locked;
        $lockedUntil = new DateTime($this->_data->locked_until);
        $now = new DateTime();
        // return ($isLocked && $lockedUntil > $now) ? true : false;

        if ($isLocked && $lockedUntil != null) {
            if ($lockedUntil > $now) {
                return true;
            }else{
                $this->unlockAccount($username);
                return false;
            }
        } 
        return false;
    }


    private function lockAccount($username)
    {
        $date = date("Y-m-d H:i:s");
        $lockedUntil = date('Y-m-d H:i:s', strtotime("+30 minutes"));
        $updateData = [
            'locked' => true,
            'locked_until' => $lockedUntil
        ];

        $whereCondition = [
            'email' => $username,
        ];
        $this->_crud->update('users', $updateData, $whereCondition);
    }

    private function unlockAccount($username)
    {
        $updateData = [
            'locked' => false,
            'total_attempts' => 0,
            'locked_until' => null
        ];

        $whereCondition = [
            'email' => $username,
        ];
        $this->_crud->update('users', $updateData, $whereCondition);
    }

    private function clearLoginAttempts($username)
    {
        $date = date("Y-m-d H:i:s");
        $updateData = [
            'attempts' => 0,
            'last_login_attempt' => $date // Reset the last login attempt timestamp
        ];

        $whereCondition = [
            'email' => $username,
        ];

        $this->_crud->update('users', $updateData, $whereCondition);
    }

    private function validateOTP($otp)
    {
        if (Session::getSessVal('_2FA_KEY_') == $otp) {
            // $currentDateTime = new DateTime();
            // if ($currentDateTime >= Session::getSessVal('_2FA_VALID_UNTIL_')) {
            //     return true;
            // }

            $currentDateTime = new DateTime();
            $storedDateTime = Session::getSessVal('_2FA_VALID_UNTIL_');

            if ($currentDateTime >= $storedDateTime) {
                // The time in _2FA_VALID_UNTIL_ has passed
                return false;
            } else {
                // The time in _2FA_VALID_UNTIL_ has not passed
                return true;
            }
        }
        return false;
    }




    public function hasPermission($keys)
    {
        $group = $this->_crud->getSpecific('user_groups', 'id', '=', $this->data()->access);
        if ($group) {

            $permissions = json_decode($group->permissions, true);
            if (is_array($keys)) {
                foreach ($keys as $key) {
                    if ($permissions[$key] == true) {
                        return true;
                    }
                }
            } else {
                if ($permissions[$keys] == true) {
                    return true;
                }
            }
        }
        return false;
    }

    public function update($fields = array(), $id = null)
    {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        if ($this->_crud->update('users', $fields, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function logout()
    {
        if (Session::delete($this->_sessionName)) {
            return true;
        }
        return false;
    }

    public function data()
    {
        return $this->_data;
    }

    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    public function exists()
    {
        return (!empty($this->_data)) ? true : false;
    }
}
