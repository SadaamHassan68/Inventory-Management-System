<?php
/**
 * Application Bootstrap File
 * Initializes the Inventory Management System
 */

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('APP_URL', 'http://localhost/inventory%20ms/public/index.php');
define('STORAGE_PATH', APP_ROOT . '/storage');
define('UPLOAD_PATH', APP_ROOT . '/storage/uploads');

// Load configuration
$config = require_once APP_ROOT . '/config/config.php';

// Set application timezone from config
if (isset($config['app']['timezone'])) {
    date_default_timezone_set($config['app']['timezone']);
}

// Load core classes
require_once APP_ROOT . '/app/utils/Database.php';
require_once APP_ROOT . '/app/models/BaseModel.php';

/**
 * Simple autoloader for application classes
 */
spl_autoload_register(function ($className) {
    $directories = [
        APP_ROOT . '/app/models/',
        APP_ROOT . '/app/controllers/',
        APP_ROOT . '/app/middleware/',
        APP_ROOT . '/app/utils/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Simple Router Class
 */
class Router {
    private $routes = [];
    private $middleware = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }
    
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function middleware($name, $callback) {
        $this->middleware[$name] = $callback;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Get path from query parameter for XAMPP compatibility
        $path = $_GET['path'] ?? '/';
        
        // Ensure path starts with slash
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        
        // Handle PUT and DELETE methods via form method override
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // Find matching route
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                if ($this->matchRoute($route, $path, $params)) {
                    return $this->executeCallback($callback, $params);
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        include APP_ROOT . '/app/views/errors/404.php';
    }
    
    private function matchRoute($route, $path, &$params) {
        $params = [];
        
        // Exact match
        if ($route === $path) {
            return true;
        }
        
        // Parameter matching
        $routeParts = explode('/', trim($route, '/'));
        $pathParts = explode('/', trim($path, '/'));
        
        if (count($routeParts) !== count($pathParts)) {
            return false;
        }
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], '{') === 0 && strpos($routeParts[$i], '}') === strlen($routeParts[$i]) - 1) {
                // Parameter
                $paramName = trim($routeParts[$i], '{}');
                $params[$paramName] = $pathParts[$i];
            } elseif ($routeParts[$i] !== $pathParts[$i]) {
                return false;
            }
        }
        
        return true;
    }
    
    private function executeCallback($callback, $params) {
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            $controller = $parts[0];
            $method = $parts[1];
            
            $controllerInstance = new $controller();
            return call_user_func_array([$controllerInstance, $method], $params);
        } elseif (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
    }
}

/**
 * Helper Functions
 */

// Redirect helper
function redirect($url) {
    if ($url[0] === '/') {
        $url = APP_URL . '?path=' . urlencode($url);
    }
    header("Location: " . $url);
    exit;
}

// View helper
function view($viewName, $data = []) {
    extract($data);
    $viewFile = APP_ROOT . '/app/views/' . str_replace('.', '/', $viewName) . '.php';
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        throw new \Exception("View not found: {$viewName}");
    }
}

// Asset helper
function asset($path) {
    return 'http://localhost/inventory%20ms/public/' . ltrim($path, '/');
}

// URL helper
function url($path = '') {
    if (empty($path) || $path === '/') {
        return APP_URL;
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return APP_URL . '?path=' . urlencode($path);
}

// Flash message helpers
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

// Authentication helpers
function auth() {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function hasRole($role) {
    $user = auth();
    return $user && $user['role'] === $role;
}

function isAdmin() {
    return hasRole('admin');
}

function isStaff() {
    return hasRole('staff');
}

// CSRF protection
function csrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="_token" value="' . csrfToken() . '">';
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Format helpers
function formatCurrency($amount) {
    global $config;
    $symbol = $config['settings']['currency_symbol'] ?? '$';
    return $symbol . number_format($amount, 2);
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals);
}

// Validation helpers
function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function setOldInput($data) {
    $_SESSION['old'] = $data;
}

function clearOldInput() {
    unset($_SESSION['old']);
}

function getErrors($key = null) {
    if ($key) {
        return $_SESSION['errors'][$key] ?? [];
    }
    return $_SESSION['errors'] ?? [];
}

function setErrors($errors) {
    $_SESSION['errors'] = $errors;
}

function clearErrors() {
    unset($_SESSION['errors']);
}

function hasErrors($key = null) {
    if ($key) {
        return !empty($_SESSION['errors'][$key]);
    }
    return !empty($_SESSION['errors']);
}

// File upload helper
function uploadFile($file, $directory = 'general') {
    global $config;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $uploadDir = UPLOAD_PATH . '/' . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $config['upload']['allowed_types'])) {
        return false;
    }
    
    if ($file['size'] > $config['upload']['max_size']) {
        return false;
    }
    
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $directory . '/' . $filename;
    }
    
    return false;
}

// Log helper
function logActivity($action, $table = null, $recordId = null, $oldValues = null, $newValues = null) {
    try {
        $db = Database::getInstance();
        $user = auth();
        
        $data = [
            'user_id' => $user['id'] ?? null,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
        
        $db->insert('activity_logs', $data);
    } catch (Exception $e) {
        // Log error silently
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

// Initialize router
$router = new Router();

// Make router globally available
$GLOBALS['router'] = $router;