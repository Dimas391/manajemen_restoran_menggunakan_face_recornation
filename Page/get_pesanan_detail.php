<?php
include "session.php";
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

if (isset($_GET['id_reservasi'])) {
    $id_reservasi = $_GET['id_reservasi'];

    // Query untuk mengambil detail pesanan berdasarkan id_reservasi
    $query = "SELECT menu.nama_menu FROM pesanan 
              JOIN menu ON pesanan.id_menu = menu.id_menu
              WHERE pesanan.id_reservasi = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_reservasi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pesanan = [];
    while ($row = $result->fetch_assoc()) {
        $pesanan[] = $row;
    }

    echo json_encode($pesanan);
}
?>
