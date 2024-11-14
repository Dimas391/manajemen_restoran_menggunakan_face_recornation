<?php
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost"; // Ganti dengan server Anda
$username = "root"; // Ganti dengan username MySQL Anda
$password = ""; // Ganti dengan password MySQL Anda
$dbname = "restoran"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Fungsi untuk sanitasi input
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Ambil data JSON dari body request
$data = json_decode(file_get_contents("php://input"));

// Periksa jika JSON valid
if ($data === null) {
    echo json_encode(["message" => "Invalid JSON input"]);
    exit;
}

// Example rehash for manual comparison
$new_hash = password_hash("rian", PASSWORD_DEFAULT);
error_log("New hash generated for comparison: " . $new_hash);

// Cek apakah data yang diperlukan ada dalam request
if (isset($data->nama_pelanggan) && isset($data->password)) {
    // Sanitasi input
    $nama_pelanggan = sanitizeInput($data->nama_pelanggan);
    $password = sanitizeInput($data->password);

    // Query untuk memeriksa username terlebih dahulu
    $sql = "SELECT * FROM pelanggan WHERE nama_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die(json_encode(["message" => "Query preparation failed: " . $conn->error]));
    }

    // Bind parameter untuk query
    $stmt->bind_param("s", $nama_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pengguna ditemukan
        $user = $result->fetch_assoc();

        // Debugging logs
        error_log("Received password (trimmed and sanitized): " . $password);
        error_log("Stored password hash: " . $user['password']);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            echo json_encode(["message" => "Login successful"]);
        } else {
            // Password salah
            echo json_encode(["message" => "Incorrect password"]);
        }
    } else {
        // Username tidak ditemukan
        echo json_encode(["message" => "Username not found"]);
    }

    // Tutup statement
    $stmt->close();
} else {
    // Input tidak valid
    echo json_encode(["message" => "Invalid input: nama_pelanggan and password are required"]);
}

// Tutup koneksi
$conn->close();
?>
