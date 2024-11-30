<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

include "session.php";

// Pastikan pengguna sudah login dan memiliki session
if (!isset($_SESSION['id_pelanggan'])) {
    echo "Anda harus login terlebih dahulu!";
    exit;
}

// Ambil ID pelanggan dari session
$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil data riwayat pembelian dari tabel pesanan
$sql = "SELECT 
            id_pesanan, 
            total_harga,
            created_at, 
            status,
            id_menu  
        FROM 
            pesanan 
        WHERE 
            id_pelanggan = ? 
        ORDER BY 
            created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pelanggan);  // Mengikat parameter
$stmt->execute();
$result = $stmt->get_result();

// Jika riwayat pembelian ditemukan
$purchases = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $purchases[] = $row;
    }
}

// Ambil data menu berdasarkan id_menu yang ada di pesanan
$menu_ids = array_column($purchases, 'id_menu');
$menu_ids = array_unique($menu_ids); // Menghindari duplikasi

$menu_data = [];
if (!empty($menu_ids)) {
    // Siapkan placeholder untuk query
    $placeholders = implode(',', array_fill(0, count($menu_ids), '?'));
    $sql_menu = "SELECT id_menu, nama_menu, gambar_menu, harga FROM menu WHERE id_menu IN ($placeholders)";
    $stmt_menu = $conn->prepare($sql_menu);
    $stmt_menu->bind_param(str_repeat('i', count($menu_ids)), ...$menu_ids); // Mengikat parameter
    $stmt_menu->execute();
    $result_menu = $stmt_menu->get_result();

    while ($row_menu = $result_menu->fetch_assoc()) {
        $menu_data[$row_menu['id_menu']] = $row_menu; // Simpan menu berdasarkan id_menu
    }

    $stmt_menu->close(); // Menutup statement menu
}

$stmt->close();  // Menutup statement pesanan
$conn->close();  // Menutup koneksi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .purchase-item {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .purchase-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        @media (max-width: 640px) {
            .purchase-details {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-900">
    <div class="max-w-screen-xl mx-auto p-4 min-h-screen pb-24">
        <!-- Header -->
        <div class="flex items-center mb-8">
            <a href="home.php" class="text-white text-2xl mr-4">
                <i class="bi bi-arrow-left"></i>
            </a>
            <img src="../assets/image/logo.png" alt="Restaurant Logo" class="h-8">
        </div>

        <!-- Page Title -->
        <h1 class="text-2xl font-bold mb-8 text-center text-purple-400">Riwayat Pembelian        </h1>

<!-- Purchase History -->
<div class="space-y-6 mb-8">
    <?php if (empty($purchases)): ?>
        <div class="text-center text-gray-400 py-12">
            <i class="bi bi-cart-x text-4xl mb-4 block"></i>
            <p>Belum ada riwayat pembelian</p>
        </div>
    <?php else: ?>
        <?php foreach ($purchases as $purchase): ?>
            <?php 
                $menu_item = $menu_data[$purchase['id_menu']] ?? null; // Ambil data menu berdasarkan id_menu
            ?>
            <div class="purchase-item rounded-xl p-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-400">
                        No. Pesanan: <?= htmlspecialchars($purchase['id_pesanan']) ?>
                    </span>
                    <span class="text-sm <?= 
                        $purchase['status'] == 'Berhasil' ? 'text-green-400' : 'text-yellow-400'
                    ?>">
                        <?= htmlspecialchars($purchase['status']) ?>
                    </span>
                </div>

                <div class="purchase-details flex items-center space-x-4 mb-4">
                    <div class="flex-1">
                        <?php if ($menu_item): ?>
                            <div class="flex items-center space-x-3 mb-2">
                                <img src="../assets/allmenu/<?= htmlspecialchars($menu_item['gambar_menu']) ?>" 
                                     alt="<?= htmlspecialchars($menu_item['nama_menu']) ?>"
                                     class="w-16 h-16 rounded-lg object-cover">
                                <div>
                                    <h3 class="text-lg font-semibold text-white">
                                        <?= htmlspecialchars($menu_item['nama_menu']) ?>
                                    </h3>
                                    
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-red-400">Menu tidak ditemukan</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-3">
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-400">Total Harga:</span>
                        <span class="text-xl font-bold text-purple-400">
                            Rp <?= number_format((int)$purchase['total_harga'], 0, ',', '.') ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-400">Tanggal:</span>
                        <span class="text-sm text-white">
                            <?= date('d M Y, H:i', strtotime($purchase['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div>

<!-- Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-gray-800 p-4">
<div class="max-w-screen-xl mx-auto flex justify-around">
    <a href="home.php" class="text-gray-400 flex flex-col items-center">
        <i class="bi bi-house"></i>
        <span class="text-sm">Home</span>
    </a>
    <a href="scan.php" class="text-gray-400 flex flex-col items-center">
        <i class="bi bi-qr-code"></i>
        <span class="text-sm">Scan</span>
    </a>
    <a href="keranjang.php" class="text-gray-400 flex flex-col items-center">
        <i class="bi bi-cart"></i>
        <span class="text-sm">Keranjang</span>
    </a>
    <a href="riwayat.php" class="text-purple-400 flex flex-col items-center">
        <i class="bi bi-clock-history"></i>
        <span class="text-sm">Riwayat</span>
    </a>
    <a href="profile.php" class="text-gray-400 flex flex-col items-center">
        <i class="bi bi-person"></i>
        <span class="text-sm">Profile</span>
    </a>
</div>
</nav>
</body>
</html>