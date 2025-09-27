<?php

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $supplierModel;

    public function __construct()
    {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
    }

    public function index()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
        $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;

        // Build filters
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if ($category_id > 0) {
            $filters['category_id'] = $category_id;
        }
        if ($supplier_id > 0) {
            $filters['supplier_id'] = $supplier_id;
        }

        // Get products with pagination
        $products = $this->productModel->getProductsWithDetails();
        // Apply filters if needed
        // TODO: Add proper filtering logic
        $totalProducts = $this->productModel->count();
        // TODO: Apply filters to count as well
        $totalPages = ceil($totalProducts / $limit);

        // Get categories and suppliers for filters
        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();

        $data = [
            'title' => 'Products',
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalProducts,
                'per_page' => $limit
            ],
            'filters' => [
                'search' => $search,
                'category_id' => $category_id,
                'supplier_id' => $supplier_id
            ]
        ];

        view('products.index', $data);
    }

    public function create()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();

        $data = [
            'title' => 'Add New Product',
            'categories' => $categories,
            'suppliers' => $suppliers
        ];

        view('products.create', $data);
    }

    public function store()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products/create');
            return;
        }

        // Validate input
        $data = $this->validateProductData($_POST);
        if (!$data) {
            setFlash('error', 'Validation failed');
            redirect('/products/create');
            return;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadProductImage($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $currentUser = auth();
        $data['created_by'] = $currentUser['id'];

        try {
            $productId = $this->productModel->create($data);
            
            // Log activity
            logActivity('product_created', 'products', $productId);
            
            setFlash('success', 'Product "' . htmlspecialchars($data['name']) . '" created successfully');
            redirect('/products');
        } catch (Exception $e) {
            error_log("Error creating product: " . $e->getMessage());
            setFlash('error', 'Failed to create product');
            redirect('/products/create');
        }
    }

    public function show($id)
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        $data = [
            'title' => 'Product Details - ' . $product['name'],
            'product' => $product
        ];

        view('products.show', $data);
    }

    public function edit($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();

        $data = [
            'title' => 'Edit Product - ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers
        ];

        view('products.edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products/edit/' . $id);
            return;
        }

        // Validate input
        $data = $this->validateProductData($_POST);
        if (!$data) {
            setFlash('error', 'Validation failed');
            redirect('/products/edit/' . $id);
            return;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadProductImage($_FILES['image']);
            if ($imagePath) {
                // Delete old image if exists
                if (!empty($product['image']) && file_exists('uploads/' . $product['image'])) {
                    unlink('uploads/' . $product['image']);
                }
                $data['image'] = $imagePath;
            }
        }

        $currentUser = auth();
        $data['updated_by'] = $currentUser['id'];

        try {
            $this->productModel->update($id, $data);
            
            // Log activity
            logActivity('product_updated', 'products', $id);
            
            setFlash('success', 'Product "' . htmlspecialchars($data['name']) . '" updated successfully');
            redirect('/products');
        } catch (Exception $e) {
            error_log("Error updating product: " . $e->getMessage());
            setFlash('error', 'Failed to update product');
            redirect('/products/edit/' . $id);
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products');
            return;
        }

        try {
            // Check if product can be safely deleted
            $canDelete = $this->checkProductCanBeDeleted($id);
            if (!$canDelete['can_delete']) {
                setFlash('error', $canDelete['message']);
                redirect('/products');
                return;
            }

            // Begin transaction for safe deletion
            $this->productModel->beginTransaction();
            
            // Delete image if exists
            if (!empty($product['image']) && file_exists('uploads/' . $product['image'])) {
                unlink('uploads/' . $product['image']);
            }

            // Delete the product
            $this->productModel->delete($id);
            
            // Log activity
            logActivity('product_deleted', 'products', $id);
            
            $this->productModel->commit();
            setFlash('success', 'Product "' . htmlspecialchars($product['name']) . '" deleted successfully');
            redirect('/products');
        } catch (Exception $e) {
            $this->productModel->rollback();
            error_log("Error deleting product: " . $e->getMessage());
            
            // Provide more specific error messages based on the exception
            if (strpos($e->getMessage(), 'foreign key constraint') !== false || 
                strpos($e->getMessage(), 'cannot delete') !== false) {
                setFlash('error', 'Cannot delete this product because it has been used in sales or stock movements. Consider deactivating it instead.');
            } else {
                setFlash('error', 'Failed to delete product: ' . $e->getMessage());
            }
            redirect('/products');
        }
    }

    public function lowStock()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $lowStockProducts = $this->productModel->getLowStockProducts();

        $data = [
            'title' => 'Low Stock Products',
            'products' => $lowStockProducts
        ];

        view('products.low_stock', $data);
    }

    public function export()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        try {
            $products = $this->productModel->getProductsWithDetails();
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="products_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($output, [
                'ID',
                'Name',
                'Description',
                'SKU',
                'Barcode',
                'Category',
                'Supplier',
                'Cost Price',
                'Selling Price',
                'Stock Quantity',
                'Min Stock Level',
                'Status',
                'Created Date'
            ]);
            
            foreach ($products as $product) {
                fputcsv($output, [
                    $product['id'],
                    $product['name'],
                    $product['description'],
                    $product['sku'],
                    $product['barcode'],
                    $product['category_name'],
                    $product['supplier_name'],
                    $product['cost_price'],
                    $product['selling_price'],
                    $product['stock_quantity'],
                    $product['min_stock_level'],
                    $product['status'],
                    $product['created_at']
                ]);
            }
            
            fclose($output);
            
            // Log activity
            logActivity('products_exported');
            
        } catch (Exception $e) {
            error_log("Error exporting products: " . $e->getMessage());
            setFlash('error', 'Export failed');
            redirect('/products');
        }
    }

    public function import()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = [
                'title' => 'Import Products'
            ];
            view('products.import', $data);
            return;
        }

        // Handle POST request
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'File upload failed');
        redirect('/products/import');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products/import');
            return;
        }

        try {
            $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');
            if (!$handle) {
                throw new Exception('Could not open CSV file');
            }

            $imported = 0;
            $errors = [];
            $line = 0;

            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                $line++;
                
                if (count($data) < 8) {
                    $errors[] = "Line $line: Insufficient data columns";
                    continue;
                }

                $productData = [
                    'name' => trim($data[0]),
                    'description' => trim($data[1]),
                    'sku' => trim($data[2]),
                    'barcode' => trim($data[3]),
                    'category_id' => (int)$data[4],
                    'supplier_id' => (int)$data[5],
                    'cost_price' => (float)$data[6],
                    'selling_price' => (float)$data[7],
                    'stock_quantity' => isset($data[8]) ? (int)$data[8] : 0,
                    'min_stock_level' => isset($data[9]) ? (int)$data[9] : 10,
                    'status' => isset($data[10]) ? trim($data[10]) : 'active'
                ];
                
                $currentUser = auth();
                $productData['created_by'] = $currentUser['id'];

                // Basic validation
                if (empty($productData['name']) || empty($productData['sku'])) {
                    $errors[] = "Line $line: Name and SKU are required";
                    continue;
                }

                try {
                    $this->productModel->create($productData);
                    $imported++;
                } catch (Exception $e) {
                    $errors[] = "Line $line: " . $e->getMessage();
                }
            }

            fclose($handle);

            // Log activity
            logActivity('products_imported');

            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
                setFlash('warning', 'Import completed with some errors. ' . $imported . ' products imported.');
                redirect('/products/import');
            } else {
                setFlash('success', 'Import completed successfully. ' . $imported . ' products imported.');
                redirect('/products');
            }

        } catch (Exception $e) {
            error_log("Error importing products: " . $e->getMessage());
            setFlash('error', 'Import failed');
            redirect('/products/import');
        }
    }

    private function validateProductData($data)
    {
        $validated = [];

        // Required fields
        if (empty($data['name'])) {
            return false;
        }
        $validated['name'] = trim($data['name']);

        if (empty($data['sku'])) {
            return false;
        }
        $validated['sku'] = trim($data['sku']);

        // Optional fields
        $validated['description'] = trim($data['description'] ?? '');
        $validated['barcode'] = trim($data['barcode'] ?? '');
        $validated['category_id'] = (int)($data['category_id'] ?? 0);
        $validated['supplier_id'] = (int)($data['supplier_id'] ?? 0);

        // Numeric fields - map form field names to database field names
        $validated['cost_price'] = (float)($data['cost_price'] ?? 0);
        $validated['price'] = (float)($data['selling_price'] ?? 0); // Map selling_price to price
        $validated['quantity_in_stock'] = (int)($data['stock_quantity'] ?? 0); // Map stock_quantity to quantity_in_stock
        $validated['min_stock_level'] = (int)($data['min_stock_level'] ?? 10);

        // Optional fields
        $validated['unit'] = trim($data['unit'] ?? 'pcs');
        
        // Status
        $validated['is_active'] = isset($data['status']) && $data['status'] === 'active' ? 1 : 1; // Default to active

        return $validated;
    }

    /**
     * Soft delete (deactivate) a product
     */
    public function deactivate($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products');
            return;
        }

        try {
            $this->productModel->update($id, ['is_active' => false]);
            
            // Log activity
            logActivity('product_deactivated', 'products', $id);
            
            setFlash('success', 'Product "' . htmlspecialchars($product['name']) . '" has been deactivated successfully');
            redirect('/products');
        } catch (Exception $e) {
            error_log("Error deactivating product: " . $e->getMessage());
            setFlash('error', 'Failed to deactivate product: ' . $e->getMessage());
            redirect('/products');
        }
    }

    /**
     * Reactivate a product
     */
    public function reactivate($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/products');
            return;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            setFlash('error', 'Product not found');
            redirect('/products');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/products');
            return;
        }

        try {
            $this->productModel->update($id, ['is_active' => true]);
            
            // Log activity
            logActivity('product_reactivated', 'products', $id);
            
            setFlash('success', 'Product "' . htmlspecialchars($product['name']) . '" has been reactivated successfully');
            redirect('/products');
        } catch (Exception $e) {
            error_log("Error reactivating product: " . $e->getMessage());
            setFlash('error', 'Failed to reactivate product: ' . $e->getMessage());
            redirect('/products');
        }
    }

    /**
     * Check if a product can be safely deleted
     * Returns array with 'can_delete' boolean and 'message' string
     */
    private function checkProductCanBeDeleted($productId)
    {
        try {
            $db = $this->productModel->getDb();
            
            // Check if product has sale items
            $saleItems = $db->fetch(
                "SELECT COUNT(*) as count FROM sale_items WHERE product_id = :product_id",
                ['product_id' => $productId]
            );
            
            if ($saleItems['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete this product because it has been sold (' . $saleItems['count'] . ' sale record(s)). Consider deactivating the product instead.'
                ];
            }
            
            // Check if product has stock movements
            $stockMovements = $db->fetch(
                "SELECT COUNT(*) as count FROM stock_movements WHERE product_id = :product_id",
                ['product_id' => $productId]
            );
            
            if ($stockMovements['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete this product because it has stock movement history (' . $stockMovements['count'] . ' movement(s)). Consider deactivating the product instead.'
                ];
            }
            
            return ['can_delete' => true, 'message' => ''];
            
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking product dependencies: ' . $e->getMessage()
            ];
        }
    }

    private function uploadProductImage($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'products/' . $filename;
        }

        return false;
    }
}