<?php
include "session.php";
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

// Mengambil data pelanggan dari sesi
$id_pelanggan = $_SESSION['id_pelanggan'];

// Query untuk mengambil data reservasi
$reservasi_query = "SELECT * FROM reservasi WHERE id_pelanggan = ?";
$stmt_reservasi = $conn->prepare($reservasi_query);
$stmt_reservasi->bind_param("s", $id_pelanggan);
$stmt_reservasi->execute();
$reservasi_result = $stmt_reservasi->get_result();

// Query untuk mengambil data pickup
$pickup_query = "SELECT * FROM pickup WHERE id_pelanggan = ?";
$stmt_pickup = $conn->prepare($pickup_query);
$stmt_pickup->bind_param("s", $id_pelanggan);
$stmt_pickup->execute();
$pickup_result = $stmt_pickup->get_result();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Reservasi dan Pickup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">


    <!-- Sidebar / Navbar Samping -->
    <div class="flex">
        <div class="w-64 bg-gray-800 h-screen p-6">
        <header>
        <div class="header-content">
            <div class="logo">
                <img src="..\assets\image\logo.png" alt="">
            </div>
        </div>
    </header>
            <h2 class="text-white text-2xl font-semibold mb-8"></h2>
            <ul class="space-y-4">
                <li>
                    <a href="dashboard.php" class="text-gray-400 hover:text-white flex items-center">
                        <i class="bi bi-house-door mr-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="menu.php" class="text-gray-400 hover:text-white flex items-center">
                        <i class="bi bi-list-ul mr-2"></i> Menu
                    </a>
                </li>
                <li>
                    <a href="reservasi_pickup_results.php" class="text-gray-400 hover:text-white flex items-center">
                        <i class="bi bi-calendar-check mr-2"></i> Reservasi dan Pickup
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="text-gray-400 hover:text-white flex items-center">
                        <i class="bi bi-box-arrow-right mr-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="mb-8">
                <a href="home.php" class="text-white text-2xl">Kembali ke Beranda</a>
            </div>

            <div class="bg-gray-800 rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Reservasi Anda</h2>

                <?php if ($reservasi_result->num_rows > 0): ?>
                    <table class="w-full table-auto text-left text-gray-400">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID Reservasi</th>
                                <th class="px-4 py-2">Meja</th>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Waktu</th>
                                <th class="px-4 py-2">Jumlah Orang</th>
                                <th class="px-4 py-2">Lokasi Meja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $reservasi_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $row['id_reservasi']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['id_meja']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['tgl_reservasi']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['waktu_reservasi']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['party_size']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['tipe_tabel']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-gray-400">Anda belum melakukan reservasi.</p>
                <?php endif; ?>
            </div>

            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Pick Up Anda</h2>

                <?php if ($pickup_result->num_rows > 0): ?>
                    <table class="w-full table-auto text-left text-gray-400">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID Pick Up</th>
                                <th class="px-4 py-2">Nomor Telepon</th>
                                <th class="px-4 py-2">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $pickup_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $row['id_pickup']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['nomor_telepon']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-gray-400">Anda belum melakukan pick up.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>

<?php
// Menutup koneksi
$stmt_reservasi->close();
$stmt_pickup->close();
$conn->close();
?>
