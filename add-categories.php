<?php
/**
 * Add Default Categories Script
 * This script adds comprehensive default categories to the inventory system
 */

// Include configuration
require_once __DIR__ . '/config/config.php';

class CategoryInstaller {
    private $config;
    private $pdo;

    public function __construct($config) {
        $this->config = $config;
    }

    public function run() {
        try {
            echo "🏷️  Adding default categories to Inventory Management System...\n\n";
            
            // Connect to database
            $this->connectToDatabase();
            
            // Add default categories
            $this->addDefaultCategories();
            
            echo "✅ Default categories added successfully!\n\n";
            echo "📋 Categories available in your system:\n";
            $this->listCategories();
            
        } catch (\Exception $e) {
            echo "❌ Failed to add categories: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function connectToDatabase() {
        echo "🔗 Connecting to database...\n";
        
        try {
            $dsn = "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['dbname']};charset={$this->config['database']['charset']}";
            $this->pdo = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                $this->config['database']['options']
            );
            echo "✅ Connected successfully\n";
        } catch (PDOException $e) {
            throw new \Exception("Failed to connect to database: " . $e->getMessage());
        }
    }

    private function addDefaultCategories() {
        echo "📦 Adding default categories...\n";
        
        $categories = [
            ['Electronics', 'Electronic devices and accessories'],
            ['Clothing', 'Apparel and fashion items'],
            ['Food & Beverages', 'Food and drink products'],
            ['Books', 'Books and educational materials'],
            ['Home & Garden', 'Home improvement and garden supplies'],
            ['Sports', 'Sports equipment and accessories'],
            ['Health & Beauty', 'Health care and beauty products'],
            ['Automotive', 'Car parts and automotive accessories'],
            ['Office Supplies', 'Office and business supplies'],
            ['Tools & Hardware', 'Tools and hardware equipment'],
            ['Toys & Games', 'Children toys and games'],
            ['Jewelry & Watches', 'Jewelry and timepieces'],
            ['Music & Movies', 'Audio, video and entertainment'],
            ['Pet Supplies', 'Pet food and accessories'],
            ['Travel & Luggage', 'Travel gear and luggage'],
            ['Mobile & Accessories', 'Mobile phones and accessories'],
            ['Computers & Software', 'Computers and software'],
            ['Kitchen & Dining', 'Kitchen appliances and dining items'],
            ['Furniture', 'Home and office furniture'],
            ['Footwear', 'Shoes and sandals']
        ];

        $sql = "INSERT INTO categories (name, description, is_active) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE description = VALUES(description)";
        $stmt = $this->pdo->prepare($sql);
        
        $added = 0;
        $updated = 0;
        
        foreach ($categories as $category) {
            try {
                // Check if category already exists
                $checkSql = "SELECT id FROM categories WHERE name = ?";
                $checkStmt = $this->pdo->prepare($checkSql);
                $checkStmt->execute([$category[0]]);
                
                if ($checkStmt->fetch()) {
                    // Update existing category
                    $updateSql = "UPDATE categories SET description = ?, is_active = 1 WHERE name = ?";
                    $updateStmt = $this->pdo->prepare($updateSql);
                    $updateStmt->execute([$category[1], $category[0]]);
                    $updated++;
                    echo "  ✏️  Updated: {$category[0]}\n";
                } else {
                    // Insert new category
                    $stmt->execute($category);
                    $added++;
                    echo "  ✅ Added: {$category[0]}\n";
                }
            } catch (PDOException $e) {
                echo "  ⚠️  Warning for '{$category[0]}': " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n📊 Summary:\n";
        echo "  • New categories added: {$added}\n";
        echo "  • Existing categories updated: {$updated}\n";
        echo "  • Total categories processed: " . count($categories) . "\n";
    }

    private function listCategories() {
        $sql = "SELECT name, description, is_active FROM categories ORDER BY name";
        $categories = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $category) {
            $status = $category['is_active'] ? '✅' : '❌';
            echo "  {$status} {$category['name']} - {$category['description']}\n";
        }
        
        echo "\n📈 Total categories in system: " . count($categories) . "\n";
    }
}

// Check if script is being run from command line or web
if (php_sapi_name() === 'cli') {
    $installer = new CategoryInstaller($config);
    $installer->run();
} else {
    // Web interface
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Default Categories - Inventory Management</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Add Default Categories</h1>
                    <p class="mt-2 text-gray-600">Install comprehensive default categories for your inventory system</p>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow">
                    <?php
                    if (isset($_POST['install'])) {
                        echo "<div class='mb-4'>";
                        
                        try {
                            ob_start();
                            $installer = new CategoryInstaller($config);
                            $installer->run();
                            $output = ob_get_clean();
                            
                            // Convert CLI output to HTML
                            $output = htmlspecialchars($output);
                            $output = str_replace("\n", "<br>", $output);
                            $output = str_replace("✅", "<span class='text-green-600'>✅</span>", $output);
                            $output = str_replace("❌", "<span class='text-red-600'>❌</span>", $output);
                            $output = str_replace("⚠️", "<span class='text-yellow-600'>⚠️</span>", $output);
                            $output = str_replace("🏷️", "<span class='text-blue-600'>🏷️</span>", $output);
                            $output = str_replace("📦", "<span class='text-purple-600'>📦</span>", $output);
                            $output = str_replace("📊", "<span class='text-indigo-600'>📊</span>", $output);
                            $output = str_replace("📋", "<span class='text-gray-600'>📋</span>", $output);
                            $output = str_replace("📈", "<span class='text-green-600'>📈</span>", $output);
                            
                            echo "<div class='bg-gray-900 text-green-400 p-4 rounded text-sm font-mono'>";
                            echo $output;
                            echo "</div>";
                            
                            echo "<div class='mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded'>";
                            echo "<h3 class='font-bold'>Success!</h3>";
                            echo "<p>Default categories have been added to your system.</p>";
                            echo "<a href='public/index.php/categories' class='inline-block mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700'>View Categories</a>";
                            echo "</div>";
                            
                        } catch (Exception $e) {
                            echo "<div class='p-4 bg-red-100 border border-red-400 text-red-700 rounded'>";
                            echo "<h3 class='font-bold'>Error!</h3>";
                            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                            echo "</div>";
                        }
                        
                        echo "</div>";
                    } else {
                        ?>
                        <form method="POST">
                            <div class="space-y-4">
                                <div class="text-center">
                                    <i class="fas fa-tags text-6xl text-blue-600 mb-4"></i>
                                    <h2 class="text-xl font-semibold text-gray-900">Ready to install default categories?</h2>
                                    <p class="text-gray-600 mt-2">This will add 20 comprehensive categories to your inventory system.</p>
                                </div>
                                
                                <div class="bg-blue-50 p-4 rounded">
                                    <h3 class="font-medium text-blue-900">Categories to be added:</h3>
                                    <div class="mt-2 text-sm text-blue-800 grid grid-cols-2 gap-1">
                                        <span>• Electronics</span>
                                        <span>• Clothing</span>
                                        <span>• Food & Beverages</span>
                                        <span>• Books</span>
                                        <span>• Home & Garden</span>
                                        <span>• Sports</span>
                                        <span>• Health & Beauty</span>
                                        <span>• Automotive</span>
                                        <span>• Office Supplies</span>
                                        <span>• Tools & Hardware</span>
                                        <span>• Toys & Games</span>
                                        <span>• Jewelry & Watches</span>
                                        <span>• Music & Movies</span>
                                        <span>• Pet Supplies</span>
                                        <span>• Travel & Luggage</span>
                                        <span>• Mobile & Accessories</span>
                                        <span>• Computers & Software</span>
                                        <span>• Kitchen & Dining</span>
                                        <span>• Furniture</span>
                                        <span>• Footwear</span>
                                    </div>
                                </div>
                                
                                <button type="submit" name="install" 
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-download mr-2"></i>
                                    Install Default Categories
                                </button>
                                
                                <p class="text-xs text-gray-500 text-center">
                                    Note: Existing categories will be updated, not duplicated.
                                </p>
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
                
                <div class="text-center">
                    <a href="public/index.php" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Application
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>