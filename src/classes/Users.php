<?php
class Users
{
    private $db;
    private $crud;
    private $input;
    private $validator;
    private $_data;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
        $this->input = new Input();
        $this->validator = new Validate();
    }

    function fetchUsers()
    {
        $tempUser = new User();
        $currentUserID = $tempUser->data()->id;
        $sql = "SELECT users.id, users.email, users.firstName,users.lastName,users.access,users.created_at,users.updated_at,users.last_login_attempt FROM users WHERE users.id != $currentUserID";

        $result = $this->db->query($sql);

        if ($result) {
            return $this->_data = $result;
        }
        return false;
    }

    function fetchTodaysUsers()
    {

        $sql = "SELECT * FROM users WHERE user_date = CURDATE() AND executed = 0";

        $result = $this->db->query($sql);

        if ($result) {
            return $this->_data = $result;
        }
        return false;
    }

    function fetchAdminCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM users WHERE access = 1";

        $result = $this->db->query($sql);

        if ($result) {
            return $this->_data = $result[0];
        }
        return false;
    }

    function fetchUser($userID)
    {
        $users  = $this->crud->getSpecific('users', 'id', '=', $userID);
        if ($users) {
            return $this->_data = $users;
        }
        return false;
    }

    function addUser($user)
    {
        if ($this->crud->add('users', $user)) {
            return true;
        }
        return false;
    }

    function updateUser($user, $userID)
    {
        if ($this->crud->update('users', $user, array('id' => $userID))) {
            return true;
        }

        return false;
    }

    function deleteUser($userID)
    {
        $result = $this->crud->delete('users', array('id' => $userID));

        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    function data()
    {
        return $this->_data;
    }
}
