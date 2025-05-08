<?php

namespace SparkPHP;

// Database connection and query builder access
class Database {
    protected $pdo; // PDO instance

    // Constructor: create PDO connection
    public function __construct($host, $user, $pass, $db) {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // Fetch associative arrays by default
        ]);
    }

    // Get a QueryBuilder for a specific table
    public function table($table) {
        return new QueryBuilder($this->pdo, $table);
    }
}
