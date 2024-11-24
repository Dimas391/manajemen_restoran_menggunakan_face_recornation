<?php
// Koneksi ke database MySQL
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

// Query untuk mengambil data menu
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu FROM menu";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Reset dan Tata Letak Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .sidebar .logo-container img {
            width: 50px;
            height: auto;
            margin-right: 10px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li {
            margin: 15px 0;
        }

        .sidebar nav ul li a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .main-content .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .main-content .header h1 {
            font-size: 24px;
            color: #700070;
        }

        .main-content .header .add-menu-btn {
            background-color: #700070;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .menu-item {
            display: block;
            background-color: #1a1a1a;
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: scale(1.05);
            background-color: #333333;
        }

        .menu-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .menu-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .menu-item p {
            font-size: 14px;
            color: #c7c7c7;
            margin-bottom: 10px;
        }

        .menu-item button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .menu-item button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="./Image/logo.png" alt="Logo" class="logo-img">
            <div class="logo-text">
                <span class="main-text">Restoran</span><br>
                <span class="sub-text">DriveThru</span>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#"><i class="bi bi-house-door"></i> Dashboard</a></li>
                <li><a href="#"><i class="bi bi-list-ul"></i> Menu</a></li>
                <li><a href="#"><i class="bi bi-calendar-check"></i> Reservasi</a></li>
                <li><a href="#"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Menu</h1>
            <button class="add-menu-btn" onclick="location.href='add_menu.php'">+ Add Menu</button>
        </div>
        <div class="menu-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='menu-item'>
                        <img src='img/allmenu/{$row['gambar_menu']}' alt='{$row['nama_menu']}'>
                        <h3>{$row['nama_menu']}</h3>
                        <p>{$row['keterangan']}</p> <!-- Menambahkan keterangan -->
                        <p>Rp.{$row['harga']}</p>
                        <button onclick=\"location.href='edit_menu.php?id_menu={$row['id_menu']}'\">Edit</button>
                        <button onclick=\"deleteMenu({$row['id_menu']})\">Delete</button>
                    </div>
                    ";
                }
            } else {
                echo "<p>Menu tidak ditemukan</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script>
    function deleteMenu(id_menu) {
        if (confirm("Apakah Anda yakin ingin menghapus menu ini?")) {
            // Kirim permintaan ke server menggunakan Fetch API
            fetch(`delete_menu.php?id_menu=${id_menu}`, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(result => {
                alert(result); // Tampilkan pesan dari server
                location.reload(); // Muat ulang halaman
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Terjadi kesalahan saat menghapus menu.");
            });
        }
    }
    </script>
</body>
</html>
