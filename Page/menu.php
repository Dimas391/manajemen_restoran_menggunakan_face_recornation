<?php
include "session.php";

// Koneksi ke database MySQL
$servername = "localhost";  // Ganti dengan nama server MySQL Anda
$username = "root";         // Ganti dengan username MySQL Anda
$password = "";             // Ganti dengan password MySQL Anda
$dbname = "restoran";  // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data menu
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu, kategori FROM menu"; // Ganti 'menu' dengan nama tabel Anda
$result = $conn->query($sql);

// Mengelompokkan menu berdasarkan kategori
$menu_by_category = []; // Inisialisasi variabel sebagai array kosong
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menu_by_category[$row['kategori']][] = $row;
    }
} else {
    // Jika tidak ada data, Anda bisa menambahkan logika di sini jika diperlukan
    // Misalnya, menampilkan pesan bahwa tidak ada menu yang tersedia
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    /* CSS styles */
    body {
        background-color: #f3f3f3;
        color: #333;
        margin: 0; /* Menghapus margin default */
        height: 100vh; /* Mengatur tinggi body */
        overflow: hidden; /* Mencegah scroll pada body */
    }

    .container {
        height: calc(100vh - 60px); /* Mengatur tinggi kontainer agar sesuai dengan tinggi viewport */
        overflow-y: auto; /* Menambahkan scroll vertikal */
    }

    .header {
        background: linear-gradient(50deg, #6366f1 0%, #4f46e5 100%);
        background-size: cover;
        height: 150px;
        border-radius: 0 0 20px 20px;
        color: white;
        text-align: center;
    }

    .menu-title {
        font-size: 24px;
        font-weight: bold;
        margin: 20px 0;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 2fr));
        gap: 15px;
        padding: 10px; /* Menambahkan padding untuk grid */
    }

    .menu-item {
        background-color: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .menu-item:hover {
        transform: scale(1.05);
    }

    .menu-item img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 12px 8px;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-around;
        z-index: 1000;
    }

    /* Navigation items */
    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6B7280;
        font-size: 12px;
        padding: 4px 8px;
    }

    .nav-item.active {
        color: #4F46E5;
    }

    .nav-icon {
        font-size: 20px;
        margin-bottom: 4px;
    }

    /* Media Queries for Mobile */
    @media (max-width: 240px) {
        .menu-title {
            font-size: 20px; /* Ukuran font lebih kecil untuk mobile */
        }

        .menu-item img {
            width: 60px; /* Ukuran gambar lebih kecil untuk mobile */
            height: 60px;
        }

        .nav-item {
            font-size: 10px; /* Ukuran font lebih kecil untuk mobile */
        }

        .bottom-nav {
            padding: 8px 4px; /* Padding lebih kecil untuk mobile */
        }
    }
</style>
</head>
<body>
<div class="container mx-auto p-4">
    <div class="header">
        <h1 class="menu-title">Menu</h1>
        <div class="back-button">
            <a href="home.php" class="text-white">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Tabs for Categories -->
    <div class="flex flex-wrap justify-around mt-4 mb-4">
        <a href="menu.php" class="bg-indigo-600 text-white px-4 py-2 rounded mb-2">All</a>
        <a href="menu.php?kategori=Chicken" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-2">Chicken</a>
        <a href="menu.php?kategori=Beef" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-2">Beef</a>
        <a href="menu.php?kategori=Vegetarian" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-2">Vegetarian</a>
        <a href="menu.php?kategori=Drink" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-2">Drink</a>
        <a href="menu.php?kategori=Dessert" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-2">Dessert</a>
    </div>

    <div class="menu-scroll mt-4">
        <div class="menu-grid">
            <?php
            // Menampilkan menu berdasarkan kategori yang dipilih
            $selected_category = isset($_GET['kategori']) ? $_GET['kategori'] : 'All';

            if ($selected_category === 'All') {
                // Tampilkan semua menu
                foreach ($menu_by_category as $category => $menus) {
                    foreach ($menus as $menu) {
                        echo '
                        <a href="detail_order.php?id_menu='.$menu['id_menu'].'" class="menu-item">
                            <img src="../assets/allmenu/'.$menu['gambar_menu'].'" alt="'.$menu['nama_menu'].'">
                            <h3 class="font-semibold">'.$menu['nama_menu'].'</h3>
                            <p class="text-gray-600">'.$menu['keterangan'].'</p>
                            <div class="price text-lg font-bold">Rp '.number_format($menu['harga'], 0, ',', '.').'</div>
                        </a>';
                    }
                }
            } else {
                // Tampilkan menu berdasarkan kategori yang dipilih
                if (isset($menu_by_category[$selected_category])) {
                    foreach ($menu_by_category[$selected_category] as $menu) {
                        echo '
                        <a href="detail_order.php?id_menu='.$menu['id_menu'].'" class="menu-item">
                            <img src="../assets/allmenu/'.$menu['gambar_menu'].'" alt="'.$menu['nama_menu'].'">
                            <h3 class="font-semibold">'.$menu['nama_menu'].'</h3>
                            <p class="text-gray-600">'.$menu['keterangan'].'</p>
                            <div class="price text-lg font-bold">Rp '.number_format($menu['harga'], 0, ',', '.').'</div>
                        </a>';
                    }
                } else {
                    echo '<p class="text-center text-gray-500">Menu tidak ditemukan untuk kategori ini.</p>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="home.php" class="nav-item active">
            <i class="bi bi-house nav-icon"></i>
            <span>Home</span>
        </a>
        <a href="scan.php" class="nav-item">
            <i class="bi bi-qr-code nav-icon"></i>
            <span>Scan</span>
        </a>
        <a href="keranjang.php" class="nav-item">
            <i class="bi bi-bag nav-icon"></i>
            <span>Orders</span>
        </a>
        <a href="profile.php" class="nav-item">
            <i class="bi bi-person nav-icon"></i>
            <span>Profile</span>
        </a>
    </div>

</div>
</body>
</html>