<?php
/**
 * Customer Model for Inventory Management System
 */
class Customer extends BaseModel {
    protected $table = 'customers';
    protected $fillable = [
        'customer_code', 'full_name', 'phone', 'email', 'address',
        'city', 'state', 'postal_code', 'date_of_birth', 'credit_limit',
        'current_balance', 'is_active', 'notes', 'created_by'
    ];

    public function create($data) {
        // Generate customer code if not provided
        if (empty($data['customer_code'])) {
            $data['customer_code'] = $this->generateCustomerCode();
        }

        return parent::create($data);
    }

    public function validateCustomer($data, $isUpdate = false, $customerId = null) {
        $rules = [
            'full_name' => 'required|min:2|max:100',
            'phone' => 'required|min:10|max:20',
            'email' => 'email',
            'customer_code' => 'required|min:3|max:20'
        ];

        $errors = $this->validate($data, $rules);

        // Check unique customer code
        if (!empty($data['customer_code'])) {
            $where = 'customer_code = :customer_code';
            $params = ['customer_code' => $data['customer_code']];
            
            if ($isUpdate && $customerId) {
                $where .= ' AND id != :id';
                $params['id'] = $customerId;
            }
            
            if ($this->db->exists($this->table, $where, $params)) {
                $errors['customer_code'][] = 'Customer code already exists';
            }
        }

        // Check unique phone
        if (!empty($data['phone'])) {
            $where = 'phone = :phone';
            $params = ['phone' => $data['phone']];
            
            if ($isUpdate && $customerId) {
                $where .= ' AND id != :id';
                $params['id'] = $customerId;
            }
            
            if ($this->db->exists($this->table, $where, $params)) {
                $errors['phone'][] = 'Phone number already exists';
            }
        }

        // Check unique email if provided
        if (!empty($data['email'])) {
            $where = 'email = :email';
            $params = ['email' => $data['email']];
            
            if ($isUpdate && $customerId) {
                $where .= ' AND id != :id';
                $params['id'] = $customerId;
            }
            
            if ($this->db->exists($this->table, $where, $params)) {
                $errors['email'][] = 'Email already exists';
            }
        }

        return $errors;
    }

    private function generateCustomerCode() {
        $prefix = 'CUST';
        $year = date('Y');
        
        // Get the latest customer code for this year
        $sql = "SELECT customer_code FROM customers 
                WHERE customer_code LIKE :pattern 
                ORDER BY customer_code DESC LIMIT 1";
        
        $pattern = $prefix . $year . '%';
        $result = $this->db->fetch($sql, ['pattern' => $pattern]);
        
        if ($result) {
            // Extract number and increment
            $lastNumber = intval(substr($result['customer_code'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getCustomersWithDetails() {
        $sql = "SELECT 
                    c.*,
                    u.full_name as created_by_name,
                    COUNT(d.id) as total_debts,
                    SUM(CASE WHEN d.status != 'paid' THEN d.remaining_amount ELSE 0 END) as outstanding_balance
                FROM customers c
                LEFT JOIN users u ON c.created_by = u.id
                LEFT JOIN debts d ON c.id = d.customer_id
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function searchCustomers($searchTerm) {
        return $this->search(['customer_code', 'full_name', 'phone', 'email'], $searchTerm, 'is_active = 1');
    }

    public function getCustomerDebts($customerId) {
        $sql = "SELECT 
                    d.*,
                    u.full_name as created_by_name
                FROM debts d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d.customer_id = :customer_id
                ORDER BY d.created_at DESC";
        
        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }

    public function getCustomerSales($customerId, $limit = 20) {
        $sql = "SELECT 
                    s.*,
                    u.full_name as created_by_name
                FROM sales s
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.customer_id = :customer_id
                ORDER BY s.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }

    public function updateBalance($customerId, $amount, $operation = 'add') {
        $customer = $this->find($customerId);
        if (!$customer) {
            throw new \Exception('Customer not found');
        }

        $currentBalance = $customer['current_balance'];
        
        if ($operation === 'add') {
            $newBalance = $currentBalance + $amount;
        } elseif ($operation === 'subtract') {
            $newBalance = $currentBalance - $amount;
        } else {
            $newBalance = $amount; // direct set
        }

        return $this->update($customerId, ['current_balance' => $newBalance]);
    }

    public function getCustomersWithOutstandingDebts() {
        $sql = "SELECT 
                    c.*,
                    SUM(d.remaining_amount) as total_outstanding,
                    COUNT(d.id) as debt_count,
                    MIN(d.due_date) as earliest_due_date
                FROM customers c
                JOIN debts d ON c.id = d.customer_id
                WHERE d.status != 'paid' AND c.is_active = 1
                GROUP BY c.id
                HAVING total_outstanding > 0
                ORDER BY total_outstanding DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getCustomersWithOverdueDebts() {
        $sql = "SELECT 
                    c.*,
                    SUM(d.remaining_amount) as overdue_amount,
                    COUNT(d.id) as overdue_count,
                    MIN(d.due_date) as earliest_overdue_date,
                    MIN(DATEDIFF(CURDATE(), d.due_date)) as min_days_overdue
                FROM customers c
                JOIN debts d ON c.id = d.customer_id
                WHERE d.due_date < CURDATE() AND d.status != 'paid' AND c.is_active = 1
                GROUP BY c.id
                HAVING overdue_amount > 0
                ORDER BY min_days_overdue DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getTopCustomers($limit = 10) {
        $sql = "SELECT 
                    c.*,
                    COUNT(s.id) as total_orders,
                    SUM(s.total_amount) as total_spent
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY total_spent DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    public function exportToCSV($customers) {
        $filename = 'customers_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = STORAGE_PATH . '/exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Write header
        fputcsv($file, [
            'Customer Code', 'Full Name', 'Phone', 'Email', 'Address',
            'City', 'State', 'Postal Code', 'Date of Birth', 'Credit Limit',
            'Current Balance', 'Status', 'Notes'
        ]);
        
        // Write data
        foreach ($customers as $customer) {
            fputcsv($file, [
                $customer['customer_code'],
                $customer['full_name'],
                $customer['phone'],
                $customer['email'] ?? '',
                $customer['address'] ?? '',
                $customer['city'] ?? '',
                $customer['state'] ?? '',
                $customer['postal_code'] ?? '',
                $customer['date_of_birth'] ?? '',
                $customer['credit_limit'] ?? '',
                $customer['current_balance'] ?? '',
                $customer['is_active'] ? 'Active' : 'Inactive',
                $customer['notes'] ?? ''
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
                    $customerData = [
                        'customer_code' => $data[0] ?? '',
                        'full_name' => $data[1] ?? '',
                        'phone' => $data[2] ?? '',
                        'email' => !empty($data[3]) ? $data[3] : null,
                        'address' => !empty($data[4]) ? $data[4] : null,
                        'city' => !empty($data[5]) ? $data[5] : null,
                        'state' => !empty($data[6]) ? $data[6] : null,
                        'postal_code' => !empty($data[7]) ? $data[7] : null,
                        'date_of_birth' => !empty($data[8]) ? $data[8] : null,
                        'credit_limit' => !empty($data[9]) ? floatval($data[9]) : 0,
                        'current_balance' => !empty($data[10]) ? floatval($data[10]) : 0,
                        'notes' => !empty($data[12]) ? $data[12] : null,
                        'is_active' => true,
                        'created_by' => auth()['id']
                    ];
                    
                    // Validate required fields
                    if (empty($customerData['full_name']) || empty($customerData['phone'])) {
                        $results['errors'][] = "Row {$row}: Full name and phone are required";
                        continue;
                    }
                    
                    // Generate customer code if empty
                    if (empty($customerData['customer_code'])) {
                        $customerData['customer_code'] = $this->generateCustomerCode();
                    }
                    
                    // Check if customer code already exists
                    if ($this->findBy('customer_code', $customerData['customer_code'])) {
                        $results['errors'][] = "Row {$row}: Customer code '{$customerData['customer_code']}' already exists";
                        continue;
                    }
                    
                    // Check if phone already exists
                    if ($this->findBy('phone', $customerData['phone'])) {
                        $results['errors'][] = "Row {$row}: Phone '{$customerData['phone']}' already exists";
                        continue;
                    }
                    
                    $this->create($customerData);
                    $results['success']++;
                    
                } catch (Exception $e) {
                    $results['errors'][] = "Row {$row}: " . $e->getMessage();
                }
            }
            
            fclose($handle);
        }
        
        return $results;
    }

    public function getCustomerStats() {
        $stats = [];
        
        // Total active customers
        $stats['total_active'] = $this->count('is_active = 1');
        
        // Customers with outstanding debts
        $stats['with_debts'] = $this->db->count(
            'customers c JOIN debts d ON c.id = d.customer_id',
            'c.is_active = 1 AND d.status != "paid"'
        );
        
        // Total outstanding amount
        $result = $this->db->fetch(
            'SELECT SUM(d.remaining_amount) as total 
             FROM customers c 
             JOIN debts d ON c.id = d.customer_id 
             WHERE c.is_active = 1 AND d.status != "paid"'
        );
        $stats['total_outstanding'] = $result['total'] ?? 0;
        
        // New customers this month
        $stats['new_this_month'] = $this->count(
            'is_active = 1 AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())'
        );
        
        return $stats;
    }
}