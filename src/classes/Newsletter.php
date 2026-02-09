<?php
class Newsletter
{
    private $db;
    private $_data;
    private $crud;
    public $_lastID;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
    }


    function updateSubscription($item, $id)
    {
        if ($this->crud->update('newsletter', $item, array('id' => $id))) {
            return true;
        }

        return false;
    }

    public function fetchSubscriptions()
    {
        $subscriptions  = $this->crud->getAll('newsletter');
        if ($subscriptions) {
            return $this->_data = $subscriptions;
        }
        return false;
    }

    public function fetchSubscription($id)
    {
        if (is_numeric($id)) {
            $subscription  = $this->crud->getSpecific('newsletter', 'id', '=', $id);
            if ($subscription) {
                return $this->_data = $subscription;
            }
            return false;
        } else {
            return false;
        }
    }

    public function fetchSubscriptionByMail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $subscription  = $this->crud->getSpecific('newsletter', 'email', '=', $email);
            if ($subscription) {
                return $this->_data = $subscription;
            }
            return false;
        } else {
            return false;
        }
    }

    public function addSubscription($fields = array())
    {

        $query = $this->crud->add('newsletter', $fields);
        if ($query['affected_rows'] > 0) {
            return true;
        }
        return false;
    }

    public function deleteSubscription($id)
    {
        $result = $this->crud->delete('newsletter', array('id' => $id));

        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        } else {
            return false;
        }
    }


    // public function unsubscribe($token, $mail)
    // {
    //     $subscriptions  = $this->crud->getSpecific('newsletter', 'token', '=', $token);
    //     if ($subscriptions) {
    //         exit(var_dump($subscriptions));
    //         if ($this->deleteSubscription($subscriptions->id)) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }

    public function unsubscribe($token, $email)
    {
        $query = "SELECT * FROM newsletter WHERE email = :email AND token = :sec_token LIMIT 1";
        $params = [
            ':email' => $email,      
            ':sec_token' => $token,
            ];
        $results = $this->db->query($query, $params);

        if ($results && count($results) > 0) {
            $subscriber = $results[0];
            if ($this->deleteSubscription($subscriber->id)) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    public function data()
    {
        return $this->_data;
    }
}
