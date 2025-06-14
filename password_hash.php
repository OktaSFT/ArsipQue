<?php
$password_plain = "123456"; // Password asli dari user

$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Password setelah di-hash: " . $password_hashed;
?>
