<?php
session_start(); // Start the session to manage session variables

// Mengecek apakah idpelanggan ada dalam query string
if (isset($_GET['id_pelanggan'])) {
    // Jika idpelanggan ada di query string, simpan ke dalam session setelah konversi ke integer
    $_SESSION['id_pelanggan'] = intval($_GET['id_pelanggan']);
    $idpelanggan = $_SESSION['id_pelanggan'];  // Assign idpelanggan to variable
} elseif (isset($_SESSION['id_pelanggan'])) {
    // Jika idpelanggan sudah ada dalam session, ambil dari session
    $idpelanggan = $_SESSION['id_pelanggan'];
} else {
    // Jika tidak ada idpelanggan, tampilkan pesan error
    echo "No idpelanggan provided.";
    exit;
}

?>
