<?php
/**
 * Database Connection Test Script
 * 
 * This script tests the database connection to help troubleshoot connection issues.
 */

echo "🔍 Testing Database Connection...\n\n";

// Load configuration
$configFile = __DIR__ . '/config/config.php';
if (!file_exists($configFile)) {
    die("❌ Configuration file not found: {$configFile}\n");
}

$config = require $configFile;
$dbConfig = $config['database'];

echo "📋 Database Configuration:\n";
echo "   Host: {$dbConfig['host']}\n";
echo "   Database: {$dbConfig['dbname']}\n";
echo "   Username: {$dbConfig['username']}\n";
echo "   Password: " . (empty($dbConfig['password']) ? '[empty]' : '[set]') . "\n";
echo "   Charset: {$dbConfig['charset']}\n\n";

try {
    echo "🔌 Attempting to connect to database...\n";
    
    // Use utf8mb4 for better compatibility
    $charset = $dbConfig['charset'] ?? 'utf8mb4';
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$charset}";
    
    $options = $dbConfig['options'] ?? [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $connection = new PDO(
        $dsn,
        $dbConfig['username'],
        $dbConfig['password'],
        $options
    );
    
    echo "✅ Successfully connected to the database!\n\n";
    
    // Test a simple query
    echo "🧪 Running test query...\n";
    $stmt = $connection->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "   MySQL Version: {$result['version']}\n\n";
    
    // Check if required tables exist
    echo "📋 Checking required tables...\n";
    $requiredTables = [
        'users', 'categories', 'suppliers', 'products', 'customers',
        'sales', 'sale_items', 'debts', 'debt_payments', 'stock_movements'
    ];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $connection->query("SHOW TABLES LIKE '{$table}'");
            $exists = $stmt->fetch();
            echo "   " . ($exists ? "✅" : "❌") . " {$table}\n";
        } catch (Exception $e) {
            echo "   ❌ {$table} - Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 Database connection test completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    
    echo "🔧 Troubleshooting steps:\n";
    echo "   1. Make sure XAMPP is running and MySQL service is started\n";
    echo "   2. Check if the database 'inventory_management' exists\n";
    echo "   3. Verify database credentials in config/config.php\n";
    echo "   4. Check if MySQL is running on port 3306\n";
    echo "   5. Try restarting XAMPP services\n\n";
    
    exit(1);
} catch (Exception $e) {
    echo "❌ Unexpected error: " . $e->getMessage() . "\n";
    exit(1);
}
?>