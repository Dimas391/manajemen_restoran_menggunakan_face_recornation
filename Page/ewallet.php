<?php
session_start();
require_once 'vendor/autoload.php'; // Midtrans autoload

// Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-0Gw4FyLGAvptH7caUcJSKFCS';
\Midtrans\Config::$isProduction = false; // Set to true for production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Get user and cart details
$id_pelanggan = $_SESSION['id_pelanggan'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fetch cart items
$sql = "SELECT k.id_menu, k.quantity, m.nama_menu, m.harga 
        FROM keranjang k 
        JOIN menu m ON k.id_menu = m.id_menu 
        WHERE k.id_pelanggan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$order_items = [];

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['harga'] * $row['quantity'];
    $total_price += $subtotal;
    $order_items[] = [
        'id' => $row['id_menu'],
        'price' => $row['harga'],
        'quantity' => $row['quantity'],
        'name' => $row['nama_menu']
    ];
}

// Fetch user details
$stmt = $conn->prepare("SELECT nama_pelanggan, email, NoHp FROM pelanggan WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Generate unique order ID
$order_id = 'ORD-' . uniqid();

// Midtrans transaction parameters
$transaction_params = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $total_price
    ],
    'item_details' => $order_items,
    'customer_details' => [
        'first_name' => $user['nama_pelanggan'],
        'email' => $user['email'],
        'phone' => $user['NoHp']
    ],
    'enabled_payments' => [
        'credit_card', 
        'mandiri_clickpay', 
        'cimb_clicks', 
        'bca_klikbank', 
        'bni_va', 
        'mandiri_va', 
        'permata_va', 
        'bca_va', 
        'bri_va', 
        'other_va', 
        'gopay', 
        'indomaret', 
        'alfamart', 
        'dana', 
        'ovo'
    ]
];

try {
    // Get Snap Token
    $snapToken = \Midtrans\Snap::getSnapToken($transaction_params);
    $insert_order_sql = "INSERT INTO pesanan (id_pelanggan, order_id, total_harga, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($insert_order_sql);
    $stmt->bind_param("iss", $id_pelanggan, $order_id, $total_price);
    $stmt->execute();

    // Set JSON header
    header('Content-Type: application/json');

    // Return the Snap token as a JSON response
    echo json_encode(['status' => 'success', 'snapToken' => $snapToken]);
} catch (Exception $e) {
    // Return an error message
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Close the database connection
    $conn->close();
}
?>
