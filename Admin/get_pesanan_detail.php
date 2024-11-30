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

header('Content-Type: application/json');

if (isset($_GET['id_reservasi'])) {
    $id_reservasi = intval($_GET['id_reservasi']);

    // Query to get menu details for this reservation with quantity
    $query = "
        SELECT 
            p.id_reservasi, 
            p.tgl_reservasi, 
            m.nama_menu,
            m.gambar_menu,
            m.harga
        FROM 
            reservasi p
        JOIN 
            menu m ON p.id_reservasi = m.id_menu
        WHERE 
            p.id_reservasi = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_reservasi);
    $stmt->execute();
    $result = $stmt->get_result();

    $details = [];
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
    }

    echo json_encode($details);
    $stmt->close();
} else {
    echo json_encode([]);
}
$conn->close();
?>