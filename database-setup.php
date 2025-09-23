<?php
/**
 * Database Setup Script for Inventory Management System
 * This script creates the database and tables automatically
 */

// Include configuration
require_once __DIR__ . '/config/config.php';

class DatabaseSetup {
    private $config;
    private $pdo;

    public function __construct($config) {
        $this->config = $config;
    }

    public function run() {
        try {
            echo "🚀 Starting Inventory Management System Setup...\n\n";
            
            // Step 1: Connect to MySQL server (without database)
            $this->connectToServer();
            
            // Step 2: Create database if it doesn't exist
            $this->createDatabase();
            
            // Step 3: Connect to the created database
            $this->connectToDatabase();
            
            // Step 4: Run the schema file
            $this->runSchemaFile();
            
            // Step 5: Verify installation
            $this->verifyInstallation();
            
            echo "✅ Setup completed successfully!\n\n";
            echo "🌟 You can now access your Inventory Management System at:\n";
            echo "   http://localhost/inventory%20ms/public/index.php\n\n";
            echo "📝 Default login credentials:\n";
            echo "   Username: admin\n";
            echo "   Password: admin123\n\n";
            echo "🔒 Please change the default password after first login!\n";
            
        } catch (\Exception $e) {
            echo "❌ Setup failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function connectToServer() {
        echo "🔌 Connecting to MySQL server...\n";
        
        try {
            $dsn = "mysql:host={$this->config['database']['host']};charset={$this->config['database']['charset']}";
            $this->pdo = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                $this->config['database']['options']
            );
            echo "✅ Connected to MySQL server successfully\n";
        } catch (PDOException $e) {
            throw new \Exception("Failed to connect to MySQL server: " . $e->getMessage());
        }
    }

    private function createDatabase() {
        echo "🗄️  Creating database if it doesn't exist...\n";
        
        $dbName = $this->config['database']['dbname'];
        $sql = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        try {
            $this->pdo->exec($sql);
            echo "✅ Database '{$dbName}' is ready\n";
        } catch (PDOException $e) {
            throw new \Exception("Failed to create database: " . $e->getMessage());
        }
    }

    private function connectToDatabase() {
        echo "🔗 Connecting to the application database...\n";
        
        try {
            $dsn = "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['dbname']};charset={$this->config['database']['charset']}";
            $this->pdo = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                $this->config['database']['options']
            );
            echo "✅ Connected to application database successfully\n";
        } catch (PDOException $e) {
            throw new \Exception("Failed to connect to application database: " . $e->getMessage());
        }
    }

    private function runSchemaFile() {
        echo "📋 Running database schema...\n";
        
        $schemaFile = __DIR__ . '/database/schema.sql';
        
        if (!file_exists($schemaFile)) {
            throw new \Exception("Schema file not found: {$schemaFile}");
        }
        
        $schema = file_get_contents($schemaFile);
        
        // Remove USE database statement since we're already connected
        $schema = preg_replace('/USE\s+[^;]+;/', '', $schema);
        
        // Split into individual statements
        $statements = array_filter(
            array_map('trim', explode(';', $schema)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
            }
        );
        
        foreach ($statements as $statement) {
            if (trim($statement)) {
                try {
                    $this->pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore errors for statements that might already exist
                    if (strpos($e->getMessage(), 'already exists') === false && 
                        strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "⚠️  Warning: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "✅ Database schema applied successfully\n";
    }

    private function verifyInstallation() {
        echo "🔍 Verifying installation...\n";
        
        // Check if tables exist
        $requiredTables = [
            'users', 'categories', 'suppliers', 'products', 'customers',
            'sales', 'sale_items', 'debts', 'debt_payments', 'stock_movements',
            'settings', 'activity_logs'
        ];
        
        foreach ($requiredTables as $table) {
            $result = $this->pdo->query("SHOW TABLES LIKE '{$table}'")->fetch();
            if (!$result) {
                throw new \Exception("Required table '{$table}' was not created");
            }
        }
        
        // Check if admin user exists
        $admin = $this->pdo->query("SELECT * FROM users WHERE username = 'admin'")->fetch();
        if (!$admin) {
            throw new \Exception("Default admin user was not created");
        }
        
        // Verify the admin password is correct
        if (!password_verify('admin123', $admin['password_hash'])) {
            // Update with correct password hash
            $correctHash = password_hash('admin123', PASSWORD_DEFAULT);
            $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'")
                     ->execute([$correctHash]);
            echo "✅ Admin password hash corrected\n";
        }
        
        // Check if default data exists
        $categoriesCount = $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($categoriesCount == 0) {
            throw new \Exception("Default categories were not created");
        }
        
        echo "✅ Installation verified successfully\n";
    }

    public function checkRequirements() {
        echo "🔍 Checking system requirements...\n";
        
        $errors = [];
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $errors[] = "PHP 7.4 or higher is required. Current version: " . PHP_VERSION;
        }
        
        // Check PDO extension
        if (!extension_loaded('pdo')) {
            $errors[] = "PDO extension is required";
        }
        
        // Check PDO MySQL driver
        if (!extension_loaded('pdo_mysql')) {
            $errors[] = "PDO MySQL driver is required";
        }
        
        // Check write permissions
        $directories = [
            __DIR__ . '/storage/logs',
            __DIR__ . '/storage/uploads'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            if (!is_writable($dir)) {
                $errors[] = "Directory '{$dir}' is not writable";
            }
        }
        
        if (!empty($errors)) {
            echo "❌ System requirements not met:\n";
            foreach ($errors as $error) {
                echo "   • {$error}\n";
            }
            return false;
        }
        
        echo "✅ All system requirements met\n";
        return true;
    }

    public function testDatabaseConnection() {
        echo "🧪 Testing database connection...\n";
        
        try {
            $dsn = "mysql:host={$this->config['database']['host']};charset={$this->config['database']['charset']}";
            $pdo = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                $this->config['database']['options']
            );
            
            echo "✅ Database connection test successful\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            echo "\n📝 Please check your database configuration in config/config.php:\n";
            echo "   • Host: {$this->config['database']['host']}\n";
            echo "   • Username: {$this->config['database']['username']}\n";
            echo "   • Password: " . (empty($this->config['database']['password']) ? '[empty]' : '[set]') . "\n";
            return false;
        }
    }
}

// Run the setup
if (php_sapi_name() === 'cli') {
    // Command line interface
    $setup = new DatabaseSetup($config);
    
    if (!$setup->checkRequirements()) {
        exit(1);
    }
    
    if (!$setup->testDatabaseConnection()) {
        exit(1);
    }
    
    $setup->run();
} else {
    // Web interface
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management System - Setup</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="text-center">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center bg-blue-600 rounded-full">
                        <i class="fas fa-warehouse text-white text-2xl"></i>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Database Setup
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Inventory Management System
                    </p>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <?php
                    if (isset($_POST['setup'])) {
                        $setup = new DatabaseSetup($config);
                        
                        echo '<div id="output" class="font-mono text-sm space-y-2">';
                        
                        ob_start();
                        try {
                            if ($setup->checkRequirements() && $setup->testDatabaseConnection()) {
                                $setup->run();
                                echo '<div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">';
                                echo '<p class="font-bold">Setup Completed Successfully!</p>';
                                echo '<p class="mt-2"><a href="public/index.php" class="text-blue-600 hover:text-blue-800">Go to Application →</a></p>';
                                echo '</div>';
                            }
                        } catch (\Exception $e) {
                            echo '<div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">';
                            echo '<p class="font-bold">Setup Failed!</p>';
                            echo '<p class="mt-2">' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                        }
                        $output = ob_get_clean();
                        
                        echo nl2br(htmlspecialchars($output, ENT_QUOTES, 'UTF-8'));
                        echo '</div>';
                    } else {
                        ?>
                        <form method="POST">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ready to Setup Database</h3>
                                    <div class="text-sm text-gray-600 space-y-2">
                                        <p>• Creates the database and all required tables</p>
                                        <p>• Inserts default data and settings</p>
                                        <p>• Creates the default admin user</p>
                                        <p>• Sets up the complete system</p>
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-800">
                                                <strong>Warning:</strong> This will create/modify database tables. 
                                                Make sure your database configuration is correct.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    type="submit" 
                                    name="setup" 
                                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <i class="fas fa-rocket mr-2"></i>
                                    Start Setup
                                </button>
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>