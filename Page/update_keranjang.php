<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw POST data
$rawData = file_get_contents("php://input");
error_log("Raw input received: " . $rawData);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Check session
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode([
        "status" => "error",
        "message" => "No active session"
    ]);
    exit;
}

// Parse JSON data
$data = json_decode($rawData, true);
error_log("Decoded data: " . print_r($data, true));

// Validate input
if (!$data || !isset($data['id_menu']) || !isset($data['quantity'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Data tidak lengkap",
        "debug" => [
            "received_data" => $data,
            "raw_json" => $rawData,
            "request_method" => $_SERVER['REQUEST_METHOD'],
            "content_type" => $_SERVER['CONTENT_TYPE'] ?? 'not set'
        ]
    ]);
    exit;
}

$id_pelanggan = (int)$_SESSION['id_pelanggan'];
$id_menu = (int)$data['id_menu'];
$quantity = (int)$data['quantity'];

// Update the cart
$stmt = $conn->prepare("UPDATE keranjang SET quantity = ? WHERE id_menu = ? AND id_pelanggan = ?");
$stmt->bind_param("iii", $quantity, $id_menu, $id_pelanggan);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Cart updated successfully",
        "data" => [
            "id_menu" => $id_menu,
            "quantity" => $quantity
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update cart",
        "debug" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>