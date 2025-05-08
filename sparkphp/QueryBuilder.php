<?php

namespace SparkPHP;

// Simple Query Builder for database operations
class QueryBuilder {
    protected $pdo;         // PDO instance
    protected $table;       // Table name
    protected $fields = '*';// Fields to select
    protected $where = '';  // WHERE clause
    protected $bindings = [];// Bindings for prepared statements
    protected $order = '';  // ORDER BY clause
    protected $limit = '';  // LIMIT clause
    protected $data = null; // Data for insert/update
    protected $action = null;// Current action (select, insert, update, delete)

    // Constructor: set PDO instance and table name
    public function __construct($pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    // Set fields for SELECT
    public function select($fields = '*') {
        $this->fields = $fields;
        $this->action = 'select';
        return $this;
    }

    // Add WHERE clause
    public function where($condition, $bindings = []) {
        $this->where = "WHERE $condition";
        $this->bindings = $bindings;
        return $this;
    }

    // Add ORDER BY clause
    public function orderBy($order) {
        $this->order = "ORDER BY $order";
        return $this;
    }

    // Add LIMIT clause
    public function limit($limit) {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    // Fetch all results
    public function all() {
        $sql = "SELECT {$this->fields} FROM {$this->table} {$this->where} {$this->order} {$this->limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }

    // Fetch first result
    public function first() {
        $this->limit(1);
        $result = $this->all();
        return $result[0] ?? null;
    }

    // Prepare for insert
    public function insert($data) {
        $this->action = 'insert';
        $this->data = $data;
        return $this;
    }

    // Prepare for update
    public function update($data) {
        $this->action = 'update';
        $this->data = $data;
        return $this;
    }

    // Prepare for delete
    public function delete() {
        $this->action = 'delete';
        return $this;
    }

    // Execute insert, update, or delete
    public function execute() {
        if ($this->action === 'insert') {
            if (!is_array($this->data)) throw new \Exception('Data must be an array');
            $columns = implode(', ', array_keys($this->data));
            $placeholders = implode(', ', array_fill(0, count($this->data), '?'));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($this->data));
            $lastId = $this->pdo->lastInsertId();
            return $lastId;
        } elseif ($this->action === 'update') {
            if (!is_array($this->data)) throw new \Exception('Data must be an array');
            if (!$this->where) throw new \Exception('Update requires where()');
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($this->data)));
            $sql = "UPDATE {$this->table} SET $set {$this->where}";
            $bindings = array_merge(array_values($this->data), $this->bindings);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->rowCount();
        } elseif ($this->action === 'delete') {
            if (!$this->where) throw new \Exception('Delete requires where()');
            $sql = "DELETE FROM {$this->table} {$this->where}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->rowCount();
        } else {
            throw new \Exception('No action specified');
        }
    }
}
