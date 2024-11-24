<?php
include "session.php";
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
$field = $data['field'] ?? null;
$value = $data['value'] ?? null;

$allowedFields = ['name', 'email'];
$fieldMap = [
    'name' => 'nama_pelanggan',
    'email' => 'email',
];

if ($id_pelanggan && in_array($field, $allowedFields)) {
    try {
        $stmt = $conn->prepare("UPDATE pelanggan SET {$fieldMap[$field]} = ? WHERE id_pelanggan = ?");
        $stmt->bind_param("si", $value, $id_pelanggan);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Field tidak valid atau sesi pengguna tidak ditemukan']);
}
?>
