-- Inventory Management System Database Schema
-- Created: 2025-09-23

-- Create database
CREATE DATABASE IF NOT EXISTS inventory_management 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE inventory_management;

-- Users table for authentication
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Product categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Suppliers table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    category_id INT,
    supplier_id INT,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2),
    quantity_in_stock INT DEFAULT 0,
    min_stock_level INT DEFAULT 10,
    max_stock_level INT DEFAULT 1000,
    unit VARCHAR(20) DEFAULT 'pcs',
    image_path VARCHAR(255),
    barcode VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sku (sku),
    INDEX idx_name (name),
    INDEX idx_category (category_id),
    INDEX idx_supplier (supplier_id)
);

-- Customers table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    date_of_birth DATE,
    credit_limit DECIMAL(10,2) DEFAULT 0.00,
    current_balance DECIMAL(10,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_customer_code (customer_code),
    INDEX idx_full_name (full_name),
    INDEX idx_phone (phone)
);

-- Sales/Transactions table
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT,
    user_id INT NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'credit', 'debit', 'bank_transfer', 'debt') NOT NULL,
    payment_status ENUM('paid', 'partial', 'pending') DEFAULT 'paid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_sale_number (sale_number),
    INDEX idx_customer (customer_id),
    INDEX idx_sale_date (sale_date)
);

-- Sale items table
CREATE TABLE sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_sale (sale_id),
    INDEX idx_product (product_id)
);

-- Customer debts table
CREATE TABLE debts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    debt_number VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    sale_id INT,
    user_id INT NOT NULL,
    original_amount DECIMAL(10,2) NOT NULL,
    remaining_amount DECIMAL(10,2) NOT NULL,
    debt_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    status ENUM('unpaid', 'partially_paid', 'paid', 'overdue') DEFAULT 'unpaid',
    description TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_debt_number (debt_number),
    INDEX idx_customer (customer_id),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status)
);

-- Debt payments table
CREATE TABLE debt_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    debt_id INT NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('cash', 'credit', 'debit', 'bank_transfer') DEFAULT 'cash',
    notes TEXT,
    recorded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (debt_id) REFERENCES debts(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_debt (debt_id),
    INDEX idx_payment_date (payment_date)
);

-- Stock movements table for inventory tracking
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('sale', 'purchase', 'adjustment', 'return') NOT NULL,
    reference_id INT,
    notes TEXT,
    user_id INT NOT NULL,
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_product (product_id),
    INDEX idx_movement_date (movement_date),
    INDEX idx_movement_type (movement_type)
);

-- System settings table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, role, is_active) VALUES
('admin', 'admin@inventory.com', '$2y$10$eImiTXuWVxfM37uY4JANjOdJT2/0fYhYL6DGNXTmjZMJsO.9r.9jS', 'System Administrator', 'admin', TRUE);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and accessories'),
('Clothing', 'Apparel and fashion items'),
('Food & Beverages', 'Food and drink products'),
('Books', 'Books and educational materials'),
('Home & Garden', 'Home improvement and garden supplies'),
('Sports', 'Sports equipment and accessories');

-- Insert default suppliers
INSERT INTO suppliers (name, contact_person, email, phone, address) VALUES
('Tech Supplies Co.', 'John Smith', 'john@techsupplies.com', '+1234567890', '123 Tech Street, Silicon Valley'),
('Fashion Hub Ltd.', 'Sarah Johnson', 'sarah@fashionhub.com', '+1234567891', '456 Fashion Ave, New York'),
('Fresh Foods Inc.', 'Mike Wilson', 'mike@freshfoods.com', '+1234567892', '789 Food Plaza, California');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('company_name', 'Your Shop Name', 'Company name displayed on reports'),
('company_address', 'Your Shop Address', 'Company address for invoices'),
('company_phone', '+1234567890', 'Company phone number'),
('company_email', 'info@yourshop.com', 'Company email address'),
('currency_symbol', '$', 'Currency symbol for prices'),
('tax_rate', '0.08', 'Default tax rate (8%)'),
('low_stock_alert', '10', 'Low stock alert threshold'),
('backup_frequency', 'daily', 'Database backup frequency');

-- Create indexes for better performance
CREATE INDEX idx_products_stock ON products(quantity_in_stock);
CREATE INDEX idx_customers_balance ON customers(current_balance);
CREATE INDEX idx_debts_amount ON debts(remaining_amount);
CREATE INDEX idx_sales_total ON sales(total_amount);

-- Create views for reporting
CREATE VIEW low_stock_products AS
SELECT 
    p.id, p.name, p.sku, p.quantity_in_stock, p.min_stock_level,
    c.name as category_name, s.name as supplier_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN suppliers s ON p.supplier_id = s.id
WHERE p.quantity_in_stock <= p.min_stock_level AND p.is_active = TRUE;

CREATE VIEW customer_debt_summary AS
SELECT 
    c.id, c.customer_code, c.full_name, c.phone,
    COUNT(d.id) as total_debts,
    SUM(d.remaining_amount) as total_outstanding
FROM customers c
LEFT JOIN debts d ON c.id = d.customer_id AND d.status != 'paid'
WHERE c.is_active = TRUE
GROUP BY c.id, c.customer_code, c.full_name, c.phone;

CREATE VIEW overdue_debts AS
SELECT 
    d.id, d.debt_number, d.original_amount, d.remaining_amount,
    d.debt_date, d.due_date, DATEDIFF(CURDATE(), d.due_date) as days_overdue,
    c.customer_code, c.full_name, c.phone
FROM debts d
JOIN customers c ON d.customer_id = c.id
WHERE d.due_date < CURDATE() AND d.status != 'paid'
ORDER BY d.due_date ASC;

-- Triggers for automatic updates
DELIMITER //

-- Update customer balance when debt is created
CREATE TRIGGER update_customer_balance_on_debt_insert
AFTER INSERT ON debts
FOR EACH ROW
BEGIN
    UPDATE customers 
    SET current_balance = current_balance + NEW.remaining_amount
    WHERE id = NEW.customer_id;
END;//

-- Update customer balance when debt payment is made
CREATE TRIGGER update_debt_on_payment
AFTER INSERT ON debt_payments
FOR EACH ROW
BEGIN
    DECLARE new_remaining DECIMAL(10,2);
    
    -- Update debt remaining amount
    UPDATE debts 
    SET remaining_amount = remaining_amount - NEW.payment_amount
    WHERE id = NEW.debt_id;
    
    -- Get new remaining amount
    SELECT remaining_amount INTO new_remaining
    FROM debts WHERE id = NEW.debt_id;
    
    -- Update debt status
    UPDATE debts 
    SET status = CASE 
        WHEN new_remaining <= 0 THEN 'paid'
        WHEN new_remaining < original_amount THEN 'partially_paid'
        ELSE status
    END
    WHERE id = NEW.debt_id;
    
    -- Update customer balance
    UPDATE customers c
    JOIN debts d ON c.id = d.customer_id
    SET c.current_balance = c.current_balance - NEW.payment_amount
    WHERE d.id = NEW.debt_id;
END;//

-- Update stock when sale is made
CREATE TRIGGER update_stock_on_sale
AFTER INSERT ON sale_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET quantity_in_stock = quantity_in_stock - NEW.quantity
    WHERE id = NEW.product_id;
    
    -- Record stock movement
    INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, user_id)
    SELECT NEW.product_id, 'out', NEW.quantity, 'sale', s.id, s.user_id
    FROM sales s WHERE s.id = NEW.sale_id;
END;//

DELIMITER ;