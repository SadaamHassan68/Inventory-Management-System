<?php
/**
 * Database Connection Test Script
 * Use this to test if your database configuration is working
 */

echo "<h2>Database Connection Test</h2>";

try {
    // Load config
    $config = require __DIR__ . '/config/config.php';
    
    echo "<p>✅ Configuration loaded successfully</p>";
    
    // Test database connection
    $dsn = "mysql:host={$config['database']['host']};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $config['database']['username'],
        $config['database']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "<p>✅ Connected to MySQL server successfully</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['database']['dbname']}'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "<p>✅ Database '{$config['database']['dbname']}' exists</p>";
        
        // Connect to the specific database
        $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']};charset=utf8mb4";
        $pdo = new PDO(
            $dsn,
            $config['database']['username'],
            $config['database']['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        echo "<p>✅ Connected to application database successfully</p>";
        
        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<p>✅ Found " . count($tables) . " tables in database</p>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ No tables found. You need to run the database setup.</p>";
            echo "<p><a href='database-setup.php'>Run Database Setup</a></p>";
        }
        
    } else {
        echo "<p>⚠️ Database '{$config['database']['dbname']}' does not exist</p>";
        echo "<p><a href='database-setup.php'>Run Database Setup</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    echo "<h3>Common Solutions:</h3>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL service is running</li>";
    echo "<li>Check database credentials in config/config.php</li>";
    echo "<li>Ensure the database user has proper permissions</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='public/index.php'>Go to Application</a> | <a href='database-setup.php'>Database Setup</a></p>";
?>