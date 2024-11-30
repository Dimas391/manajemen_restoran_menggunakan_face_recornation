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

    // Initialize total price variables
    $totalOriginalPrice = 0;
    $totalDiscountedPrice = 0;

    // Check if there are items in the cart
    if ($result_cart->num_rows > 0) {
        // Loop through cart items to calculate totals
        while ($row = $result_cart->fetch_assoc()) {
            $id_menu = $row['id_menu'];
            $quantity = $row['quantity'];

            // Fetch menu details
            $query_menu = "SELECT harga, diskon FROM menu WHERE id_menu = ?";
            $stmt_menu = $conn->prepare($query_menu);
            $stmt_menu->bind_param("i", $id_menu);
            $stmt_menu->execute();
            $result_menu = $stmt_menu->get_result();

            if ($result_menu->num_rows > 0) {
                $menu_item = $result_menu->fetch_assoc();
                $harga = $menu_item['harga'];
                $diskon = $menu_item['diskon'];

                // Calculate original and discounted prices
                $originalSubtotal = $harga * $quantity;
                $discountedSubtotal = $diskon > 0 ? $originalSubtotal * (1 - ($diskon / 100)) : $originalSubtotal;

                // Accumulate totals
                $totalOriginalPrice += $originalSubtotal;
                $totalDiscountedPrice += $discountedSubtotal;
            }
            $stmt_menu->close();
        }
    }

    // Prepare notification message
    $message = "Your order has been placed successfully!";
    $sql = "INSERT INTO notifications (id_pelanggan, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_pelanggan, $message);
    $stmt->execute();
    $stmt->close();

    // 2. Hapus semua item di keranjang untuk pelanggan ini
    $query_delete = "DELETE FROM keranjang WHERE id_pelanggan = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $id_pelanggan);
    $stmt_delete->execute();

    // 3. Commit transaksi
    $conn->commit();

    $hemat = $totalOriginalPrice - $totalDiscountedPrice;

    echo json_encode([
        'status' => 'success',
        'order_number' => 'ORD-' . time(), // Generate nomor pesanan unik
        'total_harga_asli' => $totalOriginalPrice,
        'total_harga_diskon' => $totalDiscountedPrice,
        'hemat' => $hemat,
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