<?php

class Debt extends BaseModel
{
    protected $table = 'debts';
    protected $fillable = [
        'debt_number', 'customer_id', 'sale_id', 'user_id', 
        'original_amount', 'remaining_amount', 'debt_date', 
        'due_date', 'status', 'description', 'notes'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function generateDebtNumber()
    {
        $prefix = 'DEBT';
        $date = date('Ymd');
        
        // Get the last debt number for today
        $sql = "SELECT debt_number FROM debts 
                WHERE debt_number LIKE :pattern 
                ORDER BY debt_number DESC 
                LIMIT 1";
        
        $pattern = $prefix . $date . '%';
        $result = $this->db->fetch($sql, ['pattern' => $pattern]);
        
        if ($result) {
            // Extract the sequence number and increment
            $lastNumber = substr($result['debt_number'], -4);
            $sequence = intval($lastNumber) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function create($data)
    {
        // Auto-generate debt number if not provided
        if (empty($data['debt_number'])) {
            $data['debt_number'] = $this->generateDebtNumber();
        }

        // Set remaining amount equal to original amount for new debts
        if (!isset($data['remaining_amount'])) {
            $data['remaining_amount'] = $data['original_amount'];
        }

        // Set default due date if not provided
        if (empty($data['due_date'])) {
            $defaultDueDays = 30; // Could be from config
            $data['due_date'] = date('Y-m-d', strtotime("+{$defaultDueDays} days"));
        }

        return parent::create($data);
    }

    public function getDebtsWithCustomers($limit = null, $search = null, $status = null)
    {
        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(d.debt_number LIKE :search OR c.full_name LIKE :search OR c.customer_code LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if ($status) {
            $conditions[] = "d.status = :status";
            $params['status'] = $status;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        $limitClause = $limit ? "LIMIT {$limit}" : "";

        $sql = "SELECT 
                    d.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    u.full_name as created_by_name
                FROM debts d
                JOIN customers c ON d.customer_id = c.id
                LEFT JOIN users u ON d.user_id = u.id
                {$whereClause}
                ORDER BY d.created_at DESC
                {$limitClause}";

        return $this->db->fetchAll($sql, $params);
    }

    public function getDebtWithDetails($id)
    {
        $sql = "SELECT 
                    d.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    c.address as customer_address,
                    c.credit_limit,
                    u.full_name as created_by_name,
                    s.sale_number
                FROM debts d
                JOIN customers c ON d.customer_id = c.id
                LEFT JOIN users u ON d.user_id = u.id
                LEFT JOIN sales s ON d.sale_id = s.id
                WHERE d.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function getDebtPayments($debtId)
    {
        $sql = "SELECT 
                    dp.*,
                    u.full_name as recorded_by_name
                FROM debt_payments dp
                LEFT JOIN users u ON dp.recorded_by = u.id
                WHERE dp.debt_id = :debt_id
                ORDER BY dp.payment_date DESC";

        return $this->db->fetchAll($sql, ['debt_id' => $debtId]);
    }

    public function addPayment($debtId, $amount, $paymentMethod = 'cash', $notes = null)
    {
        $debt = $this->find($debtId);
        if (!$debt) {
            throw new \Exception('Debt not found');
        }

        if ($amount <= 0) {
            throw new \Exception('Payment amount must be greater than zero');
        }

        if ($amount > $debt['remaining_amount']) {
            throw new \Exception('Payment amount cannot exceed remaining debt amount');
        }

        $currentUser = auth();
        
        try {
            $this->db->beginTransaction();

            // Insert payment record
            $paymentData = [
                'debt_id' => $debtId,
                'payment_amount' => $amount,
                'payment_method' => $paymentMethod,
                'notes' => $notes,
                'recorded_by' => $currentUser['id']
            ];

            $paymentId = $this->db->insert('debt_payments', $paymentData);

            // Update debt remaining amount and status
            $newRemainingAmount = $debt['remaining_amount'] - $amount;
            $newStatus = $newRemainingAmount <= 0 ? 'paid' : 
                        ($newRemainingAmount < $debt['original_amount'] ? 'partially_paid' : $debt['status']);

            $this->update($debtId, [
                'remaining_amount' => $newRemainingAmount,
                'status' => $newStatus
            ]);

            // Update customer balance
            $customerModel = new Customer();
            $customerModel->updateBalance($debt['customer_id'], $amount, 'subtract');

            $this->db->commit();
            return $paymentId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getOverdueDebts($limit = null)
    {
        $limitClause = $limit ? "LIMIT {$limit}" : "";

        $sql = "SELECT 
                    d.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    DATEDIFF(CURDATE(), d.due_date) as days_overdue
                FROM debts d
                JOIN customers c ON d.customer_id = c.id
                WHERE d.due_date < CURDATE() 
                AND d.status != 'paid'
                AND c.is_active = 1
                ORDER BY d.due_date ASC
                {$limitClause}";

        return $this->db->fetchAll($sql);
    }

    public function getDebtsByCustomer($customerId, $limit = null)
    {
        $limitClause = $limit ? "LIMIT {$limit}" : "";

        $sql = "SELECT 
                    d.*,
                    u.full_name as created_by_name
                FROM debts d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d.customer_id = :customer_id
                ORDER BY d.created_at DESC
                {$limitClause}";

        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }

    public function getDebtStats()
    {
        $stats = [];

        // Total debts count
        $stats['total_debts'] = $this->count();

        // Outstanding debts count
        $stats['outstanding_debts'] = $this->count("status != 'paid'");

        // Total outstanding amount
        $result = $this->db->fetch("SELECT SUM(remaining_amount) as total FROM debts WHERE status != 'paid'");
        $stats['total_outstanding_amount'] = $result['total'] ?? 0;

        // Overdue debts count
        $stats['overdue_debts'] = $this->count("due_date < CURDATE() AND status != 'paid'");

        // Total overdue amount
        $result = $this->db->fetch("SELECT SUM(remaining_amount) as total FROM debts WHERE due_date < CURDATE() AND status != 'paid'");
        $stats['total_overdue_amount'] = $result['total'] ?? 0;

        // Paid debts this month
        $stats['paid_this_month'] = $this->count("status = 'paid' AND YEAR(updated_at) = YEAR(CURDATE()) AND MONTH(updated_at) = MONTH(CURDATE())");

        return $stats;
    }

    public function validateDebt($data, $isUpdate = false, $debtId = null)
    {
        $errors = [];

        // Required fields
        if (empty($data['customer_id'])) {
            $errors['customer_id'][] = 'Customer is required';
        }

        if (empty($data['original_amount']) || $data['original_amount'] <= 0) {
            $errors['original_amount'][] = 'Original amount must be greater than zero';
        }

        if (empty($data['due_date'])) {
            $errors['due_date'][] = 'Due date is required';
        } elseif (strtotime($data['due_date']) < strtotime(date('Y-m-d'))) {
            $errors['due_date'][] = 'Due date cannot be in the past';
        }

        // Validate customer exists
        if (!empty($data['customer_id'])) {
            $customerModel = new Customer();
            $customer = $customerModel->find($data['customer_id']);
            if (!$customer) {
                $errors['customer_id'][] = 'Customer not found';
            } elseif (!$customer['is_active']) {
                $errors['customer_id'][] = 'Customer is inactive';
            }
        }

        // Validate debt number uniqueness
        if (!empty($data['debt_number'])) {
            $existing = $this->findBy('debt_number', $data['debt_number']);
            if ($existing && (!$isUpdate || $existing['id'] != $debtId)) {
                $errors['debt_number'][] = 'Debt number already exists';
            }
        }

        return $errors;
    }

    public function exportToCSV($debts)
    {
        $filename = 'debts_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = STORAGE_PATH . '/exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        $exportDir = STORAGE_PATH . '/exports';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // CSV headers
        fputcsv($file, [
            'Debt Number',
            'Customer Code',
            'Customer Name',
            'Original Amount',
            'Remaining Amount',
            'Debt Date',
            'Due Date',
            'Status',
            'Days Overdue',
            'Description',
            'Created By'
        ]);
        
        foreach ($debts as $debt) {
            $daysOverdue = '';
            if ($debt['status'] != 'paid' && strtotime($debt['due_date']) < time()) {
                $daysOverdue = ceil((time() - strtotime($debt['due_date'])) / (60 * 60 * 24));
            }
            
            fputcsv($file, [
                $debt['debt_number'],
                $debt['customer_code'] ?? '',
                $debt['customer_name'] ?? '',
                $debt['original_amount'],
                $debt['remaining_amount'],
                $debt['debt_date'],
                $debt['due_date'],
                ucfirst($debt['status']),
                $daysOverdue,
                $debt['description'] ?? '',
                $debt['created_by_name'] ?? ''
            ]);
        }
        
        fclose($file);
        return $filepath;
    }

    public function updateDebtStatus()
    {
        // Update overdue status for debts past due date
        $sql = "UPDATE debts 
                SET status = 'overdue' 
                WHERE due_date < CURDATE() 
                AND status NOT IN ('paid', 'overdue')";
        
        $stmt = $this->getDb()->query($sql);
        return $stmt->rowCount();
    }

    public function searchDebts($searchTerm)
    {
        return $this->search(['debt_number', 'description'], $searchTerm);
    }

    public function getDebtsWithPagination($conditions = [], $params = [], $limit = 20, $offset = 0)
    {
        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(DISTINCT d.id) as total 
                     FROM debts d 
                     JOIN customers c ON d.customer_id = c.id 
                     {$whereClause}";
        $totalResult = $this->getDb()->fetch($countSql, $params);
        $totalCount = $totalResult['total'];

        // Get debts for current page
        $sql = "SELECT 
                    d.*,
                    c.full_name as customer_name,
                    c.customer_code,
                    c.phone as customer_phone,
                    c.email as customer_email,
                    u.full_name as created_by_name,
                    DATEDIFF(CURDATE(), d.due_date) as days_overdue
                FROM debts d
                JOIN customers c ON d.customer_id = c.id
                LEFT JOIN users u ON d.user_id = u.id
                {$whereClause}
                ORDER BY d.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        $debts = $this->getDb()->fetchAll($sql, $params);
        
        return [
            'debts' => $debts,
            'total_count' => $totalCount
        ];
    }
}