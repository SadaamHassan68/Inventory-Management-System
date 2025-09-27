<?php

class ApiController
{
    public function __construct()
    {
        // Set JSON response header
        header('Content-Type: application/json');
        
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function searchProducts()
    {
        try {
            $query = $_GET['q'] ?? '';
            $limit = (int)($_GET['limit'] ?? 10);

            if (empty($query)) {
                echo json_encode([]);
                return;
            }

            $productModel = new Product();
            $products = $productModel->search(['name', 'sku'], $query, 'is_active = 1', [], 'name ASC', $limit);

            $results = array_map(function($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'stock' => $product['quantity_in_stock'],
                    'unit' => $product['unit'] ?? 'pcs'
                ];
            }, $products);

            echo json_encode($results);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
    }

    public function searchCustomers()
    {
        try {
            $query = $_GET['q'] ?? '';
            $limit = (int)($_GET['limit'] ?? 10);

            if (empty($query)) {
                echo json_encode([]);
                return;
            }

            $customerModel = new Customer();
            $customers = $customerModel->search(['full_name', 'customer_code', 'phone'], $query, 'status = "active"', [], 'full_name ASC', $limit);

            $results = array_map(function($customer) {
                return [
                    'id' => $customer['id'],
                    'name' => $customer['full_name'],
                    'code' => $customer['customer_code'],
                    'phone' => $customer['phone'],
                    'email' => $customer['email'] ?? '',
                    'credit_limit' => $customer['credit_limit'] ?? 0
                ];
            }, $customers);

            echo json_encode($results);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
    }

    public function dashboardStats()
    {
        try {
            $productModel = new Product();
            $customerModel = new Customer();
            $saleModel = new Sale();
            $debtModel = new Debt();

            // Get basic statistics
            $stats = [
                'products' => [
                    'total' => $productModel->count(),
                    'low_stock' => $productModel->count('quantity_in_stock <= min_stock_level AND is_active = 1'),
                    'out_of_stock' => $productModel->count('quantity_in_stock = 0 AND is_active = 1')
                ],
                'customers' => [
                    'total' => $customerModel->count(),
                    'active' => $customerModel->count('status = "active"')
                ],
                'sales' => [
                    'today' => $saleModel->count('DATE(sale_date) = CURDATE()'),
                    'this_month' => $saleModel->count('MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())')
                ],
                'debts' => [
                    'total_outstanding' => $debtModel->count('status != "paid"'),
                    'overdue' => $debtModel->count('due_date < CURDATE() AND status != "paid"')
                ]
            ];

            echo json_encode($stats);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load statistics']);
        }
    }
}