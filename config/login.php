<?php
session_start();
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    echo json_encode(["message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fungsi untuk sanitasi input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Cek jika request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data JSON dari body request
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        echo json_encode(["message" => "Invalid JSON input"]);
        exit;
    }

    // Pastikan nama_pelanggan dan password disediakan
    if (!isset($data['nama_pelanggan']) || !isset($data['password'])) {
        echo json_encode(["message" => "Invalid input: nama_pelanggan and password are required"]);
        exit;
    }

    // Sanitasi input
    $nama_pelanggan = sanitizeInput($data['nama_pelanggan']);
    $password = sanitizeInput($data['password']);

    // Query untuk mencari pengguna berdasarkan nama_pelanggan
    $sql = "SELECT * FROM pelanggan WHERE nama_pelanggan = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["message" => "Query preparation failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $nama_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pengguna ditemukan
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['id_pelanggan'] = $user['id_pelanggan']; // Simpan id_pelanggan ke session
            echo json_encode([
                "message" => "Login successful",
                "id_pelanggan" => $user['id_pelanggan'] // Kirim id_pelanggan sebagai respons
            ]);
        } else {
            // Password salah
            echo json_encode(["message" => "Incorrect password"]);
        }
    } else {
        // Username tidak ditemukan
        echo json_encode(["message" => "Username not found"]);
    }

    $stmt->close();
} else {
    // Jika bukan POST
    echo json_encode(["message" => "Invalid request method"]);
}

$conn->close();
?>
