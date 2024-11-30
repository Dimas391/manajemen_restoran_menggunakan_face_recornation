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

// Inisialisasi status jika belum ada dalam session
if (!isset($_SESSION['status_data'])) {
    $_SESSION['status_data'] = [];
}

// Proses perubahan status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_reservasi'])) {
    $id_reservasi = $_POST['id_reservasi'];
    $status = $_POST['status'];

    // Simpan status ke dalam session
    $_SESSION['status_data'][$id_reservasi] = $status;
}

// Query untuk mengambil data reservasi
$reservasi_query = "
    SELECT reservasi.id_reservasi, pelanggan.nama_pelanggan, reservasi.tgl_reservasi
    FROM reservasi
    JOIN pelanggan ON reservasi.id_pelanggan = pelanggan.id_pelanggan";
$reservasi_result = $conn->query($reservasi_query);

// Query untuk mengambil data pickup
$pickup_query = "SELECT id_pickup, nomor_telepon, email FROM pickup";
$pickup_result = $conn->query($pickup_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi dan Pickup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .status-button {
            padding: 8px 16px;
            margin-right: 8px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .status-pending {
            background-color: gray;
        }

        .status-proses {
            background-color: orange;
        }

        .status-complete {
            background-color: green;
        }

        .status-button:hover {
            opacity: 0.8;
        }
    </style>
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

            <!-- Reservasi Section -->
            <div class="bg-gray-800 rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Reservasi Anda</h2>

                <?php if ($reservasi_result->num_rows > 0): ?>
                    <table class="w-full table-auto text-left text-gray-400">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Nama Pelanggan</th>
                                <th class="px-4 py-2">Tanggal Reservasi</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $reservasi_result->fetch_assoc()): ?>
                                <?php
                                    // Ambil status dari session jika ada, jika tidak set default 'Pending'
                                    $current_status = isset($_SESSION['status_data'][$row['id_reservasi']]) ? $_SESSION['status_data'][$row['id_reservasi']] : 'Pending';
                                ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $row['nama_pelanggan']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['tgl_reservasi']; ?></td>
                                    <td class="px-4 py-2">
                                        <!-- Tombol status default yang tampil -->
                                        <button class="status-button status-<?php echo strtolower($current_status); ?>" onclick="showPopup(<?php echo $row['id_reservasi']; ?>)">
                                            <?php echo $current_status; ?>
                                        </button>
                                        <!-- Pop up status yang muncul saat tombol diklik -->
                                        <div id="popup-<?php echo $row['id_reservasi']; ?>" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
                                            <div class="bg-white text-gray-800 rounded-lg p-4 w-80">
                                                <h3 class="text-xl font-semibold mb-4">Pilih Status Reservasi</h3>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="id_reservasi" value="<?php echo $row['id_reservasi']; ?>">
                                                    <button type="submit" name="status" value="Pending" class="status-button status-pending mb-2">Pending</button>
                                                    <button type="submit" name="status" value="Proses" class="status-button status-proses mb-2">Proses</button>
                                                    <button type="submit" name="status" value="Complete" class="status-button status-complete">Complete</button>
                                                </form>
                                                <button onclick="hidePopup(<?php echo $row['id_reservasi']; ?>)" class="mt-4 text-sm text-red-500">Tutup</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button onclick="showPesananDetail(<?php echo $row['id_reservasi']; ?>)" class="bg-blue-500 text-white p-2 rounded">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-gray-400">Anda belum melakukan reservasi.</p>
                <?php endif; ?>
            </div>

            <!-- Pickup Section -->
            <div class="bg-gray-800 rounded-xl p-6">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Pick Up Anda</h2>

                <?php if ($pickup_result->num_rows > 0): ?>
                    <table class="w-full table-auto text-left text-gray-400">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">ID Pick Up</th>
                                <th class="px-4 py-2">Nomor Telepon</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $pickup_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $row['id_pickup']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['nomor_telepon']; ?></td>
                                    <td class="px-4 py-2"><?php echo $row['email']; ?></td>
                                    <td class="px-4 py-2">Tunggu Konfirmasi</td> <!-- Status hanya tampilan -->
                                    <td class="px-4 py-2">
                                        <button onclick="showPickupDetail(<?php echo $row['id_pickup']; ?>)" class="bg-blue-500 text-white p-2 rounded">
                                            Detail
                                        </button>
                                    </td>
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

    <script>
        function showPopup(id_reservasi) {
            document.getElementById('popup-' + id_reservasi).style.display = 'flex';
        }

        function hidePopup(id_reservasi) {
            document.getElementById('popup-' + id_reservasi).style.display = 'none';
        }

        function showPesananDetail(id_reservasi) {
            fetch(`get_pesanan_detail.php?id_reservasi=${id_reservasi}`)
                .then(response => response.json())
                .then(data => {
                    let detailContent = '<h3 class="text-lg font-semibold mb-4">Detail Pesanan</h3>';
                    data.forEach(item => {
                        detailContent += `
                            <p class="text-sm">${item.nama_menu}</p>
                        `;
                    });
                    document.getElementById('popup-content').innerHTML = detailContent;
                    document.getElementById('popup-detail').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function hidePopup() {
            document.getElementById('popup-detail').style.display = 'none';
        }
    </script>

    <!-- Popup untuk menampilkan detail pesanan -->
    <div id="popup-detail" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white text-gray-800 rounded-lg p-4 w-80">
            <div id="popup-content"></div>
            <button onclick="hidePopup()" class="mt-4 text-sm text-red-500">Tutup</button>
        </div>
    </div>
</body>
</html>
