<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    // Database Connection
    $host = "localhost";
    $dbname = "restoran";
    $username = "root";
    $password = "";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error);
    }
    

    // Add before processing
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Instead of throwing exceptions, log them
    error_log("Received email: " . $_POST['email']);

    // Validate input email
    if (!isset($_POST['email'])) {
        throw new Exception("Email is required");
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception("Invalid email format");
    }

    // Validate image upload
    if (!isset($_FILES['face_image'])) {
        throw new Exception("No image uploaded");
    }

    $imageFile = $_FILES['face_image'];

    // Check file upload status
    if ($imageFile['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload failed");
    }

    // Prepare to send image to face recognition API
    $flask_url = "http://localhost:5000/compare_faces";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'email' => $email,
        'uploaded_image' => new CURLFile($imageFile['tmp_name'], $imageFile['type'], $imageFile['name'])
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    // Parse face recognition API response
    $recognition_result = json_decode($response, true);

    if (!$recognition_result || !isset($recognition_result['match'])) {
        throw new Exception("Face recognition failed");
    }

    // Check if face matches
    if ($recognition_result['match'] === true) {
        // Generate password reset token
        $reset_token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update database with reset token
        $stmt = $conn->prepare("UPDATE pelanggan SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $expiry, $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Could not generate reset token");
        }

        // Send reset password email (you would implement email sending logic here)
        // sendResetPasswordEmail($email, $reset_token);

        echo json_encode([
            'status' => 'success', 
            'message' => 'Face verified. Password reset link sent to your email.',
            'reset_token' => $reset_token
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Face verification failed'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>