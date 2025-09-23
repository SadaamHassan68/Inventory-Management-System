<?php
/**
 * Fix Admin Password Script
 * This script ensures the admin user has the correct password hash
 */

require_once __DIR__ . '/bootstrap/app.php';

echo "<h2>Admin Password Fix</h2>";

try {
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Check current admin user
    $adminUser = $db->fetch("SELECT * FROM users WHERE username = 'admin'");
    
    if (!$adminUser) {
        echo "<p>❌ Admin user not found. Creating admin user...</p>";
        
        // Create admin user
        $correctHash = password_hash('admin123', PASSWORD_DEFAULT);
        $db->insert('users', [
            'username' => 'admin',
            'email' => 'admin@inventory.com',
            'password_hash' => $correctHash,
            'full_name' => 'System Administrator',
            'role' => 'admin',
            'is_active' => true
        ]);
        
        echo "<p>✅ Admin user created successfully</p>";
    } else {
        echo "<p>✅ Admin user found</p>";
        
        // Test current password
        if (password_verify('admin123', $adminUser['password_hash'])) {
            echo "<p>✅ Admin password is correct</p>";
        } else {
            echo "<p>⚠️ Admin password hash is incorrect. Fixing...</p>";
            
            // Update password hash
            $correctHash = password_hash('admin123', PASSWORD_DEFAULT);
            $db->update('users', ['password_hash' => $correctHash], 'username = :username', ['username' => 'admin']);
            
            echo "<p>✅ Admin password hash fixed</p>";
        }
    }
    
    // Test authentication
    echo "<h3>Testing Authentication</h3>";
    $userModel = new User();
    $result = $userModel->authenticate('admin', 'admin123');
    
    if ($result && is_array($result) && !isset($result['error'])) {
        echo "<p>✅ Authentication successful!</p>";
        echo "<p>User: " . htmlspecialchars($result['full_name']) . " (Role: " . htmlspecialchars($result['role']) . ")</p>";
    } else {
        echo "<p>❌ Authentication still failing</p>";
        if (is_array($result) && isset($result['error'])) {
            echo "<p>Error: " . htmlspecialchars($result['error']) . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='public/index.php'>Go to Application</a> | <a href='test-auth.php'>Test Authentication</a></p>";
?>