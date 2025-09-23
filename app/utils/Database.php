<?php
/**
 * Database utility class for Inventory Management System
 */
class Database {
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct() {
        $configFile = __DIR__ . '/../../config/config.php';
        if (!file_exists($configFile)) {
            throw new \Exception("Configuration file not found: {$configFile}");
        }
        
        $this->config = require $configFile;
        if (!is_array($this->config)) {
            throw new \Exception("Invalid configuration file format");
        }
        
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            // Validate config structure
            if (!isset($this->config['database'])) {
                throw new \Exception("Database configuration not found");
            }
            
            $dbConfig = $this->config['database'];
            $required = ['host', 'dbname', 'username', 'password'];
            
            foreach ($required as $key) {
                if (!isset($dbConfig[$key])) {
                    throw new \Exception("Missing database configuration: {$key}");
                }
            }
            
            // Use utf8mb4 for better compatibility
            $charset = $dbConfig['charset'] ?? 'utf8mb4';
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$charset}";
            
            $options = $dbConfig['options'] ?? [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $options
            );
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function exists($table, $where, $params = []) {
        $sql = "SELECT 1 FROM {$table} WHERE {$where} LIMIT 1";
        $result = $this->fetch($sql, $params);
        return !empty($result);
    }

    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$where}";
        $result = $this->fetch($sql, $params);
        return (int) $result['count'];
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollback() {
        return $this->connection->rollback();
    }

    public function getLastInsertId() {
        return $this->connection->lastInsertId();
    }

    // Pagination helper
    public function paginate($table, $page = 1, $perPage = 20, $where = '1=1', $params = [], $orderBy = 'id DESC') {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $totalCount = $this->count($table, $where, $params);
        
        // Get data - use direct values for LIMIT and OFFSET
        $sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->fetchAll($sql, $params);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalCount,
            'total_pages' => ceil($totalCount / $perPage),
            'has_next' => $page < ceil($totalCount / $perPage),
            'has_prev' => $page > 1
        ];
    }

    // Search helper
    public function search($table, $searchColumns, $searchTerm, $where = '1=1', $params = [], $orderBy = 'id DESC', $limit = 50) {
        $searchConditions = [];
        $searchParams = [];
        
        foreach ($searchColumns as $column) {
            $searchConditions[] = "{$column} LIKE :search_{$column}";
            $searchParams["search_{$column}"] = "%{$searchTerm}%";
        }
        
        $searchWhere = '(' . implode(' OR ', $searchConditions) . ')';
        $finalWhere = $where . ' AND ' . $searchWhere;
        $finalParams = array_merge($params, $searchParams);
        
        // Use direct value for LIMIT
        $sql = "SELECT * FROM {$table} WHERE {$finalWhere} ORDER BY {$orderBy} LIMIT {$limit}";
        return $this->fetchAll($sql, $finalParams);
    }

    public function __destruct() {
        $this->connection = null;
    }
}