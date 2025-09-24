<?php

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
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
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get customers with details
        $customers = $this->customerModel->getCustomersWithDetails();
        
        // Apply search filter if provided
        if (!empty($search)) {
            $customers = array_filter($customers, function($customer) use ($search) {
                return stripos($customer['full_name'], $search) !== false ||
                       stripos($customer['customer_code'], $search) !== false ||
                       stripos($customer['phone'], $search) !== false ||
                       stripos($customer['email'] ?? '', $search) !== false;
            });
        }

        $totalCustomers = count($customers);
        $totalPages = ceil($totalCustomers / $limit);
        
        // Apply pagination
        $customers = array_slice($customers, $offset, $limit);

        $data = [
            'title' => 'Customers',
            'customers' => $customers,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalCustomers,
                'per_page' => $limit
            ],
            'filters' => [
                'search' => $search
            ]
        ];

        view('customers.index', $data);
    }

    public function create()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        $data = [
            'title' => 'Add New Customer'
        ];

        view('customers.create', $data);
    }

    public function store()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/customers');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/customers/create');
            return;
        }

        // Validate input
        $data = $this->validateCustomerData($_POST);
        if (!$data) {
            setFlash('error', 'Validation failed');
            redirect('/customers/create');
            return;
        }

        $currentUser = auth();
        $data['created_by'] = $currentUser['id'];

        try {
            $customerId = $this->customerModel->create($data);
            
            // Log activity
            logActivity('customer_created', 'customers', $customerId);
            
            setFlash('success', 'Customer created successfully');
            redirect('/customers');
        } catch (Exception $e) {
            error_log("Error creating customer: " . $e->getMessage());
            setFlash('error', 'Failed to create customer');
            redirect('/customers/create');
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

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            setFlash('error', 'Customer not found');
            redirect('/customers');
            return;
        }

        // Get customer debts and sales
        $debts = $this->customerModel->getCustomerDebts($id);
        $sales = $this->customerModel->getCustomerSales($id);

        $data = [
            'title' => 'Customer Details - ' . $customer['full_name'],
            'customer' => $customer,
            'debts' => $debts,
            'sales' => $sales
        ];

        view('customers.show', $data);
    }

    public function edit($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            setFlash('error', 'Customer not found');
            redirect('/customers');
            return;
        }

        $data = [
            'title' => 'Edit Customer - ' . $customer['full_name'],
            'customer' => $customer
        ];

        view('customers.edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/customers');
            return;
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            setFlash('error', 'Customer not found');
            redirect('/customers');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/customers/edit/' . $id);
            return;
        }

        // Validate input
        $data = $this->validateCustomerData($_POST, true, $id);
        if (!$data) {
            setFlash('error', 'Validation failed');
            redirect('/customers/edit/' . $id);
            return;
        }

        try {
            $this->customerModel->update($id, $data);
            
            // Log activity
            logActivity('customer_updated', 'customers', $id);
            
            setFlash('success', 'Customer updated successfully');
            redirect('/customers');
        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            setFlash('error', 'Failed to update customer');
            redirect('/customers/edit/' . $id);
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/customers');
            return;
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            setFlash('error', 'Customer not found');
            redirect('/customers');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/customers');
            return;
        }

        try {
            // Soft delete - set is_active to 0
            $this->customerModel->update($id, ['is_active' => 0]);
            
            // Log activity
            logActivity('customer_deleted', 'customers', $id);
            
            setFlash('success', 'Customer deleted successfully');
            redirect('/customers');
        } catch (Exception $e) {
            error_log("Error deleting customer: " . $e->getMessage());
            setFlash('error', 'Failed to delete customer');
            redirect('/customers');
        }
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
            $customers = $this->customerModel->getCustomersWithDetails();
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($output, [
                'Customer Code',
                'Full Name',
                'Phone',
                'Email',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Date of Birth',
                'Credit Limit',
                'Current Balance',
                'Outstanding Balance',
                'Total Debts',
                'Status',
                'Created Date'
            ]);
            
            foreach ($customers as $customer) {
                fputcsv($output, [
                    $customer['customer_code'],
                    $customer['full_name'],
                    $customer['phone'],
                    $customer['email'] ?? '',
                    $customer['address'] ?? '',
                    $customer['city'] ?? '',
                    $customer['state'] ?? '',
                    $customer['postal_code'] ?? '',
                    $customer['date_of_birth'] ?? '',
                    $customer['credit_limit'] ?? '0',
                    $customer['current_balance'] ?? '0',
                    $customer['outstanding_balance'] ?? '0',
                    $customer['total_debts'] ?? '0',
                    ($customer['is_active'] ?? 1) ? 'Active' : 'Inactive',
                    $customer['created_at'] ?? ''
                ]);
            }
            
            fclose($output);
            
            // Log activity
            logActivity('customers_exported');
            
        } catch (Exception $e) {
            error_log("Error exporting customers: " . $e->getMessage());
            setFlash('error', 'Export failed');
            redirect('/customers');
        }
    }

    public function import()
    {
        // Check permission
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin privileges required.');
            redirect('/customers');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = [
                'title' => 'Import Customers'
            ];
            view('customers.import', $data);
            return;
        }

        // Handle POST request
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', 'File upload failed');
            redirect('/customers/import');
            return;
        }

        // Validate CSRF token
        if (!verifyCsrfToken($_POST['_token'] ?? '')) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('/customers/import');
            return;
        }

        try {
            $results = $this->customerModel->importFromCSV($_FILES['csv_file']['tmp_name']);
            
            // Log activity
            logActivity('customers_imported');

            if (!empty($results['errors'])) {
                $_SESSION['import_errors'] = $results['errors'];
                setFlash('warning', 'Import completed with some errors. ' . $results['success'] . ' customers imported.');
                redirect('/customers/import');
            } else {
                setFlash('success', 'Import completed successfully. ' . $results['success'] . ' customers imported.');
                redirect('/customers');
            }

        } catch (Exception $e) {
            error_log("Error importing customers: " . $e->getMessage());
            setFlash('error', 'Import failed');
            redirect('/customers/import');
        }
    }

    public function outstanding()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $customers = $this->customerModel->getCustomersWithOutstandingDebts();

        $data = [
            'title' => 'Customers with Outstanding Debts',
            'customers' => $customers
        ];

        view('customers.outstanding', $data);
    }

    public function overdue()
    {
        // Check permission
        if (!isAdmin() && !isStaff()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
            return;
        }

        $customers = $this->customerModel->getCustomersWithOverdueDebts();

        $data = [
            'title' => 'Customers with Overdue Debts',
            'customers' => $customers
        ];

        view('customers.overdue', $data);
    }

    private function validateCustomerData($data, $isUpdate = false, $customerId = null)
    {
        $validated = [];

        // Required fields
        if (empty($data['full_name'])) {
            return false;
        }
        $validated['full_name'] = trim($data['full_name']);

        if (empty($data['phone'])) {
            return false;
        }
        $validated['phone'] = trim($data['phone']);

        // Optional fields
        $validated['customer_code'] = trim($data['customer_code'] ?? '');
        $validated['email'] = !empty($data['email']) ? trim($data['email']) : null;
        $validated['address'] = !empty($data['address']) ? trim($data['address']) : null;
        $validated['city'] = !empty($data['city']) ? trim($data['city']) : null;
        $validated['state'] = !empty($data['state']) ? trim($data['state']) : null;
        $validated['postal_code'] = !empty($data['postal_code']) ? trim($data['postal_code']) : null;
        $validated['date_of_birth'] = !empty($data['date_of_birth']) ? $data['date_of_birth'] : null;
        $validated['credit_limit'] = (float)($data['credit_limit'] ?? 0);
        $validated['notes'] = !empty($data['notes']) ? trim($data['notes']) : null;

        // Status
        $validated['is_active'] = isset($data['status']) && $data['status'] === 'active' ? 1 : 0;

        // Validate using model validation
        $errors = $this->customerModel->validateCustomer($validated, $isUpdate, $customerId);
        if (!empty($errors)) {
            setErrors($errors);
            return false;
        }

        return $validated;
    }
}