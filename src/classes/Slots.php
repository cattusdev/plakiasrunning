<?php
class Slots
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
     * Add a new slot (single)
     *
     * @param array $fields
     * @return bool
     */
    public function addSlot($fields = [], $packageIds = [])
    {
        $result = $this->crud->add('slots', $fields);
        if ($result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];

            // Add package associations
            if (!empty($packageIds)) {
                foreach ($packageIds as $packageId) {
                    $packageResult = $this->crud->add('slot_packages', [
                        'slot_id' => $this->_lastInsertedID,
                        'package_id' => $packageId,
                    ]);

                    if (!$packageResult) {
                        // Handle insertion failure, possibly rollback
                        $response['errors'][] = "Failed to associate package ID $packageId.";
                        return false;
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Get last Inserted id in the db
     *
     * * @return int
     */
    public function lastInsertedID()
    {
        return $this->_lastInsertedID;
    }

    /**
     * Bulk Add (multi-row insert) with Therapist Support
     */
    public function addSlotsBulk($slotsData = [], $packageIds = [], $therapistId = 1)
    {
        if (empty($slotsData)) {
            return false;
        }

        // Προσθέσαμε το 'therapist_id' στα πεδία
        $fields = ['therapist_id', 'start_datetime', 'end_datetime', 'status', 'appointment_type', 'notes'];

        $placeholders = [];
        $bindParams   = [];

        foreach ($slotsData as $slot) {
            $ph = [];

            // 1. Therapist ID (Πρώτο πεδίο)
            $ph[] = '?';
            $bindParams[] = $therapistId;

            // 2. Τα υπόλοιπα πεδία
            $columnsToCheck = ['start_datetime', 'end_datetime', 'status', 'appointment_type', 'notes'];
            foreach ($columnsToCheck as $col) {
                $ph[] = '?';
                $bindParams[] = isset($slot[$col]) ? $slot[$col] : null;
            }

            $placeholders[] = '(' . implode(',', $ph) . ')';
        }

        // Multi-row INSERT
        $sql = "INSERT INTO slots (" . implode(',', $fields) . ") VALUES " . implode(',', $placeholders);
        $result = $this->db->query($sql, $bindParams);

        if ($result['affected_rows'] > 0) {
            $lastInsertedID = (int)$result['insert_id'];
            $totalRows      = $result['affected_rows'];

            // Calculate ID range to link packages
            $startId = $lastInsertedID;
            $endId   = $lastInsertedID + $totalRows - 1;

            // Fetch IDs (Safety check)
            $query = "SELECT id FROM slots WHERE id BETWEEN :start_id AND :end_id ORDER BY id ASC";
            $params = [':start_id' => $startId, ':end_id' => $endId];
            $slotIds = $this->db->query($query, $params);

            // Link Packages
            if (!empty($packageIds) && !empty($slotIds)) {
                foreach ($slotIds as $slotObj) {
                    foreach ($packageIds as $packageId) {
                        $this->crud->add('slot_packages', [
                            'slot_id'    => $slotObj->id,
                            'package_id' => $packageId
                        ]);
                    }
                }
            }

            return true;
        }

        return false;
    }



    public function fetchSlotsWithPackages_($conditions = [])
    {
        $query = "SELECT 
            s.*, 
            GROUP_CONCAT(p.title) AS package_titles 
        FROM slots s
        LEFT JOIN slot_packages sp ON s.id = sp.slot_id
        LEFT JOIN packages p ON sp.package_id = p.id
        GROUP BY s.id
    ";

        $slots = $this->db->query($query);
        return $slots ?: [];
    }

    public function fetchSlotsWithPackages($conditions = [])
    {
        // Build WHERE clause based on conditions
        $whereClauses = [];
        $params = [];

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if (strpos($key, ' IN') !== false && is_array($value)) {
                    // Handle IN clauses
                    $column = str_replace(' IN', '', $key);
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $whereClauses[] = "$column IN ($placeholders)";
                    $params = array_merge($params, $value);
                } elseif (preg_match('/^(.*?)\s*(>=|<=|!=|=|>|<|LIKE)$/', $key, $matches)) {
                    // Handle other operators
                    $column = $matches[1];
                    $operator = $matches[2];
                    $whereClauses[] = "$column $operator ?";
                    $params[] = $value;
                } else {
                    // Default to '=' operator
                    $whereClauses[] = "$key = ?";
                    $params[] = $value;
                }
            }
        }

        $where = '';
        if (!empty($whereClauses)) {
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
        }

        // Single query to fetch slots and their packages
        $query = "SELECT 
            s.id AS slot_id, 
            s.start_datetime, 
            s.end_datetime, 
            s.status, 
            s.appointment_type, 
            s.notes,
            p.id AS package_id, 
            p.title AS package_title
        FROM 
            slots s
        LEFT JOIN 
            slot_packages sp ON s.id = sp.slot_id
        LEFT JOIN 
            packages p ON sp.package_id = p.id
        $where
        ORDER BY 
            s.start_datetime ASC
    ";

        $results = $this->db->query($query, $params);
        error_log("Combined Query Result: " . json_encode($results));

        if (!$results) {
            return [];
        }

        // Organize data into slots with packages
        $slots = [];
        foreach ($results as $row) {
            if (!isset($slots[$row->slot_id])) {
                $slots[$row->slot_id] = [
                    'id'               => $row->slot_id,
                    'start_datetime'   => $row->start_datetime,
                    'end_datetime'     => $row->end_datetime,
                    'status'           => $row->status,
                    'appointment_type' => $row->appointment_type,
                    'notes'            => $row->notes,
                    'packages'         => []
                ];
            }

            if ($row->package_id) { // Only add if package_id is not null
                $slots[$row->slot_id]['packages'][] = [
                    'id'    => $row->package_id,
                    'title' => $row->package_title
                ];
            }
        }

        // Convert associative array to indexed array
        $finalSlots = array_values($slots);
        error_log("Final Organized Slots: " . json_encode($finalSlots));

        return $finalSlots;
    }

    /**
     * Update a slot
     *
     * @param array $fields
     * @param int $id
     * @return bool
     */
    public function updateSlot($fields, $id, $packageIds = [])
    {
        // Update the slot data
        $where = ['id' => $id];
        $result = $this->crud->update('slots', $fields, $where);

        if ($result !== null) {
            // Handle package associations
            if (!empty($packageIds)) {
                // Delete existing associations
                $deleteResult = $this->db->query("DELETE FROM slot_packages WHERE slot_id = ?", [$id]);
                if ($deleteResult === false) {
                    // Handle deletion failure
                    return false;
                }

                // Reinsert new package associations
                foreach ($packageIds as $packageId) {
                    $packageResult = $this->crud->add('slot_packages', [
                        'slot_id'    => $id,
                        'package_id' => $packageId,
                    ]);

                    if (!$packageResult) {
                        // Handle insertion failure, possibly rollback
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Delete a slot
     *
     * @param int $id
     * @return bool
     */
    public function deleteSlot($id)
    {
        $where = ['id' => $id];
        $result = $this->crud->delete('slots', $where);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    /**
     * Fetch a single slot by ID
     *
     * @param int $id
     * @return object|null
     */
    public function fetchSlot($id)
    {
        return $this->crud->getSpecific('slots', 'id', '=', $id);
    }

    /**
     * Fetch bookings for a given slot ID
     *
     * @param int $slotId
     * @return array|null
     */
    public function getBookingsForSlot($slotId)
    {
        if (!is_numeric($slotId)) {
            return null;
        }

        $query = "SELECT 
        b.id AS booking_id,
        b.client_id,
        b.status AS booking_status,
        b.created_at AS booking_created,
        c.first_name AS client_first_name,
        c.last_name AS client_last_name,
        c.email AS client_email,
        s.start_datetime,
        s.end_datetime,
        s.notes AS slot_notes,
        s.appointment_type,
        p.id AS package_id,
        p.title AS package_title,
        p.description AS package_description,
        p.price AS package_price,
        p.type AS package_type
    FROM bookings b
    LEFT JOIN clients c ON b.client_id = c.id
    LEFT JOIN slots s ON b.slot_id = s.id
    LEFT JOIN packages p ON b.package_id = p.id
    WHERE b.slot_id = :slot_id
    ";

        $params = [':slot_id' => $slotId];
        $results = $this->db->query($query, $params);

        return $results ?: null;
    }

    /**
     * Fetch all slots or by condition
     *
     * @param array $conditions
     * @return array|bool
     */
    public function fetchSlots($conditions = [])
    {
        if (!empty($conditions)) {
            $slots = $this->crud->getByCondition('slots', $conditions);
        } else {
            $slots = $this->crud->getAll('slots');
        }
        if ($slots) {
            return $this->_data = $slots;
        }
        return false;
    }

    public function fetchAllSlots()
    {
        $sql = "SELECT * FROM slots ORDER BY start_datetime ASC";
        $results = $this->db->query($sql);
        return $results ?: [];
    }

    /**
     * Fetch slots within a specified date range.
     *
     * @param string $start "YYYY-MM-DD HH:MM:SS"
     * @param string $end "YYYY-MM-DD HH:MM:SS"
     * @return array|bool
     */

    public function fetchSlotsRange($start, $end)
    {

        $query = "SELECT * FROM slots WHERE start_datetime BETWEEN :start AND :end ORDER BY start_datetime ASC";

        $params = [
            ':start'   => $start,
            ':end' => $end
        ];
        $results = $this->db->query($query, $params);

        return $results ?: null;
    }

    public function fetchSlotsRangeWithPackage($start, $end, $packageId)
    {
        $query = "SELECT DISTINCT s.*
        FROM slots s
        INNER JOIN slot_packages sp ON s.id = sp.slot_id
        WHERE s.start_datetime BETWEEN :start AND :end
          AND sp.package_id = :package_id
        ORDER BY s.start_datetime ASC
    ";

        $params = [
            ':start' => $start,
            ':end'   => $end,
            ':package_id' => $packageId
        ];

        $results = $this->db->query($query, $params);

        return $results ?: null;
    }

    public function fetchAvailableSlotsRangeWithPackage($start, $end, $packageId, $type)
    {
        // Only fetch slots whose status is "available"
        // Return minimal columns
        $query = "SELECT 
                s.id,
                s.start_datetime,
                s.end_datetime
            FROM slots s
            INNER JOIN slot_packages sp ON s.id = sp.slot_id
            WHERE s.start_datetime BETWEEN :start AND :end
              AND sp.package_id = :package_id
              AND s.status = 'available'
              AND (s.appointment_type = :package_type OR s.appointment_type = 'mixed')
            ORDER BY s.start_datetime ASC
        ";

        $params = [
            ':start'         => $start,
            ':end'           => $end,
            ':package_id'    => $packageId,
            ':package_type'  => $type
        ];

        $results = $this->db->query($query, $params);

        return $results ?: []; // Return an empty array if no results
    }

    public function countPastEmptySlots()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM slots
                WHERE status = 'available'
                  AND end_datetime < NOW()";

        // This will return an array of objects (each row => an object).
        $results = $this->db->query($sql);
        // Example of $results:
        // [
        //   (object) ["total" => "17"]
        // ]

        if (!$results || !isset($results[0])) {
            return 0;
        }
        // Because fetchAll(PDO::FETCH_OBJ) returns an object with ->total
        return (int) $results[0]->total;
    }


    public function deletePastEmptySlots()
    {
        $sql = "DELETE FROM slots
                WHERE status = 'available'
                  AND end_datetime < NOW()";

        // Since this is a non-SELECT statement, $this->db->query($sql)
        // will return something like:
        // [ 'insert_id' => '0', 'affected_rows' => 12 ]
        $result = $this->db->query($sql);

        // Check if 'affected_rows' is set. That's how many rows were deleted.
        if (isset($result['affected_rows'])) {
            return (int) $result['affected_rows'];
        }
        return 0;
    }



    /**
     * Custom query example to check conflict
     *
     * @param string $candidateStart
     * @param string $candidateEnd
     * @return int
     */
    public function checkConflict($candidateStart, $candidateEnd)
    {
        $sql = "SELECT COUNT(*) as cnt
                  FROM slots
                 WHERE start_datetime < :candidateEnd
                   AND end_datetime   > :candidateStart
                   AND status <> 'other' "; // or 'available' etc.
        $params = [
            ':candidateEnd'   => $candidateEnd,
            ':candidateStart' => $candidateStart
        ];
        $result = $this->db->query($sql, $params);
        if ($result && count($result) > 0) {
            return $result[0]->cnt;
        }
        return 0;
    }


    public function deleteOverlap($candidateStart, $candidateEnd)
    {
        // For example: remove any slot that overlaps
        $sql = "DELETE FROM slots
                WHERE start_datetime < :candidateEnd
                  AND end_datetime   > :candidateStart";
        $params = [
            ':candidateEnd'   => $candidateEnd,
            ':candidateStart' => $candidateStart
        ];
        $this->db->query($sql, $params);
    }

    /**
     * ΕΥΡΕΣΗ ΔΙΑΘΕΣΙΜΩΝ ΩΡΩΝ ΒΑΣΕΙ ΔΙΑΡΚΕΙΑΣ (Smart Matchmaking)
     * Ελέγχει ποια slots μπορούν να φιλοξενήσουν ένα ραντεβού συγκεκριμένης διάρκειας.
     * * @param string $date "YYYY-MM-DD"
     * @param int $durationMinutes
     * @return array Λίστα με available start times
     */
    public function findAvailableStartTimes($date, $durationMinutes)
    {
        // 1. Φέρνουμε όλα τα 'available' slots της ημέρας (πιθανά σημεία εκκίνησης)
        $startOfDay = $date . ' 00:00:00';
        $endOfDay   = $date . ' 23:59:59';

        $query = "SELECT * FROM slots 
                  WHERE start_datetime BETWEEN :startDay AND :endDay 
                  AND status = 'available'
                  ORDER BY start_datetime ASC";

        $params = [
            ':startDay' => $startOfDay,
            ':endDay'   => $endOfDay
        ];

        $potentialSlots = $this->db->query($query, $params);

        if (empty($potentialSlots)) {
            return [];
        }

        $validStartTimes = [];

        // 2. Για κάθε available slot, ελέγχουμε αν "χωράει" το πακέτο
        foreach ($potentialSlots as $slot) {
            $startTime = strtotime($slot->start_datetime);
            $endTime   = $startTime + ($durationMinutes * 60);

            // Format για τη βάση
            $startStr = date('Y-m-d H:i:s', $startTime);
            $endStr   = date('Y-m-d H:i:s', $endTime);

            // 3. Check Conflict: Υπάρχει κάποιο booked/other slot που παρεμβάλλεται;
            // ΠΡΟΣΟΧΗ: Ελέγχουμε αν υπάρχει slot που ΔΕΝ είναι available μέσα στο εύρος
            // (start < candidateEnd) AND (end > candidateStart)
            $conflictQuery = "SELECT COUNT(*) as cnt 
                              FROM slots 
                              WHERE status != 'available' 
                              AND start_datetime < :end 
                              AND end_datetime > :start";

            $conflictParams = [
                ':start' => $startStr,
                ':end'   => $endStr
            ];

            $conflictResult = $this->db->query($conflictQuery, $conflictParams);

            // --- PATCH START: Check Conflict με Group Events ---
            $hasGroupConflict = $this->checkGroupEventConflict($startStr, $endStr);
            // --- PATCH END ---

            // 4. Αν δεν υπάρχει σύγκρουση, το προσθέτουμε στη λίστα
            if ($conflictResult && $conflictResult[0]->cnt == 0 && !$hasGroupConflict) {
                // (Προαιρετικό: Θα μπορούσαμε να ελέγξουμε και αν ξεπερνάει το $endOfDay)

                $validStartTimes[] = [
                    'slot_id' => $slot->id,
                    'start'   => $slot->start_datetime,
                    'end'     => $endStr
                ];
            }
        }

        return $validStartTimes;
    }

    /**
     * Ελέγχει αν υπάρχει Group Event (από τον πίνακα packages) που επικαλύπτει το διάστημα.
     */
    public function checkGroupEventConflict($startStr, $endStr)
    {
        // Ψάχνουμε πακέτα που είναι groups (is_group=1) και έχουν ορισμένη ώρα έναρξης
        // Θεωρούμε αυθαίρετα ότι το event διαρκεί όσο το duration_minutes του πακέτου
        $sql = "SELECT COUNT(*) as cnt
                FROM packages
                WHERE is_group = 1 
                  AND start_datetime IS NOT NULL
                  AND start_datetime < :end
                  AND DATE_ADD(start_datetime, INTERVAL duration_minutes MINUTE) > :start";

        $params = [
            ':start' => $startStr,
            ':end'   => $endStr
        ];

        $result = $this->db->query($sql, $params);
        if ($result && count($result) > 0) {
            return $result[0]->cnt > 0;
        }
        return false;
    }

    public function data()
    {
        return $this->_data;
    }
}
