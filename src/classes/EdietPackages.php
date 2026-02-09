<?php
class EdietPackages
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
        $result = $this->crud->add('ediet_packages', $fields);
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

        $fields = ['title', 'description', 'price', 'includes'];
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

        $sql = "INSERT INTO ediet_packages (" . implode(',', $fields) . ") VALUES " . implode(',', $placeholders);

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
        $result = $this->crud->update('ediet_packages', $fields, $where);
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
        $result = $this->crud->delete('ediet_packages', $where);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    public function exists($id)
    {
        $result = $this->crud->getSpecific('ediet_packages', 'id', '=', $id);
        return !empty($result);
    }

    /**
     * Fetch a single package by ID
     *
     * @param int $id
     * @return object|null
     */
    public function fetchPackage($id)
    {
        return $this->crud->getSpecific('ediet_packages', 'id', '=', $id);
    }

    /**
     * Fetch all packages or by condition
     *
     * @param array $conditions
     * @return array|bool
     */
    public function fetchPackages($conditions = [])
    {
        if (!empty($conditions)) {
            $packages = $this->crud->getByCondition('ediet_packages', $conditions);
        } else {
            $packages = $this->crud->getAll('ediet_packages');
        }
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
            $query = "SELECT * FROM ediet_packages WHERE type = :type";
            $params = [':type' => $type];
        } else {
            // Fetch 'online' or 'inPerson' packages along with 'mixed' packages
            $query = "SELECT * FROM ediet_packages WHERE type = :type OR type = 'mixed'";
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

    public function data()
    {
        return $this->_data;
    }
}
