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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_menu'])) {
    $id_menu = intval($_GET['id_menu']);
    $sql = "SELECT * FROM menu WHERE id_menu = $id_menu";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $menu = $result->fetch_assoc();
    } else {
        die("Menu tidak ditemukan.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = intval($_POST['id_menu']);
    $nama_menu = $_POST['nama_menu'];
    $keterangan = $_POST['keterangan'];
    $harga = $_POST['harga'];
    $gambar_menu = $_POST['gambar_menu'];

    $sql = "UPDATE menu SET 
                nama_menu = '$nama_menu', 
                keterangan = '$keterangan', 
                harga = '$harga', 
                gambar_menu = '$gambar_menu' 
            WHERE id_menu = $id_menu";

    if ($conn->query($sql) === TRUE) {
        // Redirect ke admin_addmenu.php
        header("Location: admin_menu.php");
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
    <title>Edit Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .edit-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
        }

        .edit-container h1 {
            text-align: center;
            color: #700070;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .edit-container label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-top: 15px;
        }

        .edit-container input[type="text"],
        .edit-container input[type="number"],
        .edit-container textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .edit-container textarea {
            height: 80px;
            resize: vertical;
        }

        .edit-container button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #700070;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .edit-container button:hover {
            background-color: #590059;
        }

        .edit-container a {
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #700070;
        }

        .edit-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h1>Edit Menu</h1>
        <form method="POST" action="edit_menu.php">
            <input type="hidden" name="id_menu" value="<?= $menu['id_menu'] ?>">

            <label for="nama_menu">Nama Menu:</label>
            <input type="text" name="nama_menu" id="nama_menu" value="<?= $menu['nama_menu'] ?>" required>

            <label for="keterangan">Keterangan:</label>
            <textarea name="keterangan" id="keterangan" required><?= $menu['keterangan'] ?></textarea>

            <label for="harga">Harga:</label>
            <input type="number" name="harga" id="harga" value="<?= $menu['harga'] ?>" required>

            <label for="gambar_menu">Gambar Menu (URL):</label>
            <input type="text" name="gambar_menu" id="gambar_menu" value="<?= $menu['gambar_menu'] ?>" required>

            <button type="submit">Simpan Perubahan</button>
            <a href="admin_menu.php">Kembali ke Admin Panel</a>
        </form>
    </div>
</body>
</html>