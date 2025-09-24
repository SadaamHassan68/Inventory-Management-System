<?php

class SalesController
{
    private $saleModel;
    private $customerModel;
    private $productModel;

    public function __construct()
    {
        $this->saleModel = new Sale();
        $this->customerModel = new Customer();
        $this->productModel = new Product();
    }

    public function index()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        // Get filters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $customerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $paymentStatus = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Build conditions for pagination
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

        // Get sales with pagination using the model method
        $result = $this->saleModel->getSalesWithPagination($conditions, $params, $limit, $offset);
        $sales = $result['sales'];
        $totalSales = $result['total_count'];
        $totalPages = ceil($totalSales / $limit);

        // Get summary statistics
        $stats = $this->saleModel->getSalesStats();

        $data = [
            'title' => 'Sales Management',
            'sales' => $sales,
            'stats' => $stats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalSales,
                'per_page' => $limit
            ],
            'filters' => [
                'search' => $search,
                'payment_status' => $paymentStatus,
                'customer_id' => $customerId
            ]
        ];

        view('sales.index', $data);
    }

    public function create()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/sales');
            return;
        }

        // Get customer list for dropdown
        $customers = $this->customerModel->active('full_name ASC');
        
        // Get active products for selection
        $products = $this->productModel->active('name ASC');
        
        // Pre-select customer if provided
        $selectedCustomerId = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null;
        $selectedCustomer = null;
        if ($selectedCustomerId) {
            $selectedCustomer = $this->customerModel->find($selectedCustomerId);
        }

        $data = [
            'title' => 'New Sale',
            'customers' => $customers,
            'products' => $products,
            'selectedCustomer' => $selectedCustomer
        ];

        view('sales.create', $data);
    }

    public function store()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/sales');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/sales');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/sales/create');
            return;
        }

        // Validate input
        $errors = $this->saleModel->validateSaleData($_POST);
        if (!empty($errors)) {
            foreach ($errors as $field => $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    setFlash('error', $error);
                }
            }
            redirect('/sales/create');
            return;
        }

        $currentUser = auth();
        $saleData = [
            'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
            'user_id' => $currentUser['id'],
            'subtotal' => (float)$_POST['subtotal'],
            'tax_amount' => (float)($_POST['tax_amount'] ?? 0),
            'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
            'total_amount' => (float)$_POST['total_amount'],
            'payment_method' => $_POST['payment_method'],
            'payment_status' => $_POST['payment_status'] ?? 'paid',
            'notes' => $_POST['notes'] ?? null
        ];

        try {
            $this->saleModel->getDb()->beginTransaction();
            
            $saleId = $this->saleModel->create($saleData);
            
            // Add sale items if provided
            if (!empty($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    if (!empty($item['product_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                        $this->saleModel->addSaleItem(
                            $saleId,
                            (int)$item['product_id'],
                            (int)$item['quantity'],
                            (float)$item['unit_price']
                        );
                    }
                }
            }
            
            // If payment method is debt, create debt record
            if ($saleData['payment_method'] === 'debt' && !empty($saleData['customer_id'])) {
                $debtModel = new Debt();
                $debtData = [
                    'customer_id' => $saleData['customer_id'],
                    'sale_id' => $saleId,
                    'user_id' => $currentUser['id'],
                    'original_amount' => $saleData['total_amount'],
                    'remaining_amount' => $saleData['total_amount'],
                    'due_date' => date('Y-m-d', strtotime('+30 days')), // Default 30 days
                    'description' => 'Sale #' . $saleId
                ];
                $debtModel->create($debtData);
            }
            
            $this->saleModel->getDb()->commit();
            
            // Log activity
            logActivity('sale_created', 'sales', $saleId);
            
            setFlash('success', 'Sale recorded successfully');
            redirect('/sales/' . $saleId);
        } catch (Exception $e) {
            $this->saleModel->getDb()->rollback();
            error_log("Error creating sale: " . $e->getMessage());
            setFlash('error', 'Failed to record sale: ' . $e->getMessage());
            redirect('/sales/create');
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

        $sale = $this->saleModel->getSaleWithDetails($id);
        if (!$sale) {
            setFlash('error', 'Sale not found');
            redirect('/sales');
            return;
        }

        // Get sale items
        $items = $this->saleModel->getSaleItems($id);

        $data = [
            'title' => 'Sale Details - ' . $sale['sale_number'],
            'sale' => $sale,
            'items' => $items
        ];

        view('sales.show', $data);
    }

    public function edit($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/sales');
            return;
        }

        $sale = $this->saleModel->find($id);
        if (!$sale) {
            setFlash('error', 'Sale not found');
            redirect('/sales');
            return;
        }

        // Get customer list for dropdown
        $customers = $this->customerModel->active('full_name ASC');
        
        // Get sale items
        $items = $this->saleModel->getSaleItems($id);

        $data = [
            'title' => 'Edit Sale - ' . $sale['sale_number'],
            'sale' => $sale,
            'customers' => $customers,
            'items' => $items
        ];

        view('sales.edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/sales');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/sales');
            return;
        }

        $sale = $this->saleModel->find($id);
        if (!$sale) {
            setFlash('error', 'Sale not found');
            redirect('/sales');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/sales/edit/' . $id);
            return;
        }

        // Validate input
        $errors = $this->saleModel->validateSaleData($_POST, true, $id);
        if (!empty($errors)) {
            foreach ($errors as $field => $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    setFlash('error', $error);
                }
            }
            redirect('/sales/edit/' . $id);
            return;
        }

        $updateData = [
            'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
            'subtotal' => (float)$_POST['subtotal'],
            'tax_amount' => (float)($_POST['tax_amount'] ?? 0),
            'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
            'total_amount' => (float)$_POST['total_amount'],
            'payment_method' => $_POST['payment_method'],
            'payment_status' => $_POST['payment_status'] ?? 'paid',
            'notes' => $_POST['notes'] ?? null
        ];

        try {
            $this->saleModel->update($id, $updateData);
            
            // Log activity
            logActivity('sale_updated', 'sales', $id);
            
            setFlash('success', 'Sale updated successfully');
            redirect('/sales/' . $id);
        } catch (Exception $e) {
            error_log("Error updating sale: " . $e->getMessage());
            setFlash('error', 'Failed to update sale: ' . $e->getMessage());
            redirect('/sales/edit/' . $id);
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/sales');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/sales');
            return;
        }

        $sale = $this->saleModel->find($id);
        if (!$sale) {
            setFlash('error', 'Sale not found');
            redirect('/sales');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/sales');
            return;
        }

        try {
            $this->saleModel->cancelSale($id, 'Deleted by admin');
            
            // Log activity
            logActivity('sale_deleted', 'sales', $id);
            
            setFlash('success', 'Sale deleted successfully');
            redirect('/sales');
        } catch (Exception $e) {
            error_log("Error deleting sale: " . $e->getMessage());
            setFlash('error', 'Failed to delete sale: ' . $e->getMessage());
            redirect('/sales');
        }
    }
}