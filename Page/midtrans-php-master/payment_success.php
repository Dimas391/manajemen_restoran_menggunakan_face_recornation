<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
include "session.php";

$order_id = $_GET['order_id'];

// Update order status
$update_sql = "UPDATE pesanan SET status = 'completed' WHERE order_id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();

// Clear cart after successful payment
$clear_cart_sql = "DELETE FROM keranjang WHERE id_pelanggan = ?";
$stmt = $conn->prepare($clear_cart_sql);
$stmt->bind_param("i", $_SESSION['id_pelanggan']);
$stmt->execute();

// Redirect to success page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4 text-green-500">Pembayaran Berhasil!</h1>
        <p class="mb-6">Terima kasih telah melakukan pembayaran.</p>
        <a href="home.php" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">Kembali ke Beranda</a>
    </div>
</body>
</html>