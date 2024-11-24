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
// Pastikan pengguna sudah login
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Anda harus login terlebih dahulu'
    ]);
    exit;
}

$id_pelanggan = $_SESSION['id_pelanggan'];

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // 1. Ambil produk dari keranjang
    $query_cart = "SELECT id_menu, quantity FROM keranjang WHERE id_pelanggan = ?";
    $stmt_cart = $conn->prepare($query_cart);
    $stmt_cart->bind_param("i", $id_pelanggan);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    // 2. Hapus semua item di keranjang untuk pelanggan ini
    $query_delete = "DELETE FROM keranjang WHERE id_pelanggan = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $id_pelanggan);
    $stmt_delete->execute();

    // 3. Commit transaksi
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'order_number' => 'ORD-' . time(), // Generate nomor pesanan unik
        'message' => 'Keranjang berhasil dikosongkan'
    ]);

} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $conn->rollback();

    echo json_encode([
        'status' => 'error', 
        'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
    ]);
}

$conn->close();
?>