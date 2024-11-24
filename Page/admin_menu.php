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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f3f4f6; /* Latar belakang halaman */
            font-family: 'Arial', sans-serif;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #e5e7eb; /* Abu-abu untuk sidebar */
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo-container {
        display: flex;
        align-items: center;
        margin-bottom: 30px; /* Reduce this value to bring the logo and text closer */
        }

        .sidebar .logo-container img {
            width: 90px; /* Adjust the size if needed */
            height: 70px;
            margin-right: 1px; /* Space between logo and text */
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
            color: #FF00FF; /* Ungu untuk teks tautan */
            font-size: 16px;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #d1d5db; /* Warna latar belakang saat hover */
        }

        .sidebar nav ul li a i {
            margin-right: 15px; /* Jarak antara icon dan teks */
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: white; /* Latar belakang konten utama */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 20px; /* Space between sidebar and content */
        }

        .main-content .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .main-content .header h1 {
            font-size: 24px;
            color: #4b5563;
        }
        .add-menu-btn {
        background-color: #FF00FF; /* Ganti dengan warna yang sesuai dari gambar */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .add-menu-btn:hover {
        background-color: #FF00FF; /* Ganti dengan warna hover yang sesuai dari gambar */
    }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background-color: #1f2937; /* Latar belakang item menu */
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: scale(1.05);            
            background-color: #1f2937; /* Warna saat hover */
        }

        .menu-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .menu-item button {
        background-color: #FF00FF; /* Warna tombol edit dan delete (biru) */
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
        margin: 5px; /* Space between buttons */
        }

        .menu-item button:hover {
            background-color: #FF00FF; /* Warna saat hover (lebih gelap) */
        }

        .menu-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .menu-item p {
            font-size: 14px;
            color: #d1d5db; /* Warna teks keterangan */
            margin-bottom: 10px;
        }

        .menu-item {
        background-color: #1f2937; /* Latar belakang item menu */
        color: white;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        display: flex; /* Use flexbox for centering */
        flex-direction: column; /* Stack children vertically */
        align-items: center; /* Center align items horizontally */
    }

    .menu-item img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 10px;
        display: block; /* Ensure the image is a block element */
    }

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/Image/logo.png" alt="Logo" class="logo-img" style="width: 90px">
            <div class="logo-text">
                <span class="main-text text-black font-bold">Restoran</span><br>
                <span class="sub-text text-gray-600">DriveThru</span>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#"><i class="bi bi-house-door" style="margin: center;"></i> Dashboard</a></li>
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
                        <img src='../assets/allmenu/{$row['gambar_menu']}' alt='{$row['nama_menu']}'>
                        <h3>{$row['nama_menu']}</h3>
                        <p>{$row['keterangan']}</p>
                        <p>Rp. " . number_format($row['harga'], 0, ',', '.') . "</p>
                        <button onclick=\"location.href='edit_menu.php?id_menu={$row['id_menu']}'\">Edit</button>
                        <button onclick=\"deleteMenu({$row['id_menu']})\">Delete</button>
                    </div>
                    ";
                }
            } else {
                echo "<p class='text-center text-gray-500'>Menu tidak ditemukan</p>";
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