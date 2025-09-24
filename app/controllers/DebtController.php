<?php

class DebtController
{
    private $debtModel;
    private $customerModel;

    public function __construct()
    {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $this->debtModel = new Debt();
        $this->customerModel = new Customer();
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
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $customerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;

        // Get debts with customer details
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(d.debt_number LIKE :search OR c.full_name LIKE :search OR c.customer_code LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($status)) {
            $conditions[] = "d.status = :status";
            $params['status'] = $status;
        }

        if ($customerId) {
            $conditions[] = "d.customer_id = :customer_id";
            $params['customer_id'] = $customerId;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        // Get debts with pagination using the model method
        $result = $this->debtModel->getDebtsWithPagination($conditions, $params, $limit, $offset);
        $debts = $result['debts'];
        $totalDebts = $result['total_count'];
        $totalPages = ceil($totalDebts / $limit);

        // Get summary statistics
        $stats = $this->debtModel->getDebtStats();

        $data = [
            'title' => 'Debt Management',
            'debts' => $debts,
            'stats' => $stats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalDebts,
                'per_page' => $limit
            ],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'customer_id' => $customerId
            ]
        ];

        view('debts.index', $data);
    }

    public function create()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/debts');
            return;
        }

        // Get customer list for dropdown
        $customers = $this->customerModel->active('full_name ASC');
        
        // Pre-select customer if provided
        $selectedCustomerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $selectedCustomer = null;
        if ($selectedCustomerId) {
            $selectedCustomer = $this->customerModel->find($selectedCustomerId);
        }

        $data = [
            'title' => 'Record New Debt',
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer
        ];

        view('debts.create', $data);
    }

    public function store()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/debts');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/debts');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/debts/create');
            return;
        }

        // Validate input
        $data = $this->validateDebtData($_POST);
        if (!$data) {
            redirect('/debts/create');
            return;
        }

        $currentUser = auth();
        $data['user_id'] = $currentUser['id'];

        try {
            $debtId = $this->debtModel->create($data);
            
            // Log activity
            logActivity('debt_created', 'debts', $debtId);
            
            setFlash('success', 'Debt recorded successfully');
            redirect('/debts/' . $debtId);
        } catch (Exception $e) {
            error_log("Error creating debt: " . $e->getMessage());
            setFlash('error', 'Failed to record debt: ' . $e->getMessage());
            redirect('/debts/create');
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

        $debt = $this->debtModel->getDebtWithDetails($id);
        if (!$debt) {
            setFlash('error', 'Debt not found');
            redirect('/debts');
            return;
        }

        // Get payment history
        $payments = $this->debtModel->getDebtPayments($id);

        $data = [
            'title' => 'Debt Details - ' . $debt['debt_number'],
            'debt' => $debt,
            'payments' => $payments
        ];

        view('debts.show', $data);
    }

    public function edit($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/debts');
            return;
        }

        $debt = $this->debtModel->find($id);
        if (!$debt) {
            setFlash('error', 'Debt not found');
            redirect('/debts');
            return;
        }

        // Get customer list for dropdown
        $customers = $this->customerModel->active('full_name ASC');

        $data = [
            'title' => 'Edit Debt - ' . $debt['debt_number'],
            'debt' => $debt,
            'customers' => $customers
        ];

        view('debts.edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/debts');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/debts');
            return;
        }

        $debt = $this->debtModel->find($id);
        if (!$debt) {
            setFlash('error', 'Debt not found');
            redirect('/debts');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/debts/edit/' . $id);
            return;
        }

        // Validate input
        $data = $this->validateDebtData($_POST, true, $id);
        if (!$data) {
            redirect('/debts/edit/' . $id);
            return;
        }

        try {
            $this->debtModel->update($id, $data);
            
            // Log activity
            logActivity('debt_updated', 'debts', $id);
            
            setFlash('success', 'Debt updated successfully');
            redirect('/debts/' . $id);
        } catch (Exception $e) {
            error_log("Error updating debt: " . $e->getMessage());
            setFlash('error', 'Failed to update debt: ' . $e->getMessage());
            redirect('/debts/edit/' . $id);
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/debts');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/debts');
            return;
        }

        $debt = $this->debtModel->find($id);
        if (!$debt) {
            setFlash('error', 'Debt not found');
            redirect('/debts');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/debts');
            return;
        }

        try {
            // Only allow deletion if no payments have been made
            $payments = $this->debtModel->getDebtPayments($id);
            if (!empty($payments)) {
                setFlash('error', 'Cannot delete debt with payment history');
                redirect('/debts/' . $id);
                return;
            }

            $this->debtModel->delete($id);
            
            // Log activity
            logActivity('debt_deleted', 'debts', $id);
            
            setFlash('success', 'Debt deleted successfully');
            redirect('/debts');
        } catch (Exception $e) {
            error_log("Error deleting debt: " . $e->getMessage());
            setFlash('error', 'Failed to delete debt: ' . $e->getMessage());
            redirect('/debts');
        }
    }

    public function addPayment($id)
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/debts');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/debts/' . $id);
            return;
        }

        $debt = $this->debtModel->find($id);
        if (!$debt) {
            setFlash('error', 'Debt not found');
            redirect('/debts');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/debts/' . $id);
            return;
        }

        $amount = (float)($_POST['payment_amount'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $notes = $_POST['notes'] ?? null;

        if ($amount <= 0) {
            setFlash('error', 'Payment amount must be greater than zero');
            redirect('/debts/' . $id);
            return;
        }

        if ($amount > $debt['remaining_amount']) {
            setFlash('error', 'Payment amount cannot exceed remaining debt amount');
            redirect('/debts/' . $id);
            return;
        }

        try {
            $paymentId = $this->debtModel->addPayment($id, $amount, $paymentMethod, $notes);
            
            // Log activity
            logActivity('debt_payment_added', 'debt_payments', $paymentId);
            
            setFlash('success', 'Payment recorded successfully');
            redirect('/debts/' . $id);
        } catch (Exception $e) {
            error_log("Error adding payment: " . $e->getMessage());
            setFlash('error', 'Failed to record payment: ' . $e->getMessage());
            redirect('/debts/' . $id);
        }
    }

    public function overdue()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $overdueDebts = $this->debtModel->getOverdueDebts();

        // Calculate summary statistics
        $totalOverdueAmount = array_sum(array_column($overdueDebts, 'remaining_amount'));
        $avgDaysOverdue = 0;
        if (count($overdueDebts) > 0) {
            $totalDaysOverdue = array_sum(array_column($overdueDebts, 'days_overdue'));
            $avgDaysOverdue = round($totalDaysOverdue / count($overdueDebts));
        }

        $data = [
            'title' => 'Overdue Debts',
            'debts' => $overdueDebts,
            'summary' => [
                'total_count' => count($overdueDebts),
                'total_amount' => $totalOverdueAmount,
                'avg_days_overdue' => $avgDaysOverdue
            ]
        ];

        view('debts.overdue', $data);
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
            $debts = $this->debtModel->getDebtsWithCustomers();
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="debts_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($output, [
                'Debt Number',
                'Customer Code',
                'Customer Name',
                'Customer Phone',
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
                
                fputcsv($output, [
                    $debt['debt_number'],
                    $debt['customer_code'] ?? '',
                    $debt['customer_name'] ?? '',
                    $debt['customer_phone'] ?? '',
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
            
            fclose($output);
            
            // Log activity
            logActivity('debts_exported');
            
        } catch (Exception $e) {
            error_log("Error exporting debts: " . $e->getMessage());
            setFlash('error', 'Export failed: ' . $e->getMessage());
            redirect('/debts');
        }
    }

    private function validateDebtData($data, $isUpdate = false, $debtId = null)
    {
        $validated = [];

        // Required fields
        if (empty($data['customer_id'])) {
            setErrors(['customer_id' => ['Customer is required']]);
            return false;
        }
        $validated['customer_id'] = (int)$data['customer_id'];

        if (empty($data['original_amount']) || $data['original_amount'] <= 0) {
            setErrors(['original_amount' => ['Original amount must be greater than zero']]);
            return false;
        }
        $validated['original_amount'] = (float)$data['original_amount'];

        if (empty($data['due_date'])) {
            setErrors(['due_date' => ['Due date is required']]);
            return false;
        }
        $validated['due_date'] = $data['due_date'];

        // Optional fields
        $validated['debt_number'] = trim($data['debt_number'] ?? '');
        $validated['description'] = trim($data['description'] ?? '');
        $validated['notes'] = trim($data['notes'] ?? '');
        $validated['sale_id'] = !empty($data['sale_id']) ? (int)$data['sale_id'] : null;

        // For updates, handle remaining amount
        if ($isUpdate && isset($data['remaining_amount'])) {
            $validated['remaining_amount'] = (float)$data['remaining_amount'];
        }

        // Validate using model validation
        $errors = $this->debtModel->validateDebt($validated, $isUpdate, $debtId);
        if (!empty($errors)) {
            setErrors($errors);
            return false;
        }

        return $validated;
    }
}