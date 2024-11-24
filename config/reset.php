<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    $host = "localhost";
    $dbname = "restoran";
    $username = "root";
    $password = "";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['reset_token']) || !isset($data['new_password'])) {
        throw new Exception("Missing required fields");
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $reset_token = $data['reset_token'];
    $new_password = $data['new_password'];

    if (!$email) {
        throw new Exception("Invalid email format");
    }

    // Verify token and check expiration
    $stmt = $conn->prepare("SELECT id_pelanggan, reset_token_expiry FROM pelanggan WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $reset_token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Invalid or expired reset token");
    }

    $row = $result->fetch_assoc();
    $reset_expires = $row['reset_token_expiry'];

    // Check if the reset token has expired
    if (new DateTime() > new DateTime($reset_expires)) {
        throw new Exception("Reset token has expired");
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE pelanggan SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
    $update_stmt->bind_param("ss", $hashed_password, $email);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update password");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Password has been successfully updated'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>