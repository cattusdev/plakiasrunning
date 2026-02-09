<?php

class Database
{
    private static $instance;
    private $pdo;

    private function __construct()
    {
        $host = Config::get('mysql/host');
        $username = Config::get('mysql/userName');
        $password = Config::get('mysql/password');
        $database = Config::get('mysql/dbName');
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        try {
            $statement = $this->pdo->prepare($sql);

            if (!$statement) {
                throw new Exception("Error in query: " . $this->pdo->errorInfo()[2]);
            }

            // Execute the statement with parameters
            $statement->execute($params);

            // Trim whitespace and parentheses from the beginning of the SQL statement
            $trimmedSql = ltrim($sql, " \t\n\r\0\x0B(");

            if (stripos($trimmedSql, 'SELECT') === 0 || stripos($trimmedSql, 'WITH') === 0) {
                return $statement->fetchAll(PDO::FETCH_OBJ);
            } else {
                return [
                    'insert_id' => $this->pdo->lastInsertId(),
                    'affected_rows' => $statement->rowCount(),
                ];
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw new Exception("An error occurred while processing your request: " . $e->getMessage());
        }
    }

    // public function query($sql, $params = [])
    // {
    //     try {
    //         $statement = $this->pdo->prepare($sql);

    //         if (!$statement) {
    //             throw new Exception("Error in query: " . $this->pdo->errorInfo()[2]);
    //         }

    //         // Bind parameters
    //         foreach ($params as $key => $val) {
    //             $statement->bindValue($key, $val);
    //         }

    //         $statement->execute();

    //         if (stripos($sql, 'SELECT') === 0 || stripos($sql, 'WITH') === 0) {
    //             return $statement->fetchAll(PDO::FETCH_OBJ);
    //         } else {
    //             return [
    //                 'insert_id' => $this->pdo->lastInsertId(),
    //                 'affected_rows' => $statement->rowCount(),
    //             ];
    //         }
    //         return false;
    //     } catch (PDOException $e) {
    //         error_log("Database error: " . $e->getMessage());
    //         error_log("SQL: " . $sql);
    //         error_log("Params: " . json_encode($params));
    //         throw new Exception("An error occurred while processing your request: " . $e->getMessage());
    //     }
    // }

    private function isAssoc(array $arr)
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function close()
    {
        $this->pdo = null;
    }
}
