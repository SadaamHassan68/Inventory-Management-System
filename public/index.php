<?php
// Bootstrap the application
require_once __DIR__ . '/../bootstrap/app.php';

// Authentication middleware
$router->middleware('auth', function() {
    if (!isLoggedIn()) {
        redirect('/login');
        return false;
    }
    return true;
});

$router->middleware('admin', function() {
    if (!isAdmin()) {
        setFlash('error', 'Access denied. Admin privileges required.');
        redirect('/dashboard');
        return false;
    }
    return true;
});

// Public routes
$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');

// Protected routes
$router->get('/dashboard', 'DashboardController@index');
$router->get('/profile', 'AuthController@profile');
$router->post('/profile', 'AuthController@updateProfile');
$router->post('/profile/change-password', 'AuthController@changePassword');

// Product routes
$router->get('/products', 'ProductController@index');
$router->get('/products/create', 'ProductController@create');
$router->post('/products', 'ProductController@store');
$router->get('/products/{id}', 'ProductController@show');
$router->get('/products/{id}/edit', 'ProductController@edit');
$router->put('/products/{id}', 'ProductController@update');
$router->delete('/products/{id}', 'ProductController@delete');
$router->post('/products/{id}/deactivate', 'ProductController@deactivate');
$router->post('/products/{id}/reactivate', 'ProductController@reactivate');
$router->get('/products/low-stock', 'ProductController@lowStock');
$router->post('/products/import', 'ProductController@import');
$router->get('/products/export', 'ProductController@export');

// Customer routes
$router->get('/customers', 'CustomerController@index');
$router->get('/customers/create', 'CustomerController@create');
$router->post('/customers', 'CustomerController@store');
$router->get('/customers/{id}', 'CustomerController@show');
$router->get('/customers/{id}/edit', 'CustomerController@edit');
$router->put('/customers/{id}', 'CustomerController@update');
$router->delete('/customers/{id}', 'CustomerController@delete');
$router->post('/customers/import', 'CustomerController@import');
$router->get('/customers/export', 'CustomerController@export');

// Debt routes
$router->get('/debts', 'DebtController@index');
$router->get('/debts/create', 'DebtController@create');
$router->post('/debts', 'DebtController@store');
$router->get('/debts/{id}', 'DebtController@show');
$router->get('/debts/{id}/edit', 'DebtController@edit');
$router->put('/debts/{id}', 'DebtController@update');
$router->delete('/debts/{id}', 'DebtController@delete');
$router->get('/debts/overdue', 'DebtController@overdue');
$router->post('/debts/{id}/payment', 'DebtController@addPayment');

// Sales routes
$router->get('/sales', 'SalesController@index');
$router->get('/sales/create', 'SalesController@create');
$router->post('/sales', 'SalesController@store');
$router->get('/sales/{id}', 'SalesController@show');
$router->get('/sales/{id}/edit', 'SalesController@edit');
$router->put('/sales/{id}', 'SalesController@update');
$router->delete('/sales/{id}', 'SalesController@delete');

// Report routes
$router->get('/reports', 'ReportController@index');
$router->get('/reports/sales', 'ReportController@sales');
$router->get('/reports/inventory', 'ReportController@inventory');
$router->get('/reports/debts', 'ReportController@debts');
$router->get('/reports/customers', 'ReportController@customers');

// Category routes
// Category routes
$router->get('/categories', 'CategoryController@index');
$router->post('/categories', 'CategoryController@store');
$router->put('/categories/{id}', 'CategoryController@update');
$router->delete('/categories/{id}', 'CategoryController@delete');

// Supplier routes
$router->get('/suppliers', 'SupplierController@index');
$router->post('/suppliers', 'SupplierController@store');
$router->put('/suppliers/{id}', 'SupplierController@update');
$router->delete('/suppliers/{id}', 'SupplierController@delete');

// Settings routes (Admin only)
$router->get('/settings', 'SettingsController@index');
$router->post('/settings', 'SettingsController@update');

// User management routes (Admin only)
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users', 'UserController@store');
$router->get('/users/{id}/edit', 'UserController@edit');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@delete');

// API routes for AJAX requests
$router->get('/api/products/search', 'ApiController@searchProducts');
$router->get('/api/customers/search', 'ApiController@searchCustomers');
$router->get('/api/dashboard/stats', 'ApiController@dashboardStats');

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    // Handle errors
    if ($config['app']['debug']) {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        view('errors.500');
    }
}