<?php
// Generate correct password hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";

// Test verification
$verified = password_verify($password, $hash);
echo "Verification: " . ($verified ? 'Success' : 'Failed') . "\n";
?>