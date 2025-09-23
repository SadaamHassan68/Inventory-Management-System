<?php
/**
 * Product Model for Inventory Management System
 */
class Product extends BaseModel {
    protected $table = 'products';
    protected $fillable = [
        'name', 'sku', 'category_id', 'supplier_id', 'description',
        'price', 'cost_price', 'quantity_in_stock', 'min_stock_level',
        'max_stock_level', 'unit', 'image_path', 'barcode', 'is_active', 'created_by'
    ];

    public function validateProduct($data, $isUpdate = false, $productId = null) {
        $rules = [
            'name' => 'required|min:2|max:200',
            'sku' => 'required|min:2|max:50',
            'price' => 'required|numeric',
            'quantity_in_stock' => 'required|numeric',
            'min_stock_level' => 'required|numeric',
        ];

        $errors = $this->validate($data, $rules);

        // Check unique SKU
        if (!empty($data['sku'])) {
            $where = 'sku = :sku';
            $params = ['sku' => $data['sku']];
            
            if ($isUpdate && $productId) {
                $where .= ' AND id != :id';
                $params['id'] = $productId;
            }
            
            if ($this->db->exists($this->table, $where, $params)) {
                $errors['sku'][] = 'SKU already exists';
            }
        }

        return $errors;
    }

    public function getProductsWithDetails() {
        $sql = "SELECT 
                    p.*,
                    c.name as category_name,
                    s.name as supplier_name,
                    u.full_name as created_by_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                LEFT JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getLowStockProducts() {
        $sql = "SELECT 
                    p.*,
                    c.name as category_name,
                    s.name as supplier_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.quantity_in_stock <= p.min_stock_level 
                AND p.is_active = 1
                ORDER BY (p.quantity_in_stock / p.min_stock_level) ASC";
        
        return $this->db->fetchAll($sql);
    }

    public function searchProducts($searchTerm) {
        return $this->search(['name', 'sku', 'description'], $searchTerm, 'is_active = 1');
    }

    public function updateStock($productId, $quantity, $movementType = 'adjustment', $referenceType = 'adjustment', $referenceId = null, $notes = null) {
        $this->db->beginTransaction();
        
        try {
            // Get current product
            $product = $this->find($productId);
            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Calculate new quantity
            $currentQuantity = $product['quantity_in_stock'];
            
            if ($movementType === 'in') {
                $newQuantity = $currentQuantity + $quantity;
            } elseif ($movementType === 'out') {
                $newQuantity = $currentQuantity - $quantity;
                if ($newQuantity < 0) {
                    throw new \Exception('Insufficient stock');
                }
            } else {
                $newQuantity = $quantity; // direct set for adjustment
            }

            // Update product stock
            $this->update($productId, ['quantity_in_stock' => $newQuantity]);

            // Record stock movement
            $user = auth();
            $this->db->insert('stock_movements', [
                'product_id' => $productId,
                'movement_type' => $movementType,
                'quantity' => abs($quantity),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => $user['id'],
                'movement_date' => date('Y-m-d H:i:s')
            ]);

            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getStockMovements($productId, $limit = 20) {
        $sql = "SELECT 
                    sm.*,
                    u.full_name as user_name
                FROM stock_movements sm
                LEFT JOIN users u ON sm.user_id = u.id
                WHERE sm.product_id = :product_id
                ORDER BY sm.movement_date DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['product_id' => $productId]);
    }

    public function getInventoryValue() {
        $result = $this->db->fetch(
            'SELECT SUM(price * quantity_in_stock) as total FROM products WHERE is_active = 1'
        );
        return $result['total'] ?? 0;
    }

    public function getTopSellingProducts($limit = 10) {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    SUM(si.quantity) as total_sold,
                    SUM(si.total_price) as total_revenue
                FROM products p
                JOIN sale_items si ON p.id = si.product_id
                JOIN sales s ON si.sale_id = s.id
                WHERE p.is_active = 1
                GROUP BY p.id, p.name, p.sku
                ORDER BY total_sold DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    public function exportToCSV($products) {
        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = STORAGE_PATH . '/exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Write header
        fputcsv($file, [
            'Name', 'SKU', 'Category', 'Supplier', 'Price', 'Cost Price',
            'Stock Quantity', 'Min Stock Level', 'Unit', 'Status'
        ]);
        
        // Write data
        foreach ($products as $product) {
            fputcsv($file, [
                $product['name'],
                $product['sku'],
                $product['category_name'] ?? '',
                $product['supplier_name'] ?? '',
                $product['price'],
                $product['cost_price'] ?? '',
                $product['quantity_in_stock'],
                $product['min_stock_level'],
                $product['unit'],
                $product['is_active'] ? 'Active' : 'Inactive'
            ]);
        }
        
        fclose($file);
        return $filepath;
    }

    public function importFromCSV($csvFile) {
        $results = ['success' => 0, 'errors' => []];
        
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $headers = fgetcsv($handle); // Skip header row
            $row = 1;
            
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                
                try {
                    $productData = [
                        'name' => $data[0] ?? '',
                        'sku' => $data[1] ?? '',
                        'price' => floatval($data[4] ?? 0),
                        'cost_price' => floatval($data[5] ?? 0),
                        'quantity_in_stock' => intval($data[6] ?? 0),
                        'min_stock_level' => intval($data[7] ?? 10),
                        'unit' => $data[8] ?? 'pcs',
                        'is_active' => true,
                        'created_by' => auth()['id']
                    ];
                    
                    // Validate required fields
                    if (empty($productData['name']) || empty($productData['sku'])) {
                        $results['errors'][] = "Row {$row}: Name and SKU are required";
                        continue;
                    }
                    
                    // Check if SKU already exists
                    if ($this->findBy('sku', $productData['sku'])) {
                        $results['errors'][] = "Row {$row}: SKU '{$productData['sku']}' already exists";
                        continue;
                    }
                    
                    $this->create($productData);
                    $results['success']++;
                    
                } catch (Exception $e) {
                    $results['errors'][] = "Row {$row}: " . $e->getMessage();
                }
            }
            
            fclose($handle);
        }
        
        return $results;
    }
}

/**
 * Category Model
 */
class Category extends BaseModel {
    protected $table = 'categories';
    protected $fillable = ['name', 'description', 'is_active'];

    public function getActiveCategories() {
        return $this->where('is_active', '=', true, 'name ASC');
    }

    public function getCategoriesWithProductCount() {
        $sql = "SELECT 
                    c.*,
                    COUNT(p.id) as product_count
                FROM categories c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id, c.name, c.description, c.is_active, c.created_at, c.updated_at
                ORDER BY c.name ASC";
        
        return $this->db->fetchAll($sql);
    }
}

/**
 * Supplier Model
 */
class Supplier extends BaseModel {
    protected $table = 'suppliers';
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address', 'is_active'];

    public function getActiveSuppliers() {
        return $this->where('is_active', '=', true, 'name ASC');
    }

    public function getSuppliersWithProductCount() {
        $sql = "SELECT 
                    s.*,
                    COUNT(p.id) as product_count
                FROM suppliers s
                LEFT JOIN products p ON s.id = p.supplier_id AND p.is_active = 1
                WHERE s.is_active = 1
                GROUP BY s.id, s.name, s.contact_person, s.email, s.phone, s.address, s.is_active, s.created_at, s.updated_at
                ORDER BY s.name ASC";
        
        return $this->db->fetchAll($sql);
    }

    public function validateSupplier($data) {
        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'email'
        ];

        return $this->validate($data, $rules);
    }
}