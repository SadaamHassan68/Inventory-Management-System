<?php

class Sale extends BaseModel
{
    protected $table = 'sales';
    protected $fillable = [
        'sale_number', 'customer_id', 'user_id', 'sale_date', 
        'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 
        'payment_method', 'payment_status', 'notes'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function generateSaleNumber()
    {
        $prefix = 'SALE';
        $date = date('Ymd');
        
        // Get the last sale number for today
        $sql = "SELECT sale_number FROM sales 
                WHERE sale_number LIKE :pattern 
                ORDER BY sale_number DESC 
                LIMIT 1";
        
        $pattern = $prefix . $date . '%';
        $result = $this->getDb()->fetch($sql, ['pattern' => $pattern]);
        
        if ($result) {
            // Extract the sequence number and increment
            $lastNumber = substr($result['sale_number'], -4);
            $sequence = intval($lastNumber) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function create($data)
    {
        // Auto-generate sale number if not provided
        if (empty($data['sale_number'])) {
            $data['sale_number'] = $this->generateSaleNumber();
        }

        // Set default sale date if not provided
        if (empty($data['sale_date'])) {
            $data['sale_date'] = date('Y-m-d H:i:s');
        }

        // Calculate total amount if not provided
        if (!isset($data['total_amount'])) {
            $subtotal = $data['subtotal'] ?? 0;
            $taxAmount = $data['tax_amount'] ?? 0;
            $discountAmount = $data['discount_amount'] ?? 0;
            $data['total_amount'] = $subtotal + $taxAmount - $discountAmount;
        }

        $filteredData = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert($this->table, $filteredData);
        return $id; // Return just the ID, not the full record
    }

    public function getSalesWithCustomers($limit = null, $search = null, $customerId = null, $paymentStatus = null)
    {
        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(s.sale_number LIKE :search OR c.full_name LIKE :search OR c.customer_code LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if ($customerId) {
            $conditions[] = "s.customer_id = :customer_id";
            $params['customer_id'] = $customerId;
        }

        if ($paymentStatus) {
            $conditions[] = "s.payment_status = :payment_status";
            $params['payment_status'] = $paymentStatus;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $limitClause = $limit ? "LIMIT {$limit}" : "";

        $sql = "SELECT 
                    s.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    u.full_name as created_by_name
                FROM sales s
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON s.user_id = u.id
                {$whereClause}
                ORDER BY s.created_at DESC
                {$limitClause}";

        return $this->getDb()->fetchAll($sql, $params);
    }

    public function getSaleWithDetails($id)
    {
        $sql = "SELECT 
                    s.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    c.address as customer_address,
                    u.full_name as created_by_name
                FROM sales s
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.id = :id";

        return $this->getDb()->fetch($sql, ['id' => $id]);
    }

    public function getSaleItems($saleId)
    {
        $sql = "SELECT 
                    si.*,
                    p.name as product_name,
                    p.sku as product_sku,
                    p.unit as product_unit
                FROM sale_items si
                JOIN products p ON si.product_id = p.id
                WHERE si.sale_id = :sale_id
                ORDER BY si.id ASC";

        return $this->getDb()->fetchAll($sql, ['sale_id' => $saleId]);
    }

    public function addSaleItem($saleId, $productId, $quantity, $unitPrice)
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than zero');
        }

        if ($unitPrice < 0) {
            throw new \Exception('Unit price cannot be negative');
        }

        $totalPrice = $quantity * $unitPrice;

        $itemData = [
            'sale_id' => $saleId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice
        ];

        return $this->getDb()->insert('sale_items', $itemData);
    }

    public function getSalesWithPagination($conditions = [], $params = [], $limit = 20, $offset = 0)
    {
        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(DISTINCT s.id) as total 
                     FROM sales s 
                     LEFT JOIN customers c ON s.customer_id = c.id 
                     {$whereClause}";
        $totalResult = $this->getDb()->fetch($countSql, $params);
        $totalCount = $totalResult['total'];

        // Get sales for current page
        $sql = "SELECT 
                    s.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    u.full_name as created_by_name
                FROM sales s
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON s.user_id = u.id
                {$whereClause}
                ORDER BY s.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $sales = $this->getDb()->fetchAll($sql, $params);
        
        return [
            'sales' => $sales,
            'total_count' => $totalCount
        ];
    }

    public function getSalesStats()
    {
        $stats = [];
        
        // Total sales count
        $stats['total_sales'] = $this->count();
        
        // Total sales amount
        $result = $this->getDb()->fetch('SELECT SUM(total_amount) as total FROM sales');
        $stats['total_amount'] = $result['total'] ?? 0;
        
        // Sales today
        $stats['sales_today'] = $this->count('DATE(sale_date) = CURDATE()');
        
        // Sales this month
        $stats['sales_this_month'] = $this->count('YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())');
        
        // Average sale amount
        $result = $this->getDb()->fetch('SELECT AVG(total_amount) as average FROM sales');
        $stats['average_sale'] = $result['average'] ?? 0;
        
        return $stats;
    }

    public function getTopSellingProducts($limit = 10)
    {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    SUM(si.quantity) as total_sold,
                    SUM(si.total_price) as total_revenue
                FROM products p
                JOIN sale_items si ON p.id = si.product_id
                JOIN sales s ON si.sale_id = s.id
                GROUP BY p.id, p.name, p.sku
                ORDER BY total_sold DESC
                LIMIT {$limit}";
        
        return $this->getDb()->fetchAll($sql);
    }

    public function validateSaleData($data, $isUpdate = false, $saleId = null)
    {
        $errors = [];
        
        // Required fields
        if (empty($data['subtotal']) || $data['subtotal'] <= 0) {
            $errors['subtotal'][] = 'Subtotal must be greater than zero';
        }
        
        if (empty($data['total_amount']) || $data['total_amount'] <= 0) {
            $errors['total_amount'][] = 'Total amount must be greater than zero';
        }
        
        if (empty($data['payment_method'])) {
            $errors['payment_method'][] = 'Payment method is required';
        }
        
        // Validate payment method
        $validPaymentMethods = ['cash', 'credit', 'debit', 'bank_transfer', 'debt'];
        if (!empty($data['payment_method']) && !in_array($data['payment_method'], $validPaymentMethods)) {
            $errors['payment_method'][] = 'Invalid payment method';
        }
        
        // Validate payment status
        $validPaymentStatuses = ['paid', 'partial', 'pending'];
        if (!empty($data['payment_status']) && !in_array($data['payment_status'], $validPaymentStatuses)) {
            $errors['payment_status'][] = 'Invalid payment status';
        }
        
        // Check if customer exists (if provided)
        if (!empty($data['customer_id'])) {
            $customerModel = new Customer();
            if (!$customerModel->find($data['customer_id'])) {
                $errors['customer_id'][] = 'Selected customer does not exist';
            }
        }
        
        return $errors;
    }

    public function cancelSale($saleId, $reason = null)
    {
        try {
            $this->getDb()->beginTransaction();
            
            // Get sale details
            $sale = $this->find($saleId);
            if (!$sale) {
                throw new \Exception('Sale not found');
            }
            
            if ($sale['payment_status'] === 'paid') {
                throw new \Exception('Cannot cancel a paid sale');
            }
            
            // Get sale items to restore stock
            $items = $this->getSaleItems($saleId);
            
            // Restore stock for each item
            foreach ($items as $item) {
                $this->getDb()->query(
                    "UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );
            }
            
            // Delete sale items
            $this->getDb()->delete('sale_items', 'sale_id = :sale_id', ['sale_id' => $saleId]);
            
            // Delete the sale
            $this->delete($saleId);
            
            $this->getDb()->commit();
            return true;
            
        } catch (Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }
    }
}