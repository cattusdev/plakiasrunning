<?php
class Crud
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add($table, $data)
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        return $this->db->query($sql, $data);
    }

    public function update($table, $data, $where)
    {
        $setFields = [];
        foreach ($data as $field => $value) {
            $setFields[] = "$field = :$field";
        }
        $setFieldsStr = implode(', ', $setFields);

        $whereFields = [];
        foreach ($where as $field => $value) {
            $whereFields[] = "$field = :where_$field";
            $data["where_$field"] = $value;
        }
        $whereStr = implode(' AND ', $whereFields);

        $sql = "UPDATE $table SET $setFieldsStr WHERE $whereStr";

        return $this->db->query($sql, $data);
    }

    public function delete($table, $where)
    {
        $whereFields = [];
        foreach ($where as $field => $value) {
            $whereFields[] = "$field = :where_$field";
            $data["where_$field"] = $value;
        }
        $whereStr = implode(' AND ', $whereFields);

        $sql = "DELETE FROM $table WHERE $whereStr";

        return $this->db->query($sql, $data);
    }

    public function getSpecific($table, $column, $operator, $value)
    {
        $allowedOperators = ['=', '<', '>', '<=', '>=', 'LIKE'];

        if (!in_array($operator, $allowedOperators)) {
            throw new InvalidArgumentException("Invalid operator: $operator");
        }

        $sql = "SELECT * FROM $table WHERE $column $operator :value";
        $result = $this->db->query($sql, ['value' => $value]);

        if (count($result) === 1) {
            return $result[0];
        }

        return null;
    }



    public function getByCondition($table, $conditions)
    {
        $whereConditions = [];
        $data = [];

        foreach ($conditions as $field => $value) {
            $whereConditions[] = "$field = :$field";
            $data[$field] = $value;
        }

        $whereStr = implode(' AND ', $whereConditions);
        $sql = "SELECT * FROM $table WHERE $whereStr";

        return $this->db->query($sql, $data);
    }

    public function getAll($table)
    {
        $sql = "SELECT * FROM $table";
        return $this->db->query($sql);
    }


    public function getAllPaginated($table, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM $table LIMIT $perPage OFFSET $offset";
        return $this->db->query($sql);
    }

    public function getAllSorted($table, $sortBy, $sortOrder = 'ASC')
    {
        $sql = "SELECT * FROM $table ORDER BY $sortBy $sortOrder";
        return $this->db->query($sql);
    }

    //EXAMPLES

    // Get all records from 'posts'
    // $allPosts = $crud->getAll('posts');

    // // Get specific post by title
    // $specificPost = $crud->getSpecific('posts', 'title', '=', 'Post Title');

    // // Get all posts with specific conditions
    // $filteredPosts = $crud->getByCondition('posts', ['category' => 'Technology', 'status' => 'published']);

    // // Get paginated posts (page 2, 10 per page)
    // $paginatedPosts = $crud->getAllPaginated('posts', 2, 10);

    // // Get all posts sorted by date descending
    // $sortedPosts = $crud->getAllSorted('posts', 'date', 'DESC');



    //CUSTOM QUERY WITH PARAMS

    // function fetchVehiclesWith($id)
    // {

    //     $sql = "SELECT * FROM vehicles
    //     LEFT JOIN vehicle_categories ON vehicle_categories.id = vehicles.vehicle_category 
    //     LEFT JOIN vehicle_types ON vehicle_types.id = vehicles.vehicle_type
    //     WHERE vehicles.id = :vehicleID";
    //     $params = [
    //         ':vehicleID' => $id
    //     ];
    //     $result = $this->db->query($sql, $params);

    //     if ($result) {
    //         return $this->_data = $result;
    //     }
    //     return false;
    // }

    //Ussage
    //  $test = new Vehicles();
    //  $testv = $test->fetchVehiclesWith(2);
    //  echo $testv[0]->vehicle_title;


}
