<?php
/**
 * Dashboard Controller for Inventory Management System
 */
class DashboardController {
    private $userModel;
    private $db;

    public function __construct() {
        // Check authentication
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    public function index() {
        try {
            // Get dashboard statistics
            $stats = $this->getDashboardStats();
            
            // Get recent activities
            $recentActivities = $this->getRecentActivities();
            
            // Get low stock products
            $lowStockProducts = $this->getLowStockProducts();
            
            // Get overdue debts
            $overdueDebts = $this->getOverdueDebts();
            
            // Get monthly sales data for chart
            $monthlySales = $this->getMonthlySalesData();
            
            view('dashboard.index', [
                'stats' => $stats,
                'recentActivities' => $recentActivities,
                'lowStockProducts' => $lowStockProducts,
                'overdueDebts' => $overdueDebts,
                'monthlySales' => $monthlySales
            ]);
            
        } catch (\Exception $e) {
            setFlash('error', 'Error loading dashboard: ' . $e->getMessage());
            view('dashboard.index', [
                'stats' => [],
                'recentActivities' => [],
                'lowStockProducts' => [],
                'overdueDebts' => [],
                'monthlySales' => []
            ]);
        }
    }

    private function getDashboardStats() {
        $stats = [];
        
        // Total products
        $stats['total_products'] = $this->db->count('products', 'is_active = 1');
        
        // Low stock products
        $stats['low_stock_products'] = $this->db->count(
            'products', 
            'quantity_in_stock <= min_stock_level AND is_active = 1'
        );
        
        // Total customers
        $stats['total_customers'] = $this->db->count('customers', 'is_active = 1');
        
        // Total outstanding debts
        $result = $this->db->fetch(
            'SELECT SUM(remaining_amount) as total FROM debts WHERE status != "paid"'
        );
        $stats['total_outstanding_debts'] = $result['total'] ?? 0;
        
        // Today's sales
        $result = $this->db->fetch(
            'SELECT COUNT(*) as count, SUM(total_amount) as total 
             FROM sales 
             WHERE DATE(sale_date) = CURDATE()'
        );
        $stats['today_sales_count'] = $result['count'] ?? 0;
        $stats['today_sales_amount'] = $result['total'] ?? 0;
        
        // This month's sales
        $result = $this->db->fetch(
            'SELECT COUNT(*) as count, SUM(total_amount) as total 
             FROM sales 
             WHERE YEAR(sale_date) = YEAR(CURDATE()) 
             AND MONTH(sale_date) = MONTH(CURDATE())'
        );
        $stats['month_sales_count'] = $result['count'] ?? 0;
        $stats['month_sales_amount'] = $result['total'] ?? 0;
        
        // Overdue debts count
        $stats['overdue_debts_count'] = $this->db->count(
            'debts', 
            'due_date < CURDATE() AND status != "paid"'
        );
        
        // Total inventory value
        $result = $this->db->fetch(
            'SELECT SUM(price * quantity_in_stock) as total 
             FROM products 
             WHERE is_active = 1'
        );
        $stats['total_inventory_value'] = $result['total'] ?? 0;
        
        return $stats;
    }

    private function getRecentActivities($limit = 10) {
        $sql = "SELECT 
                    al.action, 
                    al.table_name, 
                    al.created_at,
                    u.full_name,
                    u.username
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql);
    }

    private function getLowStockProducts($limit = 5) {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.sku,
                    p.quantity_in_stock,
                    p.min_stock_level,
                    c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.quantity_in_stock <= p.min_stock_level 
                AND p.is_active = 1
                ORDER BY (p.quantity_in_stock / p.min_stock_level) ASC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql);
    }

    private function getOverdueDebts($limit = 5) {
        $sql = "SELECT 
                    d.id,
                    d.debt_number,
                    d.remaining_amount,
                    d.due_date,
                    DATEDIFF(CURDATE(), d.due_date) as days_overdue,
                    c.full_name as customer_name,
                    c.phone as customer_phone
                FROM debts d
                JOIN customers c ON d.customer_id = c.id
                WHERE d.due_date < CURDATE() 
                AND d.status != 'paid'
                ORDER BY d.due_date ASC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql);
    }

    private function getMonthlySalesData() {
        $sql = "SELECT 
                    DATE_FORMAT(sale_date, '%Y-%m') as month,
                    COUNT(*) as sales_count,
                    SUM(total_amount) as total_amount
                FROM sales
                WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
                ORDER BY month ASC";
        
        $results = $this->db->fetchAll($sql);
        
        // Fill in missing months with zero values
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $months[] = date('M Y', strtotime("-{$i} months"));
            
            $found = false;
            foreach ($results as $result) {
                if ($result['month'] === $month) {
                    $data[] = [
                        'month' => date('M Y', strtotime($month . '-01')),
                        'sales_count' => (int)$result['sales_count'],
                        'total_amount' => (float)$result['total_amount']
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $data[] = [
                    'month' => date('M Y', strtotime($month . '-01')),
                    'sales_count' => 0,
                    'total_amount' => 0
                ];
            }
        }
        
        return $data;
    }

    public function getQuickStats() {
        // This method can be called via AJAX for real-time updates
        header('Content-Type: application/json');
        
        try {
            $stats = $this->getDashboardStats();
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getNotifications() {
        $notifications = [];
        
        // Low stock notifications
        $lowStockCount = $this->db->count(
            'products', 
            'quantity_in_stock <= min_stock_level AND is_active = 1'
        );
        if ($lowStockCount > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "{$lowStockCount} products are running low on stock",
                'url' => url('products/low-stock')
            ];
        }
        
        // Overdue debts notifications
        $overdueCount = $this->db->count(
            'debts', 
            'due_date < CURDATE() AND status != "paid"'
        );
        if ($overdueCount > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$overdueCount} customers have overdue payments",
                'url' => url('debts/overdue')
            ];
        }
        
        // Critical stock notifications (below 5 units)
        $criticalStockCount = $this->db->count(
            'products', 
            'quantity_in_stock <= 5 AND is_active = 1'
        );
        if ($criticalStockCount > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$criticalStockCount} products are critically low",
                'url' => url('products/low-stock')
            ];
        }
        
        return $notifications;
    }

    public function exportData() {
        // This method handles exporting dashboard data
        if (!isAdmin()) {
            setFlash('error', 'Access denied');
            redirect('/dashboard');
        }
        
        $type = $_GET['type'] ?? 'summary';
        
        switch ($type) {
            case 'summary':
                $this->exportSummaryReport();
                break;
            case 'activities':
                $this->exportActivitiesReport();
                break;
            default:
                setFlash('error', 'Invalid export type');
                redirect('/dashboard');
        }
    }

    private function exportSummaryReport() {
        $stats = $this->getDashboardStats();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="dashboard_summary_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Metric', 'Value']);
        fputcsv($output, ['Total Products', $stats['total_products']]);
        fputcsv($output, ['Low Stock Products', $stats['low_stock_products']]);
        fputcsv($output, ['Total Customers', $stats['total_customers']]);
        fputcsv($output, ['Total Outstanding Debts', formatCurrency($stats['total_outstanding_debts'])]);
        fputcsv($output, ['Today Sales Count', $stats['today_sales_count']]);
        fputcsv($output, ['Today Sales Amount', formatCurrency($stats['today_sales_amount'])]);
        fputcsv($output, ['Month Sales Count', $stats['month_sales_count']]);
        fputcsv($output, ['Month Sales Amount', formatCurrency($stats['month_sales_amount'])]);
        fputcsv($output, ['Overdue Debts Count', $stats['overdue_debts_count']]);
        fputcsv($output, ['Total Inventory Value', formatCurrency($stats['total_inventory_value'])]);
        
        fclose($output);
    }

    private function exportActivitiesReport() {
        $activities = $this->getRecentActivities(100);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="recent_activities_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Date', 'User', 'Action', 'Table']);
        
        foreach ($activities as $activity) {
            fputcsv($output, [
                formatDate($activity['created_at']),
                $activity['full_name'] ?? $activity['username'] ?? 'Unknown',
                $activity['action'],
                $activity['table_name'] ?? ''
            ]);
        }
        
        fclose($output);
    }
}