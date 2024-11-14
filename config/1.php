<?php
$stored_hash = '$2y$10$W4nFXqu1E8PzeiMmkKsIau3UZ.kmXp7T7.m0kF.aiJ2'; // The hash from your database
$password = 'adam'; // The password you want to check

// Trim any spaces from the password (to avoid issues)
$password = trim($password);

// Check if the password matches the stored hash
if (password_verify($password, $stored_hash)) {
    echo 'Password match';
} else {
    echo 'Password does not match';
}
?>
