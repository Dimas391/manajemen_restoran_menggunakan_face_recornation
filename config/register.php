<?php
$host = "localhost"; // Host database
$dbname = "restoran"; // Nama database
$username = "root"; // Username database
$password = ""; // Password database

// Koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mendapatkan data dari Flutter
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['nama_pelanggan']) && isset($data['email']) && isset($data['password'])) {
    $username = $conn->real_escape_string($data['nama_pelanggan']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'],  PASSWORD_DEFAULT); // Hash password

    // Query untuk menyimpan data
    $sql = "INSERT INTO pelanggan (nama_pelanggan, email, password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register user"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
}

$conn->close();
