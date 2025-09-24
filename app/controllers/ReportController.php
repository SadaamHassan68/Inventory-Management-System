<?php

class ReportController
{
    private $db;

    public function __construct()
    {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $this->db = Database::getInstance();
    }

    /**
     * Reports overview page
     */
    public function index()
    {
        $title = 'Reports Overview';
        
        // Get summary statistics for all reports
        $data = [
            'title' => $title,
            'sales_stats' => $this->getSalesStats(),
            'inventory_stats' => $this->getInventoryStats(),
            'debt_stats' => $this->getDebtStats(),
            'customer_stats' => $this->getCustomerStats()
        ];
        
        view('reports.index', $data);
    }

    /**
     * Sales report
     */
    public function sales()
    {
        $title = 'Sales Report';
        
        // Get filter parameters
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-01'), // First day of current month
            'end_date' => $_GET['end_date'] ?? date('Y-m-d'), // Today
            'customer_id' => $_GET['customer_id'] ?? '',
            'payment_status' => $_GET['payment_status'] ?? ''
        ];
        
        // Build query conditions
        $conditions = ['1=1'];
        $params = [];
        
        if (!empty($filters['start_date'])) {
            $conditions[] = 'DATE(sale_date) >= ?';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $conditions[] = 'DATE(sale_date) <= ?';
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['customer_id'])) {
            $conditions[] = 'customer_id = ?';
            $params[] = $filters['customer_id'];
        }
        
        if (!empty($filters['payment_status'])) {
            $conditions[] = 'payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Get sales data
        $sales = $this->db->fetchAll(
            "SELECT s.*, c.full_name as customer_name, c.customer_code,
                    u.full_name as created_by_name
             FROM sales s
             LEFT JOIN customers c ON s.customer_id = c.id
             LEFT JOIN users u ON s.user_id = u.id
             WHERE {$whereClause}
             ORDER BY s.sale_date DESC",
            $params
        );
        
        // Get summary statistics
        $summary = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_sale,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN payment_status = 'pending' THEN total_amount ELSE 0 END) as pending_amount
             FROM sales
             WHERE {$whereClause}",
            $params
        );
        
        // Get customers for filter dropdown
        $customers = $this->db->fetchAll(
            'SELECT id, customer_code, full_name FROM customers ORDER BY full_name'
        );
        
        $data = [
            'title' => $title,
            'sales' => $sales,
            'summary' => $summary,
            'customers' => $customers,
            'filters' => $filters
        ];
        
        view('reports.sales', $data);
    }

    /**
     * Inventory report
     */
    public function inventory()
    {
        $title = 'Inventory Report';
        
        // Get filter parameters
        $filters = [
            'category_id' => $_GET['category_id'] ?? '',
            'supplier_id' => $_GET['supplier_id'] ?? '',
            'low_stock' => $_GET['low_stock'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];
        
        // Build query conditions
        $conditions = ['p.id IS NOT NULL'];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $conditions[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['supplier_id'])) {
            $conditions[] = 'p.supplier_id = ?';
            $params[] = $filters['supplier_id'];
        }
        
        if ($filters['low_stock'] === '1') {
            $conditions[] = 'p.quantity_in_stock <= p.min_stock_level';
        }
        
        if (!empty($filters['status'])) {
            $conditions[] = 'p.is_active = ?';
            $params[] = $filters['status'] === 'active' ? 1 : 0;
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Get inventory data
        $inventory = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, s.name as supplier_name,
                    (p.price * p.quantity_in_stock) as total_value,
                    CASE WHEN p.quantity_in_stock <= p.min_stock_level THEN 1 ELSE 0 END as is_low_stock
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN suppliers s ON p.supplier_id = s.id
             WHERE {$whereClause}
             ORDER BY p.name",
            $params
        );
        
        // Get summary statistics
        $summary = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_products,
                SUM(quantity_in_stock) as total_quantity,
                SUM(price * quantity_in_stock) as total_value,
                COUNT(CASE WHEN quantity_in_stock <= min_stock_level THEN 1 END) as low_stock_count,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count
             FROM products p
             WHERE {$whereClause}",
            $params
        );
        
        // Get categories and suppliers for filter dropdowns
        $categories = $this->db->fetchAll('SELECT id, name FROM categories ORDER BY name');
        $suppliers = $this->db->fetchAll('SELECT id, name FROM suppliers ORDER BY name');
        
        $data = [
            'title' => $title,
            'inventory' => $inventory,
            'summary' => $summary,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'filters' => $filters
        ];
        
        view('reports.inventory', $data);
    }

    /**
     * Debts report
     */
    public function debts()
    {
        $title = 'Debt Report';
        
        // Get filter parameters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'customer_id' => $_GET['customer_id'] ?? '',
            'overdue_only' => $_GET['overdue_only'] ?? ''
        ];
        
        // Build query conditions
        $conditions = ['d.id IS NOT NULL'];
        $params = [];
        
        if (!empty($filters['status'])) {
            $conditions[] = 'd.status = ?';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['customer_id'])) {
            $conditions[] = 'd.customer_id = ?';
            $params[] = $filters['customer_id'];
        }
        
        if ($filters['overdue_only'] === '1') {
            $conditions[] = 'd.due_date < CURDATE() AND d.status != "paid"';
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Get debts data
        $debts = $this->db->fetchAll(
            "SELECT d.*, c.full_name as customer_name, c.customer_code,
                    CASE WHEN d.due_date < CURDATE() AND d.status != 'paid' THEN 1 ELSE 0 END as is_overdue,
                    DATEDIFF(CURDATE(), d.due_date) as days_overdue
             FROM debts d
             LEFT JOIN customers c ON d.customer_id = c.id
             WHERE {$whereClause}
             ORDER BY d.due_date DESC",
            $params
        );
        
        // Get summary statistics
        $summary = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_debts,
                SUM(original_amount) as total_debt_amount,
                SUM(original_amount - remaining_amount) as total_paid_amount,
                SUM(remaining_amount) as outstanding_amount,
                COUNT(CASE WHEN due_date < CURDATE() AND status != 'paid' THEN 1 END) as overdue_count,
                SUM(CASE WHEN due_date < CURDATE() AND status != 'paid' THEN remaining_amount ELSE 0 END) as overdue_amount
             FROM debts d
             WHERE {$whereClause}",
            $params
        );
        
        // Get customers for filter dropdown
        $customers = $this->db->fetchAll(
            'SELECT id, customer_code, full_name FROM customers ORDER BY full_name'
        );
        
        $data = [
            'title' => $title,
            'debts' => $debts,
            'summary' => $summary,
            'customers' => $customers,
            'filters' => $filters
        ];
        
        view('reports.debts', $data);
    }

    /**
     * Customers report
     */
    public function customers()
    {
        $title = 'Customer Report';
        
        // Get customers with their statistics
        $customers = $this->db->fetchAll(
            "SELECT c.*,
                    COUNT(DISTINCT s.id) as total_sales,
                    COALESCE(SUM(s.total_amount), 0) as total_spent,
                    COUNT(DISTINCT d.id) as total_debts,
                    COALESCE(SUM(d.remaining_amount), 0) as outstanding_debt,
                    MAX(s.sale_date) as last_sale_date
             FROM customers c
             LEFT JOIN sales s ON c.id = s.customer_id
             LEFT JOIN debts d ON c.id = d.customer_id AND d.status != 'paid'
             GROUP BY c.id
             ORDER BY total_spent DESC"
        );
        
        // Get summary statistics
        $summary = $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT c.id) as total_customers,
                COUNT(DISTINCT s.customer_id) as customers_with_sales,
                COALESCE(AVG(customer_totals.total_spent), 0) as average_spent,
                COALESCE(SUM(outstanding.outstanding_debt), 0) as total_outstanding
             FROM customers c
             LEFT JOIN (
                 SELECT customer_id, SUM(total_amount) as total_spent
                 FROM sales
                 GROUP BY customer_id
             ) customer_totals ON c.id = customer_totals.customer_id
             LEFT JOIN sales s ON c.id = s.customer_id
             LEFT JOIN (
                 SELECT customer_id, SUM(remaining_amount) as outstanding_debt
                 FROM debts
                 WHERE status != 'paid'
                 GROUP BY customer_id
             ) outstanding ON c.id = outstanding.customer_id"
        );
        
        $data = [
            'title' => $title,
            'customers' => $customers,
            'summary' => $summary
        ];
        
        view('reports.customers', $data);
    }

    /**
     * Get sales statistics for overview
     */
    private function getSalesStats()
    {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total_sales,
                SUM(total_amount) as total_revenue,
                COUNT(CASE WHEN DATE(sale_date) = CURDATE() THEN 1 END) as today_sales,
                SUM(CASE WHEN DATE(sale_date) = CURDATE() THEN total_amount ELSE 0 END) as today_revenue
             FROM sales"
        );
    }

    /**
     * Get inventory statistics for overview
     */
    private function getInventoryStats()
    {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total_products,
                SUM(quantity_in_stock) as total_quantity,
                SUM(price * quantity_in_stock) as total_value,
                COUNT(CASE WHEN quantity_in_stock <= min_stock_level THEN 1 END) as low_stock_count
             FROM products 
             WHERE is_active = 1"
        );
    }

    /**
     * Get debt statistics for overview
     */
    private function getDebtStats()
    {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total_debts,
                SUM(remaining_amount) as outstanding_amount,
                COUNT(CASE WHEN due_date < CURDATE() AND status != 'paid' THEN 1 END) as overdue_count,
                SUM(CASE WHEN due_date < CURDATE() AND status != 'paid' THEN remaining_amount ELSE 0 END) as overdue_amount
             FROM debts"
        );
    }

    /**
     * Get customer statistics for overview
     */
    private function getCustomerStats()
    {
        return $this->db->fetch(
            "SELECT 
                COUNT(DISTINCT c.id) as total_customers,
                COUNT(DISTINCT s.customer_id) as active_customers,
                COALESCE(AVG(customer_sales.total_spent), 0) as average_spent
             FROM customers c
             LEFT JOIN sales s ON c.id = s.customer_id
             LEFT JOIN (
                 SELECT customer_id, SUM(total_amount) as total_spent
                 FROM sales
                 GROUP BY customer_id
             ) customer_sales ON c.id = customer_sales.customer_id"
        );
    }
}