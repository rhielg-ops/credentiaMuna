<?php
$password = 'superadmin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n\n";

echo "Copy this SQL query and run it in phpMyAdmin:\n\n";
echo "UPDATE users SET password = '$hash' WHERE email = 'artryry6@gmail.com';\n";
?>