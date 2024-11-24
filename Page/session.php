<?php
session_start(); // Start the session to manage session variables

// Mengecek apakah idpelanggan ada dalam query string
if (isset($_GET['id_pelanggan'])) {
    $_SESSION['id_pelanggan'] = intval($_GET['id_pelanggan']);
} elseif (!isset($_SESSION['id_pelanggan'])) {
    echo "No idpelanggan provided.";
    exit;
}

// Mengubah bahasa jika form di-submit
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_language'])) {
//     $_SESSION['language'] = $_POST['language'];
//     header("Location: " . $_SERVER['PHP_SELF']); // Redirect ke halaman yang sama
//     exit();
// }

// // Ambil bahasa yang dipilih dari session
// $selectedLanguage = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// // Load language file
// $lang = include "languages/{$selectedLanguage}.php";
?>