<?php
class Bookings
{
    private $db;
    private $crud;
    private $_data;
    private $_lastInsertedID;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
    }

    /**
     * FETCH ΓΙΑ DATATABLE
     */
    public function fetchJoinedBookings()
    {
        $sql = "SELECT 
                    b.id AS booking_id,
                    b.status AS booking_status,
                    b.appointment_type,
                    b.start_datetime,
                    b.end_datetime,
                    b.attendees_count, -- NEW FIELD
                    b.created_at,
                    b.notes,
                    
                    c.id AS client_id,
                    c.first_name AS client_fname,
                    c.last_name AS client_lname,
                    c.phone AS client_phone,
                    
                    t.id AS therapist_id,
                    t.first_name AS therapist_fname,
                    t.last_name AS therapist_lname,
                    
                    p.id AS package_id,
                    p.title AS package_title,
                    p.is_group
                FROM bookings b
                JOIN clients c ON b.client_id = c.id
                LEFT JOIN therapists t ON b.therapist_id = t.id
                LEFT JOIN packages p ON b.package_id = p.id
                ORDER BY b.start_datetime DESC";

        $results = $this->db->query($sql);
        return $results ?: [];
    }

    /**
     * ΛΕΠΤΟΜΕΡΕΙΕΣ ΚΡΑΤΗΣΗΣ
     */
    public function getBookingDetails($id)
    {
        $sql = "SELECT b.*, 
                       c.first_name as c_fname, c.last_name as c_lname, c.phone as c_phone,
                       t.first_name as t_fname, t.last_name as t_lname,
                       p.title as package_title, p.max_attendants, p.is_group
                FROM bookings b
                LEFT JOIN clients c ON b.client_id = c.id
                LEFT JOIN therapists t ON b.therapist_id = t.id
                LEFT JOIN packages p ON b.package_id = p.id
                WHERE b.id = :id";

        $res = $this->db->query($sql, [':id' => $id]);

        if ($res && count($res) > 0) {
            $this->_data = $res[0];
            return $this->_data;
        }
        return false;
    }

    /**
     * [ΝΕΟ] ΥΠΟΛΟΓΙΣΜΟΣ ΣΥΜΜΕΤΟΧΩΝ ΣΕ ΧΡΟΝΙΚΟ ΕΥΡΟΣ
     * Επιστρέφει το άθροισμα των ατόμων που έχουν κλείσει σε επικαλυπτόμενο διάστημα.
     */
    public function getCapacityUsage($therapistId, $start, $end, $excludeBookingId = null)
    {
        // Ψάχνουμε κρατήσεις που πέφτουν πάνω στο slot μας
        $sql = "SELECT SUM(b.attendees_count) as total_pax
                FROM bookings b
                LEFT JOIN packages p ON b.package_id = p.id
                WHERE b.therapist_id = :tid 
                AND b.status != 'canceled'
                AND (
                    -- Logic: Overlap
                    :start < DATE_ADD(b.end_datetime, INTERVAL IFNULL(p.buffer_minutes, 0) MINUTE)
                    AND 
                    :end > b.start_datetime
                )";

        $params = [':tid' => $therapistId, ':start' => $start, ':end' => $end];

        if ($excludeBookingId) {
            $sql .= " AND b.id != :bid";
            $params[':bid'] = $excludeBookingId;
        }

        $res = $this->db->query($sql, $params);
        return ($res && $res[0]->total_pax) ? (int)$res[0]->total_pax : 0;
    }

    // --- CRUD METHODS ---

    public function createBooking($data)
    {
        $result = $this->crud->add('bookings', $data);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    public function updateBooking($id, $data)
    {
        $result = $this->crud->update('bookings', $data, ['id' => $id]);
        return ($result);
    }

    public function deleteBooking($id)
    {
        $result = $this->crud->delete('bookings', ['id' => $id]);
        return isset($result['affected_rows']) && $result['affected_rows'] > 0;
    }

    public function fetchBooking($id)
    {
        return $this->crud->getSpecific('bookings', 'id', '=', $id);
    }
}
