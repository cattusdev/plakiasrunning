<?php
class Notifications
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

    public function addNotification($fields = array())
    {
        $query = $this->crud->add('notifications', $fields);
        if ($query['affected_rows'] > 0) {
            $this->_lastID = $query['insert_id'];
            return true;
        }
        return false;

    }

    public function updateNotification($item, $id)
    {
        if ($this->crud->update('notifications', $item, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteNotification($id)
    {
        $result = $this->crud->delete('notifications', array('id' => $id));

        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchNotifications()
    {
        $sql = "SELECT * FROM notifications ORDER BY notifications.is_read ASC";
        $notifications = $this->db->query($sql);  // no params needed since no WHERE conditions

        if ($notifications) {
            return $this->_data = $notifications;
        }
        return false;

    }

    public function fetchNotification($id)
    {
        if (is_numeric($id)) {
            $notification  = $this->crud->getSpecific('notifications', 'id', '=', $id);
            if ($notification) {
                return $this->_data = $notification;
            }
            return false;
        } else {
            return false;
        }
    }


    public function fetchUserNotifications($userId)
    {
        if (is_numeric($userId)) {
            $notifications  = $this->crud->getSpecific('notifications', 'user_id', '=', $userId, 'ORDER BY created_at DESC');
            if ($notifications) {
                return $this->_data = $notifications;
            }
            return false;
        } else {
            return false;
        }
    }

    public function markAsRead($id)
    {
        return $this->updateNotification(['is_read' => true], $id);
    }

    public function data()
    {
        return $this->_data;
    }
}
