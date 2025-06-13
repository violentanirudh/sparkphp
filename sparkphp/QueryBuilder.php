<?php

namespace SparkPHP;

class QueryBuilder {
    protected $pdo;
    protected $table;
    protected $fields = '*';
    protected $where = '';
    protected $bindings = [];
    protected $order = '';
    protected $limit = '';
    protected $offset = ''; // <-- Add this line
    protected $data = null;
    protected $action = null;

    public function __construct($pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function select($fields = '*') {
        $this->fields = $fields;
        $this->action = 'select';
        return $this;
    }

    public function where($condition, $bindings = []) {
        $this->where = "WHERE $condition";
        $this->bindings = $bindings;
        return $this;
    }

    public function order($order) {
        $this->order = "ORDER BY $order";
        return $this;
    }

    public function limit($limit) {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    // Add offset method
    public function offset($offset) {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    public function all() {
        $sql = "SELECT {$this->fields} FROM {$this->table} {$this->where} {$this->order} {$this->limit} {$this->offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }

    public function first() {
        $this->limit(1);
        $result = $this->all();
        return $result[0] ?? null;
    }

    public function insert($data) {
        $this->action = 'insert';
        $this->data = $data;
        return $this;
    }

    public function update($data) {
        $this->action = 'update';
        $this->data = $data;
        return $this;
    }

    public function delete() {
        $this->action = 'delete';
        return $this;
    }

    public function query($sql, $bindings = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

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
