<?php
// Configuration file for Inventory Management System

return [
    // Database Configuration
    'database' => [
        'host' => 'localhost',
        'dbname' => 'inventory_management',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    // Application Configuration
    'app' => [
        'name' => 'Inventory Management System',
        'version' => '1.0.0',
        'timezone' => 'UTC',
        'debug' => true,
        'url' => 'http://localhost/inventory%20ms/public/index.php',
        'session_lifetime' => 7200, // 2 hours
    ],

    // Security Configuration
    'security' => [
        'password_min_length' => 6,
        'session_name' => 'inventory_session',
        'csrf_token_name' => '_token',
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],

    // File Upload Configuration
    'upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'csv', 'xlsx'],
        'upload_path' => 'storage/uploads/',
    ],

    // Pagination Configuration
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],

    // Stock Alert Configuration
    'stock' => [
        'low_stock_threshold' => 10,
        'critical_stock_threshold' => 5,
    ],

    // Debt Management Configuration
    'debt' => [
        'default_due_days' => 30,
        'overdue_notification_days' => 7,
    ],

    // Roles and Permissions
    'roles' => [
        'admin' => [
            'manage_products',
            'manage_customers',
            'manage_debts',
            'manage_users',
            'view_reports',
            'system_settings',
        ],
        'staff' => [
            'add_sales',
            'update_stock',
            'record_debts',
            'view_products',
            'view_customers',
        ],
    ],
];