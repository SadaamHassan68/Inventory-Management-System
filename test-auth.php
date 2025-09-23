<?php
/**
 * Authentication Test Script
 */

require_once __DIR__ . '/bootstrap/app.php';

echo "<h2>Authentication Test</h2>";

try {
    // Test database connection first
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Test user model
    $userModel = new User();
    echo "<p>✅ User model loaded successfully</p>";
    
    // Test authentication with default admin credentials
    echo "<h3>Testing Admin Authentication</h3>";
    $result = $userModel->authenticate('admin', 'admin123');
    
    if ($result === false) {
        echo "<p>❌ Authentication failed - Invalid credentials</p>";
        
        // Check if admin user exists
        $adminUser = $userModel->findByWithPassword('username', 'admin');
        if ($adminUser) {
            echo "<p>✅ Admin user exists in database</p>";
            echo "<p>Password hash: " . substr($adminUser['password_hash'], 0, 20) . "...</p>";
            
            // Test password verification directly
            $isValidPassword = password_verify('admin123', $adminUser['password_hash']);
            echo "<p>Password verification test: " . ($isValidPassword ? '✅ Valid' : '❌ Invalid') . "</p>";
            
            if (!$isValidPassword) {
                echo "<p>⚠️ The stored password hash doesn't match 'admin123'. The database may need to be reset.</p>";
            }
        } else {
            echo "<p>❌ Admin user not found in database</p>";
            echo "<p>You need to run the database setup first.</p>";
            echo "<p><a href='database-setup.php'>Run Database Setup</a></p>";
        }
    } elseif (is_array($result) && isset($result['error'])) {
        echo "<p>⚠️ Authentication error: " . htmlspecialchars($result['error']) . "</p>";
    } else {
        echo "<p>✅ Authentication successful!</p>";
        echo "<p>User: " . htmlspecialchars($result['full_name']) . " (Role: " . htmlspecialchars($result['role']) . ")</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='public/index.php'>Go to Application</a> | <a href='database-setup.php'>Database Setup</a></p>";
?>