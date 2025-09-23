<?php
/**
 * Base Model class for Inventory Management System
 */
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Create a new record
    public function create($data) {
        $filteredData = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert($this->table, $filteredData);
        return $this->find($id);
    }

    // Find a record by ID
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = $this->db->fetch($sql, ['id' => $id]);
        return $result ? $this->hideFields($result) : null;
    }

    // Find a record by specific field
    public function findBy($field, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        $result = $this->db->fetch($sql, ['value' => $value]);
        return $result ? $this->hideFields($result) : null;
    }

    // Get all records
    public function all($orderBy = null) {
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        $results = $this->db->fetchAll($sql);
        return array_map([$this, 'hideFields'], $results);
    }

    // Get records with pagination
    public function paginate($page = 1, $perPage = 20, $where = '1=1', $params = [], $orderBy = null) {
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        $result = $this->db->paginate($this->table, $page, $perPage, $where, $params, $orderBy);
        $result['data'] = array_map([$this, 'hideFields'], $result['data']);
        return $result;
    }

    // Update a record
    public function update($id, $data) {
        $filteredData = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $where = "{$this->primaryKey} = :id";
        $whereParams = ['id' => $id];
        
        $affectedRows = $this->db->update($this->table, $filteredData, $where, $whereParams);
        return $affectedRows > 0 ? $this->find($id) : null;
    }

    // Delete a record
    public function delete($id) {
        $where = "{$this->primaryKey} = :id";
        $params = ['id' => $id];
        return $this->db->delete($this->table, $where, $params) > 0;
    }

    // Soft delete (if the table has is_active column)
    public function softDelete($id) {
        return $this->update($id, ['is_active' => false]);
    }

    // Check if record exists
    public function exists($id) {
        $where = "{$this->primaryKey} = :id";
        $params = ['id' => $id];
        return $this->db->exists($this->table, $where, $params);
    }

    // Count records
    public function count($where = '1=1', $params = []) {
        return $this->db->count($this->table, $where, $params);
    }

    // Search records
    public function search($searchColumns, $searchTerm, $where = '1=1', $params = [], $orderBy = null, $limit = 50) {
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        $results = $this->db->search($this->table, $searchColumns, $searchTerm, $where, $params, $orderBy, $limit);
        return array_map([$this, 'hideFields'], $results);
    }

    // Get records by specific condition
    public function where($field, $operator, $value, $orderBy = null) {
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        $sql = "SELECT * FROM {$this->table} WHERE {$field} {$operator} :value ORDER BY {$orderBy}";
        $results = $this->db->fetchAll($sql, ['value' => $value]);
        return array_map([$this, 'hideFields'], $results);
    }

    // Get records where field is in array of values
    public function whereIn($field, $values, $orderBy = null) {
        if (empty($values)) {
            return [];
        }
        
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE {$field} IN ({$placeholders}) ORDER BY {$orderBy}";
        $results = $this->db->fetchAll($sql, $values);
        return array_map([$this, 'hideFields'], $results);
    }

    // Get active records (if table has is_active column)
    public function active($orderBy = null) {
        $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
        return $this->where('is_active', '=', true, $orderBy);
    }

    // Filter data to only include fillable fields
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    // Hide sensitive fields from result
    protected function hideFields($data) {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }

    // Begin database transaction
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    // Commit database transaction
    public function commit() {
        return $this->db->commit();
    }

    // Rollback database transaction
    public function rollback() {
        return $this->db->rollback();
    }

    // Execute raw SQL query
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    // Get database instance
    public function getDb() {
        return $this->db;
    }

    // Validation helper
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $singleRule) {
                $error = $this->validateField($field, $value, $singleRule, $data);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }
        
        return $errors;
    }

    // Individual field validation
    private function validateField($field, $value, $rule, $data) {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    return "{$field} is required";
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "{$field} must be a valid email address";
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleParam) {
                    return "{$field} must be at least {$ruleParam} characters";
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleParam) {
                    return "{$field} must not exceed {$ruleParam} characters";
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return "{$field} must be a number";
                }
                break;
                
            case 'unique':
                if (!empty($value)) {
                    $table = $ruleParam ?? $this->table;
                    if ($this->db->exists($table, "{$field} = :value", ['value' => $value])) {
                        return "{$field} already exists";
                    }
                }
                break;
        }
        
        return null;
    }
}