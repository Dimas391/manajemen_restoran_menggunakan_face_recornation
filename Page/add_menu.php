<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $keterangan = $_POST['keterangan'];
    $harga = $_POST['harga'];
    $gambar_menu = $_POST['gambar_menu'];

    $sql = "INSERT INTO menu (nama_menu, keterangan, harga, gambar_menu) VALUES ('$nama_menu','$keterangan', '$harga', '$gambar_menu')";

    if ($conn->query($sql) === TRUE) {
        // Show alert and redirect to admin_menu.php
        echo "<script>
                alert('Menu berhasil ditambahkan!');
                window.location.href = 'admin_menu.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #700070;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background-color: #700070;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #500050;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add New Menu</h1>
        <form method="POST" action="">
            <label for="nama_menu">Nama Menu:</label>
            <input type="text" id="nama_menu" name="nama_menu" required>

            <label for="keterangan">Keterangan:</label>
            <input type="text" id="keterangan" name="keterangan" required>

            <label for="harga">Harga:</label>
            <input type="number" id="harga" name="harga" required>

            <label for="gambar_menu">Gambar Menu (URL):</label>
            <input type="text" id="gambar_menu" name="gambar_menu" required>

            <button type="submit">Save Menu</button>
        </form>
    </div>
</body>
</html>