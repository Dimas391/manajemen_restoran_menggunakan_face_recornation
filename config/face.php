<?php
$host = "localhost";
$dbname = "restoran";
$username = "root";
$password = "";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah file gambar ada
if (isset($_FILES['image_face'])) {
    $image = $_FILES['image_face']['tmp_name'];

    // Membaca file gambar
    $imageData = file_get_contents($image);
    
    // URL endpoint Flask API untuk pengenalan wajah
    $flask_url = "http://localhost:5000/recognize"; // Sesuaikan dengan URL API Python

    // Mengirim gambar ke API Flask
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'image_face' => new CURLFile($image) // Mengirimkan gambar menggunakan CURLFile
    ]);

    // Eksekusi cURL dan dapatkan respons dari server Flask
    $response = curl_exec($ch);
    curl_close($ch);

    // Cek apakah cURL berhasil
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        // Menampilkan respons dari server Flask
        $data = json_decode($response, true); // Mengonversi respons JSON dari Flask

        if ($data && isset($data['status']) && $data['status'] == 'success') {
            // Mendapatkan ID pelanggan terakhir
            $sql = "SELECT id_pelanggan FROM pelanggan ORDER BY id_pelanggan DESC LIMIT 1"; 
            $result = $conn->query($sql);
            if ($result && $row = $result->fetch_assoc()) {
                $id_pelanggan = $row['id_pelanggan']; // ID pelanggan terbaru

                // Menyimpan gambar ke dalam database pada ID terbaru
                $sql = "UPDATE pelanggan SET image_face = ? WHERE id_pelanggan = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("si", $imageData, $id_pelanggan);  // Menggunakan id_pelanggan terbaru
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to update image']);
                    }
                    $stmt->close();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No customer found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to recognize the face']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No image file received']);
}

// Menutup koneksi database
$conn->close();
?>
