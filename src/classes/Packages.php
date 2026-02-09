<?php
class Packages
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
     * Add a new package
     *
     * @param array $fields
     * @return bool
     */
    public function addPackage($fields = [])
    {
        $result = $this->crud->add('packages', $fields);
        if ($result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }
    

    /**
     * Get last inserted ID
     *
     * @return int
     */
    public function lastInsertedID()
    {
        return $this->_lastInsertedID;
    }

    /**
     * Bulk add packages
     *
     * @param array $packagesData
     * @return bool
     */
    public function addPackagesBulk($packagesData = [])
    {
        if (empty($packagesData)) {
            return false;
        }

        $fields = ['title', 'description', 'price', 'includes', 'type'];
        $placeholders = [];
        $bindParams = [];

        foreach ($packagesData as $package) {
            $ph = [];
            foreach ($fields as $field) {
                $ph[] = '?';
                $bindParams[] = $package[$field] ?? null;
            }
            $placeholders[] = '(' . implode(',', $ph) . ')';
        }

        $sql = "INSERT INTO packages (" . implode(',', $fields) . ") VALUES " . implode(',', $placeholders);

        $result = $this->db->query($sql, $bindParams);
        if ($result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    /**
     * Update a package
     *
     * @param array $fields
     * @param int $id
     * @return bool
     */
    public function updatePackage($fields, $id)
    {
        $where = ['id' => $id];
        $result = $this->crud->update('packages', $fields, $where);
        if ($result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    /**
     * Delete a package
     *
     * @param int $id
     * @return bool
     */
    public function deletePackage($id)
    {
        $where = ['id' => $id];
        $result = $this->crud->delete('packages', $where);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    public function exists($id)
    {
        $result = $this->crud->getSpecific('packages', 'id', '=', $id);
        return !empty($result);
    }

    /**
     * Fetch a single package by ID (Run Route)
     * Includes Category Name via JOIN
     *
     * @param int $id
     * @return object|null
     */
    public function fetchPackage($id)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM packages p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";

        $result = $this->db->query($sql, [$id]);
        return ($result && count($result) > 0) ? $result[0] : null;
    }

    /**
     * Fetch all packages (Running Routes)
     * Includes Category Name via JOIN
     *
     * @param array $conditions
     * @return array|bool
     */
    public function fetchPackages($conditions = [])
    {
        // Σημείωση: Αγνοούμε τα $conditions του Crud για τώρα για να κάνουμε σωστό Join.
        // Αν χρειαστείς φίλτρα, θα πρέπει να χτίσουμε το query δυναμικά.

        $sql = "SELECT p.*, c.name as category_name 
                FROM packages p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";

        $packages = $this->db->query($sql);

        if ($packages) {
            return $this->_data = $packages;
        }
        return false;
    }

    /**
     * Fetch packages by type
     *
     * @param string $type
     * @return array|null
     */
    public function fetchPackagesByType($type)
    {
        // Validate the type
        if (!in_array($type, ['online', 'inPerson', 'mixed'])) {
            return null;
        }

        if ($type === 'mixed') {
            // Fetch only 'mixed' packages
            $query = "SELECT * FROM packages WHERE type = :type";
            $params = [':type' => $type];
        } else {
            // Fetch 'online' or 'inPerson' packages along with 'mixed' packages
            $query = "SELECT * FROM packages WHERE type = :type OR type = 'mixed'";
            $params = [':type' => $type];
        }

        $result = $this->db->query($query, $params);

        return $result ?: null;
    }

    /**
     * Fetch includes for a package
     *
     * @param int $id
     * @return array|null
     */
    public function fetchPackageIncludes($id)
    {
        $package = $this->fetchPackage($id);
        if ($package && isset($package->includes)) {
            return json_decode($package->includes, true);
        }
        return null;
    }

    /**
     * Sync Therapists (Pivot Table)
     */
    public function syncTherapists($packageId, $therapistIds = [])
    {
        // 1. Delete old
        $this->db->query("DELETE FROM package_therapists WHERE package_id = ?", [$packageId]);

        // 2. Insert new
        if (!empty($therapistIds) && is_array($therapistIds)) {
            foreach ($therapistIds as $tId) {
                $tId = (int)$tId;
                if ($tId > 0) {
                    $this->crud->add('package_therapists', [
                        'package_id' => $packageId,
                        'therapist_id' => $tId
                    ]);
                }
            }
        }
    }

    /**
     * Get Linked Therapists IDs
     */
    public function getTherapistIds($packageId)
    {
        $sql = "SELECT therapist_id
         FROM package_therapists WHERE package_id = ?";
        $results = $this->db->query($sql, [$packageId]);
        
        $ids = [];
        if ($results) {
            foreach ($results as $row) {
                $ids[] = $row->therapist_id;
            }
        }
        return $ids;
    }

    /**
     * Fetch Packages with linked Therapist IDs
     * Χρησιμοποιείται για το φιλτράρισμα στο Bulk Create Modal
     */
    public function fetchPackagesWithTherapists()
    {
        $sql = "SELECT p.*, GROUP_CONCAT(pt.therapist_id) as linked_therapists 
                FROM packages p 
                LEFT JOIN package_therapists pt ON p.id = pt.package_id
                GROUP BY p.id
                ORDER BY p.title ASC";

        $results = $this->db->query($sql);
        return $results ?: [];
    }

    public function data()
    {
        return $this->_data;
    }
}
