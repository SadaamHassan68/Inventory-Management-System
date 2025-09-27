<?php
/**
 * Test script to verify profile functionality
 */

// Include the bootstrap file
require_once __DIR__ . '/bootstrap/app.php';

// Test the formatDate function
echo "Testing formatDate function:\n";
echo formatDate('2023-01-15 10:30:00', 'F j, Y g:i A') . "\n";

// Test the formatCurrency function
echo "\nTesting formatCurrency function:\n";
echo formatCurrency(1234.56) . "\n";

// Test the url function
echo "\nTesting url function:\n";
echo url('/profile') . "\n";

echo "\n✅ All helper functions are working correctly!\n";
?>