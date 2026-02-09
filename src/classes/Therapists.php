<?php
class Therapists
{
    private $db;
    private $crud;
    private $_data;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
    }

    // Add
    public function addTherapist($fields = [])
    {
        $result = $this->crud->add('therapists', $fields);
        if ($result['affected_rows'] > 0) {
            return true;
        }
        return false;
    }

    // Update
    public function updateTherapist($fields, $id)
    {
        $where = ['id' => $id];
        $result = $this->crud->update('therapists', $fields, $where);
        // Επιστρέφουμε true ακόμα και αν δεν άλλαξαν rows (π.χ. ίδιο update) για να μην βγάζει error
        return isset($result['affected_rows']);
    }

    // Delete
    public function deleteTherapist($id)
    {
        $where = ['id' => $id];
        $result = $this->crud->delete('therapists', $where);
        return isset($result['affected_rows']) && $result['affected_rows'] > 0;
    }

    // Fetch One
    public function fetchTherapist($id)
    {
        return $this->crud->getSpecific('therapists', 'id', '=', $id);
    }

    // Fetch All
    public function fetchTherapists()
    {
        $sql = "SELECT * FROM therapists ORDER BY last_name ASC, first_name ASC";
        $results = $this->db->query($sql);
        if ($results) {
            return $this->_data = $results;
        }
        return false;
    }

    public function data()
    {
        return $this->_data;
    }
}
