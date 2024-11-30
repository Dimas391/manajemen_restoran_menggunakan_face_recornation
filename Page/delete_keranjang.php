<?php
// delete_keranjang.php

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error', 
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]));
}

include "session.php";

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_pelanggan'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Anda harus login terlebih dahulu!'
    ]);
    exit;
}

// Ambil ID pelanggan dari session
$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil data JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['id_menu'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'ID menu tidak ditemukan'
    ]);
    exit;
}

$id_menu = $data['id_menu'];

// Query untuk menghapus item dari keranjang
$sql = "DELETE FROM keranjang 
        WHERE id_pelanggan = ? AND id_menu = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_pelanggan, $id_menu);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'Item berhasil dihapus dari keranjang'
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Gagal menghapus item: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>