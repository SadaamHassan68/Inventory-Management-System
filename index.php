<?php
/**
 * Root Index File - Redirects to proper application entry point
 * This file handles XAMPP environment routing constraints
 */

// Get the requested path
$path = $_GET['path'] ?? '/';

// Redirect to the public index with the path parameter
$publicUrl = 'public/index.php';
if ($path !== '/') {
    $publicUrl .= '?path=' . urlencode($path);
}

header('Location: ' . $publicUrl);
exit;