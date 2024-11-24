<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['id_menu'])) {
    $id_menu = intval($_GET['id_menu']);

    $sql = "DELETE FROM menu WHERE id_menu = $id_menu";
    if ($conn->query($sql) === TRUE) {
        echo "Menu berhasil dihapus.";
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }
} else {
    echo "ID menu tidak ditemukan.";
}

$conn->close();
?>
