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
$query = "SELECT nama_pelanggan, email FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();
$pelanggan = $result->fetch_assoc();

// Menyimpan data berdasarkan form yang dikirimkan
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reservation_submit'])) {
        $id_meja = $_POST['id_meja'];
        $nomor_telepon = $_POST['nomor_telepon'];
        $tgl_reservasi = $_POST['tgl_reservasi'];
        $waktu_reservasi = $_POST['waktu_reservasi'];
        $party_size = $_POST['party_size'];
        $tipe_tabel = $_POST['tipe_tabel'];
        
        // Gunakan prepared statement untuk mencegah SQL injection
        $sql = "INSERT INTO reservasi (id_meja, id_pelanggan, nomor_telepon, tgl_reservasi, waktu_reservasi, party_size, tipe_tabel) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", 
            $id_meja,
            $id_pelanggan,
            $nomor_telepon,
            $tgl_reservasi,
            $waktu_reservasi,
            $party_size,
            $tipe_tabel
        );

        if ($stmt->execute()) {
            $id_reservasi = $conn->insert_id;
            $message = '<script>
                alert("Anda berhasil melakukan reservasi silahkan memilih menu! ID Reservasi: ' . $id_reservasi . '");
                window.location.href = "menu.php";
            </script>';
        } else {
            $message = '<script>alert("Gagal menyimpan reservasi: ' . $stmt->error . '");</script>';
        }
        $stmt->close();
    }
    
    // Handle pickup form submission
    if (isset($_POST['pickup_submit'])) {
        $nomor_telepon = $_POST['nomor_telepon'];
        $email = $pelanggan['email'];
        
        // Cek apakah pickup sudah ada
        $select = mysqli_query($conn, "SELECT * FROM pickup WHERE id_pelanggan = '$id_pelanggan'");
        if (mysqli_num_rows($select) > 0) {
            $message = '<script>alert("Pickup sudah ada untuk ID Pelanggan ini");</script>';
        } else {
            // Generate ID pickup
            $idpickup = 'PICKUP-' . date('Ymd') . '-' . rand(1000, 9999);
            $query = "INSERT INTO pickup (id_pickup, id_pelanggan, nomor_telepon, email) 
                      VALUES (?, ?, ?, ?)";
            $stmtPickup = $conn->prepare($query);
            $stmtPickup->bind_param("ssss", $idpickup, $id_pelanggan, $nomor_telepon, $email);
            if ($stmtPickup->execute()) {
                $message = '<script>
                    alert("Data pickup berhasil dimasukkan");
                    window.location.href = "menu.php";
                </script>';
            } else {
                $message = '<script>alert("Data pickup gagal dimasukkan: ' . $stmtPickup->error . '");</script>';
            }
            $stmtPickup->close();
        }
    }
}

echo $message;
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .action-button {
            background-color: #9333EA;
            transition: all 0.3s ease;
            border-radius: 25px;
        }

        .action-button:hover {
            transform: scale(1.05);
            background-color: #7C3AED;
        }

        .form-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #9333EA;
            background: rgba(255, 255, 255, 0.15);
        }

        .tab-active {
            border-bottom: 2px solid #9333EA;
            color: #9333EA;
        }

        .action-button {
        /* Ukuran default untuk desktop */
        width: 100%;
        height: 70px;
        padding: 1rem;
        font-size: 1.125rem; /* 18px */
    }

        /* Styling untuk perangkat mobile */
        @media (max-width: 768px) {
            .action-button {
                width: 90%; /* Lebar tombol lebih kecil di mobile */
                height: 50px; /* Tinggi tombol lebih kecil di mobile */
                padding: 0.9rem; /* Padding lebih kecil */
                font-size: 0.875rem; /* 14px */
                margin-left: 20px
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

        <!-- Tabs -->
        <div class="flex justify-center space-x-8 mb-8">
            <a href="?page=reservasi" class="text-lg font-medium py-2 px-4 <?= (!isset($_GET['page']) || $_GET['page'] == 'reservasi') ? 'tab-active' : 'text-gray-400' ?>">
                Reservasi
            </a>
            <a href="?page=pickup" class="text-lg font-medium py-2 px-4 <?= (isset($_GET['page']) && $_GET['page'] == 'pickup') ? 'tab-active' : 'text-gray-400' ?>">
                Pick Up
            </a>
        </div>

        <?php if (!isset($_GET['page']) || $_GET['page'] == 'reservasi'): ?>
        <!-- Reservation Form -->
        <form action="" method="POST" class="space-y-6">
            <div class="bg-gray-800 rounded-xl p-6 space-y-4">
                <h3 class="text-xl font-semibold text-purple-400 mb-4">Informasi Pribadi</h3>
                
                <div>
                    <input type="text" name="id_reservasi" class="form-input bg-gray-700" 
                           value="<?php echo date('ymdHi') . rand(100, 999) ?>" readonly>
                </div>
                
                <div>
                    <input type="text" name="id_meja" class="form-input" 
                           placeholder="Nomor Meja" required>
                </div>
                
                <div>
                <input type="text" name="id_pelanggan" class="form-input" 
                    value="<?php echo htmlspecialchars($pelanggan['nama_pelanggan']); ?>" 
                    readonly>
                </div>
                
                <div>
                    <input type="tel" name="nomor_telepon" class="form-input" 
                           placeholder="Nomor Telepon" required>
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl p-6 space-y-4">
                <h3 class="text-xl font-semibold text-purple-400 mb-4">Detail Reservasi</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 mb-2">Tanggal</label>
                        <input type="date" name="tgl_reservasi" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-gray-400 mb-2">Waktu</label>
                        <input type="time" name="waktu_reservasi" class="form-input" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 mb-2">Jumlah Orang</label>
                    <input type="number" name="party_size" class="form-input" 
                           min="1" max="10" value="1" required>
                </div>

                <div>
                    <label class="block text-gray-400 mb-2">Lokasi Meja</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 bg-gray-700 rounded-xl cursor-pointer">
                            <input type="radio" name="tipe_tabel" value="Inside" 
                                   class="mr-3" checked>
                            <span>Inside</span>
                        </label>
                        <label class="flex items-center p-4 bg-gray-700 rounded-xl cursor-pointer">
                            <input type="radio" name="tipe_tabel" value="Outside" 
                                   class="mr-3">
                            <span>Outside</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" name="reservation_submit" 
                    class="action-button w-full py-4 text-white font-semibold text-lg">
                Konfirmasi Reservasi
            </button>
        </form>

        <?php else: ?>
        <!-- Pickup Form -->
        <form action="" method="POST" class="space-y-6">
            <div class="bg-gray-800 rounded-xl p-6 space-y-4">
                <h3 class="text-xl font-semibold text-purple-400 mb-4">Informasi Pick Up</h3>
                
                <div>
                    <input type="text" name="id_pickup" class="form-input bg-gray-700" 
                           value="<?php echo date('ymdHi') . rand(100, 999); ?>" readonly>
                </div>
                
                <div>
                    <input type="text" name="id_pelanggan" class="form-input" 
                           value="<?php echo htmlspecialchars($pelanggan['nama_pelanggan']); ?>" 
                           readonly>
                </div>
                
                <div>
                    <input type="tel" name="nomor_telepon" class="form-input" 
                           placeholder="Nomor Telepon" required>
                </div>
                
                <div>
                    <input type="text" name="email" class="form-input" 
                           value="<?php echo htmlspecialchars($pelanggan['email']); ?>" 
                           readonly>
                </div>
            </div>

            <button type="submit" name="pickup_submit" 
                    class="action-button w-full py-4 text-white font-semibold text-lg">
                Konfirmasi Pick Up
            </button>
        </form>
        <?php endif; ?>
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
            <a href="profile.php" class="text-gray-400 flex flex-col items-center">
                <i class="bi bi-person"></i>
                <span class="text-sm">Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>