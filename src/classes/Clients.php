<?php
class Clients
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

    function fetchClients()
    {
        // Αντί για getAll, χρησιμοποιούμε getSpecific ή custom query
        // Υποθέτω ότι το crud->getSpecific φέρνει ένα row, οπότε ίσως χρειαστεί custom query αν το crud σου είναι limited.
        // Εδώ γράφω raw query για σιγουριά με βάση το στυλ σου:

        $sql = "SELECT * FROM clients WHERE is_active = 1 ORDER BY first_name ASC";
        $results = $this->db->query($sql);

        if ($results) {
            return $this->_data = $results;
        }
        return false;
    }

    function fetchClient($clientID)
    {
        $client  = $this->crud->getSpecific('clients', 'id', '=', $clientID);
        if ($client) {
            return $this->_data = $client;
        }
        return false;
    }

    function searchClients($term)
    {
        $term = trim($term);
        // Χρησιμοποιούμε ξεχωριστές παραμέτρους για κάθε πεδίο στο WHERE clause
        // για να αποφύγουμε το error "Invalid parameter number"
        $sql = "SELECT id, first_name, last_name, phone, email 
                FROM clients 
                WHERE is_active = 1 
                AND (
                    first_name LIKE :t1 
                    OR last_name LIKE :t2 
                    OR phone LIKE :t3 
                    OR email LIKE :t4
                )
                ORDER BY first_name ASC LIMIT 20";

        $wildcard = "%$term%";
        $params = [
            ':t1' => $wildcard,
            ':t2' => $wildcard,
            ':t3' => $wildcard,
            ':t4' => $wildcard
        ];

        $results = $this->db->query($sql, $params);

        return $results ?: [];
    }

    function fetchClientByPhone($clientID)
    {
        $client  = $this->crud->getSpecific('clients', 'phone', '=', $clientID);
        if ($client) {
            return $this->_data = $client;
        }
        return false;
    }

    function fetchClientByEmail($email)
    {
        $client  = $this->crud->getSpecific('clients', 'email', '=', $email);
        if ($client) {
            return $this->_data = $client;
        }
        return false;
    }

    function findClientByPhoneOrEmail($phone, $email)
    {
        $query = "SELECT clients.id, clients.phone FROM clients WHERE clients.phone =:phone OR  clients.email = :email";

        $params = [
            ':phone' => $phone,
            ':email'   => $email
        ];

        $results = $this->db->query($query, $params);
        if ($results) {
            return $this->_data = $results;
        }

        return false;
    }

    function clientExists($searchParam, $email = null, $returnID = false)
    {
        $exists = false;
        $client  = $this->crud->getSpecific('clients', 'phone', '=', $searchParam);
        if ($client) {
            $exists = true;
        }

        if ($email) {
            $client  = $this->crud->getSpecific('clients', 'email', '=', $email);
            if ($client) {
                $exists = true;
            }
        }

        if ($returnID) {
            return $this->_data = $client;
        }
        return $exists;
    }

    function addClient($clientData, $returnID = false)
    {
        $result = $this->crud->add('clients', $clientData);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            if ($returnID) {
                return $result['insert_id'];
            } else {
                return true;
            }
        }
        return false;
    }

    function updateClient($clientData, $clientID)
    {
        if ($this->crud->update('clients', $clientData, array('id' => $clientID))) {
            return true;
        }

        return false;
    }

    // function deleteClient($clientID)
    // {
    //     $result = $this->crud->delete('clients', array('id' => $clientID));

    //     if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    //SOFT DELETE
    function deleteClient($clientID)
    {
        // Αντί για $this->crud->delete... κάνουμε update
        $result = $this->crud->update('clients', ['is_active' => 0], ['id' => $clientID]);

        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        }
        return false;
    }

    function data()
    {
        return $this->_data;
    }
}
