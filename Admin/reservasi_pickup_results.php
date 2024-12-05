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

// Process status change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_reservasi'])) {
    $id_reservasi = $_POST['id_reservasi'];
    $status = $_POST['status'];

    // Optional: Update status in database
    $update_query = "UPDATE reservasi SET status = ? WHERE id_reservasi = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $id_reservasi);
    $stmt->execute();
    $stmt->close();

    // Save status to session
    $_SESSION['status_data'][$id_reservasi] = $status;
}

// Query to fetch reservations
$reservasi_query = "
    SELECT 
        reservasi.id_reservasi, 
        pelanggan.nama_pelanggan, 
        reservasi.tgl_reservasi,
        COALESCE(reservasi.status, 'Pending') AS status
    FROM reservasi
    JOIN pelanggan ON reservasi.id_pelanggan = pelanggan.id_pelanggan
    ORDER BY reservasi.tgl_reservasi DESC";
$reservasi_result = $conn->query($reservasi_query);

// Query to fetch pickup data
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
    <!-- Link untuk Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Base and Mobile-First Styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Sidebar Mobile */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px; /* Hidden by default */
            width: 250px;
            height: 100vh;
            background-color: #e5e7eb;
            transition: left 0.3s ease;
            z-index: 1000;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active {
            left: 0; /* Show sidebar */
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-toggle {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #FF00FF;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .main-content {
            margin-left: 0;
            padding: 15px;
            transition: margin-left 0.3s ease;
        }

        /* Tables */
        .responsive-table {
            overflow-x: auto;
            width: 100%;
        }

        .responsive-table table {
            width: 100%;
            min-width: 500px; /* Ensures horizontal scrolling on small screens */
        }

        .status-button {
            padding: 8px 16px;
            margin-right: 8px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        /* Responsive Buttons */
        .status-button, .action-button {
            width: 100%;
            margin-bottom: 5px;
        }

        /* Tablets and Larger Screens */
        @media (min-width: 768px) {
            .sidebar {
                left: 0; /* Always visible */
            }

            .sidebar-toggle {
                display: none; /* Hide toggle button */
            }

            .main-content {
                margin-left: 250px;
            }

            .status-button, .action-button {
                width: auto;
            }
        }

        /* Status Button Styles */
        .status-pending {
            background-color: gray;
            color: white;
        }

        .status-proses {
            background-color: orange;
            color: white;
        }

        .status-complete {
            background-color: green;
            color: white;
        }

        /* Logo and Navigation Styles */
        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 60px;
            height: 60px;
            margin-right: 10px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li a {
            display: block;
            padding: 20px;
            color: #FF00FF;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #d1d5db;
        }

        /* Menggeser tombol Detail sedikit ke kiri */
        .action-button {
            margin-left: -10px; /* Menggeser tombol sedikit ke kiri */
        }
    </style>
</head>
<body class="bg-white text-black">
    <!-- Sidebar Toggle Button for Mobile -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar / Navbar Samping -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/Image/logo.png" alt="Logo" class="logo-img">
            <div class="logo-text">
                <span class="main-text text-black font-bold">Restoran</span><br>
                <span class="sub-text text-gray-600">DriveThru</span>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="reservasi_pickup_results.php"><i class="bi bi-cart"></i> Pemesanan</a></li>
                <li><a href="admin_menu.php"><i class="bi bi-list-ul"></i> Menu</a></li>
                <li><a href="admin_profile.php"><i class="bi bi-calendar-check"></i> Detail Reservasi</a></li>
                <li><a href="kitchen.php">🍳 Kitchen</a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="mb-8">
                <a href="home.php" class="text-black text-2xl">Admin</a>
            </div>

            <!-- Reservasi Section -->
            <div class="bg-gray-800 rounded-xl p-4 mb-8">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Reservasi</h2>

                <div class="responsive-table">
                    <?php if ($reservasi_result->num_rows > 0): ?>
                        <table class="w-full table-auto text-left text-gray-400">
                            <thead>
                                <tr>
                                    <th class="px-2 py-2">Nama Pelanggan</th>
                                    <th class="px-2 py-2">Tanggal Reservasi</th>
                                    <th class="px-2 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $reservasi_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['tgl_reservasi']); ?></td>
                                        <td class="px-2 py-2">
                                            <a href="admin_profile.php?id_reservasi=<?php echo $row['id_reservasi']; ?>" 
                                               class="action-button bg-blue-500 text-white p-2 rounded w-full block text-center">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Tidak ada data reservasi ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pickup Section -->
            <div class="bg-gray-800 rounded-xl p-4">
                <h2 class="text-2xl font-semibold text-purple-400 mb-4">Pickup</h2>
                <div class="responsive-table">
                    <?php if ($pickup_result->num_rows > 0): ?>
                        <table class="w-full table-auto text-left text-gray-400">
                            <thead>
                                <tr>
                                    <th class="px-2 py-2">Nomor Telepon</th>
                                    <th class="px-2 py-2">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $pickup_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['nomor_telepon']); ?></td>
                                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Tidak ada data pickup ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('display-block');
        }
    </script>
</body>
</html>

<?php
// Menutup koneksi ke database
$conn->close();
?>
